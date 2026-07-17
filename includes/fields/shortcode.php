<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_shortcode extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'shortcode';
        $this->label = __( 'Shortcode', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        if ( $this->should_hide_field_input( $field ) ) {
            return;
        }

        $field->value = null === $field->value ? $this->get_option( $field, 'default_value' ) : $field->value;
    ?>
        <textarea name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" rows="3" spellcheck="false"><?php echo esc_textarea( $field->value ); ?></textarea>
    <?php
    }


    function options_html( $key, $field ) {
        $role_choices = $this->get_role_choices();
        $allowed_roles = $this->get_allowed_roles( $field );
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Default Value', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'textarea',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][default_value]',
                        'value' => $this->get_option( $field, 'default_value' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label>
                    <?php esc_html_e( 'Allowed User Role Groups', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                    <div class="cfs_tooltip">?
                        <div class="tooltip_inner"><?php esc_html_e( 'Select the roles that can enter or change shortcodes. If left blank, only administrators can edit this field.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                    </div>
                </label>
            </td>
            <td>
                <input type="hidden" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][allowed_roles][]" value="" />
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_class' => 'select2 cfs-shortcode-role-select',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][allowed_roles]',
                        'options' => [
                            'multiple' => '1',
                            'choices' => $role_choices,
                            'placeholder' => __( 'Default: administrators only', 'atshift-fields-maintenance-for-custom-field-suite' ),
                        ],
                        'value' => $allowed_roles,
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    function pre_save( $value, $field = null ) {
        $value = is_array( $value ) ? reset( $value ) : $value;
        return trim( wp_kses_post( (string) $value ) );
    }


    function format_value_for_api( $value, $field = null ) {
        $value = is_scalar( $value ) ? (string) $value : '';

        if ( '' === trim( $value ) ) {
            return '';
        }

        return do_shortcode( $value );
    }


    function should_hide_field_input( $field ) {
        return ! $this->current_user_can_edit_shortcode( $field );
    }


    function should_skip_input_validation( $field ) {
        return ! $this->current_user_can_edit_shortcode( $field );
    }


    function should_preserve_existing_value_on_save( $field, $post_id ) {
        return ! $this->current_user_can_edit_shortcode( $field, $post_id );
    }


    protected function current_user_can_edit_shortcode( $field = null, $post_id = 0 ) {
        $post_id = absint( $post_id );

        if ( 1 > $post_id ) {
            global $post;
            $post_id = $post instanceof WP_Post ? absint( $post->ID ) : 0;
        }

        if ( 0 < $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
            return false;
        }

        $allowed_roles = $this->get_allowed_roles( $field );
        $user = wp_get_current_user();

        return $user instanceof WP_User && ! empty( array_intersect( $allowed_roles, (array) $user->roles ) );
    }


    protected function get_allowed_roles( $field = null ) {
        $roles = $this->get_option( $field, 'allowed_roles', [] );
        $roles = array_filter( array_map( 'sanitize_key', (array) $roles ) );

        if ( empty( $roles ) ) {
            $roles = $this->get_default_allowed_roles();
        }

        return array_values( array_unique( $roles ) );
    }


    protected function get_default_allowed_roles() {
        $roles = [ 'administrator' ];

        foreach ( wp_roles()->roles as $role_key => $role ) {
            $role_key = sanitize_key( $role_key );
            $role_name = isset( $role['name'] ) ? translate_user_role( $role['name'] ) : $role_key;

            if ( in_array( $role_key, [ 'web_admin', 'web_manager', 'website_manager' ], true ) || 'Web管理者' === $role_name ) {
                $roles[] = $role_key;
            }
        }

        return array_values( array_unique( $roles ) );
    }


    protected function get_role_choices() {
        $choices = [];

        foreach ( wp_roles()->roles as $role_key => $role ) {
            $role_label = isset( $role['name'] ) ? translate_user_role( $role['name'] ) : $role_key;
            $choices[ sanitize_key( $role_key ) ] = $role_label;
        }

        return $choices;
    }


    function pre_save_field( $field ) {
        $field['options']['allowed_roles'] = isset( $field['options']['allowed_roles'] ) ? array_values( array_filter( array_map( 'sanitize_key', (array) $field['options']['allowed_roles'] ) ) ) : [];

        if ( empty( $field['options']['allowed_roles'] ) ) {
            $field['options']['allowed_roles'] = $this->get_default_allowed_roles();
        }

        $field['options']['default_value'] = isset( $field['options']['default_value'] ) ? wp_kses_post( (string) $field['options']['default_value'] ) : '';
        unset( $field['options']['required'] );

        return $field;
    }
}
