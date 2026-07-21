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

        if ( is_wp_error( $terms ) ) {
            echo '<p class="notes">' . esc_html( $this->get_no_terms_message( $taxonomy ) ) . '</p>';
            return;
        }

        $terms = is_array( $terms ) ? $terms : [];
        $can_add_terms = $this->current_user_can_add_terms( $field, $taxonomy );

        if ( empty( $terms ) && ! $can_add_terms ) {
            echo '<p class="notes">' . esc_html( $this->get_no_terms_message( $taxonomy ) ) . '</p>';
            return;
        }

        $children = [];
        foreach ( $terms as $term ) {
            $children[ (int) $term->parent ][] = $term;
        }

        $auto_select_children = 0 < (int) $this->get_option( $field, 'auto_select_children' );
        $auto_select_parents = 0 < (int) $this->get_option( $field, 'auto_select_parents', 1 );
        $layout_parent_horizontal = 0 < (int) $this->get_option( $field, 'layout_parent_horizontal' );
        $layout_children_horizontal = 0 < (int) $this->get_option( $field, 'layout_children_horizontal' );
        $hide_search = 0 < (int) $this->get_option( $field, 'hide_search' );
        $hide_selected_filter = 0 < (int) $this->get_option( $field, 'hide_selected_filter' );
        $list_classes = [ 'cfs-wp-category-list' ];

        if ( $layout_parent_horizontal ) {
            $list_classes[] = 'is-parent-horizontal';
        }
        if ( $layout_children_horizontal ) {
            $list_classes[] = 'is-children-horizontal';
        }
    ?>
        <div class="cfs-wp-category-control">
            <?php if ( ! empty( $terms ) && ( ! $hide_search || ! $hide_selected_filter ) ) : ?>
            <div class="cfs-wp-category-tools">
                <?php if ( ! $hide_search ) : ?>
                <input type="search" class="cfs-wp-category-search" autocomplete="off" placeholder="<?php echo esc_attr( $this->get_search_placeholder() ); ?>" />
                <?php endif; ?>
                <?php if ( ! $hide_selected_filter ) : ?>
                <label class="cfs-wp-category-selected-only">
                    <input type="checkbox" class="cfs-wp-category-selected-only-toggle" />
                    <?php esc_html_e( 'Show selected only', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                </label>
                <?php endif; ?>
            </div>
            <?php endif; ?>
    <?php
        if ( empty( $terms ) ) {
            echo '<p class="notes cfs-wp-category-empty">' . esc_html( $this->get_no_terms_message( $taxonomy ) ) . '</p>';
        }

        echo '<div class="' . esc_attr( implode( ' ', $list_classes ) ) . '" data-taxonomy="' . esc_attr( $taxonomy_name ) . '" data-input-name="' . esc_attr( $field->input_name ) . '" data-auto-select-children="' . esc_attr( $auto_select_children ? '1' : '0' ) . '" data-auto-select-parents="' . esc_attr( $auto_select_parents ? '1' : '0' ) . '" data-default-category="' . esc_attr( $default_term_id ) . '">';
        $this->render_terms( $children, 0, $selected, $field->input_name, $default_term_id );
        echo '</div>';
        $this->render_add_term_control( $field, $taxonomy_name, $taxonomy, $children, $post_id );
        echo '</div>';
    }


    protected function render_terms( $children, $parent_id, $selected, $input_name, $default_term_id, $depth = 0 ) {
        if ( empty( $children[ $parent_id ] ) ) {
            return;
        }

        echo '<ul class="' . esc_attr( 0 === $depth ? 'cfs-wp-category-root' : 'children' ) . '">';
        foreach ( $children[ $parent_id ] as $term ) {
            $term_id = (int) $term->term_id;
            $term_name = $this->get_term_name( $term );
            $is_selected = in_array( $term_id, $selected, true );
            $has_children = ! empty( $children[ $term_id ] );
            $classes = [ 'cfs-wp-category-item', 'depth-' . min( 4, absint( $depth ) ) ];

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
            $this->render_terms( $children, $term_id, $selected, $input_name, $default_term_id, $depth + 1 );
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


    protected function render_add_term_control( $field, $taxonomy_name, $taxonomy, $children, $post_id ) {
        if ( ! $this->current_user_can_add_terms( $field, $taxonomy ) ) {
            return;
        }

        $field_id = isset( $field->id ) ? absint( $field->id ) : 0;
        $post_id = absint( $post_id );

        if ( 1 > $field_id ) {
            return;
        }
    ?>
        <div
            class="cfs-wp-category-add"
            data-taxonomy="<?php echo esc_attr( $taxonomy_name ); ?>"
            data-field-id="<?php echo esc_attr( $field_id ); ?>"
            data-post-id="<?php echo esc_attr( $post_id ); ?>"
            data-nonce="<?php echo esc_attr( wp_create_nonce( 'atshift_cfs_add_wp_category_term' ) ); ?>"
        >
            <button type="button" class="button-link cfs-wp-category-add-toggle" aria-expanded="false">
                <?php esc_html_e( '+ Add category', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
            </button>
            <div class="cfs-wp-category-add-panel" hidden>
                <input type="text" class="cfs-wp-category-add-name" autocomplete="off" placeholder="<?php esc_attr_e( 'Category name', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
                <select class="cfs-wp-category-add-parent">
                    <option value="0"><?php esc_html_e( 'Parent category', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></option>
                    <?php $this->render_parent_options( $children ); ?>
                </select>
                <div class="cfs-wp-category-add-actions">
                    <button type="button" class="button cfs-wp-category-add-submit"><?php esc_html_e( 'Add category', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></button>
                    <button type="button" class="button-link cfs-wp-category-add-cancel"><?php esc_html_e( 'Cancel', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></button>
                </div>
                <p class="description cfs-wp-category-add-message" aria-live="polite"></p>
            </div>
        </div>
    <?php
    }


    protected function render_parent_options( $children, $parent_id = 0, $depth = 0 ) {
        if ( empty( $children[ $parent_id ] ) ) {
            return;
        }

        foreach ( $children[ $parent_id ] as $term ) {
            $term_id = absint( $term->term_id );
            $prefix = str_repeat( '&mdash; ', absint( $depth ) );

            echo '<option value="' . esc_attr( $term_id ) . '" data-depth="' . esc_attr( $depth ) . '">' . wp_kses_post( $prefix ) . esc_html( $this->get_term_name( $term ) ) . '</option>';
            $this->render_parent_options( $children, $term_id, $depth + 1 );
        }
    }


    public function current_user_can_add_terms_for_request( $field_id, $post_id, $taxonomy_name ) {
        $field_id = absint( $field_id );
        $post_id = absint( $post_id );
        $taxonomy_name = sanitize_key( $taxonomy_name );

        if ( 1 > $field_id || ! taxonomy_exists( $taxonomy_name ) ) {
            return false;
        }

        if ( 0 < $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
            return false;
        }

        $params = [
            'field_id'   => [ $field_id ],
            'field_type' => [ $this->name ],
        ];

        if ( 0 < $post_id ) {
            $params['post_id'] = $post_id;
        }

        $fields = atshift_fields_maintenance_for_custom_field_suite()->api->find_input_fields( $params );

        foreach ( $fields as $field ) {
            $field = (object) $field;

            if ( $taxonomy_name !== $this->get_taxonomy_name( $field ) ) {
                continue;
            }

            return $this->current_user_can_add_terms( $field, get_taxonomy( $taxonomy_name ) );
        }

        return false;
    }


    protected function current_user_can_add_terms( $field, $taxonomy ) {
        if ( ! $taxonomy || empty( $field->id ) || 1 > (int) $this->get_option( $field, 'allow_add_terms' ) ) {
            return false;
        }

        $edit_terms_cap = isset( $taxonomy->cap->edit_terms ) ? $taxonomy->cap->edit_terms : 'manage_categories';

        if ( ! current_user_can( $edit_terms_cap ) ) {
            return false;
        }

        $allowed_roles = $this->get_allowed_add_roles( $field );

        if ( empty( $allowed_roles ) ) {
            return true;
        }

        $user = wp_get_current_user();
        return $user instanceof WP_User && ! empty( array_intersect( $allowed_roles, (array) $user->roles ) );
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

            if ( $this->should_remove_default_term( $default_term_id, $term_ids, $taxonomy_name, $field ) ) {
                $term_ids = array_values( array_diff( $term_ids, [ $default_term_id ] ) );
            }

            wp_set_post_terms( $post_id, $term_ids, $taxonomy_name, false );
        }

        return [];
    }


    function options_html( $key, $field ) {
        $category_taxonomies = $this->get_category_taxonomies();
        $choices = [];
        $role_choices = $this->get_role_choices();
        $allowed_add_roles = $this->get_allowed_add_roles( $field );

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
                <div class="cfs_tooltip">?
                    <div class="tooltip_inner"><?php esc_html_e( 'Choose the standard category or a registered Shared Taxonomy to show and save with this field.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                </div>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][taxonomy]',
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
                <label><?php esc_html_e( 'Category Display', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][layout_parent_horizontal]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'layout_parent_horizontal' ),
                        'options' => [ 'message' => __( 'Arrange parent categories horizontally', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
                <br />
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][layout_children_horizontal]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'layout_children_horizontal' ),
                        'options' => [ 'message' => __( 'Arrange child and grandchild categories horizontally', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
                <p class="description"><?php esc_html_e( 'Leave both unchecked to use the traditional category tree.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][hide_search]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'hide_search' ),
                        'options' => [ 'message' => __( 'Hide the category search box', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
                <br />
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][hide_selected_filter]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'hide_selected_filter' ),
                        'options' => [ 'message' => __( 'Hide the selected-only filter', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Category Adding', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][allow_add_terms]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'allow_add_terms' ),
                        'options' => [ 'message' => __( 'Show + Add category', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
                <p class="description"><?php esc_html_e( 'Users must also have permission to create terms for the selected taxonomy.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
                <div class="cfs-wp-category-add-roles">
                    <label>
                        <?php esc_html_e( 'Allowed Add Roles', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                        <div class="cfs_tooltip">?
                            <div class="tooltip_inner"><?php esc_html_e( 'Select the roles that can add categories from this field. Leave blank to allow any role that can create terms for this taxonomy.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                        </div>
                    </label>
                    <input type="hidden" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][add_allowed_roles][]" value="" />
                    <?php
                        atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                            'type' => 'select',
                            'input_class' => 'select2 cfs-wp-category-role-select',
                            'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][add_allowed_roles]',
                            'options' => [
                                'multiple' => '1',
                                'choices' => $role_choices,
                                'placeholder' => __( 'Leave blank to allow any role that can create categories', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            ],
                            'value' => $allowed_add_roles,
                        ] );
                    ?>
                </div>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Validation', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][required]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'required' ),
                        'options' => [ 'message' => __( 'This is a required field', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
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
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][auto_select_children]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'auto_select_children' ),
                        'options' => [ 'message' => __( 'Selecting a parent also selects all child categories', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
                <br />
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][auto_select_parents]',
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


    protected function get_allowed_add_roles( $field = null ) {
        $roles = $this->get_option( $field, 'add_allowed_roles', [] );
        $roles = array_filter( array_map( 'sanitize_key', (array) $roles ) );
        return array_values( array_unique( $roles ) );
    }


    protected function get_role_choices() {
        $choices = [];

        foreach ( wp_roles()->roles as $role_key => $role ) {
            $role_label = isset( $role['name'] ) ? translate_user_role( $role['name'] ) : $role_key;
            $choices[ sanitize_key( $role_key ) ] = $role_label;
        }

        return $choices;
    }


    function pre_save_field( $field ) {
        $taxonomy_name = isset( $field['options']['taxonomy'] ) ? sanitize_key( $field['options']['taxonomy'] ) : 'category';
        $field['options']['taxonomy'] = taxonomy_exists( $taxonomy_name ) ? $taxonomy_name : 'category';
        $field['options']['required'] = empty( $field['options']['required'] ) ? 0 : 1;
        $field['options']['auto_select_children'] = empty( $field['options']['auto_select_children'] ) ? 0 : 1;
        $field['options']['auto_select_parents'] = empty( $field['options']['auto_select_parents'] ) ? 0 : 1;
        $field['options']['layout_parent_horizontal'] = empty( $field['options']['layout_parent_horizontal'] ) ? 0 : 1;
        $field['options']['layout_children_horizontal'] = empty( $field['options']['layout_children_horizontal'] ) ? 0 : 1;
        $field['options']['hide_search'] = empty( $field['options']['hide_search'] ) ? 0 : 1;
        $field['options']['hide_selected_filter'] = empty( $field['options']['hide_selected_filter'] ) ? 0 : 1;
        $field['options']['allow_add_terms'] = empty( $field['options']['allow_add_terms'] ) ? 0 : 1;
        $field['options']['add_allowed_roles'] = isset( $field['options']['add_allowed_roles'] ) ? array_values( array_filter( array_map( 'sanitize_key', (array) $field['options']['add_allowed_roles'] ) ) ) : [];

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

        wp_localize_script(
            'atshift-cfs-wp-category',
            'AtshiftCFSWpCategory',
            [
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'messages' => [
                    'adding' => __( 'Adding category...', 'atshift-fields-maintenance-for-custom-field-suite' ),
                    'added' => __( 'Category added.', 'atshift-fields-maintenance-for-custom-field-suite' ),
                    'enter_name' => __( 'Enter a category name.', 'atshift-fields-maintenance-for-custom-field-suite' ),
                    'failed' => __( 'Failed to add category.', 'atshift-fields-maintenance-for-custom-field-suite' ),
                ],
            ]
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


    protected function should_remove_default_term( $default_term_id, $term_ids, $taxonomy_name, $field ) {
        $default_term_id = absint( $default_term_id );
        $term_ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $term_ids ) ) ) );

        if ( 1 > $default_term_id || 1 >= count( $term_ids ) || ! in_array( $default_term_id, $term_ids, true ) ) {
            return false;
        }

        $auto_select_children = 0 < (int) $this->get_option( $field, 'auto_select_children' );
        $auto_select_parents = 0 < (int) $this->get_option( $field, 'auto_select_parents', 1 );

        if ( ( $auto_select_children || $auto_select_parents ) && $this->has_selected_descendant( $default_term_id, $term_ids, $taxonomy_name ) ) {
            return false;
        }

        return true;
    }


    protected function has_selected_descendant( $ancestor_id, $term_ids, $taxonomy_name ) {
        $ancestor_id = absint( $ancestor_id );

        foreach ( (array) $term_ids as $term_id ) {
            $term_id = absint( $term_id );

            if ( 1 > $term_id || $ancestor_id === $term_id ) {
                continue;
            }

            $ancestors = array_map( 'absint', get_ancestors( $term_id, $taxonomy_name, 'taxonomy' ) );

            if ( in_array( $ancestor_id, $ancestors, true ) ) {
                return true;
            }
        }

        return false;
    }
}
