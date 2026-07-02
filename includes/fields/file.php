<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_file extends cfs_field
{

    function __construct() {
        $this->name = 'file';
        $this->label = __( 'File Upload', 'at-shift-cfs' );
    }


    function html( $field ) {
        $field->value = null === $field->value ? '' : $field->value;
        $file_url = $field->value;

        if ( ctype_digit( (string) $field->value ) ) {
            if ( wp_attachment_is_image( $field->value ) ) {
                $file_url = wp_get_attachment_image( $field->value, 'medium', false, [ 'class' => 'cfs-file-preview-image' ] );
                if ( empty( $file_url ) ) {
                    $image_src = wp_get_attachment_image_src( $field->value, 'full' );
                    $file_url = empty( $image_src[0] ) ? '' : '<img src="' . esc_url( $image_src[0] ) . '" class="cfs-file-preview-image" alt="" />';
                }
            }
            else
            {
                $file_url = wp_get_attachment_url( $field->value );
                $filename = substr( $file_url, strrpos( $file_url, '/' ) + 1 );
                $file_url = '<a href="' . esc_url( $file_url ) . '" target="_blank">' . esc_html( $filename ) . '</a>';
            }
        }

        // CSS logic for "Add" / "Remove" buttons
        $css = empty( $field->value ) ? [ '', ' hidden' ] : [ ' hidden', '' ];
    ?>
        <span class="file_url"><?php echo wp_kses_post( $file_url ); ?></span>
        <input type="button" class="media button add<?php echo esc_attr( $css[0] ); ?>" value="<?php esc_attr_e( 'Add File', 'at-shift-cfs' ); ?>" />
        <input type="button" class="media button remove<?php echo esc_attr( $css[1] ); ?>" value="<?php esc_attr_e( 'Remove', 'at-shift-cfs' ); ?>" />
        <input type="hidden" name="<?php echo esc_attr( $field->input_name ); ?>" class="file_value" value="<?php echo esc_attr( $field->value ); ?>" />
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'File Type', 'at-shift-cfs' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][file_type]',
                        'options' => [
                            'choices' => [
                                'file'  => __( 'Any', 'at-shift-cfs' ),
                                'image' => __( 'Image', 'at-shift-cfs' ),
                                'audio' => __( 'Audio', 'at-shift-cfs' ),
                                'video' => __( 'Video', 'at-shift-cfs' )
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'file_type', 'file' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Return Value', 'at-shift-cfs' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][return_value]',
                        'options' => [
                            'choices' => [
                                'url' => __( 'File URL', 'at-shift-cfs' ),
                                'id' => __( 'Attachment ID', 'at-shift-cfs' )
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'return_value', 'url' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Validation', 'at-shift-cfs' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][required]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'required' ),
                        'options' => [ 'message' => __( 'This is a required field', 'at-shift-cfs' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    function input_head( $field = null ) {
        wp_enqueue_media();
    ?>
        <?php ob_start(); ?>
        .cfs_frame .media-frame-menu {
            display: none;
        }

        .cfs_frame .media-frame-title,
        .cfs_frame .media-frame-router,
        .cfs_frame .media-frame-content,
        .cfs_frame .media-frame-toolbar {
            left: 0;
        }
        <?php wp_add_inline_style( 'cfs-input', ob_get_clean() ); ?>

        <?php ob_start(); ?>
        (function($) {
            $(function() {

                var cfs_frame;
                var $activeButton;

                function getImagePreviewUrl(attachment) {
                    if (attachment.sizes) {
                        if (attachment.sizes.medium) {
                            return attachment.sizes.medium.url;
                        }
                        if (attachment.sizes.thumbnail) {
                            return attachment.sizes.thumbnail.url;
                        }
                        if (attachment.sizes.full) {
                            return attachment.sizes.full.url;
                        }
                    }

                    return attachment.url;
                }

                $(document).on('click', '.cfs_file .media.button.add', function(e) {
                    $activeButton = $(this);

                    if (cfs_frame) {
                        cfs_frame.open();
                        return;
                    }

                    cfs_frame = wp.media.frames.cfs_frame = wp.media({
                        className: 'media-frame cfs_frame',
                        frame: 'post',
                        multiple: false,
                        library: {
                            type: 'image'
                        }
                    });

                    cfs_frame.on('insert', function() {
                        var attachment = cfs_frame.state().get('selection').first().toJSON();
                        var $button = $activeButton;
                        var $preview = $button.siblings('.file_url').empty();

                        if ('image' == attachment.type) {
                            $preview.append($('<img>', {
                                src: getImagePreviewUrl(attachment),
                                alt: '',
                                class: 'cfs-file-preview-image'
                            }));
                        }
                        else {
                            $preview.append($('<a>', {
                                href: attachment.url,
                                target: '_blank',
                                text: attachment.filename || attachment.url
                            }));
                        }
                        $button.hide();
                        $button.siblings('.media.button.remove').show();
                        $button.siblings('.file_value').val(attachment.id);
                    });

                    cfs_frame.open();
                    cfs_frame.content.mode('upload');
                });

                $(document).on('click', '.cfs_file .media.button.remove', function() {
                    $(this).siblings('.file_url').html('');
                    $(this).siblings('.file_value').val('');
                    $(this).siblings('.media.button.add').show();
                    $(this).hide();
                });
            });
        })(jQuery);
        <?php wp_add_inline_script( 'cfs-validation', ob_get_clean() ); ?>
    <?php
    }


    function format_value_for_api( $value, $field = null ) {
        if ( ctype_digit( $value ) ) {
            $return_value = $this->get_option( $field, 'return_value', 'url' );
            return ( 'id' == $return_value ) ? (int) $value : wp_get_attachment_url( $value );
        }
        return $value;
    }
}
