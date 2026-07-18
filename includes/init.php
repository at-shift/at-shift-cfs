<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_init
{
    private $force_single_column_screen_id = '';

    function __construct() {
        atshift_fields_maintenance_for_custom_field_suite()->init = $this;
        add_action( 'init', [ $this, 'init' ] );
    }


    function init() {

        add_action( 'admin_head',                       [ $this, 'admin_head' ] );
        add_action( 'current_screen',                   [ $this, 'maybe_force_single_column_screen_layout' ] );
        add_action( 'admin_enqueue_scripts',            [ $this, 'admin_enqueue_scripts' ] );
        add_action( 'admin_notices',                    [ $this, 'admin_notices' ] );
        add_action( 'admin_menu',                       [ $this, 'admin_menu' ] );
        add_action( 'admin_footer',                     [ $this, 'show_credits' ] );
        add_action( 'save_post',                        [ $this, 'save_post' ] );
        add_action( 'delete_post',                      [ $this, 'delete_post' ] );
        add_action( 'add_meta_boxes',                   [ $this, 'add_meta_boxes' ] );
        add_action( 'wp_ajax_atshift_cfs_ajax_handler', [ $this, 'ajax_handler' ] );
        add_filter( 'manage_' . ATSHIFT_CFS_FIELD_GROUP_POST_TYPE . '_posts_columns', [ $this, 'atshift_cfs_columns' ] );
        add_action( 'manage_' . ATSHIFT_CFS_FIELD_GROUP_POST_TYPE . '_posts_custom_column', [ $this, 'atshift_cfs_column_content' ], 10, 2 );
        add_action( 'enqueue_block_editor_assets',      [ $this, 'enqueue_block_editor_assets' ] );
        add_filter( 'block_categories_all',             [ $this, 'block_categories' ], 10, 2 );
        add_filter( 'use_block_editor_for_post',        [ $this, 'maybe_disable_block_editor' ], 20, 2 );

        if ( version_compare( get_bloginfo( 'version' ), '5.8', '<' ) ) {
            add_filter( 'block_categories',             [ $this, 'block_categories' ], 10, 2 );
        }

        include( ATSHIFT_CFS_DIR . '/includes/api.php' );
        include( ATSHIFT_CFS_DIR . '/includes/upgrade.php' );
        include( ATSHIFT_CFS_DIR . '/includes/field.php' );
        include( ATSHIFT_CFS_DIR . '/includes/field_group.php' );
        include( ATSHIFT_CFS_DIR . '/includes/session.php' );
        include( ATSHIFT_CFS_DIR . '/includes/form.php' );
        include( ATSHIFT_CFS_DIR . '/includes/third_party.php' );
        include( ATSHIFT_CFS_DIR . '/includes/revision.php' );


        $this->register_post_type();
        atshift_fields_maintenance_for_custom_field_suite()->fields = $this->get_field_types();
        $this->register_blocks();

        // CFS is ready
        atshift_cfs_do_action_compat( 'cfs_init', 'atshift_cfs_init' );
    }


    /**
     * Register the field group post type
     */
    function register_post_type() {
        register_post_type( ATSHIFT_CFS_FIELD_GROUP_POST_TYPE, [
            'public'            => false,
            'show_ui'           => true,
            'show_in_menu'      => 'options-general.php',
            'capability_type'   => 'page',
            'hierarchical'      => false,
            'supports'          => [ 'title' ],
            'query_var'         => false,
            'labels'            => [
                'name'                  => __( 'Field Groups', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'singular_name'         => __( 'Field Group', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'all_items'             => __( 'atshift Fields', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'add_new_item'          => __( 'Add New Field Group', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'edit_item'             => __( 'Edit Field Group', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'new_item'              => __( 'New Field Group', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'view_item'             => __( 'View Field Group', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'search_items'          => __( 'Search Field Groups', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'not_found'             => __( 'No Field Groups found', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'not_found_in_trash'    => __( 'No Field Groups found in Trash', 'atshift-fields-maintenance-for-custom-field-suite' ),
            ],
        ] );
    }


    /**
     * Disable the block editor when a matching field group is configured to hide
     * the content editor. The classic editor content area is hidden later by CSS.
     */
    function maybe_disable_block_editor( $use_block_editor, $post ) {
        if ( ! $use_block_editor || ! ( $post instanceof WP_Post ) ) {
            return $use_block_editor;
        }

        if ( ATSHIFT_CFS_FIELD_GROUP_POST_TYPE === $post->post_type ) {
            return $use_block_editor;
        }

        if ( ! isset( atshift_fields_maintenance_for_custom_field_suite()->api ) ) {
            return $use_block_editor;
        }

        $matching_groups = atshift_fields_maintenance_for_custom_field_suite()->api->get_matching_groups( $post->ID );

        if ( empty( $matching_groups ) ) {
            return $use_block_editor;
        }

        $field_groups = atshift_fields_maintenance_for_custom_field_suite()->field_group->load_field_groups();

        foreach ( array_keys( $matching_groups ) as $group_id ) {
            $field_group = isset( $field_groups[ $group_id ] ) && is_array( $field_groups[ $group_id ] ) ? $field_groups[ $group_id ] : [];
            $extras = isset( $field_group['extras'] ) && is_array( $field_group['extras'] ) ? $field_group['extras'] : [];

            if ( ! empty( $extras['hide_editor'] ) || $this->field_group_has_field_type( $field_group, 'post_content' ) ) {
                return false;
            }
        }

        return $use_block_editor;
    }


    /**
     * Force the classic post edit Screen Options layout to one column when a
     * matching field group requests it.
     */
    function maybe_force_single_column_screen_layout( $screen ) {
        if ( ! is_object( $screen ) || 'post' !== $screen->base || empty( $screen->id ) || empty( $screen->post_type ) ) {
            return;
        }

        if ( ATSHIFT_CFS_FIELD_GROUP_POST_TYPE === $screen->post_type ) {
            return;
        }

        if ( ! isset( atshift_fields_maintenance_for_custom_field_suite()->api, atshift_fields_maintenance_for_custom_field_suite()->field_group ) ) {
            return;
        }

        $post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only screen setup.
        $matching_groups = [];

        if ( 0 < $post_id ) {
            $matching_groups = atshift_fields_maintenance_for_custom_field_suite()->api->get_matching_groups( $post_id );
        }
        elseif ( ! empty( $screen->post_type ) ) {
            $matching_groups = atshift_fields_maintenance_for_custom_field_suite()->api->get_matching_groups( [
                'post_types' => [ $screen->post_type ],
            ] );
        }

        if ( empty( $matching_groups ) ) {
            return;
        }

        $field_groups = atshift_fields_maintenance_for_custom_field_suite()->field_group->load_field_groups();

        foreach ( array_keys( $matching_groups ) as $group_id ) {
            $field_group = isset( $field_groups[ $group_id ] ) && is_array( $field_groups[ $group_id ] ) ? $field_groups[ $group_id ] : [];
            $extras = isset( $field_group['extras'] ) && is_array( $field_group['extras'] ) ? $field_group['extras'] : [];

            if ( ! empty( $extras['force_single_column_layout'] ) ) {
                $this->force_single_column_screen_id = $screen->id;
                add_filter( 'screen_layout_columns', [ $this, 'force_single_column_screen_layout_columns' ], 20, 3 );
                add_filter( "get_user_option_screen_layout_{$screen->id}", [ $this, 'force_single_column_screen_layout' ], 20, 3 );
                return;
            }
        }
    }


    function force_single_column_screen_layout_columns( $columns, $screen_id, $screen ) {
        $target_screen_id = is_object( $screen ) && ! empty( $screen->id ) ? $screen->id : $screen_id;

        if ( $this->force_single_column_screen_id === $target_screen_id ) {
            $columns[ $target_screen_id ] = 1;
        }

        return $columns;
    }


    function force_single_column_screen_layout( $value = null, $option = '', $user = null ) {
        return 1;
    }


    function extra_display_section_applies_to_current_user( $extras, $section ) {
        $extras = is_array( $extras ) ? $extras : [];
        $section = sanitize_key( $section );
        $mode = isset( $extras[ $section . '_section_role_mode' ] ) ? sanitize_key( $extras[ $section . '_section_role_mode' ] ) : 'all';
        $user = wp_get_current_user();
        $user_roles = $user instanceof WP_User ? array_map( 'sanitize_key', (array) $user->roles ) : [];

        if ( 'except_admins' === $mode ) {
            return empty( array_intersect( $user_roles, $this->get_admin_web_roles() ) );
        }

        if ( 'selected' === $mode ) {
            $selected_roles = isset( $extras[ $section . '_section_roles' ] ) ? array_filter( array_map( 'sanitize_key', (array) $extras[ $section . '_section_roles' ] ) ) : [];
            return ! empty( array_intersect( $user_roles, $selected_roles ) );
        }

        return true;
    }


    function get_admin_web_roles() {
        $roles = [ 'administrator' ];

        foreach ( wp_roles()->roles as $role_key => $role ) {
            $role_key = sanitize_key( $role_key );
            $role_name = isset( $role['name'] ) ? translate_user_role( $role['name'] ) : $role_key;

            if ( in_array( $role_key, [ 'web_admin', 'web_manager', 'website_manager' ], true ) || 'Web管理者' === $role_name ) {
                $roles[] = $role_key;
            }
        }

        return array_values( array_unique( $roles ) );
    }


    function get_role_choices() {
        $choices = [];

        foreach ( wp_roles()->roles as $role_key => $role ) {
            $role_label = isset( $role['name'] ) ? translate_user_role( $role['name'] ) : $role_key;
            $choices[ sanitize_key( $role_key ) ] = $role_label;
        }

        return $choices;
    }


    /**
     * Register field types
     */
    function get_field_types() {

        // support custom field types
        $field_types = atshift_cfs_apply_filters_compat( 'cfs_field_types', 'atshift_cfs_field_types', [
            'text'          => ATSHIFT_CFS_DIR . '/includes/fields/text.php',
            'textarea'      => ATSHIFT_CFS_DIR . '/includes/fields/textarea.php',
            'wysiwyg'       => ATSHIFT_CFS_DIR . '/includes/fields/wysiwyg.php',
            'tab'           => ATSHIFT_CFS_DIR . '/includes/fields/tab.php',
            'loop'          => ATSHIFT_CFS_DIR . '/includes/fields/loop.php',
            'group'         => ATSHIFT_CFS_DIR . '/includes/fields/group.php',
            'accordion'     => ATSHIFT_CFS_DIR . '/includes/fields/accordion.php',
            'conditional'   => ATSHIFT_CFS_DIR . '/includes/fields/conditional.php',
            'phone'         => ATSHIFT_CFS_DIR . '/includes/fields/phone.php',
            'email'         => ATSHIFT_CFS_DIR . '/includes/fields/email.php',
            'hyperlink'     => ATSHIFT_CFS_DIR . '/includes/fields/hyperlink.php',
            'url'           => ATSHIFT_CFS_DIR . '/includes/fields/url.php',
            'number'        => ATSHIFT_CFS_DIR . '/includes/fields/number.php',
            'select'        => ATSHIFT_CFS_DIR . '/includes/fields/select.php',
            'checkbox'      => ATSHIFT_CFS_DIR . '/includes/fields/checkbox.php',
            'true_false'    => ATSHIFT_CFS_DIR . '/includes/fields/true_false.php',
            'radio'         => ATSHIFT_CFS_DIR . '/includes/fields/radio.php',
            'date'          => ATSHIFT_CFS_DIR . '/includes/fields/date/date.php',
            'time'          => ATSHIFT_CFS_DIR . '/includes/fields/time.php',
            'file'          => ATSHIFT_CFS_DIR . '/includes/fields/file.php',
            'gallery'       => ATSHIFT_CFS_DIR . '/includes/fields/gallery.php',
            'color'         => ATSHIFT_CFS_DIR . '/includes/fields/color/color.php',
            'code_view'     => ATSHIFT_CFS_DIR . '/includes/fields/code_view.php',
            'shortcode'     => ATSHIFT_CFS_DIR . '/includes/fields/shortcode.php',
            'external_metabox' => ATSHIFT_CFS_DIR . '/includes/fields/external_metabox.php',
            'post_title'    => ATSHIFT_CFS_DIR . '/includes/fields/post_title.php',
            'post_content'  => ATSHIFT_CFS_DIR . '/includes/fields/post_content.php',
            'post_publish'  => ATSHIFT_CFS_DIR . '/includes/fields/post_publish.php',
            'wp_category'   => ATSHIFT_CFS_DIR . '/includes/fields/wp_category.php',
            'wp_tag'        => ATSHIFT_CFS_DIR . '/includes/fields/wp_tag.php',
            'featured_image' => ATSHIFT_CFS_DIR . '/includes/fields/featured_image.php',
            'term'          => ATSHIFT_CFS_DIR . '/includes/fields/term.php',
            'relationship'  => ATSHIFT_CFS_DIR . '/includes/fields/relationship.php',
            'user'          => ATSHIFT_CFS_DIR . '/includes/fields/user.php',
        ] );

        foreach ( $field_types as $type => $path ) {
            $class_name = 'Atshift_CFS_' . $type;
            $legacy_class_name = 'cfs_' . $type;

            // allow for multiple classes per file
            if ( ! class_exists( $class_name ) && ! class_exists( $legacy_class_name ) ) {
                include_once( $path );
            }

            if ( class_exists( $class_name ) ) {
                if ( ! class_exists( $legacy_class_name, false ) ) {
                    class_alias( $class_name, $legacy_class_name );
                }
                $field_types[ $type ] = new $class_name();
            }
            elseif ( class_exists( $legacy_class_name ) ) {
                $field_types[ $type ] = new $legacy_class_name();
            }
            else {
                unset( $field_types[ $type ] );
            }
        }

        return $field_types;
    }


    private function field_group_has_field_type( $field_group, $field_type ) {
        $field_types = (array) $field_type;
        $fields = isset( $field_group['fields'] ) && is_array( $field_group['fields'] ) ? $field_group['fields'] : [];

        foreach ( $fields as $field ) {
            if ( isset( $field['type'] ) && in_array( $field['type'], $field_types, true ) ) {
                return true;
            }
        }

        return false;
    }


    /**
     * admin_enqueue_scripts
     */
    function admin_enqueue_scripts( $hook_suffix = '' ) {
        $screen = get_current_screen();
        $is_field_group_screen = is_object( $screen ) && ATSHIFT_CFS_FIELD_GROUP_POST_TYPE === $screen->post_type;
        $is_tools_screen = is_object( $screen ) && ( 'tools_page_atshift-cfs-tools' === $screen->id || 'tools_page_atshift-cfs-tools' === $hook_suffix );

        if ( ! $is_field_group_screen && ! $is_tools_screen ) {
            return;
        }

        if ( $is_tools_screen ) {
            $tools_js_version = file_exists( ATSHIFT_CFS_DIR . '/assets/js/tools.js' ) ? ATSHIFT_CFS_VERSION . '.' . filemtime( ATSHIFT_CFS_DIR . '/assets/js/tools.js' ) : ATSHIFT_CFS_VERSION;
            $tools_css_version = file_exists( ATSHIFT_CFS_DIR . '/assets/css/tools.css' ) ? ATSHIFT_CFS_VERSION . '.' . filemtime( ATSHIFT_CFS_DIR . '/assets/css/tools.css' ) : ATSHIFT_CFS_VERSION;

            wp_enqueue_style( 'atshift-cfs-tools', ATSHIFT_CFS_URL . '/assets/css/tools.css', [], $tools_css_version );
            wp_enqueue_script( 'atshift-cfs-tools', ATSHIFT_CFS_URL . '/assets/js/tools.js', [ 'jquery' ], $tools_js_version, true );
            wp_localize_script(
                'atshift-cfs-tools',
                'AtshiftCFSTools',
                [
                    'nonce'               => wp_create_nonce( 'atshift_cfs_admin_nonce' ),
                    'resetConfirmMessage' => __( 'This will delete all atshift Fields data. Are you sure?', 'atshift-fields-maintenance-for-custom-field-suite' ),
                ]
            );
            return;
        }

        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'atshift-cfs-select2', ATSHIFT_CFS_URL . '/assets/js/select2/select2.min.js', [ 'jquery' ], ATSHIFT_CFS_VERSION, true );
        wp_enqueue_script( 'jquery-powertip', ATSHIFT_CFS_URL . '/assets/js/jquery-powertip/jquery.powertip.min.js', [ 'jquery' ], ATSHIFT_CFS_VERSION, true );
        $fields_js_version = file_exists( ATSHIFT_CFS_DIR . '/assets/js/fields.js' ) ? ATSHIFT_CFS_VERSION . '.' . filemtime( ATSHIFT_CFS_DIR . '/assets/js/fields.js' ) : ATSHIFT_CFS_VERSION;
        $fields_css_version = file_exists( ATSHIFT_CFS_DIR . '/assets/css/fields.css' ) ? ATSHIFT_CFS_VERSION . '.' . filemtime( ATSHIFT_CFS_DIR . '/assets/css/fields.css' ) : ATSHIFT_CFS_VERSION;
        wp_enqueue_script(
            'atshift-cfs-fields',
            ATSHIFT_CFS_URL . '/assets/js/fields.js',
            [ 'jquery', 'jquery-ui-sortable', 'atshift-cfs-select2', 'jquery-powertip' ],
            $fields_js_version,
            true
        );

        wp_enqueue_style( 'atshift-cfs-fields', ATSHIFT_CFS_URL . '/assets/css/fields.css', [], $fields_css_version );
        wp_enqueue_style( 'atshift-cfs-select2', ATSHIFT_CFS_URL . '/assets/js/select2/select2.css', [], ATSHIFT_CFS_VERSION );
        wp_enqueue_style( 'jquery-powertip', ATSHIFT_CFS_URL . '/assets/js/jquery-powertip/jquery.powertip.css', [], ATSHIFT_CFS_VERSION );
    }


    /**
     * Add the CFS block inserter category.
     */
    function block_categories( $categories, $post ) {
        foreach ( $categories as $category ) {
            if ( ATSHIFT_CFS_BLOCK_CATEGORY === $category['slug'] ) {
                return $categories;
            }
        }

        array_unshift( $categories, [
            'slug'  => ATSHIFT_CFS_BLOCK_CATEGORY,
            'title' => __( 'CFS Field Groups', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'icon'  => null,
        ] );

        return $categories;
    }


    /**
     * Register one dynamic block for each published CFS field group.
     */
    function register_blocks() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        $field_groups = atshift_fields_maintenance_for_custom_field_suite()->field_group->load_field_groups();

        foreach ( $field_groups as $group_id => $group ) {
            $block_name = ATSHIFT_CFS_BLOCK_NAMESPACE . '/field-group-' . absint( $group_id );

            if ( WP_Block_Type_Registry::get_instance()->is_registered( $block_name ) ) {
                continue;
            }

            register_block_type( $block_name, [
                'api_version'     => 3,
                'attributes'      => [
                    'groupId' => [
                        'type'    => 'number',
                        'default' => absint( $group_id ),
                    ],
                ],
                'render_callback' => [ $this, 'render_field_group_block' ],
            ] );
        }
    }


    /**
     * Load the no-build block registrations for the editor.
     */
    function enqueue_block_editor_assets() {
        global $post;

        $field_groups = atshift_fields_maintenance_for_custom_field_suite()->field_group->load_field_groups();
        $group_ids = array_keys( $field_groups );
        $groups = [];
        $hide_panels = [];

        if ( $post instanceof WP_Post && ATSHIFT_CFS_FIELD_GROUP_POST_TYPE !== $post->post_type ) {
            $matching_groups = atshift_fields_maintenance_for_custom_field_suite()->api->get_matching_groups( $post->ID );
            $group_ids = array_keys( $matching_groups );
        }

        foreach ( $group_ids as $group_id ) {
            if ( ! isset( $field_groups[ $group_id ] ) ) {
                continue;
            }

            $group = $field_groups[ $group_id ];
            $fields = isset( $group['fields'] ) && is_array( $group['fields'] ) ? $group['fields'] : [];
            $extras = isset( $group['extras'] ) && is_array( $group['extras'] ) ? $group['extras'] : [];
            $hide_side_sections = $this->extra_display_section_applies_to_current_user( $extras, 'side' );
            $hide_main_sections = $this->extra_display_section_applies_to_current_user( $extras, 'main' );

            if ( $hide_side_sections && ! empty( $extras['hide_categories'] ) ) {
                $hide_panels[] = 'taxonomy-panel-category';
            }

            if ( $hide_side_sections && ! empty( $extras['hide_tags'] ) ) {
                $hide_panels[] = 'taxonomy-panel-post_tag';
            }

            if ( $hide_side_sections && ! empty( $extras['hide_featured_image'] ) ) {
                $hide_panels[] = 'featured-image';
            }

            if ( $hide_side_sections && ! empty( $extras['hide_page_attributes'] ) ) {
                $hide_panels[] = 'page-attributes';
            }

            if ( $hide_main_sections && ! empty( $extras['hide_discussion'] ) ) {
                $hide_panels[] = 'discussion-panel';
            }

            if ( $hide_main_sections && ! empty( $extras['hide_excerpt'] ) ) {
                $hide_panels[] = 'post-excerpt';
            }

            if ( $hide_main_sections && ! empty( $extras['hide_permalink'] ) ) {
                $hide_panels[] = 'post-link';
            }

            foreach ( $fields as $field ) {
                if ( ! isset( $field['type'] ) ) {
                    continue;
                }

                if ( 'wp_category' === $field['type'] ) {
                    $taxonomy_name = $this->get_category_taxonomy_name( $field );

                    if ( '' !== $taxonomy_name ) {
                        $hide_panels[] = 'taxonomy-panel-' . $taxonomy_name;
                    }
                }
                elseif ( 'wp_tag' === $field['type'] ) {
                    $hide_panels[] = 'taxonomy-panel-post_tag';
                }
                elseif ( 'featured_image' === $field['type'] ) {
                    $hide_panels[] = 'featured-image';
                }
                elseif ( 'post_title' === $field['type'] ) {
                    $hide_panels[] = 'post-title';
                }
                elseif ( 'post_publish' === $field['type'] ) {
                    $hide_panels[] = 'post-status';
                }
            }

            $groups[] = [
                'id'         => absint( $group_id ),
                'name'       => ATSHIFT_CFS_BLOCK_NAMESPACE . '/field-group-' . absint( $group_id ),
                'title'      => $group['title'],
                /* translators: %s: field group title. */
                'blockTitle' => sprintf( __( 'CFS Field Group: %s', 'atshift-fields-maintenance-for-custom-field-suite' ), $group['title'] ),
                'fieldCount' => count( $fields ),
            ];
        }

        wp_enqueue_script(
            'atshift-cfs-block-editor',
            ATSHIFT_CFS_URL . '/assets/js/block-editor.js',
            [ 'wp-blocks', 'wp-data', 'wp-dom-ready', 'wp-element', 'wp-i18n' ],
            ATSHIFT_CFS_VERSION,
            true
        );
        wp_enqueue_style( 'atshift-cfs-block-editor', ATSHIFT_CFS_URL . '/assets/css/block-editor.css', [], ATSHIFT_CFS_VERSION );

        wp_add_inline_script(
            'atshift-cfs-block-editor',
            'window.CFSBlockEditor = ' . wp_json_encode( [
                'groups'       => $groups,
                'description'  => __( 'Displays a CFS field group.', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'fieldGroup'   => __( 'Field Group', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'fieldCount'   => __( 'Fields', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'noFields'     => __( 'No fields in this group.', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'category'     => ATSHIFT_CFS_BLOCK_CATEGORY,
                'hidePanels'   => array_values( array_unique( $hide_panels ) ),
            ] ) . ';',
            'before'
        );
    }


    private function get_category_taxonomy_name( $field ) {
        $field_object = (object) $field;

        if ( isset( atshift_fields_maintenance_for_custom_field_suite()->fields['wp_category'] ) ) {
            return atshift_fields_maintenance_for_custom_field_suite()->fields['wp_category']->get_taxonomy_name( $field_object );
        }

        if ( isset( $field['options']['taxonomy'] ) ) {
            $taxonomy_name = sanitize_key( $field['options']['taxonomy'] );
            return taxonomy_exists( $taxonomy_name ) ? $taxonomy_name : '';
        }

        return taxonomy_exists( 'category' ) ? 'category' : '';
    }


    /**
     * Render a CFS field group block on the front end.
     */
    function render_field_group_block( $attributes, $content, $block ) {
        $group_id = isset( $attributes['groupId'] ) ? absint( $attributes['groupId'] ) : 0;

        $block_pattern = '/^' . preg_quote( ATSHIFT_CFS_BLOCK_NAMESPACE, '/' ) . '\/field-group-(\d+)$/';
        if ( 0 === $group_id && is_object( $block ) && isset( $block->name ) && preg_match( $block_pattern, $block->name, $matches ) ) {
            $group_id = absint( $matches[1] );
        }

        if ( 0 === $group_id ) {
            return '';
        }

        $field_groups = atshift_fields_maintenance_for_custom_field_suite()->field_group->load_field_groups();

        if ( ! isset( $field_groups[ $group_id ] ) ) {
            return '';
        }

        $post_id = get_the_ID();

        if ( empty( $post_id ) ) {
            return '';
        }

        $matching_groups = atshift_fields_maintenance_for_custom_field_suite()->api->get_matching_groups( $post_id, true );

        if ( ! isset( $matching_groups[ $group_id ] ) ) {
            return '';
        }

        $values = atshift_fields_maintenance_for_custom_field_suite()->api->get_fields( $post_id, [ 'format' => 'api' ] );
        $fields = atshift_fields_maintenance_for_custom_field_suite()->api->find_input_fields( [ 'group_id' => $group_id ] );
        $items = [];

        foreach ( $fields as $field ) {
            if ( empty( $field['name'] ) || in_array( $field['type'], [ 'tab', 'group', 'accordion', 'conditional' ], true ) ) {
                continue;
            }

            $value = isset( $values[ $field['name'] ] ) ? $values[ $field['name'] ] : null;
            $rendered_value = $this->render_block_field_value( $value );

            if ( '' === $rendered_value ) {
                continue;
            }

            $items[] = sprintf(
                '<div class="cfs-block-field cfs-block-field-%1$s"><dt>%2$s</dt><dd>%3$s</dd></div>',
                esc_attr( sanitize_html_class( $field['name'] ) ),
                esc_html( ! empty( $field['label'] ) ? $field['label'] : $field['name'] ),
                $rendered_value
            );
        }

        if ( empty( $items ) ) {
            return '';
        }

        return sprintf(
            '<section class="cfs-block-field-group cfs-block-field-group-%1$d"><h2>%2$s</h2><dl>%3$s</dl></section>',
            absint( $group_id ),
            esc_html( $field_groups[ $group_id ]['title'] ),
            implode( '', $items )
        );
    }


    /**
     * Convert saved CFS values to safe, compact block output.
     */
    private function render_block_field_value( $value ) {
        if ( null === $value || '' === $value || [] === $value ) {
            return '';
        }

        if ( is_array( $value ) ) {
            $parts = [];

            foreach ( $value as $item ) {
                $rendered = $this->render_block_field_value( $item );

                if ( '' !== $rendered ) {
                    $parts[] = $rendered;
                }
            }

            return implode( '<br />', $parts );
        }

        if ( is_object( $value ) ) {
            if ( isset( $value->post_title ) ) {
                return esc_html( $value->post_title );
            }

            if ( isset( $value->display_name ) ) {
                return esc_html( $value->display_name );
            }

            if ( isset( $value->name ) ) {
                return esc_html( $value->name );
            }

            return '';
        }

        return esc_html( (string) $value );
    }


    /**
     * admin_head
     */
    function admin_head() {
        $screen = get_current_screen();

        if ( is_object( $screen ) && 'post' == $screen->base ) {
            include( ATSHIFT_CFS_DIR . '/templates/admin_head.php' );
        }
    }


    /**
     * show_credits
     */
    function show_credits() {
        $screen = get_current_screen();

        if ( 'edit' == $screen->base && ATSHIFT_CFS_FIELD_GROUP_POST_TYPE == $screen->post_type ) {
            include( ATSHIFT_CFS_DIR . '/templates/credits.php' );
        }
    }

    /**
    * admin_menu
    */
    function admin_menu() {
        if ( false === atshift_cfs_apply_filters_compat( 'cfs_disable_admin', 'atshift_cfs_disable_admin', false ) ) {
            add_submenu_page( 'tools.php', __( 'atshift Fields Tool', 'atshift-fields-maintenance-for-custom-field-suite' ), __( 'atshift Fields Tool', 'atshift-fields-maintenance-for-custom-field-suite' ), 'manage_options', 'atshift-cfs-tools', [ $this, 'page_tools' ] );
        }
    }

    /**
     * add_meta_boxes
     */
    function add_meta_boxes() {
        $meta_box_args = [
            '__block_editor_compatible_meta_box' => true,
        ];

        add_meta_box( 'cfs_fields', __('Fields', 'atshift-fields-maintenance-for-custom-field-suite' ), [ $this, 'meta_box' ], ATSHIFT_CFS_FIELD_GROUP_POST_TYPE, 'normal', 'high', array_merge( $meta_box_args, [ 'box' => 'fields' ] ) );
        add_meta_box( 'cfs_rules', __('Placement Rules', 'atshift-fields-maintenance-for-custom-field-suite' ), [ $this, 'meta_box' ], ATSHIFT_CFS_FIELD_GROUP_POST_TYPE, 'normal', 'high', array_merge( $meta_box_args, [ 'box' => 'rules' ] ) );
        add_meta_box( 'cfs_extras', __('Extras', 'atshift-fields-maintenance-for-custom-field-suite' ), [ $this, 'meta_box' ], ATSHIFT_CFS_FIELD_GROUP_POST_TYPE, 'normal', 'high', array_merge( $meta_box_args, [ 'box' => 'extras' ] ) );
    }


    /**
     * meta_box
     * @param object $post
     * @param array $metabox
     */
    function meta_box( $post, $metabox ) {
        $box = $metabox['args']['box'];
        include( ATSHIFT_CFS_DIR . "/templates/meta_box_$box.php" );
    }


    /**
     * page_tools
     */
    function page_tools() {
        include( ATSHIFT_CFS_DIR . '/templates/page_tools.php' );
    }


    /**
     * save_post
     */
    function save_post( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $cfs_post = isset( $_POST['cfs'] ) && is_array( $_POST['cfs'] ) ? wp_unslash( $_POST['cfs'] ) : [];

        if ( ! isset( $cfs_post['save'] ) ) {
            return;
        }

        if ( false !== wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( ATSHIFT_CFS_FIELD_GROUP_POST_TYPE !== get_post_type( $post_id ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $nonce = sanitize_text_field( $cfs_post['save'] );

        if ( wp_verify_nonce( $nonce, 'atshift_cfs_save_fields' ) ) {
            $fields = isset( $cfs_post['fields'] ) ? $this->sanitize_recursive_textarea( $cfs_post['fields'] ) : [];
            $rules = isset( $cfs_post['rules'] ) ? $this->sanitize_recursive_textarea( $cfs_post['rules'] ) : [];
            $extras = isset( $cfs_post['extras'] ) ? $this->sanitize_recursive_textarea( $cfs_post['extras'] ) : [];

            atshift_fields_maintenance_for_custom_field_suite()->field_group->save( [
                'post_id'   => $post_id,
                'fields'    => $fields,
                'rules'     => $rules,
                'extras'    => $extras,
            ] );

            if ( ! $this->has_placement_rules( $rules ) ) {
                set_transient( 'atshift_cfs_empty_rules_notice_' . (int) $post_id, 1, MINUTE_IN_SECONDS );
            }
        }
    }


    /**
     * Display guardrail notices for broadly-matching field groups.
     */
    function admin_notices() {
        $screen = get_current_screen();

        if ( ! is_object( $screen ) || ATSHIFT_CFS_FIELD_GROUP_POST_TYPE !== $screen->post_type ) {
            return;
        }

        $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

        if ( 0 === $post_id || ATSHIFT_CFS_FIELD_GROUP_POST_TYPE !== get_post_type( $post_id ) ) {
            return;
        }

        $rules = get_post_meta( $post_id, 'cfs_rules', true );

        if ( $this->has_placement_rules( $rules ) ) {
            return;
        }

        $just_saved = (bool) get_transient( 'atshift_cfs_empty_rules_notice_' . $post_id );
        delete_transient( 'atshift_cfs_empty_rules_notice_' . $post_id );

        if ( ! $just_saved ) {
            return;
        }

        $message = __( 'Saved, but this field group has no placement rules. It will appear on all editable post screens, so set a Post Type or another placement rule unless that is intentional.', 'atshift-fields-maintenance-for-custom-field-suite' );

        printf(
            '<div class="notice notice-warning"><p><strong>%s</strong> %s</p></div>',
            esc_html__( 'CFS placement warning:', 'atshift-fields-maintenance-for-custom-field-suite' ),
            esc_html( $message )
        );
    }


    /**
     * delete_post
     * @return boolean
     */
    function delete_post( $post_id ) {
        global $wpdb;

        if ( ATSHIFT_CFS_FIELD_GROUP_POST_TYPE != get_post_type( $post_id ) ) {
            $post_id = (int) $post_id;
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$wpdb->prefix}cfs_values WHERE post_id = %d",
                    $post_id
                )
            );
        }

        return true;
    }


    /**
     * ajax_handler
     */
    function ajax_handler() {
        if ( ! current_user_can( 'manage_options' ) ) {
            exit;
        }

        if ( ! check_ajax_referer( 'atshift_cfs_admin_nonce', 'nonce', false ) ) {
            exit;
        }

        $ajax_method = isset( $_POST['action_type'] ) ? sanitize_key( wp_unslash( $_POST['action_type'] ) ) : false;

        if ( $ajax_method && is_admin() ) {
            include( ATSHIFT_CFS_DIR . '/includes/ajax.php' );
            $ajax = new Atshift_CFS_ajax();
            $post_data = is_array( $_POST ) ? $this->sanitize_recursive_textarea( wp_unslash( $_POST ) ) : [];

            if ( 'import' == $ajax_method ) {
                $import_code = isset( $_POST['import_code'] ) ? sanitize_textarea_field( wp_unslash( $_POST['import_code'] ) ) : '';
                $decoded_import = json_decode( $import_code, true );
                $options = [
                    'import_code' => is_array( $decoded_import ) ? $this->sanitize_recursive_textarea( $decoded_import ) : [],
                ];
                echo wp_kses_post( atshift_fields_maintenance_for_custom_field_suite()->field_group->import( $options ) );
            }
            elseif ('export' == $ajax_method) {
                echo wp_json_encode( atshift_fields_maintenance_for_custom_field_suite()->field_group->export( $post_data ) );
            }
            elseif ('reset' == $ajax_method) {
                $ajax->reset();
                deactivate_plugins( plugin_basename( ATSHIFT_CFS_DIR . '/cfs.php' ) );
                echo esc_url( admin_url( 'plugins.php' ) );
            }
            elseif ( in_array( $ajax_method, [ 'search_posts', 'discover_meta_boxes' ], true ) && method_exists( $ajax, $ajax_method ) ) {
                echo wp_json_encode( json_decode( $ajax->$ajax_method( $post_data ), true ) );
            }
        }

        exit;
    }


    private function sanitize_recursive_textarea( $value ) {
        if ( is_array( $value ) ) {
            $sanitized = [];
            foreach ( $value as $key => $item ) {
                $sanitized_key = is_int( $key ) ? $key : sanitize_text_field( (string) $key );
                $sanitized[ $sanitized_key ] = $this->sanitize_recursive_textarea( $item );
            }
            return $sanitized;
        }

        if ( is_scalar( $value ) || null === $value ) {
            return sanitize_textarea_field( (string) $value );
        }

        return '';
    }


    /**
     * Customize table columns on the Field Group listing
     */
    function atshift_cfs_columns() {
        return [
            'cb'            => '<input type="checkbox" />',
            'title'         => __( 'Title', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'placement'     => __( 'Placement', 'atshift-fields-maintenance-for-custom-field-suite' ),
        ];
    }


    /**
     * Populate the "Placement" column on the Field Group listing
     */
    function atshift_cfs_column_content( $column_name, $post_id ) {
        if ( 'placement' == $column_name ) {
            global $wpdb;

            $labels = [
                'post_types'        => __( 'Post Types', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'user_roles'        => __( 'User Roles', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'post_ids'          => __( 'Posts', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'term_ids'          => __( 'Term IDs', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'page_templates'    => __( 'Page Templates', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'post_formats'      => __( 'Post Formats', 'atshift-fields-maintenance-for-custom-field-suite' )
            ];

            $field_groups = atshift_fields_maintenance_for_custom_field_suite()->field_group->load_field_groups();

            // Make sure the field group exists
            $rules = [];
            if ( isset( $field_groups[ $post_id ] ) ) {
                $rules = $field_groups[ $post_id ]['rules'];
            }

            if ( ! $this->has_placement_rules( $rules ) ) {
                echo '<div class="cfs-placement-warning"><strong>' . esc_html__( 'No placement rules', 'atshift-fields-maintenance-for-custom-field-suite' ) . '</strong><br />' . esc_html__( 'This field group will appear on all editable post screens.', 'atshift-fields-maintenance-for-custom-field-suite' ) . '</div>';
                return;
            }

            foreach ( $rules as $criteria => $data ) {
                if ( ! isset( $labels[ $criteria ], $data['values'] ) ) {
                    continue;
                }

                $label = $labels[ $criteria ];
                $values = (array) $data['values'];
                $operator = $this->format_placement_rule_operator( isset( $data['operator'] ) ? $data['operator'] : '==' );
                $values = $this->format_placement_rule_values( $criteria, $values );

                echo '<div><strong>' . esc_html( $label ) . '</strong> ' . esc_html( $operator ) . ' ' . esc_html( implode( ', ', $values ) ) . '</div>';
            }
        }
    }


    /**
     * Normalize stored placement operators for compact list-table display.
     */
    private function format_placement_rule_operator( $operator ) {
        $operator = is_array( $operator ) ? reset( $operator ) : $operator;
        $operator = strtolower( trim( (string) $operator ) );

        if ( in_array( $operator, [ '!=', '!==', '<>', 'not_equals', 'not_equal', 'not equals', 'not-equal', 'is not', 'not' ], true ) ) {
            return '≠';
        }

        return '=';
    }


    /**
     * Format placement rule values for list-table display.
     */
    private function format_placement_rule_values( $criteria, $values ) {
        $formatted = [];

        foreach ( (array) $values as $value ) {
            $formatted[] = $this->format_placement_rule_value( $criteria, $value );
        }

        return array_filter( $formatted, static function( $value ) {
            return '' !== (string) $value;
        } );
    }


    /**
     * Convert stored placement values into readable labels without changing saved slugs.
     */
    private function format_placement_rule_value( $criteria, $value ) {
        $value = (string) $value;

        if ( '' === $value ) {
            return '';
        }

        if ( 'post_types' === $criteria ) {
            $post_type = get_post_type_object( $value );

            if ( $post_type && ! empty( $post_type->labels->singular_name ) ) {
                return $this->format_label_with_slug( $post_type->labels->singular_name, $value );
            }

            if ( $post_type && ! empty( $post_type->label ) ) {
                return $this->format_label_with_slug( $post_type->label, $value );
            }

            return $value;
        }

        if ( 'post_formats' === $criteria ) {
            $label = get_post_format_string( $value );

            if ( is_string( $label ) && '' !== $label ) {
                return $this->format_label_with_slug( $label, $value );
            }

            return $value;
        }

        if ( 'user_roles' === $criteria ) {
            $roles = wp_roles();

            if ( $roles && isset( $roles->roles[ $value ]['name'] ) ) {
                return $this->format_label_with_slug( translate_user_role( $roles->roles[ $value ]['name'] ), $value );
            }

            return $value;
        }

        if ( 'post_ids' === $criteria ) {
            $post_id = (int) $value;
            $title = get_the_title( $post_id );

            if ( '' !== $title ) {
                return sprintf( '%s (#%d)', $title, $post_id );
            }

            return sprintf( '#%d', $post_id );
        }

        if ( 'term_ids' === $criteria ) {
            $term = get_term( (int) $value );

            if ( $term && ! is_wp_error( $term ) ) {
                return $this->format_label_with_slug( $term->name, $term->slug );
            }

            return $value;
        }

        if ( 'page_templates' === $criteria ) {
            if ( 'default' === $value ) {
                return $this->format_label_with_slug( __( 'Default' ), $value );
            }

            $template_label = array_search( $value, get_page_templates(), true );

            if ( false !== $template_label ) {
                return $this->format_label_with_slug( $template_label, $value );
            }
        }

        return $value;
    }


    /**
     * Show a friendly label while keeping the stored slug visible for precision.
     */
    private function format_label_with_slug( $label, $slug ) {
        $label = (string) $label;
        $slug = (string) $slug;

        if ( '' === $slug || $label === $slug ) {
            return $label;
        }

        return sprintf( '%s (%s)', $label, $slug );
    }


    /**
     * Check whether a field group has at least one non-empty placement rule.
     *
     * Empty rules are valid in CFS, but they can unintentionally match broadly.
     */
    private function has_placement_rules( $rules ) {
        if ( empty( $rules ) || ! is_array( $rules ) ) {
            return false;
        }

        foreach ( $rules as $criteria => $data ) {
            if ( 'operator' === $criteria || ! is_array( $data ) ) {
                continue;
            }

            if ( empty( $data['values'] ) ) {
                continue;
            }

            $values = array_filter( (array) $data['values'], static function( $value ) {
                return '' !== (string) $value;
            } );

            if ( ! empty( $values ) ) {
                return true;
            }
        }

        return false;
    }
}

$atshift_cfs_init = new Atshift_CFS_init();

if ( did_action( 'init' ) ) {
    $atshift_cfs_init->init();
}
