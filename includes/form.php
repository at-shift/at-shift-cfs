<?php

class cfs_form
{

    public $used_types;
    public $assets_loaded;
    public $session;


    public function __construct() {
        $this->used_types = [];
        $this->assets_loaded = false;

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

        if ( isset( $_POST['wp-preview'] ) && 'dopreview' == $_POST['wp-preview'] ) {
            return;
        }

        $this->session = new cfs_session();

        // Save the form
        if ( isset( $_POST['cfs']['save'] ) ) {
            if ( wp_verify_nonce( $_POST['cfs']['save'], 'cfs_save_input' ) ) {
                $session = $this->session->get();

                if ( empty( $session ) ) {
                    die( 'Your session has expired.' );
                }

                $field_data = isset( $_POST['cfs']['input'] ) ? $_POST['cfs']['input'] : [];
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
                if ( isset( $_POST['cfs']['post_title'] ) ) {
                    $post_data['post_title'] = sanitize_text_field( wp_unslash( $_POST['cfs']['post_title'] ) );
                }

                // Content
                if ( isset( $_POST['cfs']['post_content'] ) ) {
                    $post_content = wp_unslash( $_POST['cfs']['post_content'] );
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
                do_action( 'cfs_pre_save_input', $hook_params );

                // Save the input values
                $hook_params['post_data']['ID'] = CFS()->save(
                    $field_data,
                    $post_data,
                    $options
                );

                // After-save hook
                do_action( 'cfs_after_save_input', $hook_params );

                // Delete expired sessions
                $this->session->cleanup();

                // Redirect public forms
                if ( true === $is_front_end ) {
                    $redirect_url = $_SERVER['REQUEST_URI'];
                    if ( ! empty( $session['confirmation_url'] ) ) {
                        $redirect_url = $session['confirmation_url'];
                    }

                    header( 'Location: ' . $redirect_url );
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
     * unless a site explicitly overrides the decision with cfs_form_can_save.
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
        return (bool) apply_filters( 'cfs_form_can_save', $can_save, $post_id, $post_data, $session, $field_groups );
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
        wp_enqueue_script( 'cfs-validation', CFS_URL . '/assets/js/validation.js', [ 'jquery' ], CFS_VERSION );
        wp_enqueue_script( 'jquery-powertip', CFS_URL . '/assets/js/jquery-powertip/jquery.powertip.min.js', [ 'jquery' ], CFS_VERSION );
        wp_enqueue_style( 'jquery-powertip', CFS_URL . '/assets/js/jquery-powertip/jquery.powertip.css', [], CFS_VERSION );
        wp_enqueue_style( 'cfs-input', CFS_URL . '/assets/css/input.css', [], CFS_VERSION );
    }


    /**
     * Handle front-end validation
     * @since 1.8.8
     */
    function head_scripts() {
    ?>

<script>
var CFS = CFS || {};
CFS['get_field_value'] = {};
CFS['loop_buffer'] = [];
</script>

    <?php
    }


    /**
     * Allow for custom client-side validators
     * @since 1.9.5
     */
    function footer_scripts() {
        do_action( 'cfs_custom_validation' );
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

        echo '<div class="notice notice-error" id="cfs-validation-admin-notice" style="display: none;"><p><strong>';
        echo __( 'One (or more) of your fields had validation errors. More information is available below.', 'cfs' );
        echo '</strong></p><ul id="cfs-validation-error-list"></ul></div>';
    }


    /**
     * Determine whether a field should be marked as required in the editor UI.
     */
    private function is_required_field( $field ) {
        if ( isset( $field->options['required'] ) && 0 < (int) $field->options['required'] ) {
            return true;
        }

        if ( in_array( $field->type, [ 'relationship', 'term', 'user', 'loop' ], true ) ) {
            return ! empty( $field->options['limit_min'] ) && 0 < (int) $field->options['limit_min'];
        }

        return false;
    }


    /**
     * Render the required badge shown beside field labels.
     */
    private function required_badge() {
        return ' <span class="cfs-required-badge">' . esc_html__( 'Required', 'cfs' ) . '</span>';
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
            'submit_label'          => __( 'Submit', 'cfs' ),
            'front_end'             => true,
        ];

        $params = array_merge( $defaults, $params );
        $input_fields = [];

        // Keep track of field validators
        CFS()->validators = [];

        $post_id = (int) $params['post_id'];

        if ( 0 < $post_id ) {
            $post = get_post( $post_id );
        }

        if ( empty( $params['field_groups'] ) ) {
            $field_groups = CFS()->api->get_matching_groups( $post_id, true );
            $field_groups = array_keys( $field_groups );
        }
        else {
            $field_groups = $params['field_groups'];
        }

        if ( ! empty( $field_groups ) ) {
            $input_fields = CFS()->api->get_input_fields( [
                'group_id' => $field_groups
            ] );
        }

        // Hook to allow for overridden field settings
        $input_fields = apply_filters( 'cfs_pre_render_fields', $input_fields, $params );

        // The SESSION should contain all applicable field group IDs. Since add_meta_box only
        // passes 1 field group at a time, we use CFS()->group_ids from admin_head.php
        // to store all group IDs needed for the SESSION.
        $all_group_ids = ( false === $params['front_end'] ) ? CFS()->group_ids : $field_groups;

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

        if ( false !== $params['post_title'] ) {
    ?>

        <div class="field" data-validator="required">
            <label><?php echo esc_html( $params['post_title'] ); ?></label>
            <input type="text" name="cfs[post_title]" value="<?php echo empty( $post_id ) ? '' : esc_attr( $post->post_title ); ?>" />
        </div>

    <?php
        }

        if ( false !== $params['post_content'] ) {
    ?>

        <div class="field">
            <label><?php echo esc_html( $params['post_content'] ); ?></label>
            <textarea name="cfs[post_content]"><?php echo empty( $post_id ) ? '' : esc_textarea( $post->post_content ); ?></textarea>
        </div>

    <?php
        }

        // Detect tabs
        $tabs = [];
        $is_first_tab = true;
        foreach ( $input_fields as $key => $field ) {
            if ( 'tab' == $field->type ) {
                $tabs[] = $field;
            }
        }

        do_action( 'cfs_form_before_fields', $params, [
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
            if ( ! isset( CFS()->fields[ $field->type ] ) ) {
                continue;
            }

            // Output tabs
            if ( 'tab' == $field->type && $is_first_tab ) {
                echo '<div class="cfs-tabs">';
                foreach ( $tabs as $key => $tab ) {
                    echo '<div class="cfs-tab" rel="' . esc_attr( $tab->name ) . '">' . esc_html( $tab->label ) . '</div>';
                }
                echo '</div>';
                $is_first_tab = false;
            }

            // Keep track of active field types
            if ( ! isset( $this->used_types[ $field->type ] ) ) {
                CFS()->fields[ $field->type ]->input_head( $field );
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
                elseif ( isset( $format_validators[ $field->type ] ) ) {
                    $validator = 'required_' . $field->type;
                }
                else {
                    $validator = 'required';
                }
            }

            if ( ! empty( $validator ) ) {
                CFS()->validators[ $field->name ] = [
                    'rule'  => $validator,
                    'type'  => $field->type
                ];
            }

            // Ignore sub-fields
            if ( 1 > (int) $field->parent_id ) {

                // Tab handling
                if ( 'tab' == $field->type ) {

                    // Close the previous tab
                    if ( $field->name != $tabs[0]->name ) {
                        echo '</div>';
                    }
                    echo '<div class="cfs-tab-content cfs-tab-content-' . esc_attr( $field->name ) . '">';

					if ( ! empty( $field->notes ) ) {
						echo '<div class="cfs-tab-notes">' . esc_html( $field->notes ) . '</div>';
					}
                }
                else {
    ?>

        <div class="field field-<?php echo esc_attr( $field->name ); ?>" data-type="<?php echo esc_attr( $field->type ); ?>" data-name="<?php echo esc_attr( $field->name ); ?>">
            <?php if ( 'loop' == $field->type ) : ?>
            <a href="javascript:;" class="cfs_loop_toggle" title="<?php esc_html_e( 'Toggle row visibility', 'cfs' ); ?>"></a>
            <?php endif; ?>

            <?php if ( ! empty( $field->label ) ) : ?>
            <label><?php echo esc_html( $field->label ); ?><?php echo $this->is_required_field( $field ) ? $this->required_badge() : ''; ?></label>
            <?php endif; ?>

            <?php if ( ! empty( $field->notes ) ) : ?>
            <p class="notes"><?php echo esc_html( $field->notes ); ?></p>
            <?php endif; ?>

            <div class="cfs_<?php echo esc_attr( $field->type ); ?>">

    <?php
                CFS()->create_field( [
                    'id'            => $field->id,
                    'group_id'      => $field->group_id,
                    'type'          => $field->type,
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
        if ( ! empty( $tabs ) ) {
            echo '</div>';
        }

        do_action( 'cfs_form_after_fields', $params, [
            'group_ids'     => $all_group_ids,
            'input_fields'  => $input_fields
        ] );
    ?>

        <script>
        (function($) {
            CFS.field_rules = CFS.field_rules || {};
            $.extend( CFS.field_rules, <?php echo wp_json_encode( CFS()->validators ); ?> );
        })(jQuery);
        </script>
        <input type="hidden" name="cfs[save]" value="<?php echo wp_create_nonce( 'cfs_save_input' ); ?>" />
        <input type="hidden" name="cfs[session_id]" value="<?php echo esc_attr( $this->session->session_id ); ?>" />

        <?php if ( false !== $params['front_end'] ) : ?>

        <input type="submit" value="<?php echo esc_attr( $params['submit_label'] ); ?>" />
    </form>
</div>

    <?php
        endif;
    }
}

CFS()->form = new cfs_form();
