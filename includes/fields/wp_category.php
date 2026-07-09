<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_wp_category extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'wp_category';
        $this->label = __( 'Post Categories (Standard / Global)', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    public function get_taxonomy_name( $field = null ) {
        $taxonomy_name = $this->get_option( $field, 'taxonomy', 'category' );
        $taxonomy_name = sanitize_key( $taxonomy_name );

        if ( taxonomy_exists( $taxonomy_name ) ) {
            return $taxonomy_name;
        }

        return taxonomy_exists( 'category' ) ? 'category' : '';
    }


    protected function get_default_term_id( $taxonomy_name ) {
        return 'category' === $taxonomy_name ? absint( get_option( 'default_category' ) ) : 0;
    }


    protected function get_no_terms_message( $taxonomy ) {
        /* translators: %s: taxonomy label. */
        return sprintf( __( 'No categories found in %s', 'atshift-fields-maintenance-for-custom-field-suite' ), $taxonomy->labels->name );
    }


    protected function get_search_placeholder() {
        return __( 'Search categories', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        global $post;

        $taxonomy_name = $this->get_taxonomy_name( $field );
        $taxonomy = get_taxonomy( $taxonomy_name );

        if ( ! $taxonomy ) {
            return;
        }

        $post_id = $post instanceof WP_Post ? $post->ID : 0;
        $selected = 0 < $post_id ? wp_get_post_terms( $post_id, $taxonomy_name, [ 'fields' => 'ids' ] ) : [];
        $selected = array_map( 'absint', is_array( $selected ) ? $selected : [] );
        $default_term_id = $this->get_default_term_id( $taxonomy_name );

        if ( empty( $selected ) && 0 < $default_term_id ) {
            $selected = [ $default_term_id ];
        }

        $terms = get_terms( [
            'taxonomy'   => $taxonomy_name,
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ] );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            echo '<p class="notes">' . esc_html( $this->get_no_terms_message( $taxonomy ) ) . '</p>';
            return;
        }

        $children = [];
        foreach ( $terms as $term ) {
            $children[ (int) $term->parent ][] = $term;
        }

        $auto_select_children = 0 < (int) $this->get_option( $field, 'auto_select_children' );
        $auto_select_parents = 0 < (int) $this->get_option( $field, 'auto_select_parents', 1 );
    ?>
        <div class="cfs-wp-category-control">
            <div class="cfs-wp-category-tools">
                <input type="search" class="cfs-wp-category-search" autocomplete="off" placeholder="<?php echo esc_attr( $this->get_search_placeholder() ); ?>" />
                <label class="cfs-wp-category-selected-only">
                    <input type="checkbox" class="cfs-wp-category-selected-only-toggle" />
                    <?php esc_html_e( 'Show selected only', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                </label>
            </div>
    <?php
        echo '<div class="cfs-wp-category-list" data-taxonomy="' . esc_attr( $taxonomy_name ) . '" data-auto-select-children="' . esc_attr( $auto_select_children ? '1' : '0' ) . '" data-auto-select-parents="' . esc_attr( $auto_select_parents ? '1' : '0' ) . '" data-default-category="' . esc_attr( $default_term_id ) . '">';
        $this->render_terms( $children, 0, $selected, $field->input_name, $default_term_id );
        echo '</div>';
        echo '</div>';
    }


    protected function render_terms( $children, $parent_id, $selected, $input_name, $default_term_id ) {
        if ( empty( $children[ $parent_id ] ) ) {
            return;
        }

        echo '<ul>';
        foreach ( $children[ $parent_id ] as $term ) {
            $term_id = (int) $term->term_id;
            $term_name = $this->get_term_name( $term );
            $is_selected = in_array( $term_id, $selected, true );
            $has_children = ! empty( $children[ $term_id ] );
            $classes = [ 'cfs-wp-category-item' ];

            if ( $is_selected ) {
                $classes[] = 'is-selected';
            }
            if ( $has_children ) {
                $classes[] = 'has-children';
            }
            if ( $default_term_id === $term_id ) {
                $classes[] = 'is-default-category';
            }

            echo '<li class="' . esc_attr( implode( ' ', $classes ) ) . '" data-term-name="' . esc_attr( strtolower( $term_name ) ) . '">';
            echo '<label>';
            echo '<input type="checkbox" name="' . esc_attr( $input_name ) . '[]" value="' . absint( $term_id ) . '"' . checked( $is_selected, true, false ) . ' /> ';
            echo esc_html( $term_name );
            echo '</label>';
            $this->render_terms( $children, $term_id, $selected, $input_name, $default_term_id );
            echo '</li>';
        }
        echo '</ul>';
    }


    protected function get_term_name( $term ) {
        if ( 'Uncategorized' === $term->name ) {
            return __( 'Uncategorized', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        return $term->name;
    }


    function pre_save( $value, $field = null ) {
        $post_id = isset( $field->post_id ) ? absint( $field->post_id ) : 0;
        $taxonomy_name = $this->get_taxonomy_name( $field );
        $taxonomy = get_taxonomy( $taxonomy_name );

        if ( 0 < $post_id && $taxonomy && current_user_can( 'edit_post', $post_id ) && current_user_can( $taxonomy->cap->assign_terms ) ) {
            $term_ids = array_values( array_filter( array_map( 'absint', (array) $value ) ) );
            $default_term_id = $this->get_default_term_id( $taxonomy_name );
            $auto_select_parents = 0 < (int) $this->get_option( $field, 'auto_select_parents', 1 );

            if ( empty( $term_ids ) && 0 < $default_term_id ) {
                $term_ids = [ $default_term_id ];
            }

            if ( $auto_select_parents ) {
                $term_ids = $this->include_parent_terms( $term_ids, $taxonomy_name );
            }

            if ( 0 < $default_term_id && 1 < count( $term_ids ) ) {
                $term_ids = array_values( array_diff( $term_ids, [ $default_term_id ] ) );
            }

            wp_set_post_terms( $post_id, $term_ids, $taxonomy_name, false );
        }

        return [];
    }


    function options_html( $key, $field ) {
        $category_taxonomies = $this->get_category_taxonomies();
        $choices = [];

        foreach ( $category_taxonomies as $taxonomy_name => $taxonomy ) {
            if ( 'category' === $taxonomy_name ) {
                $choices[ $taxonomy_name ] = __( 'Standard Category', 'atshift-fields-maintenance-for-custom-field-suite' ) . ' (category)';
                continue;
            }

            $choices[ $taxonomy_name ] = sprintf(
                '%1$s (%2$s)',
                $taxonomy->labels->name,
                $taxonomy_name
            );
        }
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Category Source', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <p class="description"><?php esc_html_e( 'Choose the standard category or a registered Shared Taxonomy to show and save with this field.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][taxonomy]',
                        'options' => [
                            'choices' => $choices,
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'taxonomy', 'category' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Category Selection', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][auto_select_children]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'auto_select_children' ),
                        'options' => [ 'message' => __( 'Selecting a parent also selects all child categories', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
                <br />
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][auto_select_parents]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'auto_select_parents', 1 ),
                        'options' => [ 'message' => __( 'Selecting a child also selects its parent categories', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    protected function get_category_taxonomies() {
        $taxonomies = get_taxonomies( [
            'show_ui'      => true,
            'hierarchical' => true,
        ], 'objects' );

        if ( isset( $taxonomies['category'] ) ) {
            $category = $taxonomies['category'];
            unset( $taxonomies['category'] );
            $taxonomies = [ 'category' => $category ] + $taxonomies;
        }

        return apply_filters( 'atshift_cfs_wp_category_taxonomies', $taxonomies );
    }


    function pre_save_field( $field ) {
        $taxonomy_name = isset( $field['options']['taxonomy'] ) ? sanitize_key( $field['options']['taxonomy'] ) : 'category';
        $field['options']['taxonomy'] = taxonomy_exists( $taxonomy_name ) ? $taxonomy_name : 'category';
        $field['options']['auto_select_children'] = empty( $field['options']['auto_select_children'] ) ? 0 : 1;
        $field['options']['auto_select_parents'] = empty( $field['options']['auto_select_parents'] ) ? 0 : 1;

        return $field;
    }


    function input_head( $field = null ) {
        static $inserted = false;

        if ( $inserted ) {
            return;
        }

        $inserted = true;

        $script_path = ATSHIFT_CFS_DIR . '/assets/js/wp-category.js';
        $script_url  = ATSHIFT_CFS_URL . '/assets/js/wp-category.js';
        $version     = file_exists( $script_path ) ? ATSHIFT_CFS_VERSION . '.' . filemtime( $script_path ) : ATSHIFT_CFS_VERSION;

        wp_enqueue_script(
            'atshift-cfs-wp-category',
            $script_url,
            [ 'jquery' ],
            $version,
            true
        );
    }


    protected function include_parent_terms( $term_ids, $taxonomy_name = 'category' ) {
        $term_ids = array_values( array_filter( array_map( 'absint', (array) $term_ids ) ) );
        $expanded = $term_ids;

        foreach ( $term_ids as $term_id ) {
            $ancestors = get_ancestors( $term_id, $taxonomy_name, 'taxonomy' );

            if ( ! empty( $ancestors ) ) {
                $expanded = array_merge( $expanded, array_map( 'absint', $ancestors ) );
            }
        }

        return array_values( array_unique( array_filter( $expanded ) ) );
    }
}
