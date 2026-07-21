(function($) {
    'use strict';

    function trim(value) {
        return String(null == value ? '' : value).trim();
    }

    function message(key, fallback) {
        if (window.AtshiftCFSWpCategory && AtshiftCFSWpCategory.messages && AtshiftCFSWpCategory.messages[key]) {
            return AtshiftCFSWpCategory.messages[key];
        }

        return fallback;
    }

    function getAjaxUrl() {
        if (window.AtshiftCFSWpCategory && AtshiftCFSWpCategory.ajaxUrl) {
            return AtshiftCFSWpCategory.ajaxUrl;
        }

        if (window.ajaxurl) {
            return window.ajaxurl;
        }

        return '';
    }

    function refreshCategoryState($list) {
        $list.find('.cfs-wp-category-item').each(function() {
            var $item = $(this);
            var checked = $item.children('label').find('input[type="checkbox"]').prop('checked');
            $item.toggleClass('is-selected', checked);
        });
    }

    function getCategoryInput($list, termId) {
        var termIdString = String(termId);

        return $list.find('input[type="checkbox"]').filter(function() {
            return $(this).val() === termIdString;
        }).first();
    }

    function defaultCategoryHasCheckedDescendant($list, defaultCategory) {
        var $defaultInput = getCategoryInput($list, defaultCategory);

        if (!$defaultInput.length) {
            return false;
        }

        return 0 < $defaultInput.closest('.cfs-wp-category-item').children('ul').find('input[type="checkbox"]:checked').length;
    }

    function shouldKeepDefaultCategory($list, defaultCategory) {
        var autoSelectsRelatedTerms = '1' === $list.attr('data-auto-select-children') || '1' === $list.attr('data-auto-select-parents');

        return autoSelectsRelatedTerms && defaultCategoryHasCheckedDescendant($list, defaultCategory);
    }

    function applyCategoryFilter($control) {
        var query = trim($control.find('.cfs-wp-category-search').val()).toLowerCase();
        var selectedOnly = $control.find('.cfs-wp-category-selected-only-toggle').prop('checked');
        var $items = $control.find('.cfs-wp-category-item');

        $items.removeClass('is-filter-hidden is-filter-match');

        $items.each(function() {
            var $item = $(this);
            var name = ($item.attr('data-term-name') || '').toLowerCase();
            var selfMatchesQuery = '' === query || -1 < name.indexOf(query);
            var childMatchesQuery = '' !== query && 0 < $item.children('ul').find('.cfs-wp-category-item').filter(function() {
                return -1 < (($(this).attr('data-term-name') || '').toLowerCase()).indexOf(query);
            }).length;
            var selfSelected = $item.children('label').find('input[type="checkbox"]').prop('checked');
            var childSelected = 0 < $item.children('ul').find('input[type="checkbox"]:checked').length;
            var matchesQuery = selfMatchesQuery || childMatchesQuery;
            var matchesSelected = !selectedOnly || selfSelected || childSelected;

            if (matchesQuery && matchesSelected) {
                $item.addClass('is-filter-match');
                $item.parents('.cfs-wp-category-item').addClass('is-filter-match');
            }
        });

        if ('' !== query || selectedOnly) {
            $items.not('.is-filter-match').addClass('is-filter-hidden');
        }
    }

    function getItemDepth($item) {
        var depth = 0;

        $.each(($item.attr('class') || '').split(/\s+/), function(index, className) {
            var match = /^depth-(\d+)$/.exec(className);

            if (match) {
                depth = parseInt(match[1], 10) || 0;
            }
        });

        return depth;
    }

    function ensureRootList($list) {
        var $root = $list.children('ul.cfs-wp-category-root').first();

        if (!$root.length) {
            $root = $('<ul></ul>', {
                'class': 'cfs-wp-category-root'
            }).appendTo($list);
        }

        return $root;
    }

    function createCategoryItem(term, inputName, checked, depth, defaultCategory) {
        var termId = String(term.id);
        var $item = $('<li></li>', {
            'class': 'cfs-wp-category-item depth-' + Math.min(4, depth),
            'data-term-name': String(term.name || '').toLowerCase()
        });
        var $label = $('<label></label>');
        var $input = $('<input>', {
            type: 'checkbox',
            name: inputName + '[]',
            value: termId
        }).prop('checked', !!checked);

        if (defaultCategory && String(defaultCategory) === termId) {
            $item.addClass('is-default-category');
        }

        $label.append($input).append(' ').append(document.createTextNode(term.name || ''));
        $item.append($label);

        return $item;
    }

    function appendParentOption($control, term, depth) {
        var prefix = '';
        var index;

        for (index = 0; index < depth; index++) {
            prefix += '— ';
        }

        $control.find('.cfs-wp-category-add-parent').append(
            $('<option></option>', {
                value: term.id,
                text: prefix + (term.name || '')
            }).attr('data-depth', depth)
        );
    }

    function insertAddedCategory($control, term) {
        var $list = $control.find('.cfs-wp-category-list').first();
        var inputName = $list.attr('data-input-name') || '';
        var defaultCategory = $list.attr('data-default-category');
        var parentId = parseInt(term.parent, 10) || 0;
        var $parentInput;
        var $parentItem;
        var $container;
        var depth = 0;
        var $item;

        if (!inputName || !$list.length) {
            return;
        }

        if (0 < parentId) {
            $parentInput = getCategoryInput($list, parentId);
            $parentItem = $parentInput.closest('.cfs-wp-category-item');

            if ($parentItem.length) {
                $container = $parentItem.children('ul.children').first();

                if (!$container.length) {
                    $container = $('<ul></ul>', {
                        'class': 'children'
                    }).appendTo($parentItem);
                }

                $parentItem.addClass('has-children');
                depth = getItemDepth($parentItem) + 1;
            }
        }

        if (!$container || !$container.length) {
            $container = ensureRootList($list);
            depth = 0;
        }

        $item = createCategoryItem(term, inputName, true, depth, defaultCategory);
        $container.append($item);
        $control.find('.cfs-wp-category-empty').remove();
        appendParentOption($control, term, depth);
        $item.children('label').find('input[type="checkbox"]').trigger('change');
    }

    function initCategoryControls($context) {
        $context.find('.cfs-wp-category-list').each(function() {
            refreshCategoryState($(this));
        });
    }

    $(function() {
        initCategoryControls($(document));

        $(document).on('cfs/ready', '.cfs_add_field', function() {
            initCategoryControls($(document));
        });

        $(document).on('change', '.cfs-wp-category-list input[type="checkbox"]', function() {
            var $input = $(this);
            var $item = $input.closest('.cfs-wp-category-item');
            var $list = $input.closest('.cfs-wp-category-list');
            var checked = $input.prop('checked');

            if (checked && '1' === $list.attr('data-auto-select-children')) {
                $item.find('ul input[type="checkbox"]').prop('checked', checked);
            }
            else if (!checked) {
                $item.find('ul input[type="checkbox"]').prop('checked', false);
            }

            if (checked && '1' === $list.attr('data-auto-select-parents')) {
                $item.parents('.cfs-wp-category-item').children('label').find('input[type="checkbox"]').prop('checked', true);
            }
            else if (!checked && '1' === $list.attr('data-auto-select-parents')) {
                $item.parents('.cfs-wp-category-item').each(function() {
                    var $parent = $(this);
                    var hasCheckedChildren = $parent.children('ul').find('input[type="checkbox"]:checked').length > 0;

                    if (!hasCheckedChildren) {
                        $parent.children('label').find('input[type="checkbox"]').prop('checked', false);
                    }
                });
            }

            var defaultCategory = $list.attr('data-default-category');

            if (checked) {
                var isDefaultCategory = defaultCategory && defaultCategory === $input.val();

                if (isDefaultCategory) {
                    if (!shouldKeepDefaultCategory($list, defaultCategory)) {
                        $list.find('input[type="checkbox"]').not($input).prop('checked', false);
                    }
                }
                else if (defaultCategory && !shouldKeepDefaultCategory($list, defaultCategory)) {
                    getCategoryInput($list, defaultCategory).prop('checked', false);
                }
            }
            else if (defaultCategory && 0 === $list.find('input[type="checkbox"]:checked').length) {
                getCategoryInput($list, defaultCategory).prop('checked', true);
            }

            refreshCategoryState($list);
            applyCategoryFilter($input.closest('.cfs-wp-category-control'));
        });

        $(document).on('input change keyup', '.cfs-wp-category-search, .cfs-wp-category-selected-only-toggle', function() {
            applyCategoryFilter($(this).closest('.cfs-wp-category-control'));
        });

        $(document).on('click', '.cfs-wp-category-add-toggle', function() {
            var $button = $(this);
            var $panel = $button.closest('.cfs-wp-category-add').find('.cfs-wp-category-add-panel').first();
            var expanded = 'true' === $button.attr('aria-expanded');

            $button.attr('aria-expanded', expanded ? 'false' : 'true');
            $panel.prop('hidden', expanded);

            if (!expanded) {
                $panel.find('.cfs-wp-category-add-name').trigger('focus');
            }
        });

        $(document).on('click', '.cfs-wp-category-add-cancel', function() {
            var $add = $(this).closest('.cfs-wp-category-add');

            $add.find('.cfs-wp-category-add-name').val('');
            $add.find('.cfs-wp-category-add-message').removeClass('is-error is-success').text('');
            $add.find('.cfs-wp-category-add-panel').prop('hidden', true);
            $add.find('.cfs-wp-category-add-toggle').attr('aria-expanded', 'false');
        });

        $(document).on('click', '.cfs-wp-category-add-submit', function() {
            var $button = $(this);
            var $add = $button.closest('.cfs-wp-category-add');
            var $control = $button.closest('.cfs-wp-category-control');
            var $message = $add.find('.cfs-wp-category-add-message');
            var name = trim($add.find('.cfs-wp-category-add-name').val());
            var ajaxUrl = getAjaxUrl();

            if ('' === name) {
                $message.addClass('is-error').removeClass('is-success').text(message('enter_name', 'Enter a category name.'));
                return;
            }

            if (!ajaxUrl) {
                $message.addClass('is-error').removeClass('is-success').text(message('failed', 'Failed to add category.'));
                return;
            }

            $button.prop('disabled', true);
            $message.removeClass('is-error is-success').text(message('adding', 'Adding category...'));

            $.ajax({
                url: ajaxUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'atshift_cfs_add_wp_category_term',
                    nonce: $add.attr('data-nonce'),
                    taxonomy: $add.attr('data-taxonomy'),
                    field_id: $add.attr('data-field-id'),
                    post_id: $add.attr('data-post-id'),
                    parent: $add.find('.cfs-wp-category-add-parent').val(),
                    name: name
                }
            }).done(function(response) {
                if (!response || !response.success || !response.data || !response.data.term) {
                    $message.addClass('is-error').removeClass('is-success').text(
                        response && response.data && response.data.message ? response.data.message : message('failed', 'Failed to add category.')
                    );
                    return;
                }

                insertAddedCategory($control, response.data.term);
                $add.find('.cfs-wp-category-add-name').val('');
                $message.addClass('is-success').removeClass('is-error').text(response.data.message || message('added', 'Category added.'));
            }).fail(function(xhr) {
                var response = xhr.responseJSON;
                $message.addClass('is-error').removeClass('is-success').text(
                    response && response.data && response.data.message ? response.data.message : message('failed', 'Failed to add category.')
                );
            }).always(function() {
                $button.prop('disabled', false);
            });
        });
    });
})(jQuery);
