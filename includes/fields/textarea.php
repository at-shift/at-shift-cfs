<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_textarea extends cfs_field
{

    function __construct() {
        $this->name = 'textarea';
        $this->label = __( 'Textarea', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        $field->value = null === $field->value ? '' : $field->value;
    ?>
        <textarea name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" rows="4"><?php echo esc_textarea( $field->value ); ?></textarea>
    <?php
    }


    function options_html($key, $field) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Default Value', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'textarea',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][default_value]',
                        'value' => $this->get_option( $field, 'default_value' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Formatting', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][formatting]',
                        'options' => [
                            'choices' => [
                                'none' => __( 'None', 'atshift-fields-maintenance-for-custom-field-suite' ),
                                'auto_br' => __( 'Convert newlines to <br />', 'atshift-fields-maintenance-for-custom-field-suite' )
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'formatting', 'auto_br' ),
                    ] );
                ?>
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
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][required]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'required' ),
                        'options' => [ 'message' => __( 'This is a required field', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }

    function format_value_for_api( $value, $field = null ) {
        $formatting = $this->get_option( $field, 'formatting', 'none' );
        return ( 'none' == $formatting ) ? $value : nl2br( $value );
    }
}
