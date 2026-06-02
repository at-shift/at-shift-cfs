<?php

class cfs_time extends cfs_field
{

    function __construct() {
        $this->name = 'time';
        $this->label = __( 'Time', 'cfs' );
    }


    function html( $field ) {
        $value = $this->normalize_time( $field->value );
        $hour = '';
        $minute = '';

        if ( '' !== $value ) {
            list( $hour, $minute ) = explode( ':', $value );
        }

        $minute_interval = $this->get_minute_interval( $this->get_option( $field, 'minute_interval', 1 ) );
    ?>
        <select name="<?php echo esc_attr( $field->input_name ); ?>[hour]" class="<?php echo esc_attr( $field->input_class ); ?> cfs-time-hour">
            <option value=""></option>
            <?php for ( $i = 0; $i < 24; $i++ ) : ?>
            <?php $option = sprintf( '%02d', $i ); ?>
            <option value="<?php echo esc_attr( $option ); ?>"<?php selected( $hour, $option ); ?>><?php echo esc_html( $option ); ?></option>
            <?php endfor; ?>
        </select>
        :
        <select name="<?php echo esc_attr( $field->input_name ); ?>[minute]" class="<?php echo esc_attr( $field->input_class ); ?> cfs-time-minute">
            <option value=""></option>
            <?php for ( $i = 0; $i < 60; $i += $minute_interval ) : ?>
            <?php $option = sprintf( '%02d', $i ); ?>
            <option value="<?php echo esc_attr( $option ); ?>"<?php selected( $minute, $option ); ?>><?php echo esc_html( $option ); ?></option>
            <?php endfor; ?>
        </select>
    <?php
    }


    function options_html( $key, $field ) {
        $minute_interval = $this->get_minute_interval( $this->get_option( $field, 'minute_interval', 1 ) );
        $default_value = $this->normalize_time( $this->get_option( $field, 'default_value' ) );
        $default_hour = '';
        $default_minute = '';

        if ( '' !== $default_value ) {
            list( $default_hour, $default_minute ) = explode( ':', $default_value );
        }
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php _e( 'Minute Interval', 'cfs' ); ?></label>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'select',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][minute_interval]',
                        'input_class' => 'cfs-time-minute-interval',
                        'options' => [
                            'choices' => [
                                '1' => __( '1 minute', 'cfs' ),
                                '5' => __( '5 minutes', 'cfs' ),
                                '10' => __( '10 minutes', 'cfs' ),
                                '15' => __( '15 minutes', 'cfs' ),
                                '30' => __( '30 minutes', 'cfs' ),
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'minute_interval', '1' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php _e( 'Default Value', 'cfs' ); ?></label>
            </td>
            <td>
                <select name="cfs[fields][<?php echo absint( $key ); ?>][options][default_hour]" style="width:70px">
                    <option value=""></option>
                    <?php for ( $i = 0; $i < 24; $i++ ) : ?>
                    <?php $option = sprintf( '%02d', $i ); ?>
                    <option value="<?php echo esc_attr( $option ); ?>"<?php selected( $default_hour, $option ); ?>><?php echo esc_html( $option ); ?></option>
                    <?php endfor; ?>
                </select>
                :
                <select name="cfs[fields][<?php echo absint( $key ); ?>][options][default_minute]" class="cfs-time-default-minute" style="width:70px">
                    <option value=""></option>
                    <?php for ( $i = 0; $i < 60; $i += $minute_interval ) : ?>
                    <?php $option = sprintf( '%02d', $i ); ?>
                    <option value="<?php echo esc_attr( $option ); ?>"<?php selected( $default_minute, $option ); ?>><?php echo esc_html( $option ); ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php _e( 'Validation', 'cfs' ); ?></label>
            </td>
            <td>
                <?php
                    CFS()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields][' . absint( $key ) . '][options][required]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'required' ),
                        'options' => [ 'message' => __( 'This is a required field', 'cfs' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    function pre_save( $value, $field = null ) {
        if ( is_array( $value ) ) {
            $hour = isset( $value['hour'] ) ? $value['hour'] : '';
            $minute = isset( $value['minute'] ) ? $value['minute'] : '';
            $value = ( '' === $hour || '' === $minute ) ? '' : $hour . ':' . $minute;
        }

        return $this->normalize_time( $value );
    }


    function pre_save_field( $field ) {
        $default_hour = isset( $field['options']['default_hour'] ) ? $field['options']['default_hour'] : '';
        $default_minute = isset( $field['options']['default_minute'] ) ? $field['options']['default_minute'] : '';
        $minute_interval = $this->get_minute_interval( isset( $field['options']['minute_interval'] ) ? $field['options']['minute_interval'] : 1 );

        if ( '' !== $default_minute && 0 !== ( (int) $default_minute % $minute_interval ) ) {
            $default_minute = '';
        }

        $field['options']['default_value'] = ( '' === $default_hour || '' === $default_minute ) ? '' : $this->normalize_time( $default_hour . ':' . $default_minute );

        unset( $field['options']['default_hour'], $field['options']['default_minute'] );

        return $field;
    }


    function prepare_value( $value, $field = null ) {
        $value = is_array( $value ) && isset( $value[0] ) ? $value[0] : '';

        return $this->normalize_time( $value );
    }


    function format_value_for_input( $value, $field = null ) {
        return $this->normalize_time( $value );
    }


    private function normalize_time( $value ) {
        $value = trim( (string) $value );

        if ( preg_match( '/^([01]?\d|2[0-3]):([0-5]\d)$/', $value, $matches ) ) {
            return sprintf( '%02d:%02d', $matches[1], $matches[2] );
        }

        return '';
    }


    private function get_minute_interval( $value ) {
        $value = (int) $value;

        return in_array( $value, [ 1, 5, 10, 15, 30 ], true ) ? $value : 1;
    }
}
