(function($) {
    function trim(value) {
        return String(null == value ? '' : value).trim();
    }

    $(function() {
        var validationMessage = function(key, fallback) {
            return CFS.validation_messages && CFS.validation_messages[key] ? CFS.validation_messages[key] : fallback;
        };

        function resizeTextarea(textarea) {
            var minHeight = parseFloat(textarea.getAttribute('data-cfs-auto-textarea-min-height') || '0') || 0;

            textarea.style.height = 'auto';
            textarea.style.height = Math.max(textarea.scrollHeight, minHeight) + 'px';
        }

        function autoResizeTextareas($context) {
            $context.find('.cfs_input .field[data-type="textarea"] textarea, .cfs_input .cfs_textarea textarea')
                .addBack('.cfs_input .field[data-type="textarea"] textarea, .cfs_input .cfs_textarea textarea')
                .each(function() {
                    if ($(this).closest('.cfs_wysiwyg, .cfs_code_view, .cfs_post_content, .wp-editor-wrap').length) {
                        return;
                    }

                    if (!this.getAttribute('data-cfs-auto-textarea-min-height')) {
                        this.setAttribute('data-cfs-auto-textarea-min-height', this.offsetHeight || parseFloat($(this).css('min-height')) || 0);
                    }

                    this.style.overflowY = 'hidden';
                    resizeTextarea(this);
                });
        }

        CFS.validators = {
            'required': {
                'error': validationMessage('enter_value', 'Please enter a value'),
                'validate': function(val) {
                    return ('' != val && null != val);
                }
            },
            'valid_date': {
                'error': validationMessage('valid_date', 'Please enter a valid date (YYYY-MM-DD HH:MM)'),
                'validate': function(val) {
                    var regex = /^\d{4}-\d{2}-\d{2}/;
                    return regex.test(val);
                }
            },
            'valid_color': {
                'error': validationMessage('valid_color', 'Please enter a valid color HEX (#ff0000)'),
                'validate': function(val) {
                    var regex = /^#[0-9a-zA-Z]{3,}$/;
                    return regex.test(val);
                }
            },
            'valid_phone': {
                'error': validationMessage('valid_phone', 'Please enter a valid phone number'),
                'validate': function(val) {
                    val = trim(val);
                    var regex = /^[0-9+\-().\s]+$/;
                    return '' == val || regex.test(val);
                }
            },
            'required_phone': {
                'error': function(el, val) {
                    return '' == trim(val) ?
                        validationMessage('enter_phone', 'Please enter a phone number') :
                        validationMessage('valid_phone', 'Please enter a valid phone number');
                },
                'validate': function(val) {
                    val = trim(val);
                    var regex = /^[0-9+\-().\s]+$/;
                    return '' != val && null != val && regex.test(val);
                }
            },
            'valid_email': {
                'error': validationMessage('valid_email', 'Please enter a valid email address'),
                'validate': function(val) {
                    val = trim(val);
                    var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return '' == val || regex.test(val);
                }
            },
            'required_email': {
                'error': function(el, val) {
                    return '' == trim(val) ?
                        validationMessage('enter_email', 'Please enter an email address') :
                        validationMessage('valid_email', 'Please enter a valid email address');
                },
                'validate': function(val) {
                    val = trim(val);
                    var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return '' != val && null != val && regex.test(val);
                }
            },
            'valid_number': {
                'error': validationMessage('valid_number', 'Please enter a valid number'),
                'validate': function(val) {
                    val = trim(val);
                    return '' == val || /^-?(?:\d+|\d*\.\d+)$/.test(val);
                }
            },
            'required_number': {
                'error': function(el, val) {
                    return '' == trim(val) ?
                        validationMessage('enter_number', 'Please enter a number') :
                        validationMessage('valid_number', 'Please enter a valid number');
                },
                'validate': function(val) {
                    val = trim(val);
                    return '' != val && null != val && /^-?(?:\d+|\d*\.\d+)$/.test(val);
                }
            },
            'valid_url': {
                'error': validationMessage('valid_url', 'Please enter a valid URL'),
                'validate': function(val) {
                    val = trim(val);
                    return '' == val || /^(https?:\/\/|mailto:|tel:)/i.test(val);
                }
            },
            'required_url': {
                'error': function(el, val) {
                    return '' == trim(val) ?
                        validationMessage('enter_url', 'Please enter a URL') :
                        validationMessage('valid_url', 'Please enter a valid URL');
                },
                'validate': function(val) {
                    val = trim(val);
                    return '' != val && null != val && /^(https?:\/\/|mailto:|tel:)/i.test(val);
                }
            },
            'valid_time': {
                'error': validationMessage('valid_time', 'Please select a valid time'),
                'validate': function(val) {
                    val = trim(val);
                    var regex = /^([01]\d|2[0-3]):[0-5]\d$/;
                    return '' == val || regex.test(val);
                }
            },
            'required_time': {
                'error': function(el, val) {
                    return '' == trim(val) ?
                        validationMessage('select_time', 'Please select a time') :
                        validationMessage('valid_time', 'Please select a valid time');
                },
                'validate': function(val) {
                    val = trim(val);
                    var regex = /^([01]\d|2[0-3]):[0-5]\d$/;
                    return '' != val && null != val && regex.test(val);
                }
            },
            'required_code_view': {
                'error': validationMessage('enter_code', 'Please select a language and enter code'),
                'validate': function(val, el) {
                    var language = trim(el.find('.atshift-cfs-code-view-language').val());
                    var code = trim(el.find('textarea').val());
                    return '' != language && '' != code;
                }
            },
            'required_conditional': {
                'error': validationMessage('select_condition', 'Please select a condition'),
                'validate': function(val) {
                    return '' != trim(val);
                }
            },
            'limit': {
                'error': function(el) {
                    var limits = el.attr('data-validator').split('|')[1].split(',');
                    if (limits[0] == limits[1]) {
                        return validationMessage('select_items', 'Please select %s item(s)').replace('%s', limits[0]);
                    }
                    else {
                        return validationMessage('select_item_range', 'Please select between %1$s and %2$s items')
                            .replace('%1$s', limits[0])
                            .replace('%2$s', limits[1]);
                    }
                },
                'validate': function(val, el) {
                    var count = ('' == val) ? 0 : val.split(',').length;
                    var limits = el.attr('data-validator').split('|')[1].split(',');
                    var min = parseInt(limits[0]);
                    var max = parseInt(limits[1]);
                    if (0 < min && count < min) {
                        return false;
                    }
                    if (0 < max && max < count) {
                        return false;
                    }
                    return true;
                }
            }
        };

        // Get the value for non-standard field types
        CFS.get_field_value = {
            'textarea': function(el) {
                return el.find('textarea').val();
            },
            'code_view': function(el) {
                return el.find('textarea').val();
            },
            'select': function(el) {
                return el.find('select').val();
            },
            'checkbox': function(el) {
                var values = [];
                el.find('input[type="checkbox"]:checked').each(function() {
                    values.push($(this).val());
                });
                return values.join(',');
            },
            'wp_category': function(el) {
                var values = [];
                el.find('.cfs-wp-category-list input[type="checkbox"]:checked').each(function() {
                    values.push($(this).val());
                });
                return values.join(',');
            },
            'radio': function(el) {
                return el.find('input[type="radio"]:checked').val();
            },
            'relationship': function(el) {
                return el.find('input.relationship').val();
            },
            'term': function(el) {
                return el.find('input.term').val();
            },
            'user': function(el) {
                return el.find('input.user').val();
            },
            'gallery': function(el) {
                return el.find('input.gallery_value').val();
            },
            'time': function(el) {
                var hour = el.find('.cfs-time-hour').val();
                var minute = el.find('.cfs-time-minute').val();
                return ('' == hour && '' == minute) ? '' : hour + ':' + minute;
            },
            'conditional': function(el) {
                var selected = el.find('.cfs-conditional-selector:checked').val();

                if (undefined !== selected) {
                    return selected;
                }

                return el.find('select.cfs-conditional-selector').val() || '';
            },
            'wysiwyg': function(el) {
                tinyMCE.triggerSave();
                return el.find('textarea').val();
            },
            'post_content': function(el) {
                if (window.tinyMCE && 'function' === typeof tinyMCE.triggerSave) {
                    tinyMCE.triggerSave();
                }
                return el.find('textarea').val();
            },
            'loop': function(el) {
                var rows = [];
                el.find('> .cfs_loop > .loop_wrapper').each(function(index) {
                    rows.push(index);
                });
                return rows.join(',');
            }
        };

        CFS.is_draft = false;
        CFS.validation_notice_active = false;
        $(document).on('click', '#save-post', function() {
            CFS.is_draft = true;
        });

        CFS.validate_field = function(field_name, obj, options) {
            options = $.extend({
                show_empty_required: true,
                open_loop: true,
                collect_errors: false
            }, options);

            var is_valid = true;

            $('.cfs_input .field-' + field_name).each(function() {
                var $this = $(this);
                var type = obj.type;
                var validator = obj.rule.split('|')[0];

                $this.find('> .error').hide();
                $this.removeClass('cfs-field-invalid cfs-field-invalid-visual');
                $this.removeAttr('data-validation-message');

                if ($this.closest('.cfs-conditional-branch[hidden]').length) {
                    return;
                }

                if ('object' != typeof CFS.validators[validator]) {
                    return;
                }

                $this.attr('data-validator', obj.rule);

                var val = ('function' == typeof CFS.get_field_value[type]) ? CFS.get_field_value[type]($this) : $this.find('input').val();
                var is_required = 0 === validator.indexOf('required') || 'required' == validator;
                var is_empty = '' == trim(val);

                if (is_empty && !is_required && 'limit' != validator) {
                    $this.find('> .error').hide();
                    $this.removeClass('cfs-field-invalid cfs-field-invalid-visual');
                    return;
                }

                if (is_empty && is_required && !options.show_empty_required) {
                    $this.find('> .error').hide();
                    $this.removeClass('cfs-field-invalid cfs-field-invalid-visual');
                    return;
                }

                if (!CFS.validators[validator]['validate'](val, $this)) {
                    is_valid = false;
                    $this.addClass('cfs-field-invalid');

                    if ($this.find('> .error').length < 1) {
                        $this.append('<div class="error" role="alert"></div>');
                    }

                    if (options.open_loop && $this.parents('.cfs_loop_body').length > 0) {
                        var $loop = $this.parents('.cfs_loop_body');
                        $loop.addClass('open');
                        $loop.siblings('.cfs_loop_head').addClass('open');
                    }

                    var error_msg = CFS.validators[validator]['error'];
                    if ('function' == typeof error_msg) {
                        error_msg = error_msg($this, val);
                    }

                    $this.find('> .error').text(error_msg).show();
                    $this.attr('data-validation-message', error_msg);

                    if (options.collect_errors) {
                        var field_id = $this.attr('id');
                        var label = trim($this.find('> label').first().text());

                        if (!field_id) {
                            field_id = 'atshift-cfs-validation-field-' + field_name.replace(/[^a-zA-Z0-9_-]/g, '-') + '-' + CFS.validation_errors.length;
                            $this.attr('id', field_id);
                        }

                        CFS.validation_errors.push({
                            id: field_id,
                            name: field_name,
                            label: '' != label ? label : field_name,
                            message: error_msg
                        });
                    }
                }
            });

            CFS.refresh_validation_field_visuals();

            return is_valid;
        };

        CFS.render_validation_notice = function() {
            var $notice = $('#atshift-cfs-validation-admin-notice');
            var $list = $('#atshift-cfs-validation-error-list');
            var previousErrorCount = parseInt(CFS.validation_last_error_count || 0, 10);
            var hadActiveErrors = CFS.validation_notice_active && 0 < previousErrorCount;
            var errorCount = 0;

            $list.empty();

            $('.cfs_input .field.cfs-field-invalid').each(function(index) {
                var $field = $(this);
                var fieldId = $field.attr('id');
                var fieldName = $field.attr('data-name') || '';
                var label = trim($field.find('> label').first().clone().children().remove().end().text());
                var message = $field.attr('data-validation-message') || trim($field.find('> .error').text());
                var $row = $field.closest('.loop_wrapper').children('.cfs_loop_head').first().find('.label').first();

                if (!fieldId) {
                    fieldId = 'atshift-cfs-validation-field-' + fieldName.replace(/[^a-zA-Z0-9_-]/g, '-') + '-' + index;
                    $field.attr('id', fieldId);
                }

                if ($row.length && trim($row.text())) {
                    label = trim($row.text()) + ' / ' + label;
                }

                var tabLabels = CFS.get_field_tab_labels($field);
                if (tabLabels.length) {
                    label = tabLabels.join(' / ') + ' / ' + (label || fieldName);
                }

                $('<li></li>').append(
                    $('<a></a>')
                        .attr('href', '#' + encodeURIComponent(fieldId))
                        .text((label || fieldName) + ': ' + message)
                ).appendTo($list);
                errorCount++;
            });

            CFS.refresh_validation_containers();
            CFS.refresh_validation_field_visuals();
            CFS.ensure_validation_notice_visible();

            if (CFS.validation_notice_active && 0 < errorCount) {
                $notice.show();
            }
            else {
                $notice.hide();
            }

            CFS.refresh_publish_validation_feedback(errorCount);

            if (hadActiveErrors && 0 === errorCount) {
                CFS.validation_notice_active = false;
                CFS.show_publish_validation_resolved_feedback();
            }
            else if (hadActiveErrors && errorCount < previousErrorCount) {
                CFS.focus_next_validation_error_after_resolution();
            }

            CFS.validation_last_error_count = errorCount;
        };

        CFS.ensure_validation_notice_visible = function() {
            var $notice = $('#atshift-cfs-validation-admin-notice');

            if (!$notice.length) {
                return;
            }

            var $targetBox = $('.cfs_input .field.cfs-field-invalid').first().closest('.cfs_input');

            if (!$targetBox.length) {
                $targetBox = $('.cfs_input').first();
            }

            var $target = $targetBox.children('.inside').first();

            if (!$target.length) {
                $target = $targetBox;
            }

            if ($target.length && !$notice.parent().is($target)) {
                $notice.prependTo($target);
            }
        };

        CFS.refresh_validation_field_visuals = function() {
            $('.cfs_input .field')
                .removeClass('cfs-field-invalid-visual');

            $('.cfs_input .field.cfs-field-invalid').each(function() {
                var $field = $(this);
                var $error = $field.children('.error').first();

                if (!$error.length || !trim($error.text()) || !$error.is(':visible')) {
                    $field.addClass('cfs-field-invalid-visual');
                }
            });
        };

        CFS.get_tab_key = function($content) {
            var tabName = $content.attr('data-tab-key') || null;

            if (!tabName) {
                $.each(($content.attr('class') || '').split(/\s+/), function(index, className) {
                    if (0 === className.indexOf('cfs-tab-content-')) {
                        tabName = className.substring('cfs-tab-content-'.length);
                        return false;
                    }
                });
            }

            return tabName;
        };

        CFS.get_tab_for_content = function($content) {
            var tabName = CFS.get_tab_key($content);

            if (!tabName) {
                return $();
            }

            return $content.parent().children('.cfs-tabs').children('.cfs-tab').filter(function() {
                return ($(this).attr('data-tab-key') || $(this).attr('rel')) === tabName;
            });
        };

        CFS.get_tab_label = function($tab) {
            return trim($tab.clone().children().remove().end().text());
        };

        CFS.get_field_tab_labels = function($field) {
            var labels = [];

            $($field.parents('.cfs-tab-content').get().reverse()).each(function() {
                var tabLabel = CFS.get_tab_label(CFS.get_tab_for_content($(this)));

                if (tabLabel) {
                    labels.push(tabLabel);
                }
            });

            return labels;
        };

        CFS.get_field_by_id = function(fieldId) {
            if (!fieldId) {
                return $();
            }

            return $(document.getElementById(fieldId));
        };

        CFS.refresh_validation_containers = function() {
            $('.cfs-accordion').each(function() {
                $(this).toggleClass('cfs-has-error', 0 < $(this).find('.field.cfs-field-invalid').length);
            });

            $('.cfs-tab.cfs-has-error')
                .removeClass('cfs-has-error')
                .removeAttr('data-error-count')
                .children('.cfs-tab-error-count')
                .remove();

            $('.cfs-tab-content').each(function() {
                var $content = $(this);
                var errorCount = $content.find('.field.cfs-field-invalid').length;
                var $tab = CFS.get_tab_for_content($content);

                if ($tab.length && 0 < errorCount) {
                    $tab
                        .addClass('cfs-has-error')
                        .attr('data-error-count', errorCount)
                        .append(
                            $('<span></span>')
                                .addClass('cfs-tab-error-count')
                                .attr('aria-hidden', 'true')
                                .text(errorCount)
                        );
                }
            });

            $('.loop_wrapper').each(function() {
                var $wrapper = $(this);
                $wrapper.children('.cfs_loop_head').toggleClass('cfs-has-error', 0 < $wrapper.find('.field.cfs-field-invalid').length);
            });
        };

        CFS.reveal_validation_field = function($field) {
            $field.parents('.cfs_loop_body').each(function() {
                $(this).addClass('open').siblings('.cfs_loop_head').addClass('open');
            });

            $field.parents('.cfs-accordion').each(function() {
                $(this).addClass('open').children('.cfs-accordion-toggle').attr('aria-expanded', 'true');
            });

            $($field.parents('.cfs-tab-content').get().reverse()).each(function() {
                var $content = $(this);
                CFS.get_tab_for_content($content).trigger('click');
            });

            window.setTimeout(function() {
                $('html, body').animate({
                    scrollTop: Math.max(0, $field.offset().top - 80)
                }, 250);
            }, 0);
        };

        CFS.focus_next_validation_error_after_resolution = function() {
            var $nextInvalidField = $('.cfs_input .field.cfs-field-invalid').first();

            if (!$nextInvalidField.length) {
                return;
            }

            window.setTimeout(function() {
                CFS.reveal_validation_field($nextInvalidField);
            }, 120);
        };

        CFS.scroll_to_validation_notice = function() {
            var $notice = $('#atshift-cfs-validation-admin-notice');

            CFS.ensure_validation_notice_visible();

            if (!$notice.length) {
                return false;
            }

            $notice.show();

            window.setTimeout(function() {
                var scrollTop = Math.max(0, $notice.offset().top - 90);

                if (window.scrollTo) {
                    window.scrollTo({
                        top: scrollTop,
                        behavior: 'smooth'
                    });
                }

                $('html, body').stop(true).animate({
                    scrollTop: scrollTop
                }, 250);
            }, 0);

            return true;
        };

        CFS.refresh_publish_validation_feedback = function(errorCount, $activeControl) {
            var numericErrorCount = 'undefined' === typeof errorCount ?
                $('.cfs_input .field.cfs-field-invalid').length :
                parseInt(errorCount, 10);
            var hasErrors = 0 < (numericErrorCount || 0);
            var noticeText = trim($('#atshift-cfs-validation-admin-notice p strong').first().text());

            $('.cfs-post-publish-control').each(function() {
                var $control = $(this);
                var $message = $control.children('.cfs-post-publish-validation-feedback').first();
                var message = $control.attr('data-validation-error-message') ||
                    CFS.validation_save_error_message ||
                    noticeText ||
                    'The post was not saved because validation errors were found. Check the error list at the top of the field group.';
                var jumpLabel = $control.attr('data-validation-jump-label') || 'Go to error list';

                if (!$message.length) {
                    $message = $('<div></div>')
                        .addClass('cfs-post-publish-validation-feedback')
                        .attr('role', 'alert')
                        .attr('aria-live', 'assertive')
                        .prependTo($control);
                }

                if (hasErrors && (!$activeControl || $control.is($activeControl))) {
                    $message
                        .empty()
                        .removeClass('is-resolved')
                        .append(
                            $('<span></span>')
                                .addClass('cfs-post-publish-validation-feedback-text')
                                .text(message)
                        )
                        .append(' ')
                        .append(
                            $('<a></a>')
                                .addClass('cfs-post-publish-validation-jump')
                                .attr('href', '#atshift-cfs-validation-admin-notice')
                                .text(jumpLabel)
                        )
                        .show();
                    $control.addClass('cfs-post-publish-has-validation-error');
                    $control.removeClass('cfs-post-publish-has-validation-resolved');
                }
                else {
                    $message.hide().empty();
                    $control.removeClass('cfs-post-publish-has-validation-error');
                }
            });
        };

        CFS.show_publish_validation_resolved_feedback = function($activeControl) {
            var $control = $activeControl && $activeControl.length ?
                $activeControl :
                $('.cfs-post-publish-control').first();

            if (!$control.length) {
                return;
            }

            var $message = $control.children('.cfs-post-publish-validation-feedback').first();
            var message = $control.attr('data-validation-resolved-message') ||
                'Validation errors have been resolved. You can save the post now.';
            var timeoutId = $control.data('cfsValidationResolvedTimer');

            if (!$message.length) {
                $message = $('<div></div>')
                    .addClass('cfs-post-publish-validation-feedback')
                    .attr('role', 'status')
                    .attr('aria-live', 'polite')
                    .prependTo($control);
            }

            if (timeoutId) {
                window.clearTimeout(timeoutId);
            }

            $message
                .empty()
                .addClass('is-resolved')
                .append(
                    $('<span></span>')
                        .addClass('cfs-post-publish-validation-feedback-text')
                        .text(message)
                )
                .show();

            $control
                .removeClass('cfs-post-publish-has-validation-error')
                .addClass('cfs-post-publish-has-validation-resolved');

            window.setTimeout(function() {
                $('html, body').stop(true).animate({
                    scrollTop: Math.max(0, $control.offset().top - 90)
                }, 250);
            }, 0);

            timeoutId = window.setTimeout(function() {
                $message.fadeOut(180, function() {
                    $message
                        .empty()
                        .removeClass('is-resolved');
                });
                $control.removeClass('cfs-post-publish-has-validation-resolved');
                $control.removeData('cfsValidationResolvedTimer');
            }, 2800);

            $control.data('cfsValidationResolvedTimer', timeoutId);
        };

        CFS.focus_first_validation_error = function(options) {
            options = $.extend({
                prefer_notice: true
            }, options);

            var $notice = $('#atshift-cfs-validation-admin-notice:visible').first();

            if (options.prefer_notice && $notice.length) {
                CFS.scroll_to_validation_notice();
                return;
            }

            var $firstInvalidField = $('.cfs_input .field.cfs-field-invalid').first();

            if ($firstInvalidField.length) {
                CFS.reveal_validation_field($firstInvalidField);
                return;
            }

            if ($notice.length) {
                $('html, body').animate({
                    scrollTop: Math.max(0, $notice.offset().top - 80)
                }, 250);
            }
        };

        CFS.validate_all_fields = function() {
            var passthru = true;
            CFS.validation_errors = [];
            $('#atshift-cfs-validation-admin-notice').hide();
            $('#atshift-cfs-validation-error-list').empty();

            $.each(CFS.field_rules, function(field_name, obj) {
                if (!CFS.validate_field(field_name, obj, {
                    collect_errors: true,
                    open_loop: false
                })) {
                    passthru = false;
                }
            });

            if (!passthru) {
                CFS.validation_notice_active = true;
                CFS.render_validation_notice();
            }
            else {
                CFS.refresh_publish_validation_feedback(0);
            }

            return passthru;
        };

        $(document).on('input change blur', '.cfs_input .field :input', function(event) {
            var $field = $(this).closest('.field');
            var field_name = $field.attr('data-name');

            if (!field_name || !CFS.field_rules || !CFS.field_rules[field_name]) {
                return;
            }

            CFS.validate_field(field_name, CFS.field_rules[field_name], {
                show_empty_required: 'input' != event.type,
                open_loop: false
            });
            CFS.render_validation_notice();
        });

        $(document).on('click', '.cfs_input .cfs-tab', function() {
            window.setTimeout(function() {
                autoResizeTextareas($('.cfs_input'));
                CFS.refresh_validation_field_visuals();
            }, 0);
        });

        $(document).on('cfs/layout/changed', function(event) {
            var $context = $(event.target);

            window.setTimeout(function() {
                autoResizeTextareas($context);
                CFS.refresh_validation_field_visuals();
            }, 0);
            window.setTimeout(function() {
                autoResizeTextareas($context);
            }, 80);
        });

        $(document).on('input', '.cfs_input .field[data-type="textarea"] textarea, .cfs_input .cfs_textarea textarea', function() {
            if ($(this).closest('.cfs_wysiwyg, .cfs_code_view, .cfs_post_content, .wp-editor-wrap').length) {
                return;
            }

            resizeTextarea(this);
        });

        $(document).on('click', '#atshift-cfs-validation-error-list a', function(event) {
            var targetId = ($(this).attr('href') || '').substring(1);
            var $field;

            try {
                targetId = decodeURIComponent(targetId);
            }
            catch (error) {}

            $field = CFS.get_field_by_id(targetId);

            if (!$field.length) {
                return;
            }

            event.preventDefault();
            CFS.reveal_validation_field($field);
        });

        $(document).on('click', '.cfs-post-publish-validation-jump', function() {
            CFS.scroll_to_validation_notice();
        });

        $('form#post').submit(function() {

            // skip validation for drafts
            if (false === CFS.is_draft) {
                var passthru = CFS.validate_all_fields();

                if (!passthru) {
                    $('#publish').removeClass('button-primary-disabled');
                    $('#save-post').removeClass('button-disabled');
                    $('.spinner').hide();
                    CFS.focus_first_validation_error();
                    return false;
                }
            }
        });

        document.addEventListener('click', function(event) {
            var target = event.target && event.target.closest ?
                event.target.closest('.editor-post-save-draft, .editor-post-publish-button__button') :
                null;

            if (!target) {
                return;
            }

            var passthru = CFS.validate_all_fields();

            if (!passthru) {
                event.preventDefault();
                event.stopImmediatePropagation();
                CFS.focus_first_validation_error();
                return false;
            }
        }, true);

        if (CFS.server_validation_errors) {
            CFS.validation_notice_active = true;
            CFS.validate_all_fields();

            var $firstInvalidField = $('.cfs_input .field.cfs-field-invalid').first();
            if ($firstInvalidField.length) {
                CFS.reveal_validation_field($firstInvalidField);
            }
        }

        autoResizeTextareas($('.cfs_input'));
    });
})(jQuery);
