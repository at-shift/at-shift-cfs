<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_url extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'url';
        $this->label = __( 'URL', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
    ?>
        <input type="url" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" value="<?php echo esc_url( $field->value ); ?>" placeholder="<?php echo esc_attr( $this->get_input_placeholder( $field, 'https://' ) ); ?>" />
    <?php
    }


    function options_html( $key, $field ) {
        $this->text_option_html( $key, $field, 'placeholder', __( 'Placeholder', 'atshift-fields-maintenance-for-custom-field-suite' ), __( 'An input example or other helpful hint.', 'atshift-fields-maintenance-for-custom-field-suite' ) );
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


    function pre_save( $value, $field = null ) {
        $value = esc_url_raw( $value );
        $scheme = wp_parse_url( $value, PHP_URL_SCHEME );

        return in_array( $scheme, [ 'http', 'https', 'mailto', 'tel' ], true ) ? $value : '';
    }
}
