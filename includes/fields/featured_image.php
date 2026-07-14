<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_featured_image extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'featured_image';
        $this->label = __( 'Featured Image (Native)', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        global $post;

        $post_id = $post instanceof WP_Post ? $post->ID : 0;
        $attachment_id = 0 < $post_id ? absint( get_post_thumbnail_id( $post_id ) ) : 0;
        $preview = '';

        if ( 0 < $attachment_id ) {
            $preview = wp_get_attachment_image( $attachment_id, 'thumbnail' );
        }

        $css = empty( $attachment_id ) ? [ '', ' hidden' ] : [ ' hidden', '' ];
    ?>
        <span class="featured_image_preview"><?php echo wp_kses_post( $preview ); ?></span>
        <input type="button" class="media button add<?php echo esc_attr( $css[0] ); ?>" value="<?php esc_attr_e( 'Select Image', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
        <input type="button" class="media button remove<?php echo esc_attr( $css[1] ); ?>" value="<?php esc_attr_e( 'Remove Image', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
        <input type="hidden" name="<?php echo esc_attr( $field->input_name ); ?>" class="featured_image_value" value="<?php echo esc_attr( $attachment_id ); ?>" />
    <?php
    }


    function input_head( $field = null ) {
        wp_enqueue_media();
    ?>
        <?php wp_add_inline_script( 'atshift-cfs-validation', atshift_cfs_capture_output( function() { ?>
        (function($) {
            $(function() {
                var cfsFeaturedImageFrame;

                $(document).on('click', '.cfs_featured_image .media.button.add', function() {
                    var $button = $(this);

                    cfsFeaturedImageFrame = wp.media({
                        title: <?php echo wp_json_encode( __( 'Featured Image', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>,
                        button: {
                            text: <?php echo wp_json_encode( __( 'Select Image', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>
                        },
                        multiple: false,
                        library: {
                            type: 'image'
                        }
                    });

                    cfsFeaturedImageFrame.on('select', function() {
                        var attachment = cfsFeaturedImageFrame.state().get('selection').first().toJSON();
                        var imageUrl = attachment.url;

                        if (attachment.sizes && attachment.sizes.thumbnail) {
                            imageUrl = attachment.sizes.thumbnail.url;
                        }

                        $button.hide();
                        $button.siblings('.media.button.remove').show();
                        $button.siblings('.featured_image_value').val(attachment.id);
                        $button.siblings('.featured_image_preview').empty().append($('<img>', {
                            src: imageUrl,
                            alt: ''
                        }));
                    });

                    cfsFeaturedImageFrame.open();
                });

                $(document).on('click', '.cfs_featured_image .media.button.remove', function() {
                    $(this).siblings('.featured_image_preview').html('');
                    $(this).siblings('.featured_image_value').val('');
                    $(this).siblings('.media.button.add').show();
                    $(this).hide();
                });
            });
        })(jQuery);
        <?php } ) ); ?>
    <?php
    }


    function pre_save( $value, $field = null ) {
        $post_id = isset( $field->post_id ) ? absint( $field->post_id ) : 0;
        $attachment_id = is_array( $value ) ? reset( $value ) : $value;
        $attachment_id = absint( $attachment_id );

        if ( 0 < $post_id && current_user_can( 'edit_post', $post_id ) ) {
            if ( 0 < $attachment_id && wp_attachment_is_image( $attachment_id ) ) {
                set_post_thumbnail( $post_id, $attachment_id );
            }
            else {
                delete_post_thumbnail( $post_id );
            }
        }

        return [];
    }
}
