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
        $default_category = absint( get_option( 'default_category' ) );

        if ( empty( $selected ) && 0 < $default_category ) {
            $selected = [ $default_category ];
        }

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

        $auto_select_children = 0 < (int) $this->get_option( $field, 'auto_select_children' );
        $auto_select_parents = 0 < (int) $this->get_option( $field, 'auto_select_parents', 1 );
    ?>
        <div class="cfs-wp-category-tools">
            <input type="search" class="cfs-wp-category-search" autocomplete="off" placeholder="<?php esc_attr_e( 'Search categories', 'cfs' ); ?>" />
            <label class="cfs-wp-category-selected-only">
                <input type="checkbox" class="cfs-wp-category-selected-only-toggle" />
                <?php esc_html_e( 'Show selected only', 'cfs' ); ?>
            </label>
        </div>
    <?php
        echo '<div class="cfs-wp-category-list" data-auto-select-children="' . esc_attr( $auto_select_children ? '1' : '0' ) . '" data-auto-select-parents="' . esc_attr( $auto_select_parents ? '1' : '0' ) . '" data-default-category="' . esc_attr( $default_category ) . '">';
        $this->render_terms( $children, 0, $selected, $field->input_name, $default_category );
        echo '</div>';
    }


    private function render_terms( $children, $parent_id, $selected, $input_name, $default_category ) {
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
            if ( $default_category === $term_id ) {
                $classes[] = 'is-default-category';
            }

            echo '<li class="' . esc_attr( implode( ' ', $classes ) ) . '" data-term-name="' . esc_attr( strtolower( $term_name ) ) . '">';
            echo '<label>';
            echo '<input type="checkbox" name="' . esc_attr( $input_name ) . '[]" value="' . absint( $term_id ) . '"' . checked( $is_selected, true, false ) . ' /> ';
            echo esc_html( $term_name );
            echo '</label>';
            $this->render_terms( $children, $term_id, $selected, $input_name, $default_category );
            echo '</li>';
        }
        echo '</ul>';
    }


    private function get_term_name( $term ) {
        if ( 'Uncategorized' === $term->name ) {
            return __( 'Uncategorized', 'cfs' );
        }

        return $term->name;
    }


    function pre_save( $value, $field = null ) {
        $post_id = isset( $field->post_id ) ? absint( $field->post_id ) : 0;
        $taxonomy = get_taxonomy( 'category' );

        if ( 0 < $post_id && $taxonomy && current_user_can( 'edit_post', $post_id ) && current_user_can( $taxonomy->cap->assign_terms ) ) {
            $term_ids = array_values( array_filter( array_map( 'absint', (array) $value ) ) );
            $default_category = absint( get_option( 'default_category' ) );
            $auto_select_parents = 0 < (int) $this->get_option( $field, 'auto_select_parents', 1 );

            if ( empty( $term_ids ) && 0 < $default_category ) {
                $term_ids = [ $default_category ];
            }

            if ( $auto_select_parents ) {
                $term_ids = $this->include_parent_terms( $term_ids );
            }

            if ( 0 < $default_category && 1 < count( $term_ids ) ) {
                $term_ids = array_values( array_diff( $term_ids, [ $default_category ] ) );
            }

            wp_set_post_terms( $post_id, $term_ids, 'category', false );
        }

        return [];
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Category Selection', 'cfs' ); ?></label>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][auto_select_children]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'auto_select_children' ),
                        'options' => [ 'message' => __( 'Selecting a parent also selects all child categories', 'cfs' ) ],
                    ] );
                ?>
                <br />
                <?php
                    CFS()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][auto_select_parents]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'auto_select_parents', 1 ),
                        'options' => [ 'message' => __( 'Selecting a child also selects its parent categories', 'cfs' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    function input_head( $field = null ) {
    ?>
        <script>
        (function($) {
            function refreshCategoryState($list) {
                $list.find('.cfs-wp-category-item').each(function() {
                    var $item = $(this);
                    var checked = $item.children('label').find('input[type="checkbox"]').prop('checked');
                    $item.toggleClass('is-selected', checked);
                });
            }

            function applyCategoryFilter($field) {
                var query = $.trim($field.find('.cfs-wp-category-search').val()).toLowerCase();
                var selectedOnly = $field.find('.cfs-wp-category-selected-only-toggle').prop('checked');
                var $items = $field.find('.cfs-wp-category-item');

                $items.removeClass('is-filter-hidden is-filter-match');

                $items.each(function() {
                    var $item = $(this);
                    var name = $item.attr('data-term-name') || '';
                    var matchesQuery = '' === query || -1 < name.indexOf(query);
                    var matchesSelected = !selectedOnly || $item.children('label').find('input[type="checkbox"]').prop('checked');

                    if (matchesQuery && matchesSelected) {
                        $item.addClass('is-filter-match');
                        $item.parents('.cfs-wp-category-item').addClass('is-filter-match');
                    }
                });

                if ('' !== query || selectedOnly) {
                    $items.not('.is-filter-match').addClass('is-filter-hidden');
                }
            }

            $(document).on('change', '.cfs-wp-category-list input[type="checkbox"]', function() {
                var $input = $(this);
                var $item = $input.closest('.cfs-wp-category-item');
                var $list = $input.closest('.cfs-wp-category-list');
                var checked = $input.prop('checked');

                if (checked && '1' === $list.attr('data-auto-select-children')) {
                    $item.find('ul input[type="checkbox"]').prop('checked', checked);
                }
                else if (!checked) {
                    $item.find('ul input[type="checkbox"]').prop('checked', false);
                }

                if (checked && '1' === $list.attr('data-auto-select-parents')) {
                    $item.parents('.cfs-wp-category-item').children('label').find('input[type="checkbox"]').prop('checked', true);
                }
                else if (!checked && '1' === $list.attr('data-auto-select-parents')) {
                    $item.parents('.cfs-wp-category-item').each(function() {
                        var $parent = $(this);
                        var hasCheckedChildren = $parent.children('ul').find('input[type="checkbox"]:checked').length > 0;

                        if (!hasCheckedChildren) {
                            $parent.children('label').find('input[type="checkbox"]').prop('checked', false);
                        }
                    });
                }

                var defaultCategory = $list.attr('data-default-category');

                if (checked) {
                    var isDefaultCategory = defaultCategory && defaultCategory === $input.val();

                    if (isDefaultCategory) {
                        $list.find('input[type="checkbox"]').not($input).prop('checked', false);
                    }
                    else if (defaultCategory) {
                        $list.find('input[type="checkbox"][value="' + defaultCategory + '"]').prop('checked', false);
                    }
                }
                else if (defaultCategory && 0 === $list.find('input[type="checkbox"]:checked').length) {
                    $list.find('input[type="checkbox"][value="' + defaultCategory + '"]').prop('checked', true);
                }

                refreshCategoryState($list);
                applyCategoryFilter($input.closest('.field'));
            });

            $(document).on('input change', '.cfs-wp-category-search, .cfs-wp-category-selected-only-toggle', function() {
                applyCategoryFilter($(this).closest('.field'));
            });

            $(function() {
                $('.cfs-wp-category-list').each(function() {
                    refreshCategoryState($(this));
                });
            });
        })(jQuery);
        </script>
    <?php
    }


    private function include_parent_terms( $term_ids ) {
        $term_ids = array_values( array_filter( array_map( 'absint', (array) $term_ids ) ) );
        $expanded = $term_ids;

        foreach ( $term_ids as $term_id ) {
            $ancestors = get_ancestors( $term_id, 'category', 'taxonomy' );

            if ( ! empty( $ancestors ) ) {
                $expanded = array_merge( $expanded, array_map( 'absint', $ancestors ) );
            }
        }

        return array_values( array_unique( array_filter( $expanded ) ) );
    }
}
