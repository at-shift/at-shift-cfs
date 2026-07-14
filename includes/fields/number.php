<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_number extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'number';
        $this->label = __( 'Number', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        $min = $this->get_option( $field, 'min' );
        $max = $this->get_option( $field, 'max' );
        $step = $this->get_option( $field, 'step' );
    ?>
        <?php $this->input_suffix_open( $field ); ?>
        <input type="number" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" value="<?php echo esc_attr( $field->value ); ?>"<?php echo '' !== $min ? ' min="' . esc_attr( $min ) . '"' : ''; ?><?php echo '' !== $max ? ' max="' . esc_attr( $max ) . '"' : ''; ?><?php echo '' !== $step ? ' step="' . esc_attr( $step ) . '"' : ''; ?> />
        <?php $this->input_suffix_close( $field ); ?>
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Default Value', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'text',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][default_value]',
                        'value' => $this->get_option( $field, 'default_value' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Number Settings', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <div class="cfs_tooltip">?
                    <div class="tooltip_inner"><?php esc_html_e( 'Minimum sets the lowest allowed value. Maximum sets the highest allowed value. Step sets the allowed interval between values, such as 1, 0.1, or 5.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                </div>
            </td>
            <td>
                <input type="text" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][min]" value="<?php echo esc_attr( $this->get_option( $field, 'min' ) ); ?>" placeholder="<?php esc_attr_e( 'Minimum', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" style="width:80px" />
                <input type="text" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][max]" value="<?php echo esc_attr( $this->get_option( $field, 'max' ) ); ?>" placeholder="<?php esc_attr_e( 'Maximum', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" style="width:80px" />
                <input type="text" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][step]" value="<?php echo esc_attr( $this->get_option( $field, 'step' ) ); ?>" placeholder="<?php esc_attr_e( 'Step', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" style="width:80px" />
            </td>
        </tr>
        <?php $this->input_suffix_option_html( $key, $field ); ?>
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
    <?php
    }


    function pre_save( $value, $field = null ) {
        $value = trim( $value );

        if ( ! is_numeric( $value ) ) {
            return '';
        }

        $min = $this->get_option( $field, 'min' );
        $max = $this->get_option( $field, 'max' );

        if ( '' !== $min && is_numeric( $min ) && $value < $min ) {
            return '';
        }

        if ( '' !== $max && is_numeric( $max ) && $max < $value ) {
            return '';
        }

        $step = $this->get_option( $field, 'step' );

        if ( '' !== $step && is_numeric( $step ) && 0 < (float) $step ) {
            $base = ( '' !== $min && is_numeric( $min ) ) ? (float) $min : 0.0;
            $remainder = fmod( abs( (float) $value - $base ), (float) $step );

            if ( 0.000001 < $remainder && 0.000001 < abs( $remainder - (float) $step ) ) {
                return '';
            }
        }

        return $value;
    }

}
