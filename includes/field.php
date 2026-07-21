<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_field
{
    public $name;
    public $label;


    /**
     * Constructor
     * @param object $parent
     * @since 1.0.5
     */
    function __construct() {
        $this->name = 'text';
        $this->label = __( 'Text', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    /**
     * Generate the field HTML
     * @param object $field
     * @since 1.0.5
     */
    function html( $field ) {
    ?>
        <?php $this->input_suffix_open( $field ); ?>
        <input type="text" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" value="<?php echo esc_attr( $field->value ); ?>" placeholder="<?php echo esc_attr( $this->get_input_placeholder( $field ) ); ?>" />
        <?php $this->input_suffix_close( $field ); ?>
    <?php
    }


    /**
     * Generate settings HTML for the field group edit screen
     * @param int $key The unique field identifier
     * @param object $field
     * @since 1.0.5
     */
    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Validation', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type'          => 'true_false',
                        'input_name'    => "cfs[fields][$key][options][required]",
                        'input_class'   => 'true_false',
                        'value'         => $this->get_option( $field, 'required' ),
                        'options'       => [ 'message' => __( 'This is a required field', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    /**
     * Add necessary field scripts or CSS (triggered once per pageload)
     * @param mixed $field The field object (optional)
     * @since 1.0.5
     */
    function input_head( $field = null ) {

    }


    /**
     * Format the value directly after database load
     *
     * Values are retrieved from the database as an array, even for field types that
     * don't expect arrays. For field types that should return array values, make
     * sure to override this method and return $value.
     *
     * @param mixed $value
     * @param mixed $field The field object (optional)
     * @return mixed The field value
     * @since 1.6.9
     */
    function prepare_value( $value, $field = null ) {
        return $value[0];
    }


    /**
     * Format the value for use with $cfs->get
     * @param mixed $value
     * @param mixed $field The field object (optional)
     * @return mixed
     * @since 1.0.5
     */
    function format_value_for_api( $value, $field = null ) {
        return $value;
    }


    /**
     * Format the value for use with HTML input elements
     * @param mixed $value
     * @param mixed $field The field object (optional)
     * @return mixed
     * @since 1.0.5
     */
    function format_value_for_input( $value, $field = null ) {
        return $value;
    }


    /**
     * Format the value before saving to DB
     * @param mixed $value
     * @param mixed $field The field object (optional)
     * @return mixed
     * @since 1.4.2
     */
    function pre_save( $value, $field = null ) {
        return $value;
    }


    /**
     * Modify field settings before saving to DB
     * @param object $field
     * @return object
     * @since 1.6.8
     */
    function pre_save_field( $field ) {
        return $field;
    }


    /**
     * Helper method to retrieve a field setting
     * @param object $field
     * @param string $option_name
     * @param mixed $default_value
     * @return mixed
     * @since 1.4.3
     */
    function get_option( $field, $option_name, $default_value = '' ) {
        if ( isset( $field->options[ $option_name ] ) ) {
            if ( is_string( $field->options[ $option_name ] ) ) {
                return esc_attr( $field->options[ $option_name ] );
            }
            return $field->options[ $option_name ];
        }
        return $default_value;
    }


    /**
     * Retrieve a placeholder from a field being rendered for data entry.
     *
     * @param object $field
     * @param string $default_value
     * @return string
     */
    function get_input_placeholder( $field, $default_value = '' ) {
        if ( isset( $field->options['placeholder'] ) && is_string( $field->options['placeholder'] ) ) {
            return $field->options['placeholder'];
        }
        return $default_value;
    }


    /**
     * Return the default visual editor body style for CFS editor fields.
     *
     * Front-end forms can inherit theme editor styles in TinyMCE. Keep the editing
     * surface readable and consistent with WordPress admin controls.
     *
     * @return string
     */
    protected function get_tinymce_content_style() {
        return implode( "\n", [
            '/* atshift-cfs-editor-font-reset */',
            'body.mce-content-body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Hiragino Sans", "Hiragino Kaku Gothic ProN", "Yu Gothic", Meiryo, sans-serif !important; font-size: 16px; line-height: 1.7; color: #1d2327; }',
            'body.mce-content-body p, body.mce-content-body div, body.mce-content-body li, body.mce-content-body td, body.mce-content-body th, body.mce-content-body blockquote { font-family: inherit; }',
        ] );
    }


    /**
     * Retrieve text displayed after the input on data entry screens.
     *
     * @param object $field
     * @return string
     */
    function get_input_suffix( $field ) {
        if ( isset( $field->options['input_suffix'] ) && is_string( $field->options['input_suffix'] ) ) {
            return trim( $field->options['input_suffix'] );
        }
        return '';
    }


    /**
     * Open a wrapper for fields that show helper text after the input.
     *
     * @param object $field
     */
    function input_suffix_open( $field ) {
        if ( '' === $this->get_input_suffix( $field ) ) {
            return;
        }
    ?>
        <span class="cfs-input-with-suffix">
    <?php
    }


    /**
     * Close a helper-text wrapper and render the configured suffix.
     *
     * @param object $field
     */
    function input_suffix_close( $field ) {
        $suffix = $this->get_input_suffix( $field );
        if ( '' === $suffix ) {
            return;
        }
    ?>
            <span class="cfs-input-suffix"><?php echo esc_html( $suffix ); ?></span>
        </span>
    <?php
    }


    /**
     * Render the field setting for helper text shown after the input.
     *
     * @param int    $key
     * @param object $field
     */
    function input_suffix_option_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'After input', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <div class="cfs_tooltip">?
                    <div class="tooltip_inner"><?php esc_html_e( 'Shown after the input on edit screens. Use this for short units or separators such as kg, %, or ~. This text is not included in saved values or front-end output.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                </div>
            </td>
            <td>
                <input type="text" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][input_suffix]" value="<?php echo esc_attr( $this->get_input_suffix( $field ) ); ?>" />
            </td>
        </tr>
    <?php
    }


    /**
     * Render a text setting with an explanatory tooltip.
     *
     * @param int    $key
     * @param object $field
     * @param string $option_name
     * @param string $label
     * @param string $tooltip
     */
    function text_option_html( $key, $field, $option_name, $label, $tooltip ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label>
                    <?php echo esc_html( $label ); ?>
                    <div class="cfs_tooltip">?
                        <div class="tooltip_inner"><?php echo esc_html( $tooltip ); ?></div>
                    </div>
                </label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'text',
                        'input_name' => 'cfs[fields][' . $this->normalize_admin_key( $key ) . '][options][' . $option_name . ']',
                        'value' => $this->get_option( $field, $option_name ),
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    public static function is_required_field( $field ) {
        if ( isset( $field->type ) && in_array( $field->type, [ 'code_view', 'shortcode' ], true ) ) {
            return false;
        }

        if ( isset( $field->options['required'] ) && 0 < (int) $field->options['required'] ) {
            return true;
        }

        if ( in_array( $field->type, [ 'relationship', 'term', 'user', 'loop', 'gallery' ], true ) ) {
            return ! empty( $field->options['limit_min'] ) && 0 < (int) $field->options['limit_min'];
        }

        return false;
    }


    public static function required_badge() {
        return '<span class="cfs-required-badge">' . esc_html__( 'Required', 'atshift-fields-maintenance-for-custom-field-suite' ) . '</span>';
    }


    public static function field_label_html( $field ) {
        $label = isset( $field->label ) ? (string) $field->label : '';
        $html = '';

        if ( self::is_required_field( $field ) ) {
            $html .= self::required_badge();
        }

        if ( '' !== $label ) {
            $html .= '<span class="cfs-field-label-text">' . esc_html( $label ) . '</span>';
        }

        return $html;
    }


    public static function should_render_field_label( $field ) {
        $label = isset( $field->label ) ? (string) $field->label : '';
        return '' !== $label || self::is_required_field( $field );
    }


    public static function should_hide_input_field( $field ) {
        if ( ! is_object( $field ) || empty( $field->type ) ) {
            return false;
        }

        if ( ! isset( atshift_fields_maintenance_for_custom_field_suite()->fields[ $field->type ] ) ) {
            return false;
        }

        $field_type = atshift_fields_maintenance_for_custom_field_suite()->fields[ $field->type ];

        if ( ! method_exists( $field_type, 'should_hide_field_input' ) ) {
            return false;
        }

        return (bool) $field_type->should_hide_field_input( $field );
    }


    /**
     * Normalize field setting keys while preserving the JavaScript clone token.
     *
     * @param mixed $key
     * @return string
     */
    protected function normalize_admin_key( $key ) {
        return 'clone' === (string) $key ? 'clone' : (string) absint( $key );
    }


    /**
     * Escape a field setting key for use in name attributes.
     *
     * @param mixed $key
     * @return string
     */
    protected function admin_key_attr( $key ) {
        return esc_attr( $this->normalize_admin_key( $key ) );
    }
}

if ( ! class_exists( 'cfs_field', false ) ) {
    class_alias( 'Atshift_CFS_field', 'cfs_field' );
}
