<?php

class cfs_init
{

    function __construct() {
        add_action( 'init', [ $this, 'init' ] );
    }


    function init() {

        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            $this->load_textdomain();
        }

        add_action( 'admin_head',                       [ $this, 'admin_head' ] );
        add_action( 'admin_enqueue_scripts',            [ $this, 'admin_enqueue_scripts' ] );
        add_action( 'admin_menu',                       [ $this, 'admin_menu' ] );
        add_action( 'admin_footer',                     [ $this, 'show_credits' ] );
        add_action( 'save_post',                        [ $this, 'save_post' ] );
        add_action( 'delete_post',                      [ $this, 'delete_post' ] );
        add_action( 'add_meta_boxes',                   [ $this, 'add_meta_boxes' ] );
        add_action( 'wp_ajax_cfs_ajax_handler',         [ $this, 'ajax_handler' ] );
        add_filter( 'manage_cfs_posts_columns',         [ $this, 'cfs_columns' ] );
        add_action( 'manage_cfs_posts_custom_column',   [ $this, 'cfs_column_content' ], 10, 2 );
        add_action( 'enqueue_block_editor_assets',      [ $this, 'enqueue_block_editor_assets' ] );
        add_filter( 'block_categories_all',             [ $this, 'block_categories' ], 10, 2 );

        if ( version_compare( get_bloginfo( 'version' ), '5.8', '<' ) ) {
            add_filter( 'block_categories',             [ $this, 'block_categories' ], 10, 2 );
        }

        include( CFS_DIR . '/includes/api.php' );
        include( CFS_DIR . '/includes/upgrade.php' );
        include( CFS_DIR . '/includes/field.php' );
        include( CFS_DIR . '/includes/field_group.php' );
        include( CFS_DIR . '/includes/session.php' );
        include( CFS_DIR . '/includes/form.php' );
        include( CFS_DIR . '/includes/third_party.php' );
        include( CFS_DIR . '/includes/revision.php' );


        $this->register_post_type();
        CFS()->fields = $this->get_field_types();
        $this->register_blocks();

