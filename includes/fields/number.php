<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_number extends cfs_field
{

    function __construct() {
        $this->name = 'number';
        $this->label = __( 'Number', 'at-shift-cfs' );
    }


    function html( $field ) {
        $min = $this->get_option( $field, 'min' );
        $max = $this->get_option( $field, 'max' );
        $step = $this->get_option( $field, 'step' );
    ?>
        <input type="number" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" value="<?php echo esc_attr( $field->value ); ?>"<?php echo '' !== $min ? ' min="' . esc_attr( $min ) . '"' : ''; ?><?php echo '' !== $max ? ' max="' . esc_attr( $max ) . '"' : ''; ?><?php echo '' !== $step ? ' step="' . esc_attr( $step ) . '"' : ''; ?> />
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Default Value', 'at-shift-cfs' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'text',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][default_value]',
                        'value' => $this->get_option( $field, 'default_value' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Number Settings', 'at-shift-cfs' ); ?></label>
            </td>
            <td>
                <input type="text" name="cfs[fields][<?php echo absint( $key ); ?>][options][min]" value="<?php echo esc_attr( $this->get_option( $field, 'min' ) ); ?>" placeholder="min" style="width:80px" />
                <input type="text" name="cfs[fields][<?php echo absint( $key ); ?>][options][max]" value="<?php echo esc_attr( $this->get_option( $field, 'max' ) ); ?>" placeholder="max" style="width:80px" />
                <input type="text" name="cfs[fields][<?php echo absint( $key ); ?>][options][step]" value="<?php echo esc_attr( $this->get_option( $field, 'step' ) ); ?>" placeholder="step" style="width:80px" />
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Validation', 'at-shift-cfs' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][required]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'required' ),
                        'options' => [ 'message' => __( 'This is a required field', 'at-shift-cfs' ) ],
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
