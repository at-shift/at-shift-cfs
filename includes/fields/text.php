<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_text extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'text';
        $this->label = __( 'Text', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function options_html( $key, $field ) {
        $this->text_option_html( $key, $field, 'default_value', __( 'Default Value', 'atshift-fields-maintenance-for-custom-field-suite' ), __( 'The value specified from the beginning.', 'atshift-fields-maintenance-for-custom-field-suite' ) );
        $this->text_option_html( $key, $field, 'placeholder', __( 'Placeholder', 'atshift-fields-maintenance-for-custom-field-suite' ), __( 'An input example or other helpful hint.', 'atshift-fields-maintenance-for-custom-field-suite' ) );
        $this->input_suffix_option_html( $key, $field );
    ?>
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

}
