<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_wp_tag extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'wp_tag';
        $this->label = __( 'Post Tags (Native)', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function input_head( $field = null ) {
        static $inserted = false;

        if ( $inserted ) {
            return;
        }

        $inserted = true;

        $script_path = ATSHIFT_CFS_DIR . '/assets/js/wp-tag.js';
        $script_url  = ATSHIFT_CFS_URL . '/assets/js/wp-tag.js';
        $version     = file_exists( $script_path ) ? ATSHIFT_CFS_VERSION . '.' . filemtime( $script_path ) : ATSHIFT_CFS_VERSION;

        wp_enqueue_script(
            'atshift-cfs-wp-tag',
            $script_url,
            [ 'jquery' ],
            $version,
            true
        );
    }


    function html( $field ) {
        global $post;

        $post_id = $post instanceof WP_Post ? $post->ID : 0;
        $tags = 0 < $post_id ? wp_get_post_terms( $post_id, 'post_tag', [ 'fields' => 'names' ] ) : [];
        $tags = is_array( $tags ) ? $tags : [];
        $popular_tags = get_terms( [
            'taxonomy'   => 'post_tag',
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => 45,
            'hide_empty' => false,
        ] );
        $popular_tags = is_wp_error( $popular_tags ) ? [] : $popular_tags;
        $input_class = trim( $field->input_class . ' cfs-wp-tag-input' );
    ?>
        <div class="cfs-wp-tag-field">
            <input type="text" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $input_class ); ?>" value="<?php echo esc_attr( implode( ', ', $tags ) ); ?>" />
            <div class="cfs-wp-tag-selected" data-remove-label="<?php echo esc_attr__( 'Remove', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" aria-live="polite"></div>
            <p class="notes"><?php esc_html_e( 'Separate tags with commas', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
            <div class="cfs-wp-tag-popular">
                <button type="button" class="button-link cfs-wp-tag-popular-toggle" aria-expanded="false">
                    <?php esc_html_e( 'Choose from the most used tags', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                </button>
                <div class="cfs-wp-tag-popular-panel" hidden>
                    <?php if ( ! empty( $popular_tags ) ) : ?>
                        <?php foreach ( $popular_tags as $popular_tag ) : ?>
                            <button type="button" class="button-link cfs-wp-tag-popular-tag" data-tag-name="<?php echo esc_attr( $popular_tag->name ); ?>">
                                <?php echo esc_html( $popular_tag->name ); ?>
                            </button>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <span class="cfs-wp-tag-popular-empty"><?php esc_html_e( 'No frequently used tags are available yet.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
    }


    function pre_save( $value, $field = null ) {
        $post_id = isset( $field->post_id ) ? absint( $field->post_id ) : 0;
        $taxonomy = get_taxonomy( 'post_tag' );

        if ( 0 < $post_id && $taxonomy && current_user_can( 'edit_post', $post_id ) && current_user_can( $taxonomy->cap->assign_terms ) ) {
            $value = is_array( $value ) ? reset( $value ) : $value;
            $tags = array_map( 'trim', explode( ',', (string) $value ) );
            $tags = array_values( array_filter( array_map( 'sanitize_text_field', $tags ) ) );
            wp_set_post_terms( $post_id, $tags, 'post_tag', false );
        }

        return [];
    }
}
