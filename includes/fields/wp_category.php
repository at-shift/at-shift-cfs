<?php

class cfs_wp_category extends cfs_field
{

    function __construct() {
        $this->name = 'wp_category';
        $this->label = __( 'Post Categories', 'cfs' );
    }


    function html( $field ) {
        global $post;

        if ( ! taxonomy_exists( 'category' ) ) {
            return;
        }

        $post_id = $post instanceof WP_Post ? $post->ID : 0;
        $selected = 0 < $post_id ? wp_get_post_terms( $post_id, 'category', [ 'fields' => 'ids' ] ) : [];
        $selected = array_map( 'absint', is_array( $selected ) ? $selected : [] );
        $terms = get_terms( [
            'taxonomy'   => 'category',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ] );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            echo '<p class="notes">' . esc_html__( 'No categories found', 'cfs' ) . '</p>';
            return;
        }

        $children = [];
        foreach ( $terms as $term ) {
            $children[ (int) $term->parent ][] = $term;
        }

        echo '<div class="cfs-wp-category-list">';
        $this->render_terms( $children, 0, $selected, $field->input_name );
        echo '</div>';
    }


    private function render_terms( $children, $parent_id, $selected, $input_name ) {
        if ( empty( $children[ $parent_id ] ) ) {
            return;
        }

        echo '<ul>';
        foreach ( $children[ $parent_id ] as $term ) {
            $term_id = (int) $term->term_id;
            echo '<li>';
            echo '<label>';
            echo '<input type="checkbox" name="' . esc_attr( $input_name ) . '[]" value="' . absint( $term_id ) . '"' . checked( in_array( $term_id, $selected, true ), true, false ) . ' /> ';
            echo esc_html( $term->name );
            echo '</label>';
            $this->render_terms( $children, $term_id, $selected, $input_name );
            echo '</li>';
        }
        echo '</ul>';
    }


    function pre_save( $value, $field = null ) {
        $post_id = isset( $field->post_id ) ? absint( $field->post_id ) : 0;
        $taxonomy = get_taxonomy( 'category' );

        if ( 0 < $post_id && $taxonomy && current_user_can( 'edit_post', $post_id ) && current_user_can( $taxonomy->cap->assign_terms ) ) {
            $term_ids = array_values( array_filter( array_map( 'absint', (array) $value ) ) );
            wp_set_post_terms( $post_id, $term_ids, 'category', false );
        }

        return [];
    }
}
