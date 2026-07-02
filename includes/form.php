<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_form
{

    public $used_types;
    public $assets_loaded;
    public $session;
    public $submission_errors;
    private $submitted_values;
    private $submitted_post_data;


    public function __construct() {
        $this->used_types = [];
        $this->assets_loaded = false;
        $this->submission_errors = [];
        $this->submitted_values = [];
        $this->submitted_post_data = [];

        add_action( 'init', [ $this, 'init' ], 100 );
        add_action( 'admin_head', [ $this, 'head_scripts' ] );
        add_action( 'admin_print_footer_scripts', [ $this, 'footer_scripts' ] );
        add_action( 'admin_notices', [ $this, 'admin_notice' ] );
    }


    /**
     * Initialize the session and save the form
     * @since 1.8.5
     */
    public function init() {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }

        $wp_preview = isset( $_POST['wp-preview'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-preview'] ) ) : '';
        if ( 'dopreview' == $wp_preview ) {
            return;
        }

        $this->session = new Atshift_CFS_session();
        $cfs_post = isset( $_POST['cfs'] ) && is_array( $_POST['cfs'] ) ? wp_unslash( $_POST['cfs'] ) : [];

        // Save the form
        if ( isset( $cfs_post['save'] ) ) {
            $nonce = sanitize_text_field( $cfs_post['save'] );
            if ( wp_verify_nonce( $nonce, 'atshift_cfs_save_input' ) ) {
                $session = $this->session->get();

                if ( empty( $session ) ) {
                    die( 'Your session has expired.' );
                }

                $field_data = isset( $cfs_post['input'] ) ? $this->sanitize_submitted_field_data( $cfs_post['input'] ) : [];
                $post_data = [];

                // Form settings are session-based for added security
                $post_id = (int) $session['post_id'];
                $field_groups = isset( $session['field_groups'] ) ? $session['field_groups'] : [];
                $is_front_end = isset( $session['front_end'] ) ? (bool) $session['front_end'] : true;

                // Sanitize field groups
                foreach ( $field_groups as $key => $val ) {
                    $field_groups[$key] = (int) $val;
                }

                // Title
                if ( isset( $cfs_post['post_title'] ) ) {
                    $post_data['post_title'] = sanitize_text_field( $cfs_post['post_title'] );
                }

                // Content
                if ( isset( $cfs_post['post_content'] ) ) {
                    $post_content = $cfs_post['post_content'];
                    $post_data['post_content'] = current_user_can( 'unfiltered_html' ) ? $post_content : wp_kses_post( $post_content );
                }

                // New posts
                if ( $post_id < 1 ) {
                    // Post type
                    if ( isset( $session['post_type'] ) ) {
                        $post_data['post_type'] = $session['post_type'];
                    }

                    // Post status
                    if ( isset( $session['post_status'] ) ) {
                        $post_data['post_status'] = $session['post_status'];
                    }
                }
                else {
                    $post_data['ID'] = $post_id;
                }

                if ( ! $this->current_user_can_save( $post_id, $post_data, $session, $field_groups ) ) {
                    return;
                }

                if ( ! $this->is_admin_draft_save( $is_front_end ) ) {
                    $validation_errors = $this->validate_submission( $field_data, $field_groups );

                    if ( ! empty( $validation_errors ) ) {
                        if ( true === $is_front_end ) {
                            $this->submission_errors = array_values( array_unique( $validation_errors ) );
                            $this->submitted_values = $this->normalize_submitted_values( $field_data, $field_groups );
                            $this->submitted_post_data = $post_data;

                            add_filter( 'atshift_cfs_get_input_fields', [ $this, 'restore_submitted_values' ], 20, 2 );
                            return;
                        }

                        wp_die(
                            esc_html__( 'One (or more) of your fields had validation errors. More information is available below.', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            esc_html__( 'Validation', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            [ 'response' => 400 ]
                        );
                    }
                }

                $options = [
                    'format'        => 'input',
                    'field_groups'  => $field_groups
                ];

                // Hook parameters
                $hook_params = [
                    'field_data'    => $field_data,
                    'post_data'     => $post_data,
                    'options'       => $options,
                ];

                // Pre-save hook
                do_action( 'atshift_cfs_pre_save_input', $hook_params );

                // Save the input values
                $hook_params['post_data']['ID'] = atshift_fields_maintenance_for_custom_field_suite()->save(
                    $field_data,
                    $post_data,
                    $options
                );

                // After-save hook
                do_action( 'atshift_cfs_after_save_input', $hook_params );

                // Delete expired sessions
                $this->session->cleanup();

                // Redirect public forms
                if ( true === $is_front_end ) {
                    $redirect_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
                    if ( ! empty( $session['confirmation_url'] ) ) {
                        $redirect_url = esc_url_raw( $session['confirmation_url'] );
                    }

                    wp_safe_redirect( $redirect_url );
                    exit;
                }
            }
        }
    }


    /**
     * Determine whether the current request may save this CFS form.
     *
     * Public forms may still create new posts by default for backwards compatibility.
     * Updating an existing post requires the normal WordPress edit_post capability
     * unless a site explicitly overrides the decision with atshift_cfs_form_can_save.
     *
     * @param int $post_id
     * @param array $post_data
     * @param array $session
     * @param array $field_groups
     * @return bool
     */
    protected function current_user_can_save( $post_id, $post_data, $session, $field_groups ) {
        $is_front_end = isset( $session['front_end'] ) ? (bool) $session['front_end'] : true;

        if ( 0 < $post_id ) {
            $can_save = current_user_can( 'edit_post', $post_id );
        }
        else {
            $post_type = isset( $post_data['post_type'] ) ? $post_data['post_type'] : 'post';
            $post_type_obj = get_post_type_object( $post_type );

            if ( false === $is_front_end ) {
                $create_cap = is_object( $post_type_obj ) && isset( $post_type_obj->cap->create_posts ) ? $post_type_obj->cap->create_posts : 'edit_posts';
                $can_save = current_user_can( $create_cap );
            }
            else {
                $can_save = true;
            }
        }

        /**
         * Filter whether a CFS form submission may be saved.
         *
         * Returning true allows the save, returning false blocks it. This is useful
         * for public edit forms that intentionally allow non-standard workflows.
         *
         * @param bool $can_save
         * @param int $post_id
         * @param array $post_data
         * @param array $session
         * @param array $field_groups
         */
        return (bool) apply_filters( 'atshift_cfs_form_can_save', $can_save, $post_id, $post_data, $session, $field_groups );
    }


    private function is_admin_draft_save( $is_front_end ) {
        $save_action = isset( $_POST['save'] ) ? sanitize_text_field( wp_unslash( $_POST['save'] ) ) : '';
        return false === $is_front_end && '' !== $save_action;
    }


    protected function validate_submission( $field_data, $field_groups ) {
        if ( empty( $field_groups ) ) {
            return [];
        }

        $fields_by_parent = [];
        $fields = atshift_fields_maintenance_for_custom_field_suite()->api->find_input_fields( [ 'group_id' => $field_groups ] );

        foreach ( $fields as $field ) {
            $field = (object) $field;
            $fields_by_parent[ (int) $field->parent_id ][] = $field;
        }

        $errors = [];
        $this->validate_field_container( (array) $field_data, 0, $fields_by_parent, $errors );

        return $errors;
    }


    private function sanitize_submitted_field_data( $value ) {
        if ( is_array( $value ) ) {
            $sanitized = [];
            foreach ( $value as $key => $item ) {
                $sanitized_key = is_int( $key ) ? $key : sanitize_text_field( (string) $key );
                $sanitized[ $sanitized_key ] = $this->sanitize_submitted_field_data( $item );
            }
            return $sanitized;
        }

        if ( is_scalar( $value ) || null === $value ) {
            return wp_kses_post( (string) $value );
        }

        return '';
    }


    private function validate_field_container( $data, $parent_id, $fields_by_parent, &$errors, $conditional_value = null ) {
        if ( empty( $fields_by_parent[ $parent_id ] ) ) {
            return;
        }

        foreach ( $fields_by_parent[ $parent_id ] as $field ) {
            if ( null !== $conditional_value ) {
                $field_conditional_value = isset( $field->options['conditional_value'] ) ? (string) $field->options['conditional_value'] : '';
                if ( $field_conditional_value !== $conditional_value ) {
                    continue;
                }
            }

            if ( 'tab' === $field->type ) {
                continue;
            }

            if ( 'conditional' === $field->type ) {
                $field_data = isset( $data[ $field->id ] ) ? $data[ $field->id ] : [];
                $selected = '';
                if ( is_array( $field_data ) && array_key_exists( 'value', $field_data ) ) {
                    $selected_value = $field_data['value'];
                    $selected = is_array( $selected_value ) ? $this->first_scalar_submission_value( $selected_value ) : ( is_scalar( $selected_value ) ? (string) $selected_value : '' );
                }
                $choices = isset( $field->options['choices'] ) && is_array( $field->options['choices'] ) ? $field->options['choices'] : [];
                $display_type = isset( $field->options['display_type'] ) ? $field->options['display_type'] : 'radio';

                if ( 'select' !== $display_type && ! isset( $choices[ $selected ] ) ) {
                    $default_value = isset( $field->options['default_value'] ) ? (string) $field->options['default_value'] : '';
                    $selected = isset( $choices[ $default_value ] ) ? $default_value : (string) key( $choices );
                }

                if ( '' !== $selected ) {
                    $this->validate_field_container( $data, (int) $field->id, $fields_by_parent, $errors, $selected );
                }
                continue;
            }

            if ( in_array( $field->type, [ 'group', 'accordion' ], true ) ) {
                $this->validate_field_container( $data, (int) $field->id, $fields_by_parent, $errors );
                continue;
            }

            $field_data = isset( $data[ $field->id ] ) ? $data[ $field->id ] : [];

            if ( 'loop' === $field->type ) {
                $rows = is_array( $field_data ) ? $field_data : [];
                $this->validate_count_limits( $field, count( $rows ), $errors );

                foreach ( $rows as $row ) {
                    $this->validate_field_container( (array) $row, (int) $field->id, $fields_by_parent, $errors );
                }
                continue;
            }

            $value = is_array( $field_data ) && array_key_exists( 'value', $field_data ) ? $field_data['value'] : '';

            if ( in_array( $field->type, [ 'relationship', 'term', 'user' ], true ) ) {
                $this->validate_count_limits( $field, count( $this->normalize_submitted_ids( $value ) ), $errors );
            }

            if ( ! empty( $field->options['required'] ) && $this->is_empty_submission_value( $value, $field->type ) ) {
                $errors[] = $field->name;
                continue;
            }

            if ( ! $this->is_empty_submission_value( $value, $field->type ) && ! $this->is_valid_submission_format( $value, $field->type ) ) {
                $errors[] = $field->name;
            }
        }
    }


    private function validate_count_limits( $field, $count, &$errors ) {
        $min = empty( $field->options['limit_min'] ) ? 0 : (int) $field->options['limit_min'];
        $max = empty( $field->options['limit_max'] ) ? 0 : (int) $field->options['limit_max'];

        if ( ( 0 < $min && $count < $min ) || ( 0 < $max && $max < $count ) ) {
            $errors[] = $field->name;
        }
    }


    private function normalize_submitted_ids( $value ) {
        $values = [];

        foreach ( (array) $value as $item ) {
            if ( is_scalar( $item ) ) {
                $values = array_merge( $values, explode( ',', (string) $item ) );
            }
        }

        return array_values( array_filter( array_map( 'absint', $values ) ) );
    }


    private function is_empty_submission_value( $value, $field_type ) {
        if ( 'code_view' === $field_type ) {
            $value = is_array( $value ) ? $value : [];
            $language = isset( $value['language'] ) && is_scalar( $value['language'] ) ? trim( (string) $value['language'] ) : '';
            $code = isset( $value['code'] ) && is_scalar( $value['code'] ) ? trim( (string) $value['code'] ) : '';
            return '' === $language || '' === $code;
        }

        if ( is_array( $value ) ) {
            foreach ( $value as $item ) {
                if ( is_scalar( $item ) && '' !== trim( (string) $item ) ) {
                    return false;
                }
            }
            return true;
        }

        return ! is_scalar( $value ) || '' === trim( (string) $value );
    }


    private function is_valid_submission_format( $value, $field_type ) {
        if ( 'time' === $field_type && is_array( $value ) ) {
            $hour = isset( $value['hour'] ) && is_scalar( $value['hour'] ) ? (string) $value['hour'] : '';
            $minute = isset( $value['minute'] ) && is_scalar( $value['minute'] ) ? (string) $value['minute'] : '';
            $value = $hour . ':' . $minute;
        }

        if ( is_array( $value ) && in_array( $field_type, [ 'phone', 'email', 'url', 'number', 'date', 'color' ], true ) ) {
            $value = $this->first_scalar_submission_value( $value );
        }

        if ( ! is_scalar( $value ) ) {
            return true;
        }

        $value = trim( (string) $value );

        switch ( $field_type ) {
            case 'phone':
                return (bool) preg_match( '/^[0-9+\-().\s]+$/', $value );

            case 'email':
                return (bool) is_email( $value );

            case 'url':
                return (bool) preg_match( '/^(https?:\/\/|mailto:|tel:)/i', $value );

            case 'number':
                return (bool) preg_match( '/^-?(?:\d+|\d*\.\d+)$/', $value );

            case 'time':
                return (bool) preg_match( '/^([01]\d|2[0-3]):[0-5]\d$/', $value );

            case 'date':
                return (bool) preg_match( '/^\d{4}-\d{2}-\d{2}/', $value );

            case 'color':
                return (bool) preg_match( '/^#[0-9a-zA-Z]{3,}$/', $value );
        }

        return true;
    }


    private function first_scalar_submission_value( $value ) {
        foreach ( (array) $value as $item ) {
            if ( is_scalar( $item ) || null === $item ) {
                return null === $item ? '' : $item;
            }
        }

        return '';
    }


    /**
     * Restore submitted values when a front-end form fails server-side validation.
     *
     * @param array $fields
     * @param array $params
     * @return array
     */
    public function restore_submitted_values( $fields, $params ) {
        if ( empty( $this->submission_errors ) ) {
            return $fields;
        }

        foreach ( $fields as $field_id => $field ) {
            if ( array_key_exists( $field_id, $this->submitted_values ) ) {
                $field->value = $this->submitted_values[ $field_id ];
            }

            if ( in_array( $field->type, [ 'group', 'accordion', 'conditional' ], true ) ) {
                $field->values = $this->submitted_values;
            }
        }

        return $fields;
    }


    /**
     * Whether the current front-end request contains validation errors.
     *
     * @return bool
     */
    public function has_submission_errors() {
        return ! empty( $this->submission_errors );
    }


    /**
     * Get submitted values normalized to the CFS input format.
     *
     * @return array
     */
    public function get_submitted_values() {
        return $this->submitted_values;
    }


    private function normalize_submitted_values( $data, $field_groups ) {
        $field_types = [];
        $fields = atshift_fields_maintenance_for_custom_field_suite()->api->find_input_fields( [ 'group_id' => $field_groups ] );

        foreach ( $fields as $field ) {
            $field_types[ (int) $field['id'] ] = $field['type'];
        }

        return $this->normalize_submitted_container( $data, $field_types );
    }


    private function normalize_submitted_container( $data, $field_types ) {
        $values = [];

        foreach ( (array) $data as $key => $value ) {
            $field_type = isset( $field_types[ (int) $key ] ) ? $field_types[ (int) $key ] : '';

            if ( is_array( $value ) && array_key_exists( 'value', $value ) ) {
                $values[ $key ] = $this->normalize_submitted_value( $value['value'], $field_types, $field_type );
            }
            else {
                $values[ $key ] = $this->normalize_submitted_value( $value, $field_types, $field_type );
            }
        }

        return $values;
    }


    private function normalize_submitted_value( $value, $field_types, $field_type = '' ) {
        if ( ! is_array( $value ) ) {
            return $value;
        }

        if ( $this->is_scalar_input_type( $field_type ) ) {
            foreach ( $value as $item ) {
                if ( is_scalar( $item ) || null === $item ) {
                    return null === $item ? '' : $item;
                }
            }

            return '';
        }

        $normalized = [];
        foreach ( $value as $key => $item ) {
            $child_type = isset( $field_types[ (int) $key ] ) ? $field_types[ (int) $key ] : '';

            if ( is_array( $item ) && array_key_exists( 'value', $item ) ) {
                $normalized[ $key ] = $this->normalize_submitted_value( $item['value'], $field_types, $child_type );
            }
            else {
                $normalized[ $key ] = $this->normalize_submitted_value( $item, $field_types, $child_type );
            }
        }

        return $normalized;
    }


    private function is_scalar_input_type( $field_type ) {
        return in_array( $field_type, [
            'text', 'textarea', 'wysiwyg', 'phone', 'email', 'url', 'number',
            'radio', 'date', 'file', 'color', 'true_false', 'wp_tag',
            'featured_image', 'conditional',
        ], true );
    }


    /**
     * Load form dependencies
     * @since 1.8.5
     */
    public function load_assets() {
        if ( $this->assets_loaded ) {
            return;
        }

        $this->assets_loaded = true;

        add_action( 'wp_head', [ $this, 'head_scripts' ] );
        add_action( 'wp_footer', [ $this, 'footer_scripts' ], 25 );

        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        $validation_js_version = file_exists( ATSHIFT_CFS_DIR . '/assets/js/validation.js' ) ? ATSHIFT_CFS_VERSION . '.' . filemtime( ATSHIFT_CFS_DIR . '/assets/js/validation.js' ) : ATSHIFT_CFS_VERSION;
        $input_css_version = file_exists( ATSHIFT_CFS_DIR . '/assets/css/input.css' ) ? ATSHIFT_CFS_VERSION . '.' . filemtime( ATSHIFT_CFS_DIR . '/assets/css/input.css' ) : ATSHIFT_CFS_VERSION;
        wp_enqueue_script( 'atshift-cfs-validation', ATSHIFT_CFS_URL . '/assets/js/validation.js', [ 'jquery' ], $validation_js_version );
        wp_enqueue_script( 'jquery-powertip', ATSHIFT_CFS_URL . '/assets/js/jquery-powertip/jquery.powertip.min.js', [ 'jquery' ], ATSHIFT_CFS_VERSION );
        wp_enqueue_style( 'jquery-powertip', ATSHIFT_CFS_URL . '/assets/js/jquery-powertip/jquery.powertip.css', [], ATSHIFT_CFS_VERSION );
        wp_enqueue_style( 'atshift-cfs-input', ATSHIFT_CFS_URL . '/assets/css/input.css', [], $input_css_version );
    }


    /**
     * Handle front-end validation
     * @since 1.8.8
     */
    function head_scripts() {
        wp_add_inline_script(
            'atshift-cfs-validation',
            'var CFS = CFS || {};
CFS["get_field_value"] = {};
CFS["loop_buffer"] = [];
CFS["validation_messages"] = ' . wp_json_encode( [
                'enter_value'       => __( 'Please enter a value', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'valid_date'        => __( 'Please enter a valid date (YYYY-MM-DD HH:MM)', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'valid_color'       => __( 'Please enter a valid color HEX (#ff0000)', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'enter_phone'       => __( 'Please enter a phone number', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'valid_phone'       => __( 'Please enter a valid phone number', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'enter_email'       => __( 'Please enter an email address', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'valid_email'       => __( 'Please enter a valid email address', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'enter_number'      => __( 'Please enter a number', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'valid_number'      => __( 'Please enter a valid number', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'enter_url'         => __( 'Please enter a URL', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'valid_url'         => __( 'Please enter a valid URL', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'select_time'       => __( 'Please select a time', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'valid_time'        => __( 'Please select a valid time', 'atshift-fields-maintenance-for-custom-field-suite' ),
                'enter_code'        => __( 'Please select a language and enter code', 'atshift-fields-maintenance-for-custom-field-suite' ),
                /* translators: %s: required item count. */
                'select_items'      => __( 'Please select %s item(s)', 'atshift-fields-maintenance-for-custom-field-suite' ),
                /* translators: 1: minimum item count, 2: maximum item count. */
                'select_item_range' => __( 'Please select between %1$s and %2$s items', 'atshift-fields-maintenance-for-custom-field-suite' ),
            ] ) . ';',
            'before'
        );
    }


    /**
     * Allow for custom client-side validators
     * @since 1.9.5
     */
    function footer_scripts() {
        do_action( 'atshift_cfs_custom_validation' );
    }


    /**
     * Add an admin notice to be displayed in the event of
     * validation errors
     * @since 2.6
     */
    function admin_notice() {
        $screen = get_current_screen();

        if ( !isset($screen->base) || $screen->base !== 'post' ) {
            return;
        }

        echo '<div class="notice notice-error" id="atshift-cfs-validation-admin-notice" style="display: none;"><p><strong>';
        echo esc_html__( 'One (or more) of your fields had validation errors. More information is available below.', 'atshift-fields-maintenance-for-custom-field-suite' );
        echo '</strong></p><ul id="atshift-cfs-validation-error-list"></ul></div>';
    }


    /**
     * Render the HTML input form
     * @param array $params
     * @return string form HTML code
     * @since 1.8.5
     */
    public function render( $params ) {
        global $post;

        $defaults = [
            'post_id'               => false, // false = new entries
            'field_groups'          => [], // group IDs, required for new entries
            'post_title'            => false,
            'post_content'          => false,
            'post_status'           => 'draft',
            'post_type'             => 'post',
            'excluded_fields'       => [],
            'confirmation_message'  => '',
            'confirmation_url'      => '',
            'submit_label'          => __( 'Submit', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'front_end'             => true,
        ];

        $params = array_merge( $defaults, $params );
        $this->load_assets();
        $input_fields = [];

        // Keep track of field validators
        atshift_fields_maintenance_for_custom_field_suite()->validators = [];

        $post_id = (int) $params['post_id'];

        if ( 0 < $post_id ) {
            $post = get_post( $post_id );
        }

        if ( empty( $params['field_groups'] ) ) {
            $field_groups = atshift_fields_maintenance_for_custom_field_suite()->api->get_matching_groups( $post_id, true );
            $field_groups = array_keys( $field_groups );
        }
        else {
            $field_groups = $params['field_groups'];
        }

        if ( ! empty( $field_groups ) ) {
            $input_fields = atshift_fields_maintenance_for_custom_field_suite()->api->get_input_fields( [
                'group_id' => $field_groups
            ] );
        }

        // Hook to allow for overridden field settings
        $input_fields = apply_filters( 'atshift_cfs_pre_render_fields', $input_fields, $params );

        // The SESSION should contain all applicable field group IDs. Since add_meta_box only
        // passes 1 field group at a time, we use atshift_fields_maintenance_for_custom_field_suite()->group_ids from admin_head.php
        // to store all group IDs needed for the SESSION.
        $all_group_ids = ( false === $params['front_end'] ) ? atshift_fields_maintenance_for_custom_field_suite()->group_ids : $field_groups;

        $session_data = [
            'post_id'               => $post_id,
            'post_type'             => $params['post_type'],
            'post_status'           => $params['post_status'],
            'field_groups'          => $all_group_ids,
            'confirmation_message'  => $params['confirmation_message'],
            'confirmation_url'      => $params['confirmation_url'],
            'front_end'             => $params['front_end'],
        ];

        // Set the SESSION
        $this->session->set( $session_data );

        if ( false !== $params['front_end'] ) {
    ?>

<div class="cfs_input no_box">
    <form id="post" method="post" action="">

    <?php
        }

        if ( false !== $params['front_end'] && $this->has_submission_errors() ) {
            $error_labels = [];
            foreach ( $input_fields as $field ) {
                if ( in_array( $field->name, $this->submission_errors, true ) ) {
                    $error_labels[ $field->name ] = empty( $field->label ) ? $field->name : $field->label;
                }
            }
    ?>

        <div class="atshift-cfs-validation-notice" id="atshift-cfs-validation-admin-notice" role="alert" aria-live="assertive">
            <p><strong><?php esc_html_e( 'One (or more) of your fields had validation errors. More information is available below.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></strong></p>
            <ul id="atshift-cfs-validation-error-list">
                <?php foreach ( $this->submission_errors as $field_name ) : ?>
                <li><?php echo esc_html( isset( $error_labels[ $field_name ] ) ? $error_labels[ $field_name ] : $field_name ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

    <?php
        }

        if ( false !== $params['post_title'] ) {
            $post_title = empty( $post_id ) ? '' : $post->post_title;
            if ( $this->has_submission_errors() && isset( $this->submitted_post_data['post_title'] ) ) {
                $post_title = $this->submitted_post_data['post_title'];
            }
    ?>

        <div class="field" data-validator="required">
            <label><?php echo esc_html( $params['post_title'] ); ?></label>
            <input type="text" name="cfs[post_title]" value="<?php echo esc_attr( $post_title ); ?>" />
        </div>

    <?php
        }

        if ( false !== $params['post_content'] ) {
            $post_content = empty( $post_id ) ? '' : $post->post_content;
            if ( $this->has_submission_errors() && isset( $this->submitted_post_data['post_content'] ) ) {
                $post_content = $this->submitted_post_data['post_content'];
            }
    ?>

        <div class="field">
            <label><?php echo esc_html( $params['post_content'] ); ?></label>
            <textarea name="cfs[post_content]"><?php echo esc_textarea( $post_content ); ?></textarea>
        </div>

    <?php
        }

        // Detect tabs
        $tabs = [];
        $is_first_tab = true;
        $tab_content_open = false;
        $has_later_top_level_tab = [];
        $top_level_fields = [];
        foreach ( $input_fields as $key => $field ) {
            if ( 1 > (int) $field->parent_id ) {
                $top_level_fields[] = $field;

                if ( 'tab' == $field->type ) {
                    $tabs[] = $field;
                }
            }
        }
        $has_tabs = 1 < count( $tabs );

        $found_later_tab = false;
        foreach ( array_reverse( $top_level_fields ) as $field ) {
            $has_later_top_level_tab[ (int) $field->id ] = $found_later_tab;

            if ( 'tab' == $field->type ) {
                $found_later_tab = true;
            }
        }

        do_action( 'atshift_cfs_form_before_fields', $params, [
            'group_ids'     => $all_group_ids,
            'input_fields'  => $input_fields
        ] );

        // Add any necessary head scripts
        foreach ( $input_fields as $key => $field ) {

            // Exclude fields
            if ( in_array( $field->name, (array) $params['excluded_fields'] ) ) {
                continue;
            }

            // Skip missing field types
            if ( ! isset( atshift_fields_maintenance_for_custom_field_suite()->fields[ $field->type ] ) ) {
                continue;
            }

            // Output tabs
            if ( $has_tabs && 'tab' == $field->type && 1 > (int) $field->parent_id && $is_first_tab ) {
                echo '<div class="cfs-tabs">';
                foreach ( $tabs as $key => $tab ) {
                    $tab_key = 'field-' . $tab->id;
                    echo '<div class="cfs-tab" rel="' . esc_attr( $tab_key ) . '" data-tab-key="' . esc_attr( $tab_key ) . '">' . esc_html( $tab->label ) . '</div>';
                }
                echo '</div>';
                $is_first_tab = false;
            }

            // Keep track of active field types
            if ( ! isset( $this->used_types[ $field->type ] ) ) {
                atshift_fields_maintenance_for_custom_field_suite()->fields[ $field->type ]->input_head( $field );
                $this->used_types[ $field->type ] = true;
            }

            $validator = '';

            if ( in_array( $field->type, [ 'relationship', 'term', 'user', 'loop' ] ) ) {
                $min = empty( $field->options['limit_min'] ) ? 0 : (int) $field->options['limit_min'];
                $max = empty( $field->options['limit_max'] ) ? 0 : (int) $field->options['limit_max'];
                $validator = "limit|$min,$max";
            }

            $format_validators = [
                'phone'  => 'valid_phone',
                'email'  => 'valid_email',
                'number' => 'valid_number',
                'url'    => 'valid_url',
                'time'   => 'valid_time',
            ];

            if ( isset( $format_validators[ $field->type ] ) ) {
                $validator = $format_validators[ $field->type ];
            }

            if ( isset( $field->options['required'] ) && 0 < (int) $field->options['required'] ) {
                if ( 'date' == $field->type ) {
                    $validator = 'valid_date';
                }
                elseif ( 'color' == $field->type ) {
                    $validator = 'valid_color';
                }
                elseif ( 'code_view' == $field->type ) {
                    $validator = 'required_code_view';
                }
                elseif ( isset( $format_validators[ $field->type ] ) ) {
                    $validator = 'required_' . $field->type;
                }
                else {
                    $validator = 'required';
                }
            }

            if ( ! empty( $validator ) ) {
                atshift_fields_maintenance_for_custom_field_suite()->validators[ $field->name ] = [
                    'rule'  => $validator,
                    'type'  => $field->type
                ];
            }

            // Ignore sub-fields
            if ( 1 > (int) $field->parent_id ) {

                $outside_tabs = ! empty( $field->options['outside_tabs'] ) && empty( $has_later_top_level_tab[ (int) $field->id ] );

                if ( $has_tabs && $tab_content_open && $outside_tabs ) {
                    echo '</div>';
                    $tab_content_open = false;
                }

                // Tab handling
                if ( 'tab' == $field->type ) {

                    if ( $has_tabs ) {
                        // Close the previous tab
                        if ( $tab_content_open ) {
                            echo '</div>';
                        }
                        $tab_key = 'field-' . $field->id;
                        echo '<div class="cfs-tab-content cfs-tab-content-' . esc_attr( $tab_key ) . '" data-tab-key="' . esc_attr( $tab_key ) . '">';
                        $tab_content_open = true;

                        if ( ! empty( $field->notes ) ) {
                            echo '<div class="cfs-tab-notes">' . esc_html( $field->notes ) . '</div>';
                        }
                    }
                }
                else {
    ?>

        <div class="field field-<?php echo esc_attr( $field->name ); ?>" data-type="<?php echo esc_attr( $field->type ); ?>" data-name="<?php echo esc_attr( $field->name ); ?>">
            <?php if ( 'loop' == $field->type ) : ?>
            <a href="javascript:;" class="cfs_loop_toggle" title="<?php esc_attr_e( 'Toggle row visibility', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"></a>
            <?php endif; ?>

            <?php if ( 'accordion' !== $field->type && ! empty( $field->label ) ) : ?>
            <label><?php echo esc_html( $field->label ); ?><?php echo Atshift_CFS_field::is_required_field( $field ) ? wp_kses_post( Atshift_CFS_field::required_badge() ) : ''; ?></label>
            <?php endif; ?>

            <?php if ( 'accordion' !== $field->type && ! empty( $field->notes ) ) : ?>
            <p class="notes"><?php echo esc_html( $field->notes ); ?></p>
            <?php endif; ?>

            <div class="cfs_<?php echo esc_attr( $field->type ); ?>">

    <?php
                atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                    'id'            => $field->id,
                    'group_id'      => $field->group_id,
                    'type'          => $field->type,
                    'label'         => $field->label,
                    'input_name'    => "cfs[input][$field->id][value]",
                    'input_class'   => $field->type,
                    'options'       => $field->options,
                    'value'         => $field->value,
                    'notes'         => $field->notes,
                ] );
    ?>

            </div>
        </div>

    <?php
                }
            }
        }

        // Make sure to close tabs
        if ( $has_tabs && $tab_content_open ) {
            echo '</div>';
        }

        do_action( 'atshift_cfs_form_after_fields', $params, [
            'group_ids'     => $all_group_ids,
            'input_fields'  => $input_fields
        ] );
    ?>

        <?php
        wp_add_inline_script(
            'atshift-cfs-validation',
            '(function($) {
                CFS.field_rules = CFS.field_rules || {};
                $.extend( CFS.field_rules, ' . wp_json_encode( atshift_fields_maintenance_for_custom_field_suite()->validators ) . ' );
                ' . ( $this->has_submission_errors() ? 'CFS.server_validation_errors = true;' : '' ) . '
            })(jQuery);'
        );
        ?>
        <input type="hidden" name="cfs[save]" value="<?php echo esc_attr( wp_create_nonce( 'atshift_cfs_save_input' ) ); ?>" />
        <input type="hidden" name="cfs[session_id]" value="<?php echo esc_attr( $this->session->session_id ); ?>" />

        <?php if ( false !== $params['front_end'] ) : ?>

        <input type="submit" value="<?php echo esc_attr( $params['submit_label'] ); ?>" />
    </form>
</div>

    <?php
        endif;
    }
}

atshift_fields_maintenance_for_custom_field_suite()->form = new Atshift_CFS_form();
