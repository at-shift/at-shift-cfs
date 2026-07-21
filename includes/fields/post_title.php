<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_post_title extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'post_title';
        $this->label = __( 'Post Title (Native)', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        global $post;

        $post_title = $post instanceof WP_Post ? $this->get_display_title( $post ) : '';
        $post_id = $post instanceof WP_Post ? $post->ID : 0;
        $can_edit_title = $this->current_user_can_edit_title( $post_id, $field );

        if ( isset( $field->value ) && '' !== $field->value && 'Auto Draft' !== $field->value ) {
            $post_title = $field->value;
        }
    ?>
        <input type="text" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?> post_title_value" value="<?php echo esc_attr( $post_title ); ?>" placeholder="<?php echo esc_attr( $this->get_input_placeholder( $field ) ); ?>"<?php echo $can_edit_title ? '' : ' readonly="readonly"'; ?> />
        <?php if ( ! $can_edit_title ) : ?>
        <p class="notes"><?php esc_html_e( 'You do not have permission to change the native title.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
        <?php endif; ?>
    <?php
    }


    function options_html( $key, $field ) {
        $role_choices = $this->get_role_choices();
        $allowed_roles = $this->get_allowed_roles( $field );
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?> cfs-post-title-placeholder-option">
            <td class="label">
                <label>
                    <?php esc_html_e( 'Placeholder', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                    <div class="cfs_tooltip">?
                        <div class="tooltip_inner"><?php esc_html_e( 'An input example or other helpful hint.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                    </div>
                </label>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type'       => 'text',
                        'input_name' => 'cfs[fields][' . $this->normalize_admin_key( $key ) . '][options][placeholder]',
                        'value'      => $this->get_option( $field, 'placeholder' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label>
                    <?php esc_html_e( 'Allowed User Role Groups', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                    <div class="cfs_tooltip">?
                        <div class="tooltip_inner"><?php esc_html_e( 'Select the roles that can change the native title. Leave blank to allow any user role that can edit the post.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                    </div>
                </label>
            </td>
            <td>
                <input type="hidden" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][allowed_roles][]" value="" />
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_class' => 'select2 cfs-post-title-role-select',
                        'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][allowed_roles]',
                        'options' => [
                            'multiple' => '1',
                            'choices' => $role_choices,
                            'placeholder' => __( 'Leave blank to allow any role that can edit this post', 'atshift-fields-maintenance-for-custom-field-suite' ),
                        ],
                        'value' => $allowed_roles,
                    ] );
                ?>
            </td>
        </tr>
    <?php
        parent::options_html( $key, $field );
    }


    function input_head( $field = null ) {
    ?>
        <?php wp_add_inline_script( 'atshift-cfs-validation', atshift_cfs_capture_output( function() { ?>
        (function($) {
            function syncPostTitle(value) {
                var title = value || '';
                $('#title, input[name="post_title"], textarea[name="post_title"]').val(title).trigger('input').trigger('change');

                if (window.wp && wp.data && wp.data.dispatch) {
                    var editor = wp.data.dispatch('core/editor');
                    if (editor && 'function' === typeof editor.editPost) {
                        editor.editPost({ title: title });
                    }
                }
            }

            $(document).on('input change', '.cfs_post_title .post_title_value', function() {
                syncPostTitle($(this).val());
            });

            $(function() {
                $('.cfs_post_title .post_title_value').each(function() {
                    syncPostTitle($(this).val());
                });
            });
        })(jQuery);
        <?php } ) ); ?>
    <?php
    }


    function pre_save( $value, $field = null ) {
        $post_id = isset( $field->post_id ) ? absint( $field->post_id ) : 0;
        $title = is_array( $value ) ? reset( $value ) : $value;
        $title = sanitize_text_field( (string) $title );

        if ( 0 < $post_id && $this->current_user_can_edit_title( $post_id, $field ) ) {
            $post = get_post( $post_id );

            if ( $post instanceof WP_Post && $post->post_title !== $title ) {
                wp_update_post( [
                    'ID'         => $post_id,
                    'post_title' => $title,
                ] );
            }
        }

        return [];
    }


    protected function current_user_can_edit_title( $post_id, $field = null ) {
        $post_id = absint( $post_id );

        if ( 1 > $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            return false;
        }

        $allowed_roles = $this->get_allowed_roles( $field );

        if ( empty( $allowed_roles ) ) {
            return true;
        }

        $user = wp_get_current_user();
        return $user instanceof WP_User && ! empty( array_intersect( $allowed_roles, (array) $user->roles ) );
    }


    protected function get_display_title( $post ) {
        if ( ! ( $post instanceof WP_Post ) ) {
            return '';
        }

        if ( 'auto-draft' === $post->post_status || 'Auto Draft' === $post->post_title ) {
            return '';
        }

        return (string) $post->post_title;
    }


    protected function get_allowed_roles( $field = null ) {
        $roles = $this->get_option( $field, 'allowed_roles', [] );
        $roles = array_filter( array_map( 'sanitize_key', (array) $roles ) );
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
        return $field;
    }
}
