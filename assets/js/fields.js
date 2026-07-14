(function($) {
    // Accented chars and their non-accented replacements
    var latin_map = {"Á":"A","Ă":"A","Ắ":"A","Ặ":"A","Ằ":"A","Ẳ":"A","Ẵ":"A","Ǎ":"A","Â":"A","Ấ":"A","Ậ":"A","Ầ":"A","Ẩ":"A","Ẫ":"A","Ä":"A","Ǟ":"A","Ȧ":"A","Ǡ":"A","Ạ":"A","Ȁ":"A","À":"A","Ả":"A","Ȃ":"A","Ā":"A","Ą":"A","Å":"A","Ǻ":"A","Ḁ":"A","Ⱥ":"A","Ã":"A","Ꜳ":"AA","Æ":"AE","Ǽ":"AE","Ǣ":"AE","Ꜵ":"AO","Ꜷ":"AU","Ꜹ":"AV","Ꜻ":"AV","Ꜽ":"AY","Ḃ":"B","Ḅ":"B","Ɓ":"B","Ḇ":"B","Ƀ":"B","Ƃ":"B","Ć":"C","Č":"C","Ç":"C","Ḉ":"C","Ĉ":"C","Ċ":"C","Ƈ":"C","Ȼ":"C","Ď":"D","Ḑ":"D","Ḓ":"D","Ḋ":"D","Ḍ":"D","Ɗ":"D","Ḏ":"D","ǲ":"D","ǅ":"D","Đ":"D","Ƌ":"D","Ǳ":"DZ","Ǆ":"DZ","É":"E","Ĕ":"E","Ě":"E","Ȩ":"E","Ḝ":"E","Ê":"E","Ế":"E","Ệ":"E","Ề":"E","Ể":"E","Ễ":"E","Ḙ":"E","Ë":"E","Ė":"E","Ẹ":"E","Ȅ":"E","È":"E","Ẻ":"E","Ȇ":"E","Ē":"E","Ḗ":"E","Ḕ":"E","Ę":"E","Ɇ":"E","Ẽ":"E","Ḛ":"E","Ꝫ":"ET","Ḟ":"F","Ƒ":"F","Ǵ":"G","Ğ":"G","Ǧ":"G","Ģ":"G","Ĝ":"G","Ġ":"G","Ɠ":"G","Ḡ":"G","Ǥ":"G","Ḫ":"H","Ȟ":"H","Ḩ":"H","Ĥ":"H","Ⱨ":"H","Ḧ":"H","Ḣ":"H","Ḥ":"H","Ħ":"H","Í":"I","Ĭ":"I","Ǐ":"I","Î":"I","Ï":"I","Ḯ":"I","İ":"I","Ị":"I","Ȉ":"I","Ì":"I","Ỉ":"I","Ȋ":"I","Ī":"I","Į":"I","Ɨ":"I","Ĩ":"I","Ḭ":"I","Ꝺ":"D","Ꝼ":"F","Ᵹ":"G","Ꞃ":"R","Ꞅ":"S","Ꞇ":"T","Ꝭ":"IS","Ĵ":"J","Ɉ":"J","Ḱ":"K","Ǩ":"K","Ķ":"K","Ⱪ":"K","Ꝃ":"K","Ḳ":"K","Ƙ":"K","Ḵ":"K","Ꝁ":"K","Ꝅ":"K","Ĺ":"L","Ƚ":"L","Ľ":"L","Ļ":"L","Ḽ":"L","Ḷ":"L","Ḹ":"L","Ⱡ":"L","Ꝉ":"L","Ḻ":"L","Ŀ":"L","Ɫ":"L","ǈ":"L","Ł":"L","Ǉ":"LJ","Ḿ":"M","Ṁ":"M","Ṃ":"M","Ɱ":"M","Ń":"N","Ň":"N","Ņ":"N","Ṋ":"N","Ṅ":"N","Ṇ":"N","Ǹ":"N","Ɲ":"N","Ṉ":"N","Ƞ":"N","ǋ":"N","Ñ":"N","Ǌ":"NJ","Ó":"O","Ŏ":"O","Ǒ":"O","Ô":"O","Ố":"O","Ộ":"O","Ồ":"O","Ổ":"O","Ỗ":"O","Ö":"O","Ȫ":"O","Ȯ":"O","Ȱ":"O","Ọ":"O","Ő":"O","Ȍ":"O","Ò":"O","Ỏ":"O","Ơ":"O","Ớ":"O","Ợ":"O","Ờ":"O","Ở":"O","Ỡ":"O","Ȏ":"O","Ꝋ":"O","Ꝍ":"O","Ō":"O","Ṓ":"O","Ṑ":"O","Ɵ":"O","Ǫ":"O","Ǭ":"O","Ø":"O","Ǿ":"O","Õ":"O","Ṍ":"O","Ṏ":"O","Ȭ":"O","Ƣ":"OI","Ꝏ":"OO","Ɛ":"E","Ɔ":"O","Ȣ":"OU","Ṕ":"P","Ṗ":"P","Ꝓ":"P","Ƥ":"P","Ꝕ":"P","Ᵽ":"P","Ꝑ":"P","Ꝙ":"Q","Ꝗ":"Q","Ŕ":"R","Ř":"R","Ŗ":"R","Ṙ":"R","Ṛ":"R","Ṝ":"R","Ȑ":"R","Ȓ":"R","Ṟ":"R","Ɍ":"R","Ɽ":"R","Ꜿ":"C","Ǝ":"E","Ś":"S","Ṥ":"S","Š":"S","Ṧ":"S","Ş":"S","Ŝ":"S","Ș":"S","Ṡ":"S","Ṣ":"S","Ṩ":"S","Ť":"T","Ţ":"T","Ṱ":"T","Ț":"T","Ⱦ":"T","Ṫ":"T","Ṭ":"T","Ƭ":"T","Ṯ":"T","Ʈ":"T","Ŧ":"T","Ɐ":"A","Ꞁ":"L","Ɯ":"M","Ʌ":"V","Ꜩ":"TZ","Ú":"U","Ŭ":"U","Ǔ":"U","Û":"U","Ṷ":"U","Ü":"U","Ǘ":"U","Ǚ":"U","Ǜ":"U","Ǖ":"U","Ṳ":"U","Ụ":"U","Ű":"U","Ȕ":"U","Ù":"U","Ủ":"U","Ư":"U","Ứ":"U","Ự":"U","Ừ":"U","Ử":"U","Ữ":"U","Ȗ":"U","Ū":"U","Ṻ":"U","Ų":"U","Ů":"U","Ũ":"U","Ṹ":"U","Ṵ":"U","Ꝟ":"V","Ṿ":"V","Ʋ":"V","Ṽ":"V","Ꝡ":"VY","Ẃ":"W","Ŵ":"W","Ẅ":"W","Ẇ":"W","Ẉ":"W","Ẁ":"W","Ⱳ":"W","Ẍ":"X","Ẋ":"X","Ý":"Y","Ŷ":"Y","Ÿ":"Y","Ẏ":"Y","Ỵ":"Y","Ỳ":"Y","Ƴ":"Y","Ỷ":"Y","Ỿ":"Y","Ȳ":"Y","Ɏ":"Y","Ỹ":"Y","Ź":"Z","Ž":"Z","Ẑ":"Z","Ⱬ":"Z","Ż":"Z","Ẓ":"Z","Ȥ":"Z","Ẕ":"Z","Ƶ":"Z","Ĳ":"IJ","Œ":"OE","ᴀ":"A","ᴁ":"AE","ʙ":"B","ᴃ":"B","ᴄ":"C","ᴅ":"D","ᴇ":"E","ꜰ":"F","ɢ":"G","ʛ":"G","ʜ":"H","ɪ":"I","ʁ":"R","ᴊ":"J","ᴋ":"K","ʟ":"L","ᴌ":"L","ᴍ":"M","ɴ":"N","ᴏ":"O","ɶ":"OE","ᴐ":"O","ᴕ":"OU","ᴘ":"P","ʀ":"R","ᴎ":"N","ᴙ":"R","ꜱ":"S","ᴛ":"T","ⱻ":"E","ᴚ":"R","ᴜ":"U","ᴠ":"V","ᴡ":"W","ʏ":"Y","ᴢ":"Z","á":"a","ă":"a","ắ":"a","ặ":"a","ằ":"a","ẳ":"a","ẵ":"a","ǎ":"a","â":"a","ấ":"a","ậ":"a","ầ":"a","ẩ":"a","ẫ":"a","ä":"a","ǟ":"a","ȧ":"a","ǡ":"a","ạ":"a","ȁ":"a","à":"a","ả":"a","ȃ":"a","ā":"a","ą":"a","ᶏ":"a","ẚ":"a","å":"a","ǻ":"a","ḁ":"a","ⱥ":"a","ã":"a","ꜳ":"aa","æ":"ae","ǽ":"ae","ǣ":"ae","ꜵ":"ao","ꜷ":"au","ꜹ":"av","ꜻ":"av","ꜽ":"ay","ḃ":"b","ḅ":"b","ɓ":"b","ḇ":"b","ᵬ":"b","ᶀ":"b","ƀ":"b","ƃ":"b","ɵ":"o","ć":"c","č":"c","ç":"c","ḉ":"c","ĉ":"c","ɕ":"c","ċ":"c","ƈ":"c","ȼ":"c","ď":"d","ḑ":"d","ḓ":"d","ȡ":"d","ḋ":"d","ḍ":"d","ɗ":"d","ᶑ":"d","ḏ":"d","ᵭ":"d","ᶁ":"d","đ":"d","ɖ":"d","ƌ":"d","ı":"i","ȷ":"j","ɟ":"j","ʄ":"j","ǳ":"dz","ǆ":"dz","é":"e","ĕ":"e","ě":"e","ȩ":"e","ḝ":"e","ê":"e","ế":"e","ệ":"e","ề":"e","ể":"e","ễ":"e","ḙ":"e","ë":"e","ė":"e","ẹ":"e","ȅ":"e","è":"e","ẻ":"e","ȇ":"e","ē":"e","ḗ":"e","ḕ":"e","ⱸ":"e","ę":"e","ᶒ":"e","ɇ":"e","ẽ":"e","ḛ":"e","ꝫ":"et","ḟ":"f","ƒ":"f","ᵮ":"f","ᶂ":"f","ǵ":"g","ğ":"g","ǧ":"g","ģ":"g","ĝ":"g","ġ":"g","ɠ":"g","ḡ":"g","ᶃ":"g","ǥ":"g","ḫ":"h","ȟ":"h","ḩ":"h","ĥ":"h","ⱨ":"h","ḧ":"h","ḣ":"h","ḥ":"h","ɦ":"h","ẖ":"h","ħ":"h","ƕ":"hv","í":"i","ĭ":"i","ǐ":"i","î":"i","ï":"i","ḯ":"i","ị":"i","ȉ":"i","ì":"i","ỉ":"i","ȋ":"i","ī":"i","į":"i","ᶖ":"i","ɨ":"i","ĩ":"i","ḭ":"i","ꝺ":"d","ꝼ":"f","ᵹ":"g","ꞃ":"r","ꞅ":"s","ꞇ":"t","ꝭ":"is","ǰ":"j","ĵ":"j","ʝ":"j","ɉ":"j","ḱ":"k","ǩ":"k","ķ":"k","ⱪ":"k","ꝃ":"k","ḳ":"k","ƙ":"k","ḵ":"k","ᶄ":"k","ꝁ":"k","ꝅ":"k","ĺ":"l","ƚ":"l","ɬ":"l","ľ":"l","ļ":"l","ḽ":"l","ȴ":"l","ḷ":"l","ḹ":"l","ⱡ":"l","ꝉ":"l","ḻ":"l","ŀ":"l","ɫ":"l","ᶅ":"l","ɭ":"l","ł":"l","ǉ":"lj","ſ":"s","ẜ":"s","ẛ":"s","ẝ":"s","ḿ":"m","ṁ":"m","ṃ":"m","ɱ":"m","ᵯ":"m","ᶆ":"m","ń":"n","ň":"n","ņ":"n","ṋ":"n","ȵ":"n","ṅ":"n","ṇ":"n","ǹ":"n","ɲ":"n","ṉ":"n","ƞ":"n","ᵰ":"n","ᶇ":"n","ɳ":"n","ñ":"n","ǌ":"nj","ó":"o","ŏ":"o","ǒ":"o","ô":"o","ố":"o","ộ":"o","ồ":"o","ổ":"o","ỗ":"o","ö":"o","ȫ":"o","ȯ":"o","ȱ":"o","ọ":"o","ő":"o","ȍ":"o","ò":"o","ỏ":"o","ơ":"o","ớ":"o","ợ":"o","ờ":"o","ở":"o","ỡ":"o","ȏ":"o","ꝋ":"o","ꝍ":"o","ⱺ":"o","ō":"o","ṓ":"o","ṑ":"o","ǫ":"o","ǭ":"o","ø":"o","ǿ":"o","õ":"o","ṍ":"o","ṏ":"o","ȭ":"o","ƣ":"oi","ꝏ":"oo","ɛ":"e","ᶓ":"e","ɔ":"o","ᶗ":"o","ȣ":"ou","ṕ":"p","ṗ":"p","ꝓ":"p","ƥ":"p","ᵱ":"p","ᶈ":"p","ꝕ":"p","ᵽ":"p","ꝑ":"p","ꝙ":"q","ʠ":"q","ɋ":"q","ꝗ":"q","ŕ":"r","ř":"r","ŗ":"r","ṙ":"r","ṛ":"r","ṝ":"r","ȑ":"r","ɾ":"r","ᵳ":"r","ȓ":"r","ṟ":"r","ɼ":"r","ᵲ":"r","ᶉ":"r","ɍ":"r","ɽ":"r","ↄ":"c","ꜿ":"c","ɘ":"e","ɿ":"r","ś":"s","ṥ":"s","š":"s","ṧ":"s","ş":"s","ŝ":"s","ș":"s","ṡ":"s","ṣ":"s","ṩ":"s","ʂ":"s","ᵴ":"s","ᶊ":"s","ȿ":"s","ɡ":"g","ᴑ":"o","ᴓ":"o","ᴝ":"u","ť":"t","ţ":"t","ṱ":"t","ț":"t","ȶ":"t","ẗ":"t","ⱦ":"t","ṫ":"t","ṭ":"t","ƭ":"t","ṯ":"t","ᵵ":"t","ƫ":"t","ʈ":"t","ŧ":"t","ᵺ":"th","ɐ":"a","ᴂ":"ae","ǝ":"e","ᵷ":"g","ɥ":"h","ʮ":"h","ʯ":"h","ᴉ":"i","ʞ":"k","ꞁ":"l","ɯ":"m","ɰ":"m","ᴔ":"oe","ɹ":"r","ɻ":"r","ɺ":"r","ⱹ":"r","ʇ":"t","ʌ":"v","ʍ":"w","ʎ":"y","ꜩ":"tz","ú":"u","ŭ":"u","ǔ":"u","û":"u","ṷ":"u","ü":"u","ǘ":"u","ǚ":"u","ǜ":"u","ǖ":"u","ṳ":"u","ụ":"u","ű":"u","ȕ":"u","ù":"u","ủ":"u","ư":"u","ứ":"u","ự":"u","ừ":"u","ử":"u","ữ":"u","ȗ":"u","ū":"u","ṻ":"u","ų":"u","ᶙ":"u","ů":"u","ũ":"u","ṹ":"u","ṵ":"u","ᵫ":"ue","ꝸ":"um","ⱴ":"v","ꝟ":"v","ṿ":"v","ʋ":"v","ᶌ":"v","ⱱ":"v","ṽ":"v","ꝡ":"vy","ẃ":"w","ŵ":"w","ẅ":"w","ẇ":"w","ẉ":"w","ẁ":"w","ⱳ":"w","ẘ":"w","ẍ":"x","ẋ":"x","ᶍ":"x","ý":"y","ŷ":"y","ÿ":"y","ẏ":"y","ỵ":"y","ỳ":"y","ƴ":"y","ỷ":"y","ỿ":"y","ȳ":"y","ẙ":"y","ɏ":"y","ỹ":"y","ź":"z","ž":"z","ẑ":"z","ʑ":"z","ⱬ":"z","ż":"z","ẓ":"z","ȥ":"z","ẕ":"z","ᵶ":"z","ᶎ":"z","ʐ":"z","ƶ":"z","ɀ":"z","ﬀ":"ff","ﬃ":"ffi","ﬄ":"ffl","ﬁ":"fi","ﬂ":"fl","ĳ":"ij","œ":"oe","ﬆ":"st","ₐ":"a","ₑ":"e","ᵢ":"i","ⱼ":"j","ₒ":"o","ᵣ":"r","ᵤ":"u","ᵥ":"v","ₓ":"x"};

    $(function() {
        var generated_name_types = [
            'tab',
            'group',
            'accordion',
            'conditional',
            'post_title',
            'post_publish',
            'wp_category',
            'wp_tag',
            'featured_image'
        ];

        function get_parent_item($item) {
            return $item.parent('ul').closest('li');
        }

        function zebra_stripes() {
            $('.fields .field_meta').removeClass('even');
            $('.fields .field_meta:even').addClass('even');
        }

        function init_tooltip() {
            $(document).on('mouseover', '.cfs_tooltip', function() {
                if ('undefined' == typeof $(this).data('powertip')) {
                    var content = $(this).find('.tooltip_inner').html();
                    $(this).data('powertip', content);
                    $(this).powerTip({
                        placement: 'e',
                        mouseOnToPopup: true
                    });
                    $.powerTip.show(this);
                }
            });
        }

        function init_select2_controls($context) {
            if ('function' != typeof $.fn.select2) {
                return;
            }

            $context.find('.cfs-post-title-role-select, .cfs-post-native-role-select').each(function() {
                var $select = $(this);

                if ($select.data('select2')) {
                    return;
                }

                $select.select2({
                    placeholder: $select.data('placeholder') || $select.attr('data-placeholder') || '',
                    width: '99.95%'
                });
            });
        }

        function sync_parent_ids() {
            $('ul.fields li').each(function() {
                var $item = $(this);
                var parent_id = 0;
                var parent_key = '';
                var $parent = get_parent_item($item);

                if (0 < $parent.length) {
                    var $parent_field = $parent.children('.field');
                    parent_id = $parent_field.find('.field_id').first().val();
                    parent_key = $parent_field.find('.field_key').first().val();
                    set_outside_tabs($item, false);
                }

                $item.children('.field').find('.parent_id').first().val(parent_id);
                $item.children('.field').find('.parent_key').first().val(parent_key);
            });
        }

        function get_item_type($item) {
            return $item.children('.field').find('.field_form .field_type select').first().val() ||
                $.trim($item.children('.field').find('.field_meta .field_type').first().text());
        }

        function set_outside_tabs($item, is_outside) {
            var value = is_outside ? '1' : '0';

            $item.attr('data-outside-tabs', value);
            $item.children('.field').find('.outside_tabs').first().val(value).attr('value', value);
        }

        function is_outside_tabs($item) {
            return '1' == $item.attr('data-outside-tabs') ||
                '1' == $item.children('.field').find('.outside_tabs').first().val();
        }

        function has_following_top_level_tab($item) {
            if (0 < get_parent_item($item).length) {
                return false;
            }

            return 0 < $item.nextAll('li').filter(function() {
                return 'tab' == get_item_type($(this));
            }).length;
        }

        function should_break_tab_range($item, inTab) {
            if (!is_outside_tabs($item)) {
                return false;
            }

            if (0 < get_parent_item($item).length) {
                set_outside_tabs($item, false);
                return false;
            }

            if (inTab && has_following_top_level_tab($item)) {
                set_outside_tabs($item, false);
                return false;
            }

            return true;
        }

        function move_outside_tabs($item) {
            var $root = $('ul.fields').first();
            var $first_tab = $root.children('li').filter(function() {
                return 'tab' == get_item_type($(this));
            }).first();

            if (0 < $first_tab.length) {
                $first_tab.before($item);
            }
            else {
                $root.append($item);
            }
        }

        function move_after_parent($item, $parent) {
            if (0 < $parent.length) {
                $parent.after($item);
            }
            else {
                move_outside_tabs($item);
            }
        }

        function refresh_structure_markers($context) {
            var structureTypes = ['tab', 'loop', 'group', 'accordion', 'conditional'];
            var structureClasses = 'cfs-structure-tab cfs-structure-loop cfs-structure-group cfs-structure-accordion cfs-structure-conditional';

            $context.find('li').addBack('li').each(function() {
                var $item = $(this);
                var type = get_item_type($item);
                var $label = $item.children('.field').find('.field_meta .field_label a').first();

                $item.removeClass(structureClasses + ' cfs-tab-range');
                $label.find('.cfs-structure-badge').remove();

                if (-1 !== $.inArray(type, structureTypes)) {
                    $item.addClass('cfs-structure-' + type);
                    $('<span></span>', {
                        'class': 'cfs-structure-badge cfs-structure-badge-' + type,
                        text: CFS.messages && CFS.messages.structure_badges && CFS.messages.structure_badges[type] ?
                            CFS.messages.structure_badges[type] :
                            ('conditional' == type ? 'CONDITION' : type.toUpperCase())
                    }).prependTo($label);
                }
            });

            function mark_tab_ranges($list, inTab) {
                $list.children('li').each(function() {
                    var $item = $(this);
                    var childInTab = inTab;

                    if (should_break_tab_range($item, childInTab)) {
                        childInTab = false;
                    }

                    if ($item.hasClass('cfs-structure-tab')) {
                        childInTab = true;
                    }
                    else if (childInTab) {
                        $item.addClass('cfs-tab-range');
                    }

                    $item.children('ul').each(function() {
                        mark_tab_ranges($(this), childInTab);
                    });

                    inTab = childInTab;
                });
            }

            $('#cfs_fields > .inside > ul.fields').each(function() {
                mark_tab_ranges($(this), false);
            });
        }

        function normalize_field_names() {
            $('ul.fields li').each(function() {
                var $field = $(this).children('.field');
                var key = $field.find('.field_key').first().val();

                if ('' === String(key || '')) {
                    return;
                }

                $field.find('[name^="cfs[fields]"]').each(function() {
                    this.name = this.name.replace(/^cfs\[fields\]\[[^\]]+\]/, 'cfs[fields][' + key + ']');
                });
            });
        }

        function message(key, fallback) {
            return CFS.messages && CFS.messages[key] ? CFS.messages[key] : fallback;
        }

        function uses_generated_field_name(type) {
            return -1 < $.inArray(type, generated_name_types);
        }

        function get_field_name_input($item) {
            var $field = $item.children('.field');
            var $input = $field.find('.field_form .field_name input.cfs-editable-field-name:not(:disabled)').first();

            if (1 > $input.length) {
                $input = $field.find('.field_form .field_name input[type="text"]:not(:disabled)').first();
            }

            return $input;
        }

        function refresh_field_name_mode($item) {
            var $field = $item.children('.field');
            var generated = uses_generated_field_name(get_item_type($item));
            var $editable = $field.find('.field_form .field_name input.cfs-editable-field-name').first();
            var $generated = $field.find('.field_form .field_name input.cfs-generated-field-name-input').first();
            var $display = $field.find('.field_form .field_name .cfs-generated-field-name-display').first();
            var value = '';
            var displayValue = '';
            var pendingLabel = message('automatically_named_when_saved', 'Automatically named when saved');

            if (generated) {
                value = $.trim($generated.val() || '');
                displayValue = value || $.trim($display.attr('data-current-name') || '');
                $field.addClass('cfs-field-generated-name');
                $editable.prop('disabled', true).hide();
                $generated.prop('disabled', false);
                $display.text(displayValue || pendingLabel).prop('hidden', false).show();
                $field.find('.field_meta .field_name').first().text(displayValue || pendingLabel);
                return;
            }

            value = $.trim($editable.val() || '');
            if (!value && $.trim($generated.val() || '')) {
                value = $.trim($generated.val() || '');
                $editable.val(value);
            }

            $field.removeClass('cfs-field-generated-name');
            $editable.prop('disabled', false).show();
            $generated.prop('disabled', true);
            $display.prop('hidden', true).hide();
            $field.find('.field_meta .field_name').first().text(value);
        }

        function clear_duplicate_field_name_warnings() {
            $('#cfs_fields .cfs-duplicate-field-name').removeClass('cfs-duplicate-field-name');
            $('#cfs_fields .cfs-duplicate-field-name-input')
                .removeClass('cfs-duplicate-field-name-input')
                .removeAttr('aria-invalid');
            $('#cfs_fields .cfs-duplicate-field-name-warning').remove();
        }

        function collect_duplicate_field_names() {
            var names = {};
            var duplicates = {};

            $('ul.fields li').each(function() {
                var $item = $(this);
                var $input = get_field_name_input($item);
                var name = $.trim($input.val() || '');
                var key = name.toLowerCase();

                if (!key) {
                    return;
                }

                if (!names[key]) {
                    names[key] = [];
                }

                names[key].push({
                    item: $item,
                    input: $input,
                    name: name
                });
            });

            $.each(names, function(key, fields) {
                if (1 < fields.length) {
                    duplicates[key] = fields;
                }
            });

            return duplicates;
        }

        function render_duplicate_field_name_warnings() {
            var duplicates = collect_duplicate_field_names();
            var duplicate_names = [];
            var inline_message = message('duplicate_field_name_inline', 'This field name is duplicated. Use a unique field name.');

            clear_duplicate_field_name_warnings();

            $.each(duplicates, function(key, fields) {
                duplicate_names.push(fields[0].name);

                $.each(fields, function(index, field) {
                    field.item.children('.field').addClass('cfs-duplicate-field-name');
                    field.item.children('.field').find('.field_meta .field_name').first().addClass('cfs-duplicate-field-name');
                    field.input
                        .addClass('cfs-duplicate-field-name-input')
                        .attr('aria-invalid', 'true')
                        .after($('<p></p>', {
                            'class': 'description cfs-duplicate-field-name-warning',
                            text: inline_message
                        }));
                });
            });

            return duplicate_names;
        }

        function reveal_duplicate_field_name() {
            var $input = $('#cfs_fields .cfs-duplicate-field-name-input').first();
            var $field = $input.closest('.field');

            if (!$input.length) {
                return;
            }

            if (!$field.hasClass('form_open')) {
                $field.addClass('form_open');
                $field.find('.field_form').show();
            }

            $('html, body').animate({
                scrollTop: Math.max(0, $field.offset().top - 80)
            }, 200);
            $input.trigger('focus');
        }

        function restore_admin_submit_state() {
            var $publish_button = $('#publish');

            $publish_button
                .prop('disabled', false)
                .removeClass('disabled button-primary-disabled')
                .attr('aria-disabled', 'false');

            $('#publishing-action .spinner, #major-publishing-actions .spinner, #submitdiv .spinner')
                .removeClass('is-active')
                .css('visibility', 'hidden');
        }

        function maybe_outdent_dragged_item($item, event) {
            var outdented = false;
            var guard = 0;
            var outdent_threshold = 34;

            if (!event || 'number' !== typeof event.pageX) {
                return false;
            }

            while (5 > guard) {
                var $list = $item.parent('ul');
                var $parent = $list.closest('li');
                var list_offset = $list.offset();

                if (!$parent.length || !list_offset) {
                    break;
                }

                if (-1 === $.inArray(get_item_type($parent), ['loop', 'group', 'accordion', 'conditional'])) {
                    break;
                }

                if (event.pageX >= list_offset.left + outdent_threshold) {
                    break;
                }

                $parent.after($item);
                outdented = true;
                guard++;
            }

            return outdented;
        }

        function get_structure_label(type) {
            if (CFS.messages && CFS.messages.structure_badges && CFS.messages.structure_badges[type]) {
                return CFS.messages.structure_badges[type];
            }

            return 'conditional' == type ? 'CONDITION' : String(type || '').toUpperCase();
        }

        function clear_outdent_targets() {
            $('#cfs_fields .cfs-outdent-target')
                .removeClass('cfs-outdent-target')
                .removeAttr('data-cfs-drop-label');
            $('#cfs_fields .cfs-outdent-tab-target')
                .removeClass('cfs-outdent-tab-target')
                .removeAttr('data-cfs-drop-label');
        }

        function mark_outdent_targets($item) {
            var $root = $('ul.fields').first();
            var $current_list = $item.parent('ul');
            var $current_parent = $current_list.closest('li');
            var guard = 0;

            clear_outdent_targets();

            while ($current_parent.length && 5 > guard) {
                var $target_list = $current_parent.parent('ul');
                var $target_container = $target_list.closest('li');

                if ($target_container.length && -1 !== $.inArray(get_item_type($target_container), ['loop', 'group', 'accordion', 'conditional'])) {
                    $target_list
                        .addClass('cfs-outdent-target')
                        .attr('data-cfs-drop-label', message(
                            'outdent_to_container',
                            'Move here: inside %s'
                        ).replace('%s', get_structure_label(get_item_type($target_container))));
                }
                else if ($target_list.is($root) && $current_parent.hasClass('cfs-tab-range')) {
                    $current_parent
                        .addClass('cfs-outdent-tab-target')
                        .attr('data-cfs-drop-label', message(
                            'outdent_to_tab',
                            'Move here: inside the current Tab'
                        ));
                }

                $current_list = $target_list;
                $current_parent = $current_list.closest('li');
                guard++;
            }
        }

        function update_sortable_placeholder(ui) {
            if (!ui || !ui.placeholder || !ui.placeholder.length) {
                return;
            }

            ui.placeholder.attr('data-cfs-drop-label', message('move_here', 'Move here'));
        }

        function get_disallowed_parent_child_message(parent_type, child_type) {
            if ('group' == parent_type && ('tab' == child_type || 'group' == child_type || 'loop' == child_type || 'accordion' == child_type || 'conditional' == child_type)) {
                return CFS.messages && CFS.messages.disallowed_group_child ?
                    CFS.messages.disallowed_group_child :
                    'Tabs, loops, and horizontal groups cannot be placed inside a horizontal group.';
            }

            if ('accordion' == parent_type && 'tab' == child_type) {
                return CFS.messages && CFS.messages.disallowed_accordion_child ?
                    CFS.messages.disallowed_accordion_child :
                    'Tabs cannot be placed inside an accordion.';
            }

            if ('conditional' == parent_type && ('tab' == child_type || 'conditional' == child_type)) {
                return CFS.messages && CFS.messages.disallowed_conditional_child ?
                    CFS.messages.disallowed_conditional_child :
                    'Tabs and conditional groups cannot be placed inside a Conditional Group.';
            }

            return '';
        }

        function enforce_group_child_rules($item) {
            var $parent = get_parent_item($item);
            var message = '';

            if (1 > $parent.length) {
                return false;
            }

            message = get_disallowed_parent_child_message(get_item_type($parent), get_item_type($item));

            if ('' === message) {
                return false;
            }

            move_after_parent($item, $parent);
            sync_parent_ids();

            window.alert(message);

            return true;
        }

        function ensure_child_containers($context) {
            $context.find('li.loop').addBack('li.loop').filter(function() {
                return $(this).children('ul').length < 1;
            }).append('<ul></ul>');
        }

        function parse_conditional_choices(value) {
            var choices = [];

            $.each(String(value || '').split(/\r?\n/), function(index, line) {
                line = $.trim(line);
                if (!line) {
                    return;
                }

                var separator = line.indexOf(' : ');
                choices.push({
                    value: $.trim(-1 === separator ? line : line.substring(0, separator)),
                    label: $.trim(-1 === separator ? line : line.substring(separator + 3))
                });
            });

            return choices;
        }

        function refresh_conditional_assignments($context) {
            $context.find('li').addBack('li').each(function() {
                var $item = $(this);
                var $parent = get_parent_item($item);
                var $row = $item.children('.field').find('.field_conditional_value').first();
                var $separator = $item.children('.field').find('.field_conditional_separator').first();
                var $select = $row.find('.cfs-conditional-value');
                var $defaultRow = $item.children('.field').find('[name*="[options][default_value]"]').first().closest('tr');

                if ($defaultRow.length) {
                    $row.add($separator).insertBefore($defaultRow);
                }

                if (1 > $parent.length || 'conditional' != get_item_type($parent)) {
                    $row.add($separator).hide();
                    return;
                }

                var selected = String($select.val() || '');
                var choices = parse_conditional_choices($parent.children('.field').find('.cfs-conditional-choices').first().val());
                var selected_exists = '' === selected;

                $select.empty().append($('<option>', { value: '', text: '' }));
                $.each(choices, function(index, choice) {
                    $select.append($('<option>', { value: choice.value, text: choice.label + ' (' + choice.value + ')' }));
                    if (String(choice.value) === selected) {
                        selected_exists = true;
                    }
                });

                if (!selected_exists) {
                    $select.append($('<option>', { value: selected, text: selected + ' (' + selected + ')' }));
                }
                $select.val(selected);
                $row.add($separator).show();
            });

            $context.find('.cfs-conditional-display-type').each(function() {
                $(this).closest('.field').find('.cfs-conditional-default-row').toggle('radio' == $(this).val());
            });
        }

        function update_add_field_button_labels($context) {
            $context.find('.cfs_add_field_below').each(function() {
                var $button = $(this);
                var $item = $button.closest('li');
                var label = ('loop' == get_item_type($item) || 'group' == get_item_type($item) || 'accordion' == get_item_type($item) || 'conditional' == get_item_type($item)) ?
                    (CFS.messages && CFS.messages.add_field_inside ? CFS.messages.add_field_inside : 'Add field inside') :
                    (CFS.messages && CFS.messages.add_field_below ? CFS.messages.add_field_below : 'Add new field below');

                $button.val(label);
            });
        }

        function close_field_action_menus($except) {
            $('.cfs-field-action-menu-list').not($except || $()).prop('hidden', true);
            $('.cfs_field_actions_toggle').not($except ? $except.siblings('.cfs_field_actions_toggle') : $()).attr('aria-expanded', 'false');
        }

        function collect_reserved_field_names() {
            var reserved = {};

            $('ul.fields li').each(function() {
                var $field = $(this).children('.field');
                var name = $.trim(
                    $field.find('.field_form .field_name input.cfs-editable-field-name').first().val() ||
                    $field.find('.field_form .field_name input.cfs-generated-field-name-input').first().val() ||
                    ''
                );

                if (name) {
                    reserved[name.toLowerCase()] = true;
                }
            });

            return reserved;
        }

        function make_unique_copy_name(name, reserved) {
            var base = $.trim(name || '');
            var suffix = '_copy';
            var candidate = base + suffix;
            var index = 2;

            if (!base) {
                return '';
            }

            while (reserved[candidate.toLowerCase()]) {
                candidate = base + suffix + index;
                index++;
            }

            reserved[candidate.toLowerCase()] = true;
            return candidate;
        }

        function clean_cloned_field_ui($item) {
            $item.find('.select2-container').remove();
            $item.find('.select2-hidden-accessible')
                .removeClass('select2-hidden-accessible')
                .removeAttr('data-select2-id aria-hidden tabindex');
            $item.find('[data-select2-id]').removeAttr('data-select2-id');
            $item.find('.cfs-duplicate-field-name')
                .removeClass('cfs-duplicate-field-name cfs-duplicate-field-name-input')
                .removeAttr('aria-invalid');
            $item.find('.cfs-duplicate-field-name-warning').remove();
            $item.find('.cfs-field-action-menu-list').prop('hidden', true);
            $item.find('.cfs_field_actions_toggle').attr('aria-expanded', 'false');
            $item.find('.field').each(function() {
                $(this).removeClass('form_open').children('.field_form').hide();
            });
        }

        function prepare_duplicated_field_item($item, reserved_names) {
            var $field = $item.children('.field');
            var type = get_item_type($item);
            var key = CFS.field_index;
            var generated_name = uses_generated_field_name(type);
            var $editable = $field.find('.field_form .field_name input.cfs-editable-field-name').first();
            var $generated = $field.find('.field_form .field_name input.cfs-generated-field-name-input').first();
            var source_name = $.trim($editable.val() || $generated.val() || '');
            var new_name = generated_name ? '' : make_unique_copy_name(source_name, reserved_names);

            CFS.field_index = CFS.field_index + 1;
            $field.find('.field_id').first().val(0).attr('value', 0);
            $field.find('.field_key').first().val(key).attr('value', key);
            $field.find('.parent_id').first().val(0).attr('value', 0);
            $field.find('.parent_key').first().val('').attr('value', '');

            $editable.val(new_name).attr('value', new_name);
            $generated.val(new_name).attr('value', new_name);
            refresh_field_name_mode($item);
        }

        function duplicate_field_item($item) {
            var $clone = $item.clone(false, false);
            var reserved_names = collect_reserved_field_names();

            clean_cloned_field_ui($clone);

            $clone.add($clone.find('li')).each(function() {
                prepare_duplicated_field_item($(this), reserved_names);
            });

            $item.after($clone);
            $clone.children('.field').addClass('form_open').children('.field_form').show();
            normalize_field_names();
            ensure_child_containers($('ul.fields'));
            init_sortables($('ul.fields, ul.fields ul'));
            init_tooltip();
            init_select2_controls($clone);
            sync_parent_ids();
            update_add_field_button_labels($('ul.fields'));
            refresh_structure_markers($('ul.fields'));
            refresh_conditional_assignments($('ul.fields'));
            render_duplicate_field_name_warnings();
        }

        function init_sortables($containers) {
            $containers.each(function() {
                var $container = $(this);

                if ($container.data('ui-sortable')) {
                    return;
                }

                $container.sortable({
                    items: '> li',
                    connectWith: 'ul.fields, ul.fields ul',
                    placeholder: 'ui-sortable-placeholder',
                    handle: '.field_order',
                    tolerance: 'pointer',
                    dropOnEmpty: true,
                    forcePlaceholderSize: true,
                    start: function(event, ui) {
                        var $children = ui.item.children('ul').detach();

                        ui.item.data('cfs-drag-children', $children);
                        $('ul.fields, ul.fields li.loop > ul').addClass('cfs-drop-target');
                        update_sortable_placeholder(ui);
                        mark_outdent_targets(ui.item);
                    },
                    sort: function(event, ui) {
                        update_sortable_placeholder(ui);
                    },
                    change: function(event, ui) {
                        update_sortable_placeholder(ui);
                    },
                    beforeStop: function(event, ui) {
                        var $children = ui.item.data('cfs-drag-children');

                        if ($children && $children.length) {
                            ui.item.append($children);
                        }
                    },
                    stop: function(event, ui) {
                        ui.item.removeData('cfs-drag-children').removeClass('cfs-dragging-field');
                        $('ul.fields, ul.fields li.loop > ul').removeClass('cfs-drop-target cfs-drop-target-active');
                        clear_outdent_targets();
                        zebra_stripes();
                        maybe_outdent_dragged_item(ui.item, event);
                        enforce_group_child_rules(ui.item);

                        if (1 > get_parent_item(ui.item).length && 'tab' != get_item_type(ui.item)) {
                            set_outside_tabs(ui.item, false);
                        }

                        ensure_child_containers($('ul.fields'));
                        init_sortables($('ul.fields, ul.fields ul'));
                        sync_parent_ids();
                        update_add_field_button_labels($('ul.fields'));
                        refresh_structure_markers($('ul.fields'));
                        refresh_conditional_assignments($('ul.fields'));
                    },
                    over: function(event, ui) {
                        ui.item.addClass('cfs-dragging-field');
                        $(this).addClass('cfs-drop-target-active');
                    },
                    out: function(event, ui) {
                        ui.item.removeClass('cfs-dragging-field');
                        $(this).removeClass('cfs-drop-target-active');
                    },
                    update: function(event, ui) {
                        ui.item.removeClass('cfs-dragging-field');
                    }
                });
            });
        }

        zebra_stripes();
        init_tooltip();
        init_select2_controls($(document));
        $('ul.fields li').each(function() {
            refresh_field_name_mode($(this));
        });
        ensure_child_containers($('ul.fields'));
        init_sortables($('ul.fields, ul.fields ul'));
        sync_parent_ids();
        update_add_field_button_labels($('ul.fields'));
        refresh_structure_markers($('ul.fields'));
        refresh_conditional_assignments($('ul.fields'));
        render_duplicate_field_name_warnings();

        // Setup checkboxes
        $(document).on('change click', 'input[type="checkbox"]', function() {
            var val = $(this).prop('checked') ? 1 : 0;
            $(this).siblings('input').val(val);
        });

        // Add a new field
        $(document).on('click', '.cfs_add_field', function() {
            var html = CFS.field_clone.replace(/\[clone\]/g, '['+CFS.field_index+']');
            var $new_field = $('<li>' + html + '</li>');
            $('ul.fields').first().append($new_field);
            $new_field.find('.field_key').first().val(CFS.field_index);
            $new_field.find('.field_label a').click();
            $new_field.find('.field_type select').change();
            set_outside_tabs($new_field, false);
            CFS.field_index = CFS.field_index + 1;
            init_tooltip();
            init_select2_controls($new_field);
            sync_parent_ids();
            update_add_field_button_labels($('ul.fields'));
            refresh_structure_markers($('ul.fields'));
            refresh_conditional_assignments($('ul.fields'));
            render_duplicate_field_name_warnings();
        });

        // Add a new field immediately below the current field
        $(document).on('click', '.cfs_add_field_below', function() {
            var html = CFS.field_clone.replace(/\[clone\]/g, '['+CFS.field_index+']');
            var $current = $(this).closest('li');
            var parent_id = $current.children('.field').find('.parent_id').first().val();
            var $new_field = $('<li>' + html + '</li>');
            var current_type = get_item_type($current);

            $new_field.find('.field_key').first().val(CFS.field_index);
            $new_field.find('.parent_id').first().val(parent_id);

            if ('loop' == current_type || 'group' == current_type || 'accordion' == current_type || 'conditional' == current_type) {
                ensure_child_containers($current);
                $current.children('ul').first().append($new_field);
            }
            else {
                $current.after($new_field);
            }

            $new_field.find('.field_label a').click();
            $new_field.find('.field_type select').change();
            CFS.field_index = CFS.field_index + 1;
            zebra_stripes();
            init_tooltip();
            init_select2_controls($new_field);
            ensure_child_containers($('ul.fields'));
            init_sortables($('ul.fields, ul.fields ul'));
            sync_parent_ids();
            update_add_field_button_labels($('ul.fields'));
            refresh_structure_markers($('ul.fields'));
            refresh_conditional_assignments($('ul.fields'));
            render_duplicate_field_name_warnings();
        });

        // Delete a field
        $(document).on('click', '.cfs_delete_field', function() {
            $(this).closest('.field').closest('li').remove();
            refresh_structure_markers($('ul.fields'));
            refresh_conditional_assignments($('ul.fields'));
            render_duplicate_field_name_warnings();
        });

        // Duplicate a field
        $(document).on('click', '.cfs_duplicate_field', function() {
            duplicate_field_item($(this).closest('.field').closest('li'));
            close_field_action_menus();
        });

        $(document).on('click', '.cfs_field_actions_toggle', function(event) {
            event.preventDefault();
            event.stopPropagation();

            var $button = $(this);
            var $menu = $button.siblings('.cfs-field-action-menu-list').first();
            var opening = $menu.prop('hidden');

            close_field_action_menus($menu);
            $menu.prop('hidden', !opening);
            $button.attr('aria-expanded', opening ? 'true' : 'false');
        });

        $(document).on('click', '.cfs-field-action-menu', function(event) {
            event.stopPropagation();
        });

        $(document).on('click', function() {
            close_field_action_menus();
        });

        $(document).on('keydown', function(event) {
            if ('Escape' === event.key) {
                close_field_action_menus();
            }
        });

        // Pop open the edit fields
        $(document).on('click', '.cfs_edit_field', function() {
            var field = $(this).closest('.field');
            field.toggleClass('form_open');
            field.find('.field_form').slideToggle('fast');
        });

        // Add or replace field_type options
        $(document).on('change', '.field_form .field_type select', function() {
            var $item = $(this).closest('li');
            var was_in_tab = $item.hasClass('cfs-tab-range');
            var type = $(this).val();
            var input_name = $(this).attr('name').replace('[type]', '');
            var $options = $(CFS.options_html[type]);
            var $named_options = $options.filter('[name]').add($options.find('[name]'));

            $named_options.each(function() {
                this.name = this.name.replace(/cfs\[fields\]\[clone\]/g, input_name);
            });

            $(this).closest('.field').find('.field_meta .field_type a').text(type);
            $(this).closest('.field').find('.field_option').remove();
            $(this).closest('.field_basics').after($options);
            refresh_field_name_mode($item);

            if ('loop' == type || 'group' == type || 'accordion' == type || 'conditional' == type) {
                $item.addClass('loop');
                if ($item.children('ul').length < 1) {
                    $item.append('<ul></ul>');
                }
                init_sortables($item.children('ul'));
            }
            else if ($item.children('ul').children('li').length < 1) {
                $item.removeClass('loop');
                $item.children('ul').remove();
            }

            if ('tab' == type && was_in_tab && 1 > get_parent_item($item).length) {
                set_outside_tabs($item, true);
            }

            init_tooltip();
            init_select2_controls($item);
            enforce_group_child_rules($item);
            sync_parent_ids();
            update_add_field_button_labels($('ul.fields'));
            refresh_structure_markers($('ul.fields'));
            refresh_conditional_assignments($('ul.fields'));
            render_duplicate_field_name_warnings();
        });

        $(document).on('input change', '.cfs-conditional-choices', function() {
            refresh_conditional_assignments($(this).closest('li'));
        });

        $(document).on('change', '.cfs-conditional-display-type', function() {
            $(this).closest('.field').find('.cfs-conditional-default-row').toggle('radio' == $(this).val());
        });

        $(document).on('submit', '#post', function(event) {
            var duplicate_names = render_duplicate_field_name_warnings();

            if (0 < duplicate_names.length) {
                event.preventDefault();
                event.stopImmediatePropagation();
                restore_admin_submit_state();
                window.alert(message(
                    'duplicate_field_names_alert',
                    'Duplicate field names found: %s. Field names must be unique before saving.'
                ).replace('%s', duplicate_names.join(', ')));
                reveal_duplicate_field_name();
                restore_admin_submit_state();
                setTimeout(restore_admin_submit_state, 100);
                return false;
            }

            $('ul.fields li').each(function() {
                var $item = $(this);
                set_outside_tabs($item, is_outside_tabs($item));
            });
            sync_parent_ids();
            normalize_field_names();
        });

        $(document).on('change', '.cfs-time-minute-interval', function() {
            var interval = parseInt($(this).val(), 10);
            var $field = $(this).closest('.field');
            var $minute = $field.find('.cfs-time-default-minute').first();
            var selected = $minute.val();

            if (!interval || 1 > interval || 60 < interval || 0 !== 60 % interval) {
                interval = 1;
            }

            $minute.empty().append($('<option>', { value: '', text: '' }));

            for (var i = 0; i < 60; i += interval) {
                var value = ('0' + i).slice(-2);
                $minute.append($('<option>', { value: value, text: value }));
            }

            if ($minute.find('option[value="' + selected + '"]').length) {
                $minute.val(selected);
            }
            else {
                $minute.val('');
            }
        });

        // Auto-populate the field name
        $(document).on('blur', '.field_form .field_label input', function() {
            var val = $(this).val();

            // browser autofill support
            $(this).closest('.field').find('.field_meta .cfs-field-label-text').text(val);

            var name = get_field_name_input($(this).closest('li'));
            if ('' == name.val()) {
                val = $.trim(val).toLowerCase();
                val = val.replace(/[^\w- ]/g, function(a) { return latin_map[a] || ''; } ); // replace accented chars with non-accented ones or strip it out
                val = val.replace(/[- ]/g, '_'); // replace space and hyphen with underscore
                val = val.replace(/[_]{2,}/g, '_'); // strip consecutive underscores
                name.val(val);
                name.trigger('keyup');
            }
        });

        $(document).on('keyup paste', '.field_form .field_label input', function() {
            var $this = $(this);
            setTimeout(function() {
                $this.closest('.field').find('.field_meta .cfs-field-label-text').text($this.val());
            }, 1);
        });

        $(document).on('keyup input change paste', '.field_form .field_name input', function() {
            var $this = $(this);
            setTimeout(function() {
                var val = $this.val();
                var $field = $this.closest('.field');
                var label = $.trim(val || '');

                if ($this.hasClass('cfs-generated-field-name-input')) {
                    label = label || message('automatically_named_when_saved', 'Automatically named when saved');
                    $field.find('.cfs-generated-field-name-display').first().text(label);
                    $field.find('.field_meta .field_name').text(label);
                    return;
                }

                $field.find('.field_meta .field_name').text(label);
                render_duplicate_field_name_warnings();
            }, 1);
        });
    });
})(jQuery);
