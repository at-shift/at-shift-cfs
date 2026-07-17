(function($) {
    // Accented chars and their non-accented replacements
    var latin_map = {"Á":"A","Ă":"A","Ắ":"A","Ặ":"A","Ằ":"A","Ẳ":"A","Ẵ":"A","Ǎ":"A","Â":"A","Ấ":"A","Ậ":"A","Ầ":"A","Ẩ":"A","Ẫ":"A","Ä":"A","Ǟ":"A","Ȧ":"A","Ǡ":"A","Ạ":"A","Ȁ":"A","À":"A","Ả":"A","Ȃ":"A","Ā":"A","Ą":"A","Å":"A","Ǻ":"A","Ḁ":"A","Ⱥ":"A","Ã":"A","Ꜳ":"AA","Æ":"AE","Ǽ":"AE","Ǣ":"AE","Ꜵ":"AO","Ꜷ":"AU","Ꜹ":"AV","Ꜻ":"AV","Ꜽ":"AY","Ḃ":"B","Ḅ":"B","Ɓ":"B","Ḇ":"B","Ƀ":"B","Ƃ":"B","Ć":"C","Č":"C","Ç":"C","Ḉ":"C","Ĉ":"C","Ċ":"C","Ƈ":"C","Ȼ":"C","Ď":"D","Ḑ":"D","Ḓ":"D","Ḋ":"D","Ḍ":"D","Ɗ":"D","Ḏ":"D","ǲ":"D","ǅ":"D","Đ":"D","Ƌ":"D","Ǳ":"DZ","Ǆ":"DZ","É":"E","Ĕ":"E","Ě":"E","Ȩ":"E","Ḝ":"E","Ê":"E","Ế":"E","Ệ":"E","Ề":"E","Ể":"E","Ễ":"E","Ḙ":"E","Ë":"E","Ė":"E","Ẹ":"E","Ȅ":"E","È":"E","Ẻ":"E","Ȇ":"E","Ē":"E","Ḗ":"E","Ḕ":"E","Ę":"E","Ɇ":"E","Ẽ":"E","Ḛ":"E","Ꝫ":"ET","Ḟ":"F","Ƒ":"F","Ǵ":"G","Ğ":"G","Ǧ":"G","Ģ":"G","Ĝ":"G","Ġ":"G","Ɠ":"G","Ḡ":"G","Ǥ":"G","Ḫ":"H","Ȟ":"H","Ḩ":"H","Ĥ":"H","Ⱨ":"H","Ḧ":"H","Ḣ":"H","Ḥ":"H","Ħ":"H","Í":"I","Ĭ":"I","Ǐ":"I","Î":"I","Ï":"I","Ḯ":"I","İ":"I","Ị":"I","Ȉ":"I","Ì":"I","Ỉ":"I","Ȋ":"I","Ī":"I","Į":"I","Ɨ":"I","Ĩ":"I","Ḭ":"I","Ꝺ":"D","Ꝼ":"F","Ᵹ":"G","Ꞃ":"R","Ꞅ":"S","Ꞇ":"T","Ꝭ":"IS","Ĵ":"J","Ɉ":"J","Ḱ":"K","Ǩ":"K","Ķ":"K","Ⱪ":"K","Ꝃ":"K","Ḳ":"K","Ƙ":"K","Ḵ":"K","Ꝁ":"K","Ꝅ":"K","Ĺ":"L","Ƚ":"L","Ľ":"L","Ļ":"L","Ḽ":"L","Ḷ":"L","Ḹ":"L","Ⱡ":"L","Ꝉ":"L","Ḻ":"L","Ŀ":"L","Ɫ":"L","ǈ":"L","Ł":"L","Ǉ":"LJ","Ḿ":"M","Ṁ":"M","Ṃ":"M","Ɱ":"M","Ń":"N","Ň":"N","Ņ":"N","Ṋ":"N","Ṅ":"N","Ṇ":"N","Ǹ":"N","Ɲ":"N","Ṉ":"N","Ƞ":"N","ǋ":"N","Ñ":"N","Ǌ":"NJ","Ó":"O","Ŏ":"O","Ǒ":"O","Ô":"O","Ố":"O","Ộ":"O","Ồ":"O","Ổ":"O","Ỗ":"O","Ö":"O","Ȫ":"O","Ȯ":"O","Ȱ":"O","Ọ":"O","Ő":"O","Ȍ":"O","Ò":"O","Ỏ":"O","Ơ":"O","Ớ":"O","Ợ":"O","Ờ":"O","Ở":"O","Ỡ":"O","Ȏ":"O","Ꝋ":"O","Ꝍ":"O","Ō":"O","Ṓ":"O","Ṑ":"O","Ɵ":"O","Ǫ":"O","Ǭ":"O","Ø":"O","Ǿ":"O","Õ":"O","Ṍ":"O","Ṏ":"O","Ȭ":"O","Ƣ":"OI","Ꝏ":"OO","Ɛ":"E","Ɔ":"O","Ȣ":"OU","Ṕ":"P","Ṗ":"P","Ꝓ":"P","Ƥ":"P","Ꝕ":"P","Ᵽ":"P","Ꝑ":"P","Ꝙ":"Q","Ꝗ":"Q","Ŕ":"R","Ř":"R","Ŗ":"R","Ṙ":"R","Ṛ":"R","Ṝ":"R","Ȑ":"R","Ȓ":"R","Ṟ":"R","Ɍ":"R","Ɽ":"R","Ꜿ":"C","Ǝ":"E","Ś":"S","Ṥ":"S","Š":"S","Ṧ":"S","Ş":"S","Ŝ":"S","Ș":"S","Ṡ":"S","Ṣ":"S","Ṩ":"S","Ť":"T","Ţ":"T","Ṱ":"T","Ț":"T","Ⱦ":"T","Ṫ":"T","Ṭ":"T","Ƭ":"T","Ṯ":"T","Ʈ":"T","Ŧ":"T","Ɐ":"A","Ꞁ":"L","Ɯ":"M","Ʌ":"V","Ꜩ":"TZ","Ú":"U","Ŭ":"U","Ǔ":"U","Û":"U","Ṷ":"U","Ü":"U","Ǘ":"U","Ǚ":"U","Ǜ":"U","Ǖ":"U","Ṳ":"U","Ụ":"U","Ű":"U","Ȕ":"U","Ù":"U","Ủ":"U","Ư":"U","Ứ":"U","Ự":"U","Ừ":"U","Ử":"U","Ữ":"U","Ȗ":"U","Ū":"U","Ṻ":"U","Ų":"U","Ů":"U","Ũ":"U","Ṹ":"U","Ṵ":"U","Ꝟ":"V","Ṿ":"V","Ʋ":"V","Ṽ":"V","Ꝡ":"VY","Ẃ":"W","Ŵ":"W","Ẅ":"W","Ẇ":"W","Ẉ":"W","Ẁ":"W","Ⱳ":"W","Ẍ":"X","Ẋ":"X","Ý":"Y","Ŷ":"Y","Ÿ":"Y","Ẏ":"Y","Ỵ":"Y","Ỳ":"Y","Ƴ":"Y","Ỷ":"Y","Ỿ":"Y","Ȳ":"Y","Ɏ":"Y","Ỹ":"Y","Ź":"Z","Ž":"Z","Ẑ":"Z","Ⱬ":"Z","Ż":"Z","Ẓ":"Z","Ȥ":"Z","Ẕ":"Z","Ƶ":"Z","Ĳ":"IJ","Œ":"OE","ᴀ":"A","ᴁ":"AE","ʙ":"B","ᴃ":"B","ᴄ":"C","ᴅ":"D","ᴇ":"E","ꜰ":"F","ɢ":"G","ʛ":"G","ʜ":"H","ɪ":"I","ʁ":"R","ᴊ":"J","ᴋ":"K","ʟ":"L","ᴌ":"L","ᴍ":"M","ɴ":"N","ᴏ":"O","ɶ":"OE","ᴐ":"O","ᴕ":"OU","ᴘ":"P","ʀ":"R","ᴎ":"N","ᴙ":"R","ꜱ":"S","ᴛ":"T","ⱻ":"E","ᴚ":"R","ᴜ":"U","ᴠ":"V","ᴡ":"W","ʏ":"Y","ᴢ":"Z","á":"a","ă":"a","ắ":"a","ặ":"a","ằ":"a","ẳ":"a","ẵ":"a","ǎ":"a","â":"a","ấ":"a","ậ":"a","ầ":"a","ẩ":"a","ẫ":"a","ä":"a","ǟ":"a","ȧ":"a","ǡ":"a","ạ":"a","ȁ":"a","à":"a","ả":"a","ȃ":"a","ā":"a","ą":"a","ᶏ":"a","ẚ":"a","å":"a","ǻ":"a","ḁ":"a","ⱥ":"a","ã":"a","ꜳ":"aa","æ":"ae","ǽ":"ae","ǣ":"ae","ꜵ":"ao","ꜷ":"au","ꜹ":"av","ꜻ":"av","ꜽ":"ay","ḃ":"b","ḅ":"b","ɓ":"b","ḇ":"b","ᵬ":"b","ᶀ":"b","ƀ":"b","ƃ":"b","ɵ":"o","ć":"c","č":"c","ç":"c","ḉ":"c","ĉ":"c","ɕ":"c","ċ":"c","ƈ":"c","ȼ":"c","ď":"d","ḑ":"d","ḓ":"d","ȡ":"d","ḋ":"d","ḍ":"d","ɗ":"d","ᶑ":"d","ḏ":"d","ᵭ":"d","ᶁ":"d","đ":"d","ɖ":"d","ƌ":"d","ı":"i","ȷ":"j","ɟ":"j","ʄ":"j","ǳ":"dz","ǆ":"dz","é":"e","ĕ":"e","ě":"e","ȩ":"e","ḝ":"e","ê":"e","ế":"e","ệ":"e","ề":"e","ể":"e","ễ":"e","ḙ":"e","ë":"e","ė":"e","ẹ":"e","ȅ":"e","è":"e","ẻ":"e","ȇ":"e","ē":"e","ḗ":"e","ḕ":"e","ⱸ":"e","ę":"e","ᶒ":"e","ɇ":"e","ẽ":"e","ḛ":"e","ꝫ":"et","ḟ":"f","ƒ":"f","ᵮ":"f","ᶂ":"f","ǵ":"g","ğ":"g","ǧ":"g","ģ":"g","ĝ":"g","ġ":"g","ɠ":"g","ḡ":"g","ᶃ":"g","ǥ":"g","ḫ":"h","ȟ":"h","ḩ":"h","ĥ":"h","ⱨ":"h","ḧ":"h","ḣ":"h","ḥ":"h","ɦ":"h","ẖ":"h","ħ":"h","ƕ":"hv","í":"i","ĭ":"i","ǐ":"i","î":"i","ï":"i","ḯ":"i","ị":"i","ȉ":"i","ì":"i","ỉ":"i","ȋ":"i","ī":"i","į":"i","ᶖ":"i","ɨ":"i","ĩ":"i","ḭ":"i","ꝺ":"d","ꝼ":"f","ᵹ":"g","ꞃ":"r","ꞅ":"s","ꞇ":"t","ꝭ":"is","ǰ":"j","ĵ":"j","ʝ":"j","ɉ":"j","ḱ":"k","ǩ":"k","ķ":"k","ⱪ":"k","ꝃ":"k","ḳ":"k","ƙ":"k","ḵ":"k","ᶄ":"k","ꝁ":"k","ꝅ":"k","ĺ":"l","ƚ":"l","ɬ":"l","ľ":"l","ļ":"l","ḽ":"l","ȴ":"l","ḷ":"l","ḹ":"l","ⱡ":"l","ꝉ":"l","ḻ":"l","ŀ":"l","ɫ":"l","ᶅ":"l","ɭ":"l","ł":"l","ǉ":"lj","ſ":"s","ẜ":"s","ẛ":"s","ẝ":"s","ḿ":"m","ṁ":"m","ṃ":"m","ɱ":"m","ᵯ":"m","ᶆ":"m","ń":"n","ň":"n","ņ":"n","ṋ":"n","ȵ":"n","ṅ":"n","ṇ":"n","ǹ":"n","ɲ":"n","ṉ":"n","ƞ":"n","ᵰ":"n","ᶇ":"n","ɳ":"n","ñ":"n","ǌ":"nj","ó":"o","ŏ":"o","ǒ":"o","ô":"o","ố":"o","ộ":"o","ồ":"o","ổ":"o","ỗ":"o","ö":"o","ȫ":"o","ȯ":"o","ȱ":"o","ọ":"o","ő":"o","ȍ":"o","ò":"o","ỏ":"o","ơ":"o","ớ":"o","ợ":"o","ờ":"o","ở":"o","ỡ":"o","ȏ":"o","ꝋ":"o","ꝍ":"o","ⱺ":"o","ō":"o","ṓ":"o","ṑ":"o","ǫ":"o","ǭ":"o","ø":"o","ǿ":"o","õ":"o","ṍ":"o","ṏ":"o","ȭ":"o","ƣ":"oi","ꝏ":"oo","ɛ":"e","ᶓ":"e","ɔ":"o","ᶗ":"o","ȣ":"ou","ṕ":"p","ṗ":"p","ꝓ":"p","ƥ":"p","ᵱ":"p","ᶈ":"p","ꝕ":"p","ᵽ":"p","ꝑ":"p","ꝙ":"q","ʠ":"q","ɋ":"q","ꝗ":"q","ŕ":"r","ř":"r","ŗ":"r","ṙ":"r","ṛ":"r","ṝ":"r","ȑ":"r","ɾ":"r","ᵳ":"r","ȓ":"r","ṟ":"r","ɼ":"r","ᵲ":"r","ᶉ":"r","ɍ":"r","ɽ":"r","ↄ":"c","ꜿ":"c","ɘ":"e","ɿ":"r","ś":"s","ṥ":"s","š":"s","ṧ":"s","ş":"s","ŝ":"s","ș":"s","ṡ":"s","ṣ":"s","ṩ":"s","ʂ":"s","ᵴ":"s","ᶊ":"s","ȿ":"s","ɡ":"g","ᴑ":"o","ᴓ":"o","ᴝ":"u","ť":"t","ţ":"t","ṱ":"t","ț":"t","ȶ":"t","ẗ":"t","ⱦ":"t","ṫ":"t","ṭ":"t","ƭ":"t","ṯ":"t","ᵵ":"t","ƫ":"t","ʈ":"t","ŧ":"t","ᵺ":"th","ɐ":"a","ᴂ":"ae","ǝ":"e","ᵷ":"g","ɥ":"h","ʮ":"h","ʯ":"h","ᴉ":"i","ʞ":"k","ꞁ":"l","ɯ":"m","ɰ":"m","ᴔ":"oe","ɹ":"r","ɻ":"r","ɺ":"r","ⱹ":"r","ʇ":"t","ʌ":"v","ʍ":"w","ʎ":"y","ꜩ":"tz","ú":"u","ŭ":"u","ǔ":"u","û":"u","ṷ":"u","ü":"u","ǘ":"u","ǚ":"u","ǜ":"u","ǖ":"u","ṳ":"u","ụ":"u","ű":"u","ȕ":"u","ù":"u","ủ":"u","ư":"u","ứ":"u","ự":"u","ừ":"u","ử":"u","ữ":"u","ȗ":"u","ū":"u","ṻ":"u","ų":"u","ᶙ":"u","ů":"u","ũ":"u","ṹ":"u","ṵ":"u","ᵫ":"ue","ꝸ":"um","ⱴ":"v","ꝟ":"v","ṿ":"v","ʋ":"v","ᶌ":"v","ⱱ":"v","ṽ":"v","ꝡ":"vy","ẃ":"w","ŵ":"w","ẅ":"w","ẇ":"w","ẉ":"w","ẁ":"w","ⱳ":"w","ẘ":"w","ẍ":"x","ẋ":"x","ᶍ":"x","ý":"y","ŷ":"y","ÿ":"y","ẏ":"y","ỵ":"y","ỳ":"y","ƴ":"y","ỷ":"y","ỿ":"y","ȳ":"y","ẙ":"y","ɏ":"y","ỹ":"y","ź":"z","ž":"z","ẑ":"z","ʑ":"z","ⱬ":"z","ż":"z","ẓ":"z","ȥ":"z","ẕ":"z","ᵶ":"z","ᶎ":"z","ʐ":"z","ƶ":"z","ɀ":"z","ﬀ":"ff","ﬃ":"ffi","ﬄ":"ffl","ﬁ":"fi","ﬂ":"fl","ĳ":"ij","œ":"oe","ﬆ":"st","ₐ":"a","ₑ":"e","ᵢ":"i","ⱼ":"j","ₒ":"o","ᵣ":"r","ᵤ":"u","ᵥ":"v","ₓ":"x"};

    function trim(value) {
        return String(null == value ? '' : value).trim();
    }

    $(function() {
        var generated_name_types = [
            'tab',
            'group',
            'accordion',
            'conditional',
            'post_title',
            'post_content',
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

            $context.find('.cfs-post-title-role-select, .cfs-post-native-role-select, .cfs-shortcode-role-select, .cfs-extra-display-role-select').each(function() {
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
                trim($item.children('.field').find('.field_meta .field_type').first().text());
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
            var structureClasses = 'cfs-structure-tab cfs-structure-loop cfs-structure-group cfs-structure-accordion cfs-structure-conditional cfs-tab-parent cfs-tab-has-range cfs-tab-has-empty-warning cfs-tab-range cfs-tab-range-start cfs-tab-range-end';

            $('#cfs_fields .cfs-structure-child-warning').remove();

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
                $(this).children('li.cfs-structure-tab').each(function() {
                    var $tab = $(this);
                    var $range = $tab.nextUntil('li.cfs-structure-tab').filter('.cfs-tab-range');

                    $tab.addClass('cfs-tab-parent');

                    if ($range.length) {
                        $tab.addClass('cfs-tab-has-range');
                        $range.first().addClass('cfs-tab-range-start');
                        $range.last().addClass('cfs-tab-range-end');
                    }
                });
            });

            refresh_structure_group_warnings($context);
        }

        function is_countable_structure_child($item) {
            return !$item.hasClass('ui-sortable-helper') &&
                !$item.hasClass('ui-sortable-placeholder') &&
                !$item.hasClass('cfs-empty-tab-drop-target') &&
                !$item.hasClass('cfs-structure-child-warning');
        }

        function refresh_structure_group_warnings($context) {
            var warning_configs = {
                tab: {
                    min: 1,
                    key: 'tab_child_count_warning',
                    fallback: 'Add at least one field to this tab group.'
                },
                group: {
                    min: 2,
                    key: 'horizontal_group_child_count_warning',
                    fallback: 'Add two or more fields to this horizontal group.'
                },
                loop: {
                    min: 1,
                    key: 'loop_child_count_warning',
                    fallback: 'Add at least one field to this loop group.'
                },
                accordion: {
                    min: 1,
                    key: 'accordion_child_count_warning',
                    fallback: 'Add at least one field to this accordion group.'
                },
                conditional: {
                    min: 2,
                    key: 'conditional_group_two_fields_warning',
                    fallback: 'Create two or more conditions, then add fields to each condition.'
                }
            };
            var $scope = $context && $context.length ? $context : $('#cfs_fields');
            var group_selector = 'li.cfs-structure-tab, li.cfs-structure-loop, li.cfs-structure-group, li.cfs-structure-accordion, li.cfs-structure-conditional';
            var $groups = $scope.filter(group_selector).add($scope.find(group_selector));

            $scope.filter('.cfs-structure-child-warning').remove();
            $scope.find('.cfs-structure-child-warning').remove();
            $scope.filter('li').next('.cfs-structure-child-warning').remove();

            function get_child_count($group, type, $child_list) {
                var child_count = 0;

                if ($child_list.length) {
                    child_count += $child_list.children('li').filter(function() {
                        return is_countable_structure_child($(this));
                    }).length;
                }

                if ('tab' === type) {
                    child_count += $group.nextUntil('li.cfs-structure-tab').filter(function() {
                        return is_countable_structure_child($(this)) && $(this).hasClass('cfs-tab-range');
                    }).length;
                }

                return child_count;
            }

            function get_warning_text(type, warning_config, child_count) {
                if ('conditional' === type && 1 === child_count) {
                    return message(
                        'conditional_group_one_more_field_warning',
                        'Add fields to each empty condition.'
                    );
                }

                return message(warning_config.key, warning_config.fallback);
            }

            function add_group_warning($group, type, text, $target_list) {
                var $warning = $('<li></li>', {
                    'class': 'cfs-structure-child-warning cfs-structure-child-warning-' + type,
                    text: text
                });

                if ('tab' === type) {
                    $group.addClass('cfs-tab-has-empty-warning');
                    $warning
                        .addClass('cfs-tab-range cfs-tab-range-start cfs-tab-range-end')
                        .insertAfter($group);
                    return;
                }

                if ($target_list && $target_list.length) {
                    $warning.prependTo($target_list);
                }
            }

            $groups.each(function() {
                var $group = $(this);
                var type = get_item_type($group);
                var warning_config = warning_configs[type];
                var $child_list = $group.children('ul').first();
                var child_count;
                var branch_state;
                var choices;

                if (!warning_config) {
                    return;
                }

                if ('conditional' === type) {
                    choices = get_conditional_choices($group);

                    if (1 > choices.length) {
                        add_group_warning(
                            $group,
                            type,
                            message('conditional_group_no_conditions_warning', 'Set conditions and add fields to each condition.'),
                            get_conditional_primary_list($group)
                        );
                        return;
                    }

                    if (2 > choices.length) {
                        add_group_warning(
                            $group,
                            type,
                            message(warning_config.key, warning_config.fallback),
                            get_conditional_primary_list($group)
                        );
                        return;
                    }

                    refresh_conditional_branch_lists($group);
                    branch_state = get_conditional_branch_state($group);

                    if (0 < branch_state.empty_count) {
                        add_group_warning(
                            $group,
                            type,
                            branch_state.total_count ? get_warning_text(type, warning_config, 1) : message('conditional_child_count_warning', 'Add fields to each condition.'),
                            get_conditional_primary_list($group)
                        );
                    }

                    return;
                }

                child_count = get_child_count($group, type, $child_list);

                if (warning_config.min <= child_count) {
                    return;
                }

                add_group_warning($group, type, get_warning_text(type, warning_config, child_count), $child_list);
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

        function format_message(key, fallback) {
            var text = message(key, fallback);
            var args = Array.prototype.slice.call(arguments, 2);

            $.each(args, function(index, value) {
                text = text.replace('%' + (index + 1) + '$s', value);
                text = text.replace('%s', value);
            });

            return text;
        }

        function refresh_field_toggle_state($field) {
            var is_open = $field.hasClass('form_open');
            var title = is_open ?
                message('close_field_settings', 'Close field settings') :
                message('open_field_settings', 'Open field settings');

            $field.find('.field_meta .field_type a.cfs_edit_field')
                .attr('aria-expanded', is_open ? 'true' : 'false')
                .attr('title', title);
        }

        function refresh_field_toggle_states($context) {
            $context.find('.field').addBack('.field').each(function() {
                refresh_field_toggle_state($(this));
            });
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
                value = trim($generated.val());
                displayValue = value || trim($display.attr('data-current-name'));
                $field.addClass('cfs-field-generated-name');
                $editable.prop('disabled', true).hide();
                $generated.prop('disabled', false);
                $display.text(displayValue || pendingLabel).prop('hidden', false).show();
                $field.find('.field_meta .field_name').first().text(displayValue || pendingLabel);
                return;
            }

            value = trim($editable.val());
            if (!value && trim($generated.val())) {
                value = trim($generated.val());
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
                var name = trim($input.val());
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

        function reveal_field_control($control) {
            var $field = $control.closest('.field');

            if (!$control.length) {
                return;
            }

            if (!$field.hasClass('form_open')) {
                $field.addClass('form_open');
                $field.find('.field_form').show();
            }

            $('html, body').animate({
                scrollTop: Math.max(0, $field.offset().top - 80)
            }, 200);
            $control.trigger('focus');
        }

        function reveal_duplicate_field_name() {
            reveal_field_control($('#cfs_fields .cfs-duplicate-field-name-input').first());
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

        function clear_outdent_targets() {
            $('#cfs_fields .cfs-outdent-target')
                .removeClass('cfs-outdent-target cfs-outdent-visible')
                .removeAttr('data-cfs-drop-label');
            $('#cfs_fields .cfs-outdent-tab-target')
                .removeClass('cfs-outdent-tab-target cfs-outdent-visible')
                .removeAttr('data-cfs-drop-label');
            $('#cfs_fields .cfs-outdent-tab-exit-target')
                .removeClass('cfs-outdent-tab-exit-target cfs-outdent-visible')
                .removeAttr('data-cfs-drop-label');
        }

        function update_outdent_target_visibility(ui, event) {
            var $fields = $('#cfs_fields');
            var $targets = $fields.find('.cfs-outdent-target, .cfs-outdent-tab-target, .cfs-outdent-tab-exit-target');
            var pointer_x = event && 'number' === typeof event.pageX ? event.pageX : null;
            var pointer_y = event && 'number' === typeof event.pageY ? event.pageY : null;
            var previous_y = $fields.data('cfs-drag-y');
            var moving_down = null;
            var $best = $();
            var best_score = Infinity;

            $targets.removeClass('cfs-outdent-visible');

            if (null === pointer_x || null === pointer_y) {
                return;
            }

            if ('number' === typeof previous_y) {
                if (pointer_y > previous_y + 2) {
                    moving_down = true;
                }
                else if (pointer_y < previous_y - 2) {
                    moving_down = false;
                }
            }
            $fields.data('cfs-drag-y', pointer_y);

            $targets.each(function() {
                var $target = $(this);
                var offset = $target.offset();
                var target_y = 0;
                var target_left = 0;
                var target_right = 0;
                var distance = 0;
                var score = 0;

                if (!offset) {
                    return;
                }

                target_y = offset.top;
                target_left = offset.left;
                target_right = target_left + $target.outerWidth();

                if (!$target.is('ul')) {
                    target_y += $target.outerHeight();
                }

                if (true === moving_down && target_y < pointer_y - 36) {
                    return;
                }

                if (false === moving_down && target_y > pointer_y + 36) {
                    return;
                }

                if (pointer_x < target_left - 120 || pointer_x > target_right + 120) {
                    return;
                }

                if (
                    ($target.hasClass('cfs-outdent-target') || $target.hasClass('cfs-outdent-tab-target')) &&
                    pointer_x > target_left + 170
                ) {
                    return;
                }

                distance = Math.abs(pointer_y - target_y);

                if (80 < distance) {
                    return;
                }

                score = distance + ($target.hasClass('cfs-outdent-tab-exit-target') ? 14 : 0);

                if (score < best_score) {
                    best_score = score;
                    $best = $target;
                }
            });

            if ($best.length) {
                $best.addClass('cfs-outdent-visible');
            }
        }

        function mark_outdent_targets($item) {
            var $root = $('ul.fields').first();
            var $current_list = $item.parent('ul');
            var $current_parent = $current_list.closest('li');
            var $tab_scope = $item.hasClass('cfs-tab-range') ? $item : $item.closest('li.cfs-tab-range');
            var guard = 0;

            clear_outdent_targets();

            while ($current_parent.length && 5 > guard) {
                var $target_list = $current_parent.parent('ul');
                var $target_container = $target_list.closest('li');

                if ($target_container.length && -1 !== $.inArray(get_item_type($target_container), ['loop', 'group', 'accordion', 'conditional'])) {
                    $target_list.addClass('cfs-outdent-target');
                }
                else if ($target_list.is($root) && $current_parent.hasClass('cfs-tab-range')) {
                    $current_parent.addClass('cfs-outdent-tab-target');
                }

                $current_list = $target_list;
                $current_parent = $current_list.closest('li');
                guard++;
            }

            if ($tab_scope.length) {
                var $tab_start = $tab_scope.prevAll('li.cfs-structure-tab').first();
                var $tab_range = $tab_start.length ?
                    $tab_start.nextUntil('li.cfs-structure-tab').filter('li.cfs-tab-range') :
                    $tab_scope;
                var $tab_end = $tab_range.not($item).last();

                if (!$tab_end.length) {
                    $tab_end = $tab_range.last();
                }

                $tab_end
                    .addClass('cfs-outdent-tab-exit-target');
            }
        }

        function update_sortable_placeholder(ui, event) {
            if (!ui || !ui.placeholder || !ui.placeholder.length) {
                return;
            }

            var $placeholder = ui.placeholder;
            var $root = $('ul.fields').first();
            var force_outside_tabs = null;

            $('#cfs_fields .cfs-tab-range-placeholder-open').removeClass('cfs-tab-range-placeholder-open');
            $placeholder
                .removeAttr('data-cfs-drop-label')
                .removeClass('cfs-placeholder-tab-range cfs-placeholder-tab-range-end');
            ui.item.removeData('cfs-drop-outside-tabs');

            if ($placeholder.parent('ul').is($root)) {
                var $previous = $placeholder.prevAll('li:not(.ui-sortable-helper)').first();
                var $next = $placeholder.nextAll('li:not(.ui-sortable-helper)').first();
                var pointer_y = event && 'number' === typeof event.pageY ? event.pageY : null;
                var previous_offset = $previous.length ? $previous.offset() : null;
                var after_tab_end = $previous.hasClass('cfs-tab-range-end');

                if (after_tab_end) {
                    force_outside_tabs = previous_offset && null !== pointer_y &&
                        pointer_y > previous_offset.top + $previous.outerHeight() + 34;
                    ui.item.data('cfs-drop-outside-tabs', force_outside_tabs);
                }

                var is_inside_tab_range = (
                    ($previous.hasClass('cfs-tab-range') && !$previous.hasClass('cfs-tab-range-end')) ||
                    (after_tab_end && !force_outside_tabs) ||
                    $next.hasClass('cfs-tab-range') ||
                    $previous.hasClass('cfs-empty-tab-drop-target') ||
                    $next.hasClass('cfs-empty-tab-drop-target') ||
                    ($previous.hasClass('cfs-tab-parent') && $previous.hasClass('cfs-tab-has-range'))
                );

                if (is_inside_tab_range) {
                    $placeholder.addClass('cfs-placeholder-tab-range');

                    if (after_tab_end && !force_outside_tabs) {
                        $previous.addClass('cfs-tab-range-placeholder-open');
                        $placeholder.addClass('cfs-placeholder-tab-range-end');
                    }
                }
            }
        }

        function remove_empty_tab_drop_spacers() {
            $('#cfs_fields .cfs-empty-tab-drop-target').remove();
            $('#cfs_fields .cfs-tab-range-placeholder-open').removeClass('cfs-tab-range-placeholder-open');
        }

        function add_empty_tab_drop_spacers($dragged_item) {
            var $root = $('ul.fields').first();

            remove_empty_tab_drop_spacers();

            if ('tab' == get_item_type($dragged_item)) {
                return;
            }

            $root.children('li.cfs-structure-tab').each(function() {
                var $tab = $(this);
                var $range = $tab.nextUntil('li.cfs-structure-tab').filter(function() {
                    var $item = $(this);

                    return !$item.is($dragged_item) &&
                        !$item.hasClass('ui-sortable-helper') &&
                        !$item.hasClass('cfs-empty-tab-drop-target') &&
                        !$item.hasClass('cfs-placeholder-tab-range') &&
                        !is_outside_tabs($item);
                });

                if (0 < $range.length) {
                    return;
                }

                $('<li class="cfs-empty-tab-drop-target" aria-hidden="true"></li>').insertAfter($tab);
            });

            if ($root.data('ui-sortable')) {
                $root.sortable('refresh');
                $root.sortable('refreshPositions');
            }
        }

        function should_place_outside_current_tab($item) {
            var $root = $('ul.fields').first();
            var $previous = $item.prevAll('li').first();
            var forced_outside_tabs = $item.data('cfs-drop-outside-tabs');

            if (!$item.parent('ul').is($root) || 'tab' == get_item_type($item)) {
                return false;
            }

            if ('boolean' === typeof forced_outside_tabs) {
                return forced_outside_tabs;
            }

            return $previous.hasClass('cfs-tab-range-end') ||
                $previous.hasClass('cfs-outdent-tab-exit-target') ||
                is_outside_tabs($previous);
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

        function conditional_branch_unnamed_label(index) {
            var base = message('unnamed_condition', 'Unnamed condition');

            return base + ' ' + (index + 1);
        }

        function parse_conditional_choices(value) {
            var choices = [];

            $.each(String(value || '').split(/\r?\n/), function(index, line) {
                line = trim(line);
                if (!line) {
                    return;
                }

                var separator = line.indexOf(' : ');
                choices.push({
                    value: trim(-1 === separator ? line : line.substring(0, separator)),
                    label: trim(-1 === separator ? line : line.substring(separator + 3))
                });
            });

            return choices;
        }

        function conditional_choice_label(choice, index) {
            return trim(choice.label) || trim(choice.value) || conditional_branch_unnamed_label(index);
        }

        function conditional_choice_drop_label(label) {
            return format_message(
                'conditional_branch_drop_label',
                'Condition "%s"',
                label
            );
        }

        function is_saved_conditional_item($item) {
            return 'conditional' == get_item_type($item) &&
                (
                    0 < parseInt($item.children('.field').find('.field_id').first().val(), 10) ||
                    $item.hasClass('cfs-conditional-branches-ready')
                );
        }

        function get_conditional_choices($item) {
            return parse_conditional_choices($item.children('.field').find('.cfs-conditional-choices').first().val());
        }

        function get_conditional_primary_list($conditional) {
            var $primary = $conditional.children('ul').not('.cfs-conditional-branch-list').first();

            if (1 > $primary.length) {
                $primary = $('<ul></ul>').prependTo($conditional);
            }

            $primary.addClass('cfs-conditional-unassigned-list');

            return $primary;
        }

        function find_conditional_branch_list($conditional, value) {
            return $conditional.children('ul.cfs-conditional-branch-list').filter(function() {
                return String($(this).attr('data-cfs-conditional-value') || '') === String(value);
            }).first();
        }

        function set_conditional_value_for_item($item, value) {
            var $select = $item.children('.field').find('.cfs-conditional-value').first();

            if (1 > $select.length) {
                return;
            }

            value = String(value || '');

            if (value && 1 > $select.find('option').filter(function() {
                return String($(this).attr('value') || '') === value;
            }).length) {
                $select.append($('<option>', { value: value, text: value + ' (' + value + ')' }));
            }

            $select.val(value);
        }

        function get_conditional_value_for_item($item) {
            return String($item.children('.field').find('.cfs-conditional-value').first().val() || '');
        }

        function get_conditional_branch_state($conditional) {
            var state = {
                branches: [],
                empty_count: 0,
                total_count: 0
            };

            $.each(get_conditional_choices($conditional), function(index, choice) {
                var value = String(choice.value || '');
                var $branch;
                var count = 0;

                if (!value) {
                    return;
                }

                $branch = find_conditional_branch_list($conditional, value);

                if ($branch.length) {
                    count = $branch.children('li').filter(function() {
                        return is_countable_structure_child($(this));
                    }).length;
                }

                state.branches.push({
                    value: value,
                    label: conditional_choice_label(choice, index),
                    list: $branch,
                    count: count
                });
                state.total_count += count;

                if (1 > count) {
                    state.empty_count++;
                }
            });

            return state;
        }

        function auto_assign_unassigned_conditional_children($context) {
            var changed = false;
            var $scope = $context && $context.length ? $context : $('ul.fields');
            var $conditionals = $scope.find('li').addBack('li').filter(function() {
                return 'conditional' == get_item_type($(this));
            });

            $conditionals.each(function() {
                var $conditional = $(this);
                var choices = get_conditional_choices($conditional);
                var $primary;
                var branch_state;
                var empty_branches;

                if (1 > choices.length) {
                    return;
                }

                refresh_conditional_branch_lists($conditional);
                $primary = get_conditional_primary_list($conditional);
                branch_state = get_conditional_branch_state($conditional);
                empty_branches = $.grep(branch_state.branches, function(branch) {
                    return branch.list && branch.list.length && 1 > branch.count;
                });

                if (1 > empty_branches.length) {
                    return;
                }

                $primary.children('li').filter(function() {
                    return is_countable_structure_child($(this));
                }).each(function() {
                    var $child = $(this);
                    var current = get_conditional_value_for_item($child);
                    var branch = empty_branches.shift();

                    if (current && find_conditional_branch_list($conditional, current).length) {
                        return;
                    }

                    if (!branch) {
                        return false;
                    }

                    set_conditional_value_for_item($child, branch.value);
                    $child.appendTo(branch.list);
                    branch.count++;
                    changed = true;
                });

                if (changed) {
                    refresh_conditional_branch_lists($conditional);
                }
            });

            return changed;
        }

        function assign_conditional_branch_drop($item) {
            var $list = $item.parent('ul');

            if ($list.hasClass('cfs-conditional-branch-list')) {
                set_conditional_value_for_item($item, $list.attr('data-cfs-conditional-value') || '');
                return;
            }

            if ($list.hasClass('cfs-conditional-unassigned-list') || 'conditional' == get_item_type(get_parent_item($item))) {
                set_conditional_value_for_item($item, '');
            }
        }

        function remove_conditional_branch_lists($conditional) {
            var $primary = get_conditional_primary_list($conditional);

            $conditional.children('ul.cfs-conditional-branch-list').each(function() {
                $(this).children('li').appendTo($primary);
                $(this).remove();
            });

            $primary.removeClass('cfs-conditional-has-branches');
        }

        function refresh_conditional_branch_lists($conditional) {
            var choices = get_conditional_choices($conditional);
            var $primary = get_conditional_primary_list($conditional);
            var values = {};

            if (!is_saved_conditional_item($conditional) || 1 > choices.length) {
                remove_conditional_branch_lists($conditional);
                return;
            }

            $primary.addClass('cfs-conditional-has-branches');

            $.each(choices, function(index, choice) {
                var value = String(choice.value || '');
                var label = conditional_choice_label(choice, index);
                var $branch = find_conditional_branch_list($conditional, value);

                if (!value) {
                    return;
                }

                values[value] = true;

                if (1 > $branch.length) {
                    $branch = $('<ul></ul>', {
                        'class': 'cfs-conditional-branch-list',
                        'data-cfs-conditional-value': value
                    }).appendTo($conditional);
                }

                $branch
                    .attr('data-cfs-conditional-label', label)
                    .attr('data-cfs-conditional-drop-label', conditional_choice_drop_label(label));
            });

            $conditional.children('ul.cfs-conditional-branch-list').each(function() {
                var $branch = $(this);
                var value = String($branch.attr('data-cfs-conditional-value') || '');

                if (!values[value]) {
                    $branch.children('li').appendTo($primary);
                    $branch.remove();
                }
            });

            $conditional.children('ul').children('li').filter(function() {
                return is_countable_structure_child($(this));
            }).each(function() {
                var $child = $(this);
                var value = get_conditional_value_for_item($child);
                var $branch = value ? find_conditional_branch_list($conditional, value) : $();

                if ($branch.length && !$child.parent('ul').is($branch)) {
                    $child.appendTo($branch);
                }
                else if (!$branch.length && !$child.parent('ul').is($primary)) {
                    $child.appendTo($primary);
                }
            });

            $conditional.children('ul.cfs-conditional-branch-list').each(function() {
                $(this).toggleClass('cfs-conditional-branch-has-fields', 0 < $(this).children('li').filter(function() {
                    return is_countable_structure_child($(this));
                }).length);
            });

            init_sortables($conditional.children('ul'));
        }

        function refresh_conditional_assignments($context) {
            var $items = $context.find('li').addBack('li');
            var $conditional_items = $items.filter(function() {
                return 'conditional' == get_item_type($(this));
            });

            $items.each(function() {
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

            $conditional_items.each(function() {
                refresh_conditional_branch_lists($(this));
            });

            $context.find('.cfs-conditional-display-type').each(function() {
                $(this).closest('.field').find('.cfs-conditional-default-row').toggle('radio' == $(this).val());
            });

            refresh_structure_group_warnings($context);
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
            $('.cfs-field-action-menu').removeClass('cfs-field-action-menu-above');
            $('#cfs_fields .cfs-actions-open, #cfs_fields .cfs-actions-open-ancestor')
                .removeClass('cfs-actions-open cfs-actions-open-ancestor');
        }

        function position_field_action_menu($menu) {
            var $wrapper = $menu.closest('.cfs-field-action-menu');
            var menu = $menu.get(0);
            var field = $wrapper.closest('.field').get(0);
            var viewport_bottom = window.innerHeight || document.documentElement.clientHeight || 0;
            var menu_rect;
            var field_rect;

            $wrapper.removeClass('cfs-field-action-menu-above');

            if (!menu || $menu.prop('hidden')) {
                return;
            }

            menu_rect = menu.getBoundingClientRect();
            field_rect = field ? field.getBoundingClientRect() : null;

            if (
                menu_rect.bottom > viewport_bottom - 16 ||
                (field_rect && menu_rect.bottom > field_rect.bottom - 8)
            ) {
                $wrapper.addClass('cfs-field-action-menu-above');
            }
        }

        function mark_field_action_menu_scope($menu) {
            var $field = $menu.closest('.field');
            var $item = $field.closest('li');
            var $ancestor_items = $item.parents('li');

            $field.add($item).addClass('cfs-actions-open');
            $ancestor_items.addClass('cfs-actions-open-ancestor');
            $ancestor_items.children('.field').addClass('cfs-actions-open-ancestor');
            position_field_action_menu($menu);
        }

        function collect_reserved_field_names() {
            var reserved = {};

            $('ul.fields li').each(function() {
                var $field = $(this).children('.field');
                var name = trim(
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
            var base = trim(name);
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

        function collect_duplicated_conditional_values($item) {
            var values = [];

            $item.add($item.find('li')).each(function() {
                values.push(get_conditional_value_for_item($(this)));
            });

            return values;
        }

        function restore_duplicated_conditional_values($item, values) {
            var index = 0;

            $item.add($item.find('li')).each(function() {
                set_conditional_value_for_item($(this), values[index] || '');
                index++;
            });
        }

        function mark_duplicated_conditional_branches($item) {
            $item.add($item.find('li')).each(function() {
                var $conditional = $(this);

                if ('conditional' != get_item_type($conditional)) {
                    return;
                }

                if (
                    0 < $conditional.children('ul.cfs-conditional-branch-list').length ||
                    0 < parseInt($conditional.children('.field').find('.field_id').first().val(), 10)
                ) {
                    $conditional.addClass('cfs-conditional-branches-ready');
                }
            });
        }

        function prepare_duplicated_field_item($item, reserved_names, key_map) {
            var $field = $item.children('.field');
            var type = get_item_type($item);
            var old_key = String($field.find('.field_key').first().val() || '');
            var old_parent_key = String($field.find('.parent_key').first().val() || '');
            var key = CFS.field_index;
            var generated_name = uses_generated_field_name(type);
            var $editable = $field.find('.field_form .field_name input.cfs-editable-field-name').first();
            var $generated = $field.find('.field_form .field_name input.cfs-generated-field-name-input').first();
            var source_name = trim($editable.val() || $generated.val());
            var new_name = generated_name ? '' : make_unique_copy_name(source_name, reserved_names);

            if (old_key) {
                key_map[old_key] = String(key);
            }

            $item.data('cfs-duplicated-old-parent-key', old_parent_key);

            CFS.field_index = CFS.field_index + 1;
            $field.find('.field_id').first().val(0).attr('value', 0);
            $field.find('.field_key').first().val(key).attr('value', key);
            $field.find('.parent_id').first().val(0).attr('value', 0);
            $field.find('.parent_key').first().val('').attr('value', '');

            $editable.val(new_name).attr('value', new_name);
            $generated.val(new_name).attr('value', new_name);
            refresh_field_name_mode($item);
        }

        function relink_duplicated_parent_keys($item, key_map) {
            $item.add($item.find('li')).each(function() {
                var $duplicated = $(this);
                var old_parent_key = String($duplicated.data('cfs-duplicated-old-parent-key') || '');
                var parent_key = old_parent_key && key_map[old_parent_key] ? key_map[old_parent_key] : '';

                $duplicated.children('.field').find('.parent_id').first().val(0).attr('value', 0);
                $duplicated.children('.field').find('.parent_key').first().val(parent_key).attr('value', parent_key);
                $duplicated.removeData('cfs-duplicated-old-parent-key');
            });
        }

        function duplicate_field_item($item) {
            var conditional_values = collect_duplicated_conditional_values($item);
            var $clone = $item.clone(false, false);
            var reserved_names = collect_reserved_field_names();
            var key_map = {};

            mark_duplicated_conditional_branches($clone);
            clean_cloned_field_ui($clone);

            $clone.add($clone.find('li')).each(function() {
                prepare_duplicated_field_item($(this), reserved_names, key_map);
            });

            relink_duplicated_parent_keys($clone, key_map);
            restore_duplicated_conditional_values($clone, conditional_values);

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
                        $('#cfs_fields').addClass('cfs-is-dragging');
                        $('#cfs_fields').removeData('cfs-drag-y');
                        $('#cfs_fields ul.fields, #cfs_fields ul.fields ul').addClass('cfs-drop-target');
                        add_empty_tab_drop_spacers(ui.item);
                        update_sortable_placeholder(ui, event);
                        mark_outdent_targets(ui.item);
                        update_outdent_target_visibility(ui, event);
                    },
                    sort: function(event, ui) {
                        update_sortable_placeholder(ui, event);
                        update_outdent_target_visibility(ui, event);
                    },
                    change: function(event, ui) {
                        update_sortable_placeholder(ui, event);
                        update_outdent_target_visibility(ui, event);
                    },
                    beforeStop: function(event, ui) {
                        var $children = ui.item.data('cfs-drag-children');

                        if ($children && $children.length) {
                            ui.item.append($children);
                        }
                    },
                    stop: function(event, ui) {
                        var placed_outside_current_tab = should_place_outside_current_tab(ui.item);

                        ui.item.removeData('cfs-drag-children cfs-drop-outside-tabs').removeClass('cfs-dragging-field');
                        $('#cfs_fields').removeClass('cfs-is-dragging');
                        $('#cfs_fields').removeData('cfs-drag-y');
                        $('#cfs_fields ul.fields, #cfs_fields ul.fields ul').removeClass('cfs-drop-target cfs-drop-target-active');
                        clear_outdent_targets();
                        remove_empty_tab_drop_spacers();
                        zebra_stripes();
                        maybe_outdent_dragged_item(ui.item, event);
                        assign_conditional_branch_drop(ui.item);
                        enforce_group_child_rules(ui.item);

                        if (1 > get_parent_item(ui.item).length && 'tab' != get_item_type(ui.item)) {
                            set_outside_tabs(ui.item, placed_outside_current_tab);
                        }

                        ensure_child_containers($('ul.fields'));
                        init_sortables($('ul.fields, ul.fields ul'));
                        sync_parent_ids();
                        update_add_field_button_labels($('ul.fields'));
                        refresh_structure_markers($('ul.fields'));
                        refresh_conditional_assignments($('ul.fields'));
                        if (auto_assign_unassigned_conditional_children($('ul.fields'))) {
                            sync_parent_ids();
                            refresh_conditional_assignments($('ul.fields'));
                            refresh_structure_markers($('ul.fields'));
                        }
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
        refresh_field_toggle_states($(document));

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
            if (auto_assign_unassigned_conditional_children($('ul.fields'))) {
                sync_parent_ids();
                refresh_conditional_assignments($('ul.fields'));
                refresh_structure_markers($('ul.fields'));
            }
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
            assign_conditional_branch_drop($new_field);
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
            if (auto_assign_unassigned_conditional_children($('ul.fields'))) {
                sync_parent_ids();
                refresh_conditional_assignments($('ul.fields'));
                refresh_structure_markers($('ul.fields'));
            }
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

            if (opening) {
                mark_field_action_menu_scope($menu);
            }
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
            refresh_field_toggle_state(field);
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

            var $field_type_toggle = $(this).closest('.field').find('.field_meta .field_type a').first();
            var $field_type_text = $field_type_toggle.find('.cfs-field-type-text').first();

            if (0 < $field_type_text.length) {
                $field_type_text.text(type);
            }
            else {
                $field_type_toggle.text(type);
            }
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
            var $conditional = $(this).closest('li');

            refresh_conditional_assignments($conditional);
            if (auto_assign_unassigned_conditional_children($conditional)) {
                sync_parent_ids();
                refresh_conditional_assignments($conditional);
                refresh_structure_markers($('ul.fields'));
            }
        });

        $(document).on('change', '.cfs-conditional-value', function() {
            var $item = $(this).closest('li');
            var $parent = get_parent_item($item);

            if ($parent.length && 'conditional' == get_item_type($parent)) {
                refresh_conditional_assignments($parent);
            }
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

            refresh_conditional_assignments($('ul.fields'));

            if (auto_assign_unassigned_conditional_children($('ul.fields'))) {
                sync_parent_ids();
                refresh_conditional_assignments($('ul.fields'));
                refresh_structure_markers($('ul.fields'));
            }

            remove_empty_tab_drop_spacers();

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
                val = trim(val).toLowerCase();
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
                var label = trim(val);

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