        // CFS is ready
        do_action( 'cfs_init' );
    }


    /**
     * Register the field group post type
     */
    function register_post_type() {
        register_post_type( 'cfs', [
            'public'            => false,
            'show_ui'           => true,
            'show_in_menu'      => 'options-general.php',
            'capability_type'   => 'page',
            'hierarchical'      => false,
            'supports'          => [ 'title' ],
            'query_var'         => false,
            'labels'            => [
                'name'                  => __( 'Field Groups', 'cfs' ),
                'singular_name'         => __( 'Field Group', 'cfs' ),
                'all_items'             => __( 'Custom Field Suite', 'cfs' ),
                'add_new_item'          => __( 'Add New Field Group', 'cfs' ),
                'edit_item'             => __( 'Edit Field Group', 'cfs' ),
                'new_item'              => __( 'New Field Group', 'cfs' ),
                'view_item'             => __( 'View Field Group', 'cfs' ),
                'search_items'          => __( 'Search Field Groups', 'cfs' ),
                'not_found'             => __( 'No Field Groups found', 'cfs' ),
                'not_found_in_trash'    => __( 'No Field Groups found in Trash', 'cfs' ),
            ],
        ] );
    }


    function load_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'cfs' );
        $mofile = WP_LANG_DIR . '/custom-field-suite/cfs-' . $locale . '.mo';

        if ( file_exists( $mofile ) ) {
            load_textdomain( 'cfs', $mofile );
        }
        else {
            load_plugin_textdomain( 'cfs', false, 'custom-field-suite/languages' );
        }
    }


    /**
     * Register field types
     */
    function get_field_types() {

        // support custom field types
        $field_types = apply_filters( 'cfs_field_types', [
            'text'          => CFS_DIR . '/includes/fields/text.php',
            'phone'         => CFS_DIR . '/includes/fields/phone.php',
            'email'         => CFS_DIR . '/includes/fields/email.php',
            'number'        => CFS_DIR . '/includes/fields/number.php',
            'url'           => CFS_DIR . '/includes/fields/url.php',
            'time'          => CFS_DIR . '/includes/fields/time.php',
            'textarea'      => CFS_DIR . '/includes/fields/textarea.php',
            'wysiwyg'       => CFS_DIR . '/includes/fields/wysiwyg.php',
            'hyperlink'     => CFS_DIR . '/includes/fields/hyperlink.php',
            'date'          => CFS_DIR . '/includes/fields/date/date.php',
            'color'         => CFS_DIR . '/includes/fields/color/color.php',
            'true_false'    => CFS_DIR . '/includes/fields/true_false.php',
            'checkbox'      => CFS_DIR . '/includes/fields/checkbox.php',
            'radio'         => CFS_DIR . '/includes/fields/radio.php',
            'select'        => CFS_DIR . '/includes/fields/select.php',
            'relationship'  => CFS_DIR . '/includes/fields/relationship.php',
            'term'          => CFS_DIR . '/includes/fields/term.php',
            'user'          => CFS_DIR . '/includes/fields/user.php',
            'file'          => CFS_DIR . '/includes/fields/file.php',
            'wp_category'   => CFS_DIR . '/includes/fields/wp_category.php',
            'wp_tag'        => CFS_DIR . '/includes/fields/wp_tag.php',
            'featured_image' => CFS_DIR . '/includes/fields/featured_image.php',
            'loop'          => CFS_DIR . '/includes/fields/loop.php',
            'tab'           => CFS_DIR . '/includes/fields/tab.php',
            'group'         => CFS_DIR . '/includes/fields/group.php',
        ] );

        foreach ( $field_types as $type => $path ) {
            $class_name = 'cfs_' . $type;

            // allow for multiple classes per file
            if ( ! class_exists( $class_name ) ) {
                include_once( $path );
            }

            $field_types[ $type ] = new $class_name();
        }

        return $field_types;
    }


    /**
     * admin_enqueue_scripts
     */
    function admin_enqueue_scripts() {
        $screen = get_current_screen();

        if ( ! is_object( $screen ) || 'cfs' !== $screen->post_type ) {
            return;
        }

        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'cfs-select2', CFS_URL . '/assets/js/select2/select2.min.js', [ 'jquery' ], CFS_VERSION, true );
        wp_enqueue_script( 'jquery-powertip', CFS_URL . '/assets/js/jquery-powertip/jquery.powertip.min.js', [ 'jquery' ], CFS_VERSION, true );
        wp_enqueue_script(
            'cfs-fields',
            CFS_URL . '/assets/js/fields.js',
            [ 'jquery', 'jquery-ui-sortable', 'cfs-select2', 'jquery-powertip' ],
            CFS_VERSION,
            true
        );

        wp_enqueue_style( 'cfs-fields', CFS_URL . '/assets/css/fields.css', [], CFS_VERSION );
        wp_enqueue_style( 'cfs-select2', CFS_URL . '/assets/js/select2/select2.css', [], CFS_VERSION );
        wp_enqueue_style( 'jquery-powertip', CFS_URL . '/assets/js/jquery-powertip/jquery.powertip.css', [], CFS_VERSION );
    }


    /**
     * Add the CFS block inserter category.
     */
    function block_categories( $categories, $post ) {
        foreach ( $categories as $category ) {
            if ( 'cfs' === $category['slug'] ) {
                return $categories;
            }
        }

        array_unshift( $categories, [
            'slug'  => 'cfs',
            'title' => __( 'CFS Field Groups', 'cfs' ),
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

        $field_groups = CFS()->field_group->load_field_groups();

        foreach ( $field_groups as $group_id => $group ) {
            $block_name = 'cfs/field-group-' . absint( $group_id );

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

        $field_groups = CFS()->field_group->load_field_groups();
        $group_ids = array_keys( $field_groups );
        $groups = [];
        $hide_panels = [];

        if ( $post instanceof WP_Post && 'cfs' !== $post->post_type ) {
            $matching_groups = CFS()->api->get_matching_groups( $post->ID );
            $group_ids = array_keys( $matching_groups );
        }

        foreach ( $group_ids as $group_id ) {
            if ( ! isset( $field_groups[ $group_id ] ) ) {
                continue;
            }

            $group = $field_groups[ $group_id ];
            $fields = isset( $group['fields'] ) && is_array( $group['fields'] ) ? $group['fields'] : [];

            foreach ( $fields as $field ) {
                if ( ! isset( $field['type'] ) ) {
                    continue;
                }

                if ( 'wp_category' === $field['type'] ) {
                    $hide_panels[] = 'taxonomy-panel-category';
                }
                elseif ( 'wp_tag' === $field['type'] ) {
                    $hide_panels[] = 'taxonomy-panel-post_tag';
                }
                elseif ( 'featured_image' === $field['type'] ) {
                    $hide_panels[] = 'featured-image';
                }
            }

            $groups[] = [
                'id'         => absint( $group_id ),
                'name'       => 'cfs/field-group-' . absint( $group_id ),
                'title'      => $group['title'],
                'blockTitle' => sprintf( __( 'CFS Field Group: %s', 'cfs' ), $group['title'] ),
                'fieldCount' => count( $fields ),
            ];
        }

        wp_enqueue_script(
            'cfs-block-editor',
            CFS_URL . '/assets/js/block-editor.js',
            [ 'wp-blocks', 'wp-data', 'wp-dom-ready', 'wp-element', 'wp-i18n' ],
            CFS_VERSION,
            true
        );
        wp_enqueue_style( 'cfs-block-editor', CFS_URL . '/assets/css/block-editor.css', [], CFS_VERSION );

        wp_add_inline_script(
            'cfs-block-editor',
            'window.CFSBlockEditor = ' . wp_json_encode( [
                'groups'       => $groups,
                'description'  => __( 'Displays a CFS field group.', 'cfs' ),
                'fieldGroup'   => __( 'Field Group', 'cfs' ),
                'fieldCount'   => __( 'Fields', 'cfs' ),
                'noFields'     => __( 'No fields in this group.', 'cfs' ),
                'hidePanels'   => array_values( array_unique( $hide_panels ) ),
            ] ) . ';',
            'before'
        );
    }


    /**
     * Render a CFS field group block on the front end.
     */
    function render_field_group_block( $attributes, $content, $block ) {
        $group_id = isset( $attributes['groupId'] ) ? absint( $attributes['groupId'] ) : 0;

        if ( 0 === $group_id && is_object( $block ) && isset( $block->name ) && preg_match( '/^cfs\/field-group-(\d+)$/', $block->name, $matches ) ) {
            $group_id = absint( $matches[1] );
        }

        if ( 0 === $group_id ) {
            return '';
        }

        $field_groups = CFS()->field_group->load_field_groups();

        if ( ! isset( $field_groups[ $group_id ] ) ) {
            return '';
        }

        $post_id = get_the_ID();

        if ( empty( $post_id ) ) {
            return '';
        }

        $matching_groups = CFS()->api->get_matching_groups( $post_id, true );

        if ( ! isset( $matching_groups[ $group_id ] ) ) {
            return '';
        }

        $values = CFS()->api->get_fields( $post_id, [ 'format' => 'api' ] );
        $fields = CFS()->api->find_input_fields( [ 'group_id' => $group_id ] );
        $items = [];

        foreach ( $fields as $field ) {
            if ( empty( $field['name'] ) || in_array( $field['type'], [ 'tab', 'group' ], true ) ) {
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
            include( CFS_DIR . '/templates/admin_head.php' );
        }
    }


    /**
     * show_credits
     */
    function show_credits() {
        $screen = get_current_screen();

        if ( 'edit' == $screen->base && 'cfs' == $screen->post_type ) {
            include( CFS_DIR . '/templates/credits.php' );
        }
    }

    /**
    * admin_menu
    */
    function admin_menu() {
        if ( false === apply_filters( 'cfs_disable_admin', false ) ) {
            add_submenu_page( 'tools.php', __( 'CFS Tools', 'cfs' ), __( 'CFS Tools', 'cfs' ), 'manage_options', 'cfs-tools', [ $this, 'page_tools' ] );
        }
    }

    /**
     * add_meta_boxes
     */
    function add_meta_boxes() {
        add_meta_box( 'cfs_fields', __('Fields', 'cfs'), [ $this, 'meta_box' ], 'cfs', 'normal', 'high', [ 'box' => 'fields' ] );
        add_meta_box( 'cfs_rules', __('Placement Rules', 'cfs'), [ $this, 'meta_box' ], 'cfs', 'normal', 'high', [ 'box' => 'rules' ] );
        add_meta_box( 'cfs_extras', __('Extras', 'cfs'), [ $this, 'meta_box' ], 'cfs', 'normal', 'high', [ 'box' => 'extras' ] );
    }


    /**
     * meta_box
     * @param object $post
     * @param array $metabox
     */
    function meta_box( $post, $metabox ) {
        $box = $metabox['args']['box'];
        include( CFS_DIR . "/templates/meta_box_$box.php" );
    }


    /**
     * page_tools
     */
    function page_tools() {
        include( CFS_DIR . '/templates/page_tools.php' );
    }


    /**
     * save_post
     */
    function save_post( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! isset( $_POST['cfs']['save'] ) ) {
            return;
        }

        if ( false !== wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( wp_verify_nonce( $_POST['cfs']['save'], 'cfs_save_fields' ) ) {
            $fields = isset( $_POST['cfs']['fields'] ) ? $_POST['cfs']['fields'] : [];
            $rules = isset( $_POST['cfs']['rules'] ) ? $_POST['cfs']['rules'] : [];
            $extras = isset( $_POST['cfs']['extras'] ) ? $_POST['cfs']['extras'] : [];

            CFS()->field_group->save( [
                'post_id'   => $post_id,
                'fields'    => $fields,
                'rules'     => $rules,
                'extras'    => $extras,
            ] );
        }
    }


    /**
     * delete_post
     * @return boolean
     */
    function delete_post( $post_id ) {
        global $wpdb;

        if ( 'cfs' != get_post_type( $post_id ) ) {
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

        if ( ! check_ajax_referer( 'cfs_admin_nonce', 'nonce', false ) ) {
            exit;
        }

        $ajax_method = isset( $_POST['action_type'] ) ? sanitize_key( $_POST['action_type'] ) : false;

        if ( $ajax_method && is_admin() ) {
            include( CFS_DIR . '/includes/ajax.php' );
            $ajax = new cfs_ajax();

            if ( 'import' == $ajax_method ) {
                $options = [
                    'import_code' => json_decode( stripslashes( $_POST['import_code'] ), true ),
                ];
                echo CFS()->field_group->import( $options );
            }
            elseif ('export' == $ajax_method) {
                echo wp_json_encode( CFS()->field_group->export( $_POST ) );
            }
            elseif ('reset' == $ajax_method) {
                $ajax->reset();
                deactivate_plugins( plugin_basename( __FILE__ ) );
                echo esc_url_raw( admin_url( 'plugins.php' ) );
            }
            elseif ( in_array( $ajax_method, [ 'search_posts' ], true ) && method_exists( $ajax, $ajax_method ) ) {
                echo $ajax->$ajax_method( $_POST );
            }
        }

        exit;
    }


    /**
     * Customize table columns on the Field Group listing
     */
    function cfs_columns() {
        return [
            'cb'            => '<input type="checkbox" />',
            'title'         => __( 'Title', 'cfs' ),
            'placement'     => __( 'Placement', 'cfs' ),
        ];
    }


    /**
     * Populate the "Placement" column on the Field Group listing
     */
    function cfs_column_content( $column_name, $post_id ) {
        if ( 'placement' == $column_name ) {
            global $wpdb;

            $labels = [
                'post_types'        => __( 'Post Types', 'cfs' ),
                'user_roles'        => __( 'User Roles', 'cfs' ),
                'post_ids'          => __( 'Posts', 'cfs' ),
                'term_ids'          => __( 'Term IDs', 'cfs' ),
                'page_templates'    => __( 'Page Templates', 'cfs' ),
                'post_formats'      => __( 'Post Formats', 'cfs' )
            ];

            $field_groups = CFS()->field_group->load_field_groups();

            // Make sure the field group exists
            $rules = [];
            if ( isset( $field_groups[ $post_id ] ) ) {
                $rules = $field_groups[ $post_id ]['rules'];
            }

            foreach ( $rules as $criteria => $data ) {
                $label = $labels[ $criteria ];
                $values = $data['values'];
                $operator = ( '==' == $data['operator'] ) ? '=' : '!=';

                // Get post titles
                if ( 'post_ids' == $criteria ) {
                    $temp = [];
                    foreach ( $values as $val ) {
                        $temp[] = get_the_title( (int) $val );
                    }
                    $values = $temp;
                }

                echo "<div><strong>$label</strong> " . $operator . ' ' . esc_html( implode( ', ', $values ) ) . '</div>';
            }
        }
    }
}

new cfs_init();
