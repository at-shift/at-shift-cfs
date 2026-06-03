(function($) {
    $(function() {
        CFS.validators = {
            'required': {
                'error': 'Please enter a value',
                'validate': function(val) {
                    return ('' != val && null != val);
                }
            },
            'valid_date': {
                'error': 'Please enter a valid date (YYYY-MM-DD HH:MM)',
                'validate': function(val) {
                    var regex = /^\d{4}-\d{2}-\d{2}/;
                    return regex.test(val);
                }
            },
            'valid_color': {
                'error': 'Please enter a valid color HEX (#ff0000)',
                'validate': function(val) {
                    var regex = /^#[0-9a-zA-Z]{3,}$/;
                    return regex.test(val);
                }
            },
            'valid_phone': {
                'error': 'Please enter a valid phone number',
                'validate': function(val) {
                    val = $.trim(val);
                    var regex = /^[0-9+\-().\s]+$/;
                    return '' == val || regex.test(val);
                }
            },
            'required_phone': {
                'error': 'Please enter a valid phone number',
                'validate': function(val) {
                    val = $.trim(val);
                    var regex = /^[0-9+\-().\s]+$/;
                    return '' != val && null != val && regex.test(val);
                }
            },
            'valid_email': {
                'error': 'Please enter a valid email address',
                'validate': function(val) {
                    val = $.trim(val);
                    var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return '' == val || regex.test(val);
                }
            },
            'required_email': {
                'error': 'Please enter a valid email address',
                'validate': function(val) {
                    val = $.trim(val);
                    var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return '' != val && null != val && regex.test(val);
                }
            },
            'valid_number': {
                'error': 'Please enter a valid number',
                'validate': function(val) {
                    val = $.trim(val);
                    return '' == val || /^-?(?:\d+|\d*\.\d+)$/.test(val);
                }
            },
            'required_number': {
                'error': 'Please enter a valid number',
                'validate': function(val) {
                    val = $.trim(val);
                    return '' != val && null != val && /^-?(?:\d+|\d*\.\d+)$/.test(val);
                }
            },
            'valid_url': {
                'error': 'Please enter a valid URL',
                'validate': function(val) {
                    val = $.trim(val);
                    return '' == val || /^(https?:\/\/|mailto:|tel:)/i.test(val);
                }
            },
            'required_url': {
                'error': 'Please enter a valid URL',
                'validate': function(val) {
                    val = $.trim(val);
                    return '' != val && null != val && /^(https?:\/\/|mailto:|tel:)/i.test(val);
                }
            },
            'valid_time': {
                'error': 'Please select a valid time',
                'validate': function(val) {
                    val = $.trim(val);
                    var regex = /^([01]\d|2[0-3]):[0-5]\d$/;
                    return '' == val || regex.test(val);
                }
            },
            'required_time': {
                'error': 'Please select a valid time',
                'validate': function(val) {
                    val = $.trim(val);
                    var regex = /^([01]\d|2[0-3]):[0-5]\d$/;
                    return '' != val && null != val && regex.test(val);
                }
            },
            'limit': {
                'error': function(el) {
                    var limits = el.attr('data-validator').split('|')[1].split(',');
                    if (limits[0] == limits[1]) {
                        return 'Please select ' + limits[0] + ' item(s)';
                    }
                    else {
                        return 'Please select between ' + limits[0] + ' and ' + limits[1] + ' items';
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
            'time': function(el) {
                var hour = el.find('.cfs-time-hour').val();
                var minute = el.find('.cfs-time-minute').val();
                return ('' == hour && '' == minute) ? '' : hour + ':' + minute;
            },
            'wysiwyg': function(el) {
                tinyMCE.triggerSave();
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
                $this.removeClass('cfs-field-invalid');

                if ('object' != typeof CFS.validators[validator]) {
                    return;
                }

                $this.attr('data-validator', obj.rule);

                var val = ('function' == typeof CFS.get_field_value[type]) ? CFS.get_field_value[type]($this) : $this.find('input').val();
                var is_required = 0 === validator.indexOf('required') || 'required' == validator;
                var is_empty = '' == $.trim(null == val ? '' : val);

                if (is_empty && !is_required) {
                    $this.find('> .error').hide();
                    $this.removeClass('cfs-field-invalid');
                    return;
                }

                if (is_empty && is_required && !options.show_empty_required) {
                    $this.find('> .error').hide();
                    $this.removeClass('cfs-field-invalid');
                    return;
                }

                if (!CFS.validators[validator]['validate'](val, $this)) {
                    is_valid = false;
                    $this.addClass('cfs-field-invalid');

                    if ($this.find('> .error').length < 1) {
                        $this.append('<div class="error"></div>');
                    }

                    if (options.open_loop && $this.parents('.cfs_loop_body').length > 0) {
                        var $loop = $this.parents('.cfs_loop_body');
                        $loop.addClass('open');
                        $loop.siblings('.cfs_loop_head').addClass('open');
                    }

                    var error_msg = CFS.validators[validator]['error'];
                    if ('function' == typeof error_msg) {
                        error_msg = error_msg($this);
                    }

                    $this.find('> .error').text(error_msg).show();

                    if (options.collect_errors) {
                        var field_id = $this.attr('id');
                        var label = $.trim($this.find('> label').first().text());

                        if (!field_id) {
                            field_id = 'cfs-validation-field-' + field_name.replace(/[^a-zA-Z0-9_-]/g, '-') + '-' + CFS.validation_errors.length;
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

            return is_valid;
        };

        CFS.render_validation_notice = function() {
            var $notice = $('#cfs-validation-admin-notice');
            var $list = $('#cfs-validation-error-list');

            $list.empty();

            $.each(CFS.validation_errors || [], function(index, item) {
                $('<li></li>').append(
                    $('<a></a>')
                        .attr('href', '#' + item.id)
                        .text(item.label + ': ' + item.message)
                ).appendTo($list);
            });

            $notice.show();
        };

        CFS.validate_all_fields = function() {
            var passthru = true;
            CFS.validation_errors = [];
            $('#cfs-validation-admin-notice').hide();
            $('#cfs-validation-error-list').empty();

            $.each(CFS.field_rules, function(field_name, obj) {
                if (!CFS.validate_field(field_name, obj, {
                    collect_errors: true
                })) {
                    passthru = false;
                }
            });

            if (!passthru) {
                CFS.render_validation_notice();
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
        });

        $('form#post').submit(function() {

            // skip validation for drafts
            if (false === CFS.is_draft) {
                var passthru = CFS.validate_all_fields();

                if (!passthru) {
                    $('#publish').removeClass('button-primary-disabled');
                    $('#save-post').removeClass('button-disabled');
                    $('.spinner').hide();
                    return false;
                }
            }
        });
    });
})(jQuery);
