<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_post_publish extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'post_publish';
        $this->label = __( 'Save / Publish (Native)', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        global $post;

        if ( ! ( $post instanceof WP_Post ) ) {
            return;
        }

        $post_id = absint( $post->ID );
        $can_edit = current_user_can( 'edit_post', $post_id );
        $allow_status = $this->option_enabled( $field, 'allow_status', true );
        $allow_visibility = $this->option_enabled( $field, 'allow_visibility', true );
        $allow_date = $this->option_enabled( $field, 'allow_date', true );
        $visibility = $this->get_post_visibility( $post );
        $date_value = $this->format_local_datetime_value( $post->post_date );

        if ( ! $can_edit ) {
            echo '<p class="notes">' . esc_html__( 'You do not have permission to change save / publish settings.', 'atshift-fields-maintenance-for-custom-field-suite' ) . '</p>';
            return;
        }
    ?>
        <div class="cfs-post-publish-control">
            <?php if ( $allow_status ) : ?>
            <div class="cfs-post-publish-row cfs-post-publish-status">
                <label for="cfs-post-publish-status-<?php echo absint( $field->id ); ?>"><?php esc_html_e( 'Status', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <select id="cfs-post-publish-status-<?php echo absint( $field->id ); ?>" name="<?php echo esc_attr( $field->input_name ); ?>[status]" class="post_publish_status">
                    <?php foreach ( $this->get_status_choices( $post ) as $status => $label ) : ?>
                    <option value="<?php echo esc_attr( $status ); ?>"<?php selected( $post->post_status, $status ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <?php if ( $allow_visibility ) : ?>
            <div class="cfs-post-publish-row cfs-post-publish-visibility">
                <label for="cfs-post-publish-visibility-<?php echo absint( $field->id ); ?>"><?php esc_html_e( 'Visibility', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <select id="cfs-post-publish-visibility-<?php echo absint( $field->id ); ?>" name="<?php echo esc_attr( $field->input_name ); ?>[visibility]" class="post_publish_visibility">
                    <option value="public"<?php selected( $visibility, 'public' ); ?>><?php esc_html_e( 'Public', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></option>
                    <option value="password"<?php selected( $visibility, 'password' ); ?>><?php esc_html_e( 'Password protected', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></option>
                    <option value="private"<?php selected( $visibility, 'private' ); ?>><?php esc_html_e( 'Private', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></option>
                </select>
                <input type="text" name="<?php echo esc_attr( $field->input_name ); ?>[password]" class="post_publish_password" value="<?php echo esc_attr( $post->post_password ); ?>" placeholder="<?php esc_attr_e( 'Password', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
            </div>
            <?php endif; ?>

            <?php if ( $allow_date ) : ?>
            <div class="cfs-post-publish-row cfs-post-publish-date">
                <label for="cfs-post-publish-date-<?php echo absint( $field->id ); ?>"><?php esc_html_e( 'Publish Date', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <span class="cfs-post-publish-date-control">
                    <input id="cfs-post-publish-date-<?php echo absint( $field->id ); ?>" type="datetime-local" name="<?php echo esc_attr( $field->input_name ); ?>[date]" class="post_publish_date" value="<?php echo esc_attr( $date_value ); ?>" />
                    <button type="button" class="button post_publish_now"><?php esc_html_e( 'Set to current date and time', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></button>
                </span>
            </div>
            <?php endif; ?>
        </div>
    <?php
    }


    function input_head( $field = null ) {
    ?>
        <?php wp_add_inline_script( 'atshift-cfs-validation', atshift_cfs_capture_output( function() { ?>
        (function($) {
            function syncClassicPostFields($control) {
                var status = $control.find('.post_publish_status').val();
                var visibility = $control.find('.post_publish_visibility').val();
                var password = $control.find('.post_publish_password').val() || '';
                var dateValue = $control.find('.post_publish_date').val();
                var editorPost = {};

                if ('private' === visibility) {
                    status = 'private';
                }

                if (status) {
                    $('input[name="post_status"], #hidden_post_status').val(status);
                    $('#post_status').val(status);
                    editorPost.status = status;
                }

                if (visibility) {
                    $('input[name="visibility"][value="' + visibility + '"]').prop('checked', true);
                    $('#post_password').val('password' === visibility ? password : '');
                    editorPost.password = 'password' === visibility ? password : '';
                }

                if (dateValue && /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(dateValue)) {
                    var parts = dateValue.split(/[-T:]/);
                    $('#aa').val(parts[0]);
                    $('#mm').val(parts[1]);
                    $('#jj').val(parts[2]);
                    $('#hh').val(parts[3]);
                    $('#mn').val(parts[4]);
                    $('#ss').val('00');
                    editorPost.date = dateValue + ':00';
                }

                if (Object.keys(editorPost).length && window.wp && wp.data && wp.data.dispatch) {
                    var editor = wp.data.dispatch('core/editor');
                    if (editor && 'function' === typeof editor.editPost) {
                        editor.editPost(editorPost);
                    }
                }
            }

            function refreshPassword($control) {
                $control.find('.post_publish_password').toggle('password' === $control.find('.post_publish_visibility').val());
            }

            function formatCurrentLocalDateTime() {
                var date = new Date();
                var pad = function(value) {
                    return String(value).padStart(2, '0');
                };

                return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) + 'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
            }

            $(document).on('change input', '.cfs_post_publish :input', function() {
                var $control = $(this).closest('.cfs_post_publish');
                refreshPassword($control);
                syncClassicPostFields($control);
            });

            $(document).on('click', '.cfs_post_publish .post_publish_now', function(e) {
                e.preventDefault();

                var $control = $(this).closest('.cfs_post_publish');
                $control.find('.post_publish_date').val(formatCurrentLocalDateTime()).trigger('change');
            });

            $(function() {
                $('.cfs_post_publish').each(function() {
                    refreshPassword($(this));
                    syncClassicPostFields($(this));
                });
            });
        })(jQuery);
        <?php } ) ); ?>
    <?php
    }


    function pre_save( $value, $field = null ) {
        $post_id = isset( $field->post_id ) ? absint( $field->post_id ) : 0;

        if ( 1 > $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            return [];
        }

        $post = get_post( $post_id );

        if ( ! ( $post instanceof WP_Post ) ) {
            return [];
        }

        $value = is_array( $value ) ? $value : [];
        $post_data = [ 'ID' => $post_id ];
        $can_publish = $this->current_user_can_publish( $post );
        $allow_status = $this->option_enabled( $field, 'allow_status', true );
        $allow_visibility = $this->option_enabled( $field, 'allow_visibility', true );
        $allow_date = $this->option_enabled( $field, 'allow_date', true );

        if ( $allow_status && isset( $value['status'] ) ) {
            $status = sanitize_key( $value['status'] );
            $allowed_statuses = array_keys( $this->get_status_choices( $post ) );

            if ( in_array( $status, $allowed_statuses, true ) && $this->can_set_status( $status, $can_publish ) ) {
                $post_data['post_status'] = $status;
            }
        }

        if ( $allow_visibility && isset( $value['visibility'] ) ) {
            $visibility = sanitize_key( $value['visibility'] );

            if ( 'private' === $visibility && $can_publish ) {
                $post_data['post_status'] = 'private';
                $post_data['post_password'] = '';
            }
            elseif ( 'password' === $visibility ) {
                $post_data['post_password'] = isset( $value['password'] ) && is_scalar( $value['password'] ) ? sanitize_text_field( (string) $value['password'] ) : '';

                if ( 'private' === $post->post_status && $can_publish && empty( $post_data['post_status'] ) ) {
                    $post_data['post_status'] = 'publish';
                }
            }
            elseif ( 'public' === $visibility ) {
                $post_data['post_password'] = '';

                if ( 'private' === $post->post_status && $can_publish && empty( $post_data['post_status'] ) ) {
                    $post_data['post_status'] = 'publish';
                }
            }
        }

        if ( $allow_date && isset( $value['date'] ) ) {
            $date = $this->sanitize_local_datetime( $value['date'] );

            if ( '' !== $date ) {
                $post_data['post_date'] = $date;
                $post_data['post_date_gmt'] = get_gmt_from_date( $date );
            }
        }

        if ( 1 < count( $post_data ) ) {
            wp_update_post( $post_data );
        }

        return [];
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Save / Publish Settings', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <div class="cfs_tooltip">?
                    <div class="tooltip_inner"><?php esc_html_e( 'Choose which native WordPress save and publish controls are shown in this field.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                </div>
            </td>
            <td>
                <?php $this->render_option_checkbox( $key, $field, 'allow_status', __( 'Allow status changes', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>
                <br />
                <?php $this->render_option_checkbox( $key, $field, 'allow_visibility', __( 'Allow visibility changes', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>
                <br />
                <?php $this->render_option_checkbox( $key, $field, 'allow_date', __( 'Allow publish date changes', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>
            </td>
        </tr>
    <?php
    }


    protected function render_option_checkbox( $key, $field, $option_name, $message ) {
        atshift_fields_maintenance_for_custom_field_suite()->create_field( [
            'type' => 'true_false',
            'input_name' => 'cfs[fields][' . absint( $key ) . '][options][' . $option_name . ']',
            'input_class' => 'true_false',
            'value' => $this->get_option( $field, $option_name, 1 ),
            'options' => [ 'message' => $message ],
        ] );
    }


    protected function get_status_choices( $post ) {
        $choices = [
            'draft'   => __( 'Draft', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'pending' => __( 'Pending Review', 'atshift-fields-maintenance-for-custom-field-suite' ),
        ];

        if ( $this->current_user_can_publish( $post ) ) {
            $choices['publish'] = __( 'Published', 'atshift-fields-maintenance-for-custom-field-suite' );
            $choices['future'] = __( 'Scheduled', 'atshift-fields-maintenance-for-custom-field-suite' );
            $choices['private'] = __( 'Private', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        if ( ! isset( $choices[ $post->post_status ] ) ) {
            $status_object = get_post_status_object( $post->post_status );
            $choices[ $post->post_status ] = $status_object ? $status_object->label : $post->post_status;
        }

        return $choices;
    }


    protected function get_post_visibility( $post ) {
        if ( 'private' === $post->post_status ) {
            return 'private';
        }

        if ( '' !== (string) $post->post_password ) {
            return 'password';
        }

        return 'public';
    }


    protected function current_user_can_publish( $post ) {
        $post_type = get_post_type_object( $post->post_type );

        if ( ! $post_type || empty( $post_type->cap->publish_posts ) ) {
            return false;
        }

        return current_user_can( $post_type->cap->publish_posts );
    }


    protected function can_set_status( $status, $can_publish ) {
        if ( in_array( $status, [ 'publish', 'future', 'private' ], true ) ) {
            return $can_publish;
        }

        return in_array( $status, [ 'draft', 'pending' ], true );
    }


    protected function option_enabled( $field, $option_name, $default = true ) {
        $value = $this->get_option( $field, $option_name, $default ? 1 : 0 );
        return 0 < (int) $value;
    }


    protected function format_local_datetime_value( $date ) {
        if ( empty( $date ) || '0000-00-00 00:00:00' === $date ) {
            return date( 'Y-m-d\TH:i', current_time( 'timestamp' ) );
        }

        $timestamp = strtotime( $date );
        return false === $timestamp ? '' : date( 'Y-m-d\TH:i', $timestamp );
    }


    protected function sanitize_local_datetime( $value ) {
        if ( ! is_scalar( $value ) ) {
            return '';
        }

        $value = trim( (string) $value );

        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value ) ) {
            return '';
        }

        $date = str_replace( 'T', ' ', $value ) . ':00';
        $datetime = date_create_from_format( 'Y-m-d H:i:s', $date, wp_timezone() );

        if ( ! $datetime || $datetime->format( 'Y-m-d H:i:s' ) !== $date ) {
            return '';
        }

        return $date;
    }


    function pre_save_field( $field ) {
        $field['options'] = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : [];
        $field['options']['allow_status'] = isset( $field['options']['allow_status'] ) ? (int) $field['options']['allow_status'] : 1;
        $field['options']['allow_visibility'] = isset( $field['options']['allow_visibility'] ) ? (int) $field['options']['allow_visibility'] : 1;
        $field['options']['allow_date'] = isset( $field['options']['allow_date'] ) ? (int) $field['options']['allow_date'] : 1;

        return $field;
    }
}
