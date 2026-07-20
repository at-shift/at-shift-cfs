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
        $allow_trash = $this->option_enabled( $field, 'allow_trash', true );
        $visibility = $this->get_post_visibility( $post );
        $date_value = $this->format_local_datetime_value( $post->post_date );
        $status_preview = $this->get_status_preview_label( $post, $visibility, $date_value, $allow_visibility );
        $status_row_class = '' === $status_preview ? ' is-empty' : '';
        $status_change_value = '';
        $publish_status_option_label = $this->get_publish_status_option_label( $post );
        $preview_link = $this->get_preview_link( $post );
        $preview_label = $this->get_preview_button_label( $post );
        $trash_link = $allow_trash && $this->show_trash_link( $post, $field ) ? get_delete_post_link( $post_id ) : '';
        $show_top_actions = $this->show_save_draft_button( $post ) || '' !== $preview_link;
        $final_actions_class = 'cfs-post-publish-final-actions' . ( '' !== $trash_link ? ' has-trash' : '' );

        if ( ! $can_edit ) {
            echo '<p class="notes">' . esc_html__( 'You do not have permission to change save / publish settings.', 'atshift-fields-maintenance-for-custom-field-suite' ) . '</p>';
            return;
        }
    ?>
        <div class="cfs-post-publish-control"
            data-status-public="<?php echo esc_attr__( 'Public', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-status-scheduled="<?php echo esc_attr__( 'Scheduled', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-status-draft="<?php echo esc_attr__( 'Draft', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-status-pending="<?php echo esc_attr__( 'Pending Review', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-status-private="<?php echo esc_attr__( 'Private', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-status-autosaving="<?php echo esc_attr__( 'Autosaving', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-button-update="<?php echo esc_attr__( 'Update', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-button-publish="<?php echo esc_attr__( 'Publish', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-button-schedule="<?php echo esc_attr__( 'Schedule', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-button-submit-review="<?php echo esc_attr__( 'Submit for Review', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>"
            data-allow-status="<?php echo $allow_status ? '1' : '0'; ?>"
            data-can-publish="<?php echo $this->current_user_can_publish( $post ) ? '1' : '0'; ?>"
            data-current-status="<?php echo esc_attr( $post->post_status ); ?>">
            <?php if ( $show_top_actions ) : ?>
            <div class="cfs-post-publish-top-actions">
                <span class="cfs-post-publish-draft-action">
                    <?php if ( $this->show_save_draft_button( $post ) ) : ?>
                    <button type="button" class="button post_publish_save_draft"><?php esc_html_e( 'Save Draft', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></button>
                    <?php endif; ?>
                </span>
                <span class="cfs-post-publish-preview-action">
                    <?php if ( '' !== $preview_link ) : ?>
                    <a class="button post_publish_preview" href="<?php echo esc_url( $preview_link ); ?>" target="wp-preview-<?php echo absint( $post_id ); ?>" rel="noopener"><?php echo esc_html( $preview_label ); ?></a>
                    <?php endif; ?>
                </span>
            </div>
            <?php endif; ?>

            <div class="cfs-post-publish-row cfs-post-publish-status<?php echo esc_attr( $status_row_class ); ?>">
                <label><?php esc_html_e( 'Status', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <span class="post_publish_status_preview"><?php echo esc_html( $status_preview ); ?></span>
            </div>
            <?php if ( $allow_status ) : ?>
            <div class="cfs-post-publish-row cfs-post-publish-status-change">
                <label for="cfs-post-publish-status-change-<?php echo absint( $field->id ); ?>"><?php esc_html_e( 'Change Status', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <select id="cfs-post-publish-status-change-<?php echo absint( $field->id ); ?>" name="<?php echo esc_attr( $field->input_name ); ?>[status]" class="post_publish_status">
                    <option value=""<?php selected( $status_change_value, '' ); ?>><?php esc_html_e( 'Do not change', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></option>
                    <?php if ( 'draft' !== $post->post_status ) : ?>
                    <option value="draft"<?php selected( $status_change_value, 'draft' ); ?>><?php esc_html_e( 'Change to Draft', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></option>
                    <?php endif; ?>
                    <?php if ( 'pending' !== $post->post_status ) : ?>
                    <option value="pending"<?php selected( $status_change_value, 'pending' ); ?>><?php esc_html_e( 'Change to Pending Review', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></option>
                    <?php endif; ?>
                    <?php if ( $this->current_user_can_publish( $post ) ) : ?>
                    <option value="publish"<?php selected( $status_change_value, 'publish' ); ?>><?php echo esc_html( $publish_status_option_label ); ?></option>
                    <?php endif; ?>
                </select>
            </div>
            <?php endif; ?>
            <?php if ( $allow_visibility ) : ?>
            <div class="cfs-post-publish-row cfs-post-publish-visibility">
                <label for="cfs-post-publish-visibility-<?php echo absint( $field->id ); ?>"><?php esc_html_e( 'Visibility', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <span class="cfs-post-publish-visibility-control">
                    <select id="cfs-post-publish-visibility-<?php echo absint( $field->id ); ?>" name="<?php echo esc_attr( $field->input_name ); ?>[visibility]" class="post_publish_visibility">
                        <option value="public"<?php selected( $visibility, 'public' ); ?>><?php esc_html_e( 'Public', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></option>
                        <option value="password"<?php selected( $visibility, 'password' ); ?>><?php esc_html_e( 'Password protected', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></option>
                        <option value="private"<?php selected( $visibility, 'private' ); ?>><?php esc_html_e( 'Private', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></option>
                    </select>
                    <input type="text" name="<?php echo esc_attr( $field->input_name ); ?>[password]" class="post_publish_password" value="<?php echo esc_attr( $post->post_password ); ?>" placeholder="<?php esc_attr_e( 'Password', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
                </span>
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

            <div class="cfs-post-publish-row cfs-post-publish-actions">
                <span class="<?php echo esc_attr( $final_actions_class ); ?>">
                    <?php if ( '' !== $trash_link ) : ?>
                    <a class="submitdelete deletion cfs-post-publish-trash" href="<?php echo esc_url( $trash_link ); ?>"><?php esc_html_e( 'Move to Trash', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></a>
                    <?php endif; ?>
                    <button type="button" class="button button-primary post_publish_submit"><?php echo esc_html( $this->get_submit_button_label( $post ) ); ?></button>
                </span>
            </div>
        </div>
    <?php
    }


    function input_head( $field = null ) {
    ?>
        <?php wp_add_inline_script( 'atshift-cfs-validation', atshift_cfs_capture_output( function() { ?>
        (function($) {
            function isFutureDateTime(dateValue) {
                var parts;
                var target;

                if (!dateValue || !/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(dateValue)) {
                    return false;
                }

                parts = dateValue.split(/[-T:]/).map(function(value) {
                    return parseInt(value, 10);
                });
                target = new Date(parts[0], parts[1] - 1, parts[2], parts[3], parts[4], 0);

                return target.getTime() > Date.now();
            }

            function getEffectiveStatus($control, submitIntent) {
                var visibility = $control.find('.post_publish_visibility').val();
                var dateValue = $control.find('.post_publish_date').val();
                var allowStatus = '1' === String($control.data('allowStatus'));
                var canPublish = '1' === String($control.data('canPublish'));
                var currentStatus = String($control.data('currentStatus') || '');
                var selectedStatus = allowStatus ? $control.find('.post_publish_status').val() : '';

                if ('private' === visibility || (!visibility && 'private' === currentStatus)) {
                    return 'private';
                }

                if (selectedStatus) {
                    if ('publish' === selectedStatus && isFutureDateTime(dateValue)) {
                        return 'future';
                    }

                    return selectedStatus;
                }

                if (!canPublish) {
                    return 'pending';
                }

                if (allowStatus && !selectedStatus) {
                    if ('future' === currentStatus) {
                        return isFutureDateTime(dateValue) ? 'future' : 'publish';
                    }

                    if ('draft' === currentStatus || 'pending' === currentStatus) {
                        return currentStatus;
                    }
                }

                if (submitIntent && isFutureDateTime(dateValue)) {
                    return 'future';
                }

                if (submitIntent) {
                    return 'publish';
                }

                if ('future' === currentStatus) {
                    return isFutureDateTime(dateValue) ? 'future' : 'publish';
                }

                if ('draft' === currentStatus || 'pending' === currentStatus) {
                    return currentStatus;
                }

                return isFutureDateTime(dateValue) ? 'future' : 'publish';
            }

            function getStatusLabel($control, name) {
                return $control.attr('data-status-' + name) || '';
            }

            function getButtonLabel($control, name) {
                return $control.attr('data-button-' + name) || '';
            }

            function setStatusPreview($control, label) {
                label = String(label || '').trim();
                $control.find('.post_publish_status_preview').text(label);
                $control.find('.cfs-post-publish-status').toggleClass('is-empty', '' === label);
            }

            function getDisplayStatus($control) {
                var currentStatus = String($control.data('currentStatus') || '');

                if ('future' === currentStatus) {
                    return 'future';
                }

                if ('draft' === currentStatus || 'pending' === currentStatus || 'private' === currentStatus || 'publish' === currentStatus) {
                    return currentStatus;
                }

                return '';
            }

            function refreshStatusPreview($control) {
                var currentStatus = String($control.data('currentStatus') || '');
                var status = getDisplayStatus($control);
                var label = getStatusLabel($control, 'public');

                if ('auto-draft' === currentStatus) {
                    setStatusPreview($control, '');
                    return;
                }

                if ('future' === status) {
                    label = getStatusLabel($control, 'scheduled');
                }
                else if ('draft' === status) {
                    label = getStatusLabel($control, 'draft');
                }
                else if ('pending' === status) {
                    label = getStatusLabel($control, 'pending');
                }
                else if ('private' === status) {
                    label = getStatusLabel($control, 'private');
                }

                setStatusPreview($control, label || $control.find('.post_publish_status_preview').text());
            }

            function refreshAutosaveStatus() {
                var isAutosaving = false;

                if (
                    window.wp &&
                    wp.data &&
                    'function' === typeof wp.data.select &&
                    wp.data.select('core/editor') &&
                    'function' === typeof wp.data.select('core/editor').isAutosavingPost
                ) {
                    isAutosaving = !!wp.data.select('core/editor').isAutosavingPost();
                }

                $('.cfs-post-publish-control').each(function() {
                    var $control = $(this);

                    if (isAutosaving) {
                        setStatusPreview($control, getStatusLabel($control, 'autosaving'));
                    }
                    else {
                        refreshStatusPreview($control);
                    }
                });
            }

            function refreshSubmitButtonLabels($control) {
                var status = getEffectiveStatus($control, true);
                var allowStatus = '1' === String($control.data('allowStatus'));
                var currentStatus = String($control.data('currentStatus') || '');
                var canPublish = '1' === String($control.data('canPublish'));
                var selectedStatus = allowStatus ? $control.find('.post_publish_status').val() : '';
                var label;

                if ('draft' === selectedStatus || 'pending' === selectedStatus) {
                    label = getButtonLabel($control, 'update');
                }
                else if (allowStatus && !selectedStatus && ('draft' === status || 'pending' === status)) {
                    label = getButtonLabel($control, 'update');
                }
                else if ('draft' === status || 'pending' === status) {
                    label = getButtonLabel($control, 'update');
                }
                else if ('publish' === currentStatus || 'future' === currentStatus || 'private' === currentStatus) {
                    label = getButtonLabel($control, 'update');
                }
                else if (!canPublish) {
                    label = getButtonLabel($control, 'submit-review');
                }
                else if ('future' === status) {
                    label = getButtonLabel($control, 'schedule');
                }
                else {
                    label = getButtonLabel($control, 'publish');
                }

                if (label) {
                    $control.find('.post_publish_submit').text(label);
                }
            }

            function syncClassicPostFields($control, submitIntent) {
                var status = submitIntent ? getEffectiveStatus($control, true) : '';
                var visibility = $control.find('.post_publish_visibility').val();
                var password = $control.find('.post_publish_password').val() || '';
                var dateValue = $control.find('.post_publish_date').val();
                var editorPost = {};

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

            function setPublishIntent(value) {
                var $forms = $('form#post');
                var $field = $('input[name="atshift_cfs_publish_intent"]');

                if (!$forms.length) {
                    $forms = $('form').first();
                }

                if (!$field.length) {
                    $field = $('<input type="hidden" name="atshift_cfs_publish_intent" />').appendTo($forms);
                }

                $field.val(value);
            }

            function validateBeforeSave(isDraft) {
                var passthru;

                if (window.CFS) {
                    CFS.is_draft = !!isDraft;
                }

                if (isDraft || !window.CFS || 'function' !== typeof CFS.validate_all_fields) {
                    return true;
                }

                passthru = CFS.validate_all_fields();
                if (!passthru) {
                    $('#publish').removeClass('button-primary-disabled');
                    $('#save-post').removeClass('button-disabled');
                    $('#publishing-action .spinner, #major-publishing-actions .spinner, #submitdiv .spinner').hide();
                    return false;
                }

                return true;
            }

            function submitPost($control, status, isDraft) {
                var $form = $('form#post').first();

                if (window.wp && wp.data && wp.data.dispatch) {
                    var editor = wp.data.dispatch('core/editor');
                    if (editor && 'function' === typeof editor.editPost) {
                        editor.editPost({ status: status });
                    }
                    if (editor && 'function' === typeof editor.savePost) {
                        editor.savePost();
                        return true;
                    }
                }

                if ($form.length && $form[0]) {
                    if ('function' === typeof $form[0].requestSubmit) {
                        $form[0].requestSubmit();
                    }
                    else {
                        $form[0].submit();
                    }
                    return true;
                }

                $control.closest('form').trigger('submit');
                return true;
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

            $(document).on('change input blur', '.cfs-post-publish-control :input', function() {
                var $control = $(this).closest('.cfs-post-publish-control');
                refreshPassword($control);
                refreshStatusPreview($control);
                refreshSubmitButtonLabels($control);
                syncClassicPostFields($control, false);
            });

            $(document).on('click', '.cfs-post-publish-control .post_publish_now', function(e) {
                e.preventDefault();

                var $control = $(this).closest('.cfs-post-publish-control');
                $control.find('.post_publish_date').val(formatCurrentLocalDateTime()).trigger('change');
            });

            $(document).on('click', '.cfs-post-publish-control .post_publish_save_draft', function(e) {
                e.preventDefault();

                var $control = $(this).closest('.cfs-post-publish-control');

                setPublishIntent('draft');
                $control.find('.post_publish_status').val('draft');
                refreshPassword($control);
                refreshStatusPreview($control);
                refreshSubmitButtonLabels($control);
                syncClassicPostFields($control, true);
                $('input[name="post_status"], #hidden_post_status').val('draft');
                $('#post_status').val('draft');

                if (!validateBeforeSave(true)) {
                    return;
                }

                submitPost($control, 'draft', true);
            });

            $(document).on('click', '.cfs-post-publish-control .post_publish_submit', function(e) {
                e.preventDefault();

                var $control = $(this).closest('.cfs-post-publish-control');
                var allowStatus = '1' === String($control.data('allowStatus'));
                var selectedStatus = allowStatus ? $control.find('.post_publish_status').val() : '';
                var status = getEffectiveStatus($control, true);
                var isDraft = 'draft' === status;

                setPublishIntent(allowStatus ? (selectedStatus ? selectedStatus : 'nochange') : 'publish');
                refreshPassword($control);
                syncClassicPostFields($control, true);

                if (!validateBeforeSave(isDraft)) {
                    return;
                }

                submitPost($control, status, isDraft);
            });

            $(function() {
                $('.cfs-post-publish-control').each(function() {
                    refreshPassword($(this));
                    refreshStatusPreview($(this));
                    refreshSubmitButtonLabels($(this));
                    syncClassicPostFields($(this), false);
                });

                if (window.wp && wp.data && 'function' === typeof wp.data.subscribe) {
                    wp.data.subscribe(refreshAutosaveStatus);
                }
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

        if ( $this->is_autosave_request( $post_id ) ) {
            return [];
        }

        $value = is_array( $value ) ? $value : [];
        $post_data = [ 'ID' => $post_id ];
        $can_publish = $this->current_user_can_publish( $post );
        $allow_status = $this->option_enabled( $field, 'allow_status', true );
        $allow_visibility = $this->option_enabled( $field, 'allow_visibility', true );
        $allow_date = $this->option_enabled( $field, 'allow_date', true );
        $visibility = $allow_visibility && isset( $value['visibility'] ) ? sanitize_key( $value['visibility'] ) : '';

        $post_data['post_status'] = $this->get_submission_status( $post, $visibility, $can_publish, $allow_status, $allow_visibility, $value );

        if ( $allow_visibility && '' !== $visibility ) {
            if ( 'private' === $visibility && $can_publish ) {
                $post_data['post_password'] = '';
            }
            elseif ( 'password' === $visibility ) {
                $post_data['post_password'] = isset( $value['password'] ) && is_scalar( $value['password'] ) ? sanitize_text_field( (string) $value['password'] ) : '';
            }
            elseif ( 'public' === $visibility ) {
                $post_data['post_password'] = '';
            }
        }

        if ( $allow_date && isset( $value['date'] ) ) {
            $date = $this->sanitize_local_datetime( $value['date'] );

            if ( '' !== $date ) {
                $post_data['edit_date'] = true;
                $post_data['post_date'] = $date;
                $post_data['post_date_gmt'] = get_gmt_from_date( $date );

                if ( 'publish' === $post_data['post_status'] && $this->is_future_local_datetime( $value['date'] ) ) {
                    $post_data['post_status'] = 'future';
                }
                elseif ( 'future' === $post_data['post_status'] && ! $this->is_future_local_datetime( $value['date'] ) ) {
                    $post_data['post_status'] = 'publish';
                }
            }
        }

        if ( 1 < count( $post_data ) ) {
            wp_update_post( $post_data );
        }

        return [];
    }


    function options_html( $key, $field ) {
        $role_choices = $this->get_role_choices();
        $trash_allowed_roles = $this->get_allowed_roles( $field, 'trash_allowed_roles' );
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
                <br />
                <?php $this->render_option_checkbox( $key, $field, 'allow_trash', __( 'Allow moving to trash', 'atshift-fields-maintenance-for-custom-field-suite' ) ); ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label>
                    <?php esc_html_e( 'Allowed Trash Roles', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>
                    <div class="cfs_tooltip">?
                        <div class="tooltip_inner"><?php esc_html_e( 'Select the roles that can move posts to trash from this field. Leave blank to allow any user role that can delete the post.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                    </div>
                </label>
            </td>
            <td>
                <input type="hidden" name="cfs[fields][<?php echo $this->admin_key_attr( $key ); ?>][options][trash_allowed_roles][]" value="" />
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type' => 'select',
                        'input_class' => 'select2 cfs-post-native-role-select',
                        'input_name' => 'cfs[fields][' . $this->normalize_admin_key( $key ) . '][options][trash_allowed_roles]',
                        'options' => [
                            'multiple' => '1',
                            'choices' => $role_choices,
                            'placeholder' => __( 'Leave blank to allow any role that can delete this post', 'atshift-fields-maintenance-for-custom-field-suite' ),
                        ],
                        'value' => $trash_allowed_roles,
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    protected function render_option_checkbox( $key, $field, $option_name, $message ) {
        atshift_fields_maintenance_for_custom_field_suite()->create_field( [
            'type' => 'true_false',
            'input_name' => 'cfs[fields]['  . $this->normalize_admin_key( $key ) . '][options][' . $option_name . ']',
            'input_class' => 'true_false',
            'value' => $this->get_option( $field, $option_name, 1 ),
            'options' => [ 'message' => $message ],
        ] );
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


    protected function current_user_can_trash( $post, $field = null ) {
        if ( ! ( $post instanceof WP_Post ) || ! current_user_can( 'delete_post', $post->ID ) ) {
            return false;
        }

        $allowed_roles = $this->get_allowed_roles( $field, 'trash_allowed_roles' );

        if ( empty( $allowed_roles ) ) {
            return true;
        }

        $user = wp_get_current_user();
        return $user instanceof WP_User && ! empty( array_intersect( $allowed_roles, (array) $user->roles ) );
    }


    protected function show_trash_link( $post, $field = null ) {
        if ( ! ( $post instanceof WP_Post ) || in_array( $post->post_status, [ 'auto-draft', 'trash' ], true ) ) {
            return false;
        }

        return $this->current_user_can_trash( $post, $field ) && '' !== (string) get_delete_post_link( $post->ID );
    }


    protected function get_preview_link( $post ) {
        if ( ! ( $post instanceof WP_Post ) || 'auto-draft' === $post->post_status || ! current_user_can( 'edit_post', $post->ID ) ) {
            return '';
        }

        if ( ! in_array( $post->post_status, [ 'draft', 'pending', 'publish', 'future', 'private' ], true ) ) {
            return '';
        }

        $preview_args = [
            'preview_id' => $post->ID,
            'preview_nonce' => wp_create_nonce( 'post_preview_' . $post->ID ),
        ];
        $preview_link = get_preview_post_link( $post, $preview_args );

        if ( empty( $preview_link ) ) {
            $permalink = get_permalink( $post );
            $preview_link = empty( $permalink ) ? '' : add_query_arg(
                array_merge( $preview_args, [ 'preview' => 'true' ] ),
                $permalink
            );
        }

        return is_string( $preview_link ) ? $preview_link : '';
    }


    protected function get_preview_button_label( $post ) {
        if ( $post instanceof WP_Post && in_array( $post->post_status, [ 'publish', 'future', 'private' ], true ) ) {
            return __( 'Preview Changes', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        return __( 'Preview', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    protected function get_publish_status_option_label( $post ) {
        if ( ! ( $post instanceof WP_Post ) || 'auto-draft' === $post->post_status ) {
            return __( 'Publish or Schedule', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        if ( 'publish' === $post->post_status ) {
            return __( 'Change to Scheduled', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        return __( 'Change to Publish or Schedule', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    protected function get_allowed_roles( $field = null, $option_name = 'trash_allowed_roles' ) {
        $roles = $this->get_option( $field, $option_name, [] );
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


    protected function get_submission_status( $post, $visibility, $can_publish, $allow_status, $allow_visibility, $value = [] ) {
        $explicit_status = isset( $value['status'] ) ? sanitize_key( $value['status'] ) : '';
        $requested_status = $this->get_requested_post_status();

        if ( $allow_status && '' !== $requested_status && '' !== $explicit_status && $this->can_set_status( $explicit_status, $can_publish ) ) {
            return $this->normalize_status_for_save( $explicit_status );
        }

        if ( '' !== $requested_status && $this->can_set_status( $requested_status, $can_publish ) ) {
            return $this->normalize_status_for_save( $requested_status );
        }

        if ( 'private' === $visibility && $can_publish ) {
            return 'private';
        }

        if ( $allow_visibility && in_array( $visibility, [ 'public', 'password' ], true ) && 'private' === $post->post_status && $can_publish ) {
            return 'publish';
        }

        if ( ! $allow_visibility && 'private' === $post->post_status ) {
            return 'private';
        }

        if ( 'auto-draft' === $post->post_status ) {
            return $can_publish ? 'publish' : 'pending';
        }

        if ( 'future' === $post->post_status ) {
            return 'future';
        }

        if ( in_array( $post->post_status, [ 'draft', 'pending', 'publish', 'future', 'private' ], true ) ) {
            return $post->post_status;
        }

        return $can_publish ? 'publish' : 'pending';
    }


    protected function get_status_preview_label( $post, $visibility, $date_value, $allow_visibility ) {
        if ( 'auto-draft' === $post->post_status ) {
            return '';
        }

        $status = $this->get_submission_status( $post, $visibility, $this->current_user_can_publish( $post ), true, $allow_visibility );

        if ( 'draft' === $status ) {
            return __( 'Draft', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        if ( 'publish' === $status && $this->is_future_local_datetime( $date_value ) ) {
            return __( 'Scheduled', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        if ( 'future' === $status ) {
            return __( 'Scheduled', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        if ( 'private' === $status ) {
            return __( 'Private', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        if ( 'pending' === $status ) {
            return __( 'Pending Review', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        return __( 'Public', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    protected function get_requested_post_status() {
        if ( isset( $_POST['atshift_cfs_publish_intent'] ) && is_scalar( $_POST['atshift_cfs_publish_intent'] ) ) {
            $intent = sanitize_key( wp_unslash( $_POST['atshift_cfs_publish_intent'] ) );

            if ( 'draft' === $intent ) {
                return 'draft';
            }

            if ( 'pending' === $intent ) {
                return 'pending';
            }

            if ( 'publish' === $intent ) {
                return 'publish';
            }

            return '';
        }

        if ( isset( $_POST['save'] ) ) {
            return 'draft';
        }

        if ( isset( $_POST['publish'] ) ) {
            return 'publish';
        }

        foreach ( [ 'post_status', 'hidden_post_status' ] as $key ) {
            if ( isset( $_POST[ $key ] ) && is_scalar( $_POST[ $key ] ) ) {
                return sanitize_key( wp_unslash( $_POST[ $key ] ) );
            }
        }

        return '';
    }


    protected function is_autosave_request( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return true;
        }

        return 0 < $post_id && false !== wp_is_post_autosave( $post_id );
    }


    protected function can_set_status( $status, $can_publish ) {
        if ( in_array( $status, [ 'publish', 'future', 'private' ], true ) ) {
            return $can_publish;
        }

        return in_array( $status, [ 'draft', 'pending' ], true );
    }


    protected function normalize_status_for_save( $status ) {
        return $status;
    }


    protected function get_submit_button_label( $post ) {
        if ( in_array( $post->post_status, [ 'publish', 'future', 'private' ], true ) ) {
            return __( 'Update', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        if ( ! $this->current_user_can_publish( $post ) ) {
            return __( 'Submit for Review', 'atshift-fields-maintenance-for-custom-field-suite' );
        }

        return __( 'Publish', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    protected function show_save_draft_button( $post ) {
        return ! in_array( $post->post_status, [ 'publish', 'future', 'private' ], true );
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


    protected function is_future_local_datetime( $value ) {
        $date = $this->sanitize_local_datetime( $value );

        if ( '' === $date ) {
            return false;
        }

        $timestamp = strtotime( $date );
        return false !== $timestamp && current_time( 'timestamp' ) < $timestamp;
    }


    function pre_save_field( $field ) {
        $field['options'] = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : [];
        $field['options']['allow_status'] = isset( $field['options']['allow_status'] ) ? (int) $field['options']['allow_status'] : 1;
        $field['options']['allow_visibility'] = isset( $field['options']['allow_visibility'] ) ? (int) $field['options']['allow_visibility'] : 1;
        $field['options']['allow_date'] = isset( $field['options']['allow_date'] ) ? (int) $field['options']['allow_date'] : 1;
        $field['options']['allow_trash'] = isset( $field['options']['allow_trash'] ) ? (int) $field['options']['allow_trash'] : 1;
        $field['options']['trash_allowed_roles'] = isset( $field['options']['trash_allowed_roles'] ) ? array_values( array_filter( array_map( 'sanitize_key', (array) $field['options']['trash_allowed_roles'] ) ) ) : [];

        return $field;
    }
}
