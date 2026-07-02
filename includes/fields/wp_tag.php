<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_wp_tag extends cfs_field
{

    function __construct() {
        $this->name = 'wp_tag';
        $this->label = __( 'Post Tags', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        global $post;

        $post_id = $post instanceof WP_Post ? $post->ID : 0;
        $tags = 0 < $post_id ? wp_get_post_terms( $post_id, 'post_tag', [ 'fields' => 'names' ] ) : [];
        $tags = is_array( $tags ) ? $tags : [];
    ?>
        <input type="text" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" value="<?php echo esc_attr( implode( ', ', $tags ) ); ?>" />
        <p class="notes"><?php esc_html_e( 'Separate tags with commas', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
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
