<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_embed_code extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'embed_code';
        $this->label = __( 'Embed Code', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        if ( $this->should_hide_field_input( $field ) ) {
            return;
        }

        $field->value = $this->sanitize_embed_code( $field->value, $field );
    ?>
        <textarea name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" rows="4" spellcheck="false" placeholder="<?php esc_attr_e( 'Paste iframe embed code here', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"><?php echo esc_textarea( $field->value ); ?></textarea>
    <?php
    }


    function options_html( $key, $field ) {
        $provider_choices = $this->get_provider_choices();
        $allowed_providers = $this->get_allowed_providers( $field );
        $role_choices = $this->get_role_choices();
        $allowed_roles = $this->get_allowed_roles( $field );
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label>
                    <?php esc_html_e( 'Allowed Providers', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                    <div class="cfs_tooltip">?
                        <div class="tooltip_inner"><?php esc_html_e( 'Select the iframe embed providers allowed for this field. Script-based embeds are not supported.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                    </div>
                </label>
            </td>
            <td>
                <input type="hidden" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][allowed_providers][]" value="" />
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_class' => 'select2 cfs-embed-provider-select',
                        'input_name' => 'cfs[fields][' . $this->normalize_admin_key( $key ) . '][options][allowed_providers]',
                        'options' => [
                            'multiple' => '1',
                            'choices' => $provider_choices,
                            'placeholder' => __( 'Default: all supported providers', 'atshift-fields-maintenance-for-custom-field-suite' ),
                        ],
                        'value' => $allowed_providers,
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label>
                    <?php esc_html_e( 'Allowed User Role Groups', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                    <div class="cfs_tooltip">?
                        <div class="tooltip_inner"><?php esc_html_e( 'Select the roles that can enter or change embed code. If left blank, only administrators can edit this field.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                    </div>
                </label>
            </td>
            <td>
                <input type="hidden" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][allowed_roles][]" value="" />
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_class' => 'select2 cfs-embed-role-select',
                        'input_name' => 'cfs[fields][' . $this->normalize_admin_key( $key ) . '][options][allowed_roles]',
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
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Validation', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'true_false',
                        'input_name' => 'cfs[fields][' . $this->normalize_admin_key( $key ) . '][options][required]',
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'required' ),
                        'options' => [ 'message' => __( 'This is a required field', 'atshift-fields-maintenance-for-custom-field-suite' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    function sanitize_submitted_value( $value, $field = null ) {
        return $this->sanitize_embed_code( $value, $field );
    }


    function pre_save( $value, $field = null ) {
        $value = is_array( $value ) ? reset( $value ) : $value;
        return $this->sanitize_embed_code( $value, $field );
    }


    function format_value_for_api( $value, $field = null ) {
        return $this->sanitize_embed_code( $value, $field );
    }


    function format_value_for_input( $value, $field = null ) {
        return $this->sanitize_embed_code( $value, $field );
    }


    function should_hide_field_input( $field ) {
        return ! $this->current_user_can_edit_embed_code( $field );
    }


    function should_skip_input_validation( $field ) {
        return ! $this->current_user_can_edit_embed_code( $field );
    }


    function should_preserve_existing_value_on_save( $field, $post_id ) {
        return ! $this->current_user_can_edit_embed_code( $field, $post_id );
    }


    protected function sanitize_embed_code( $value, $field = null ) {
        $value = is_scalar( $value ) ? trim( (string) $value ) : '';

        if ( '' === $value ) {
            return '';
        }

        $allowed_html = [
            'iframe' => [
                'src' => true,
                'width' => true,
                'height' => true,
                'title' => true,
                'class' => true,
                'style' => true,
                'loading' => true,
                'allow' => true,
                'allowfullscreen' => true,
                'frameborder' => true,
                'referrerpolicy' => true,
                'aria-label' => true,
            ],
        ];

        $allowed_html = atshift_cfs_apply_filters_compat(
            'cfs_embed_code_allowed_html',
            'atshift_cfs_embed_code_allowed_html',
            $allowed_html,
            $field
        );

        $sanitized = trim( wp_kses( $value, $allowed_html ) );

        if ( ! preg_match( '/<iframe\b[^>]*>\s*<\/iframe>/i', $sanitized, $matches ) ) {
            return '';
        }

        $iframe = $matches[0];
        $src = $this->extract_iframe_src( $iframe );

        if ( ! $this->is_allowed_embed_src( $src, $field ) ) {
            return '';
        }

        return $iframe;
    }


    protected function extract_iframe_src( $iframe ) {
        if ( ! is_string( $iframe ) || '' === $iframe ) {
            return '';
        }

        if ( preg_match( '/\bsrc\s*=\s*([\'"])(.*?)\1/i', $iframe, $matches ) ) {
            return html_entity_decode( $matches[2], ENT_QUOTES, get_bloginfo( 'charset' ) );
        }

        return '';
    }


    protected function is_allowed_embed_src( $src, $field = null ) {
        $src = is_string( $src ) ? esc_url_raw( $src, [ 'http', 'https' ] ) : '';

        if ( '' === $src ) {
            return false;
        }

        $parts = wp_parse_url( $src );

        if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) || ! in_array( strtolower( $parts['scheme'] ), [ 'http', 'https' ], true ) ) {
            return false;
        }

        $host = strtolower( $parts['host'] );
        $path = isset( $parts['path'] ) ? (string) $parts['path'] : '';
        $allowed_providers = $this->get_allowed_providers( $field );
        $provider_allowed = false;

        foreach ( $allowed_providers as $provider ) {
            if ( $this->provider_allows_src( $provider, $host, $path, $src ) ) {
                $provider_allowed = true;
                break;
            }
        }

        return (bool) atshift_cfs_apply_filters_compat(
            'cfs_embed_code_src_allowed',
            'atshift_cfs_embed_code_src_allowed',
            $provider_allowed,
            $src,
            $field,
            $allowed_providers
        );
    }


    protected function provider_allows_src( $provider, $host, $path, $src ) {
        switch ( $provider ) {
            case 'google_maps':
                return in_array( $host, [ 'www.google.com', 'maps.google.com', 'www.google.co.jp', 'maps.google.co.jp' ], true ) && ( 0 === strpos( $path, '/maps/embed' ) || preg_match( '#^/maps/d/(?:[^/]+/)?embed#', $path ) );

            case 'youtube':
                return in_array( $host, [ 'www.youtube.com', 'youtube.com', 'www.youtube-nocookie.com' ], true ) && 0 === strpos( $path, '/embed/' );

            case 'vimeo':
                return 'player.vimeo.com' === $host && 0 === strpos( $path, '/video/' );

            case 'openstreetmap':
                return 'www.openstreetmap.org' === $host && '/export/embed.html' === $path;

            case 'google_calendar':
                return 'calendar.google.com' === $host && 0 === strpos( $path, '/calendar/embed' );

            case 'google_forms':
                return 'docs.google.com' === $host && 0 === strpos( $path, '/forms/' );

            case 'spotify':
                return 'open.spotify.com' === $host && 0 === strpos( $path, '/embed/' );

            case 'soundcloud':
                return 'w.soundcloud.com' === $host && 0 === strpos( $path, '/player/' );
        }

        return false;
    }


    protected function current_user_can_edit_embed_code( $field = null, $post_id = 0 ) {
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


    protected function get_provider_choices() {
        return [
            'google_maps' => __( 'Google Maps', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'youtube' => __( 'YouTube', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'vimeo' => __( 'Vimeo', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'openstreetmap' => __( 'OpenStreetMap', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'google_calendar' => __( 'Google Calendar', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'google_forms' => __( 'Google Forms', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'spotify' => __( 'Spotify', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'soundcloud' => __( 'SoundCloud', 'atshift-fields-maintenance-for-custom-field-suite' ),
        ];
    }


    protected function get_allowed_providers( $field = null ) {
        $providers = $this->get_option( $field, 'allowed_providers', [] );
        $providers = array_filter( array_map( 'sanitize_key', (array) $providers ) );
        $choices = $this->get_provider_choices();
        $providers = array_values( array_intersect( $providers, array_keys( $choices ) ) );

        if ( empty( $providers ) ) {
            $providers = array_keys( $choices );
        }

        return array_values( array_unique( $providers ) );
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
        $provider_choices = $this->get_provider_choices();
        $field['options']['allowed_providers'] = isset( $field['options']['allowed_providers'] ) ? array_values( array_filter( array_map( 'sanitize_key', (array) $field['options']['allowed_providers'] ) ) ) : [];
        $field['options']['allowed_providers'] = array_values( array_intersect( $field['options']['allowed_providers'], array_keys( $provider_choices ) ) );

        if ( empty( $field['options']['allowed_providers'] ) ) {
            $field['options']['allowed_providers'] = array_keys( $provider_choices );
        }

        $field['options']['allowed_roles'] = isset( $field['options']['allowed_roles'] ) ? array_values( array_filter( array_map( 'sanitize_key', (array) $field['options']['allowed_roles'] ) ) ) : [];

        if ( empty( $field['options']['allowed_roles'] ) ) {
            $field['options']['allowed_roles'] = $this->get_default_allowed_roles();
        }

        unset( $field['options']['default_value'] );

        return $field;
    }
}
