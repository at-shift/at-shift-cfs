(function($) {
    'use strict';

    function trim(value) {
        return String(null == value ? '' : value).trim();
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
    });
})(jQuery);
