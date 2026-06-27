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
        add_action( 'admin_notices',                    [ $this, 'admin_notices' ] );
        add_action( 'admin_menu',                       [ $this, 'admin_menu' ] );
        add_action( 'admin_footer',                     [ $this, 'show_credits' ] );
        add_action( 'save_post',                        [ $this, 'save_post' ] );
        add_action( 'delete_post',                      [ $this, 'delete_post' ] );
        add_action( 'add_meta_boxes',                   [ $this, 'add_meta_boxes' ] );
        add_action( 'wp_ajax_cfs_ajax_handler',         [ $this, 'ajax_handler' ] );
        add_filter( 'plugin_row_meta',                  [ $this, 'plugin_row_meta' ], 10, 2 );
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
                'name'                  => __( 'Field Groups', 'at-shift-cfs' ),
                'singular_name'         => __( 'Field Group', 'at-shift-cfs' ),
                'all_items'             => __( 'at-shift CFS', 'at-shift-cfs' ),
                'add_new_item'          => __( 'Add New Field Group', 'at-shift-cfs' ),
                'edit_item'             => __( 'Edit Field Group', 'at-shift-cfs' ),
                'new_item'              => __( 'New Field Group', 'at-shift-cfs' ),
                'view_item'             => __( 'View Field Group', 'at-shift-cfs' ),
                'search_items'          => __( 'Search Field Groups', 'at-shift-cfs' ),
                'not_found'             => __( 'No Field Groups found', 'at-shift-cfs' ),
                'not_found_in_trash'    => __( 'No Field Groups found in Trash', 'at-shift-cfs' ),
            ],
        ] );
    }


    function load_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'at-shift-cfs' );
        $mofile = WP_LANG_DIR . '/plugins/at-shift-cfs-' . $locale . '.mo';

        if ( file_exists( $mofile ) ) {
            load_textdomain( 'at-shift-cfs', $mofile );
        }
        else {
            load_plugin_textdomain(
                'at-shift-cfs',
                false,
                dirname( plugin_basename( CFS_DIR . '/cfs.php' ) ) . '/languages'
            );
        }
    }


    /**
     * Register field types
     */
    function get_field_types() {

        // support custom field types
        $field_types = apply_filters( 'cfs_field_types', [
            'text'          => CFS_DIR . '/includes/fields/text.php',
            'textarea'      => CFS_DIR . '/includes/fields/textarea.php',
            'wysiwyg'       => CFS_DIR . '/includes/fields/wysiwyg.php',
            'phone'         => CFS_DIR . '/includes/fields/phone.php',
            'email'         => CFS_DIR . '/includes/fields/email.php',
            'hyperlink'     => CFS_DIR . '/includes/fields/hyperlink.php',
            'url'           => CFS_DIR . '/includes/fields/url.php',
            'number'        => CFS_DIR . '/includes/fields/number.php',
            'select'        => CFS_DIR . '/includes/fields/select.php',
            'checkbox'      => CFS_DIR . '/includes/fields/checkbox.php',
            'true_false'    => CFS_DIR . '/includes/fields/true_false.php',
            'radio'         => CFS_DIR . '/includes/fields/radio.php',
            'date'          => CFS_DIR . '/includes/fields/date/date.php',
            'time'          => CFS_DIR . '/includes/fields/time.php',
            'file'          => CFS_DIR . '/includes/fields/file.php',
            'color'         => CFS_DIR . '/includes/fields/color/color.php',
            'code_view'     => CFS_DIR . '/includes/fields/code_view.php',
            'wp_category'   => CFS_DIR . '/includes/fields/wp_category.php',
            'wp_tag'        => CFS_DIR . '/includes/fields/wp_tag.php',
            'featured_image' => CFS_DIR . '/includes/fields/featured_image.php',
            'term'          => CFS_DIR . '/includes/fields/term.php',
            'relationship'  => CFS_DIR . '/includes/fields/relationship.php',
            'user'          => CFS_DIR . '/includes/fields/user.php',
            'loop'          => CFS_DIR . '/includes/fields/loop.php',
            'tab'           => CFS_DIR . '/includes/fields/tab.php',
            'accordion'     => CFS_DIR . '/includes/fields/accordion.php',
            'group'         => CFS_DIR . '/includes/fields/group.php',
            'conditional'   => CFS_DIR . '/includes/fields/conditional.php',
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
        $fields_js_version = file_exists( CFS_DIR . '/assets/js/fields.js' ) ? CFS_VERSION . '.' . filemtime( CFS_DIR . '/assets/js/fields.js' ) : CFS_VERSION;
        $fields_css_version = file_exists( CFS_DIR . '/assets/css/fields.css' ) ? CFS_VERSION . '.' . filemtime( CFS_DIR . '/assets/css/fields.css' ) : CFS_VERSION;
        wp_enqueue_script(
            'cfs-fields',
            CFS_URL . '/assets/js/fields.js',
            [ 'jquery', 'jquery-ui-sortable', 'cfs-select2', 'jquery-powertip' ],
            $fields_js_version,
            true
        );

        wp_enqueue_style( 'cfs-fields', CFS_URL . '/assets/css/fields.css', [], $fields_css_version );
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
            'title' => __( 'CFS Field Groups', 'at-shift-cfs' ),
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
                'blockTitle' => sprintf( __( 'CFS Field Group: %s', 'at-shift-cfs' ), $group['title'] ),
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
                'description'  => __( 'Displays a CFS field group.', 'at-shift-cfs' ),
                'fieldGroup'   => __( 'Field Group', 'at-shift-cfs' ),
                'fieldCount'   => __( 'Fields', 'at-shift-cfs' ),
                'noFields'     => __( 'No fields in this group.', 'at-shift-cfs' ),
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
            add_submenu_page( 'tools.php', __( 'at-shift CFS Tools', 'at-shift-cfs' ), __( 'at-shift CFS Tools', 'at-shift-cfs' ), 'manage_options', 'cfs-tools', [ $this, 'page_tools' ] );
        }
    }

    /**
     * add_meta_boxes
     */
    function add_meta_boxes() {
        add_meta_box( 'cfs_fields', __('Fields', 'at-shift-cfs' ), [ $this, 'meta_box' ], 'cfs', 'normal', 'high', [ 'box' => 'fields' ] );
        add_meta_box( 'cfs_rules', __('Placement Rules', 'at-shift-cfs' ), [ $this, 'meta_box' ], 'cfs', 'normal', 'high', [ 'box' => 'rules' ] );
        add_meta_box( 'cfs_extras', __('Extras', 'at-shift-cfs' ), [ $this, 'meta_box' ], 'cfs', 'normal', 'high', [ 'box' => 'extras' ] );
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

        if ( 'cfs' !== get_post_type( $post_id ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $nonce = sanitize_text_field( wp_unslash( $_POST['cfs']['save'] ) );

        if ( wp_verify_nonce( $nonce, 'cfs_save_fields' ) ) {
            $fields = isset( $_POST['cfs']['fields'] ) ? $_POST['cfs']['fields'] : [];
            $rules = isset( $_POST['cfs']['rules'] ) ? $_POST['cfs']['rules'] : [];
            $extras = isset( $_POST['cfs']['extras'] ) ? $_POST['cfs']['extras'] : [];

            CFS()->field_group->save( [
                'post_id'   => $post_id,
                'fields'    => $fields,
                'rules'     => $rules,
                'extras'    => $extras,
            ] );

            if ( ! $this->has_placement_rules( $rules ) ) {
                set_transient( 'cfs_empty_rules_notice_' . (int) $post_id, 1, MINUTE_IN_SECONDS );
            }
        }
    }


    /**
     * Display guardrail notices for broadly-matching field groups.
     */
    function admin_notices() {
        $screen = get_current_screen();

        if ( ! is_object( $screen ) || 'cfs' !== $screen->post_type ) {
            return;
        }

        $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

        if ( 0 === $post_id || 'cfs' !== get_post_type( $post_id ) ) {
            return;
        }

        $rules = get_post_meta( $post_id, 'cfs_rules', true );

        if ( $this->has_placement_rules( $rules ) ) {
            return;
        }

        $just_saved = (bool) get_transient( 'cfs_empty_rules_notice_' . $post_id );
        delete_transient( 'cfs_empty_rules_notice_' . $post_id );

        $message = __( 'This field group has no placement rules. It will appear on all editable post screens, so set a Post Type or another placement rule unless that is intentional.', 'at-shift-cfs' );

        if ( $just_saved ) {
            $message = __( 'Saved, but this field group has no placement rules. It will appear on all editable post screens, so set a Post Type or another placement rule unless that is intentional.', 'at-shift-cfs' );
        }

        printf(
            '<div class="notice notice-warning"><p><strong>%s</strong> %s</p></div>',
            esc_html__( 'CFS placement warning:', 'at-shift-cfs' ),
            esc_html( $message )
        );
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
     * Show GitHub update status on the Plugins screen.
     */
    function plugin_row_meta( $links, $file ) {
        if ( plugin_basename( CFS_DIR . '/cfs.php' ) !== $file ) {
            return $links;
        }

        $status = $this->get_github_update_status();

        if ( 'update' === $status['state'] ) {
            $links[] = sprintf(
                '<a class="cfs-github-update-link" style="color:#d63638;font-weight:600;" href="%1$s" target="_blank" rel="noopener">%2$s</a>',
                esc_url( $status['url'] ),
                esc_html( sprintf( __( 'A newer version %s is available on GitHub.', 'at-shift-cfs' ), $status['version'] ) )
            );
        }

        return $links;
    }


    /**
     * Fetch and cache the latest GitHub tag for this maintenance build.
     */
    private function get_github_update_status() {
        $repo_url = 'https://github.com/at-shift/at-shift-cfs';
        $fallback = [
            'state'                   => 'unknown',
            'version'                 => '',
            'url'                     => $repo_url,
            'checked_current_version' => CFS_VERSION,
        ];

        $cached = get_transient( 'cfs_github_update_status' );

        if (
            is_array( $cached ) &&
            isset( $cached['state'], $cached['url'], $cached['checked_current_version'] ) &&
            CFS_VERSION === $cached['checked_current_version']
        ) {
            return $cached;
        }

        $response = wp_remote_get( 'https://api.github.com/repos/at-shift/at-shift-cfs/tags', [
            'timeout' => 5,
            'headers' => [
                'User-Agent' => 'at-shift CFS/' . CFS_VERSION . '; ' . home_url( '/' ),
            ],
        ] );

        if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
            set_transient( 'cfs_github_update_status', $fallback, HOUR_IN_SECONDS );
            return $fallback;
        }

        $tags = json_decode( wp_remote_retrieve_body( $response ), true );
        $latest_version = '';
        $latest_tag = '';

        if ( is_array( $tags ) ) {
            foreach ( $tags as $tag ) {
                if ( empty( $tag['name'] ) ) {
                    continue;
                }

                $version = $this->normalize_github_version( $tag['name'] );

                if ( '' === $version ) {
                    continue;
                }

                if ( '' === $latest_version || version_compare( $latest_version, $version, '<' ) ) {
                    $latest_version = $version;
                    $latest_tag = $tag['name'];
                }
            }
        }

        if ( '' === $latest_version ) {
            set_transient( 'cfs_github_update_status', $fallback, HOUR_IN_SECONDS );
            return $fallback;
        }

        $status = [
            'state'                   => version_compare( CFS_VERSION, $latest_version, '<' ) ? 'update' : 'current',
            'version'                 => $latest_tag,
            'url'                     => $repo_url . '/releases',
            'checked_current_version' => CFS_VERSION,
        ];

        set_transient( 'cfs_github_update_status', $status, 12 * HOUR_IN_SECONDS );

        return $status;
    }


    /**
     * Normalize tags like v2.6.7.39 to values version_compare can read.
     */
    private function normalize_github_version( $tag ) {
        $version = ltrim( (string) $tag, "vV \t\n\r\0\x0B" );

        if ( ! preg_match( '/^\d+(?:\.\d+)+(?:[-+][0-9A-Za-z.-]+)?$/', $version ) ) {
            return '';
        }

        return $version;
    }


    /**
     * Customize table columns on the Field Group listing
     */
    function cfs_columns() {
        return [
            'cb'            => '<input type="checkbox" />',
            'title'         => __( 'Title', 'at-shift-cfs' ),
            'placement'     => __( 'Placement', 'at-shift-cfs' ),
        ];
    }


    /**
     * Populate the "Placement" column on the Field Group listing
     */
    function cfs_column_content( $column_name, $post_id ) {
        if ( 'placement' == $column_name ) {
            global $wpdb;

            $labels = [
                'post_types'        => __( 'Post Types', 'at-shift-cfs' ),
                'user_roles'        => __( 'User Roles', 'at-shift-cfs' ),
                'post_ids'          => __( 'Posts', 'at-shift-cfs' ),
                'term_ids'          => __( 'Term IDs', 'at-shift-cfs' ),
                'page_templates'    => __( 'Page Templates', 'at-shift-cfs' ),
                'post_formats'      => __( 'Post Formats', 'at-shift-cfs' )
            ];

            $field_groups = CFS()->field_group->load_field_groups();

            // Make sure the field group exists
            $rules = [];
            if ( isset( $field_groups[ $post_id ] ) ) {
                $rules = $field_groups[ $post_id ]['rules'];
            }

            if ( ! $this->has_placement_rules( $rules ) ) {
                echo '<div class="cfs-placement-warning"><strong>' . esc_html__( 'No placement rules', 'at-shift-cfs' ) . '</strong><br />' . esc_html__( 'This field group will appear on all editable post screens.', 'at-shift-cfs' ) . '</div>';
                return;
            }

            foreach ( $rules as $criteria => $data ) {
                if ( ! isset( $labels[ $criteria ], $data['values'] ) ) {
                    continue;
                }

                $label = $labels[ $criteria ];
                $values = (array) $data['values'];
                $operator = ( isset( $data['operator'] ) && '==' == $data['operator'] ) ? '=' : '!=';

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

new cfs_init();
