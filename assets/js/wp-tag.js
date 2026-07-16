(function($) {
    function get_tags(value) {
        return $.map((value || '').split(','), function(tag) {
            tag = $.trim(tag);
            return tag ? tag : null;
        });
    }

    function render_selected_tags($field) {
        var $input = $field.find('.cfs-wp-tag-input').first();
        var $selected = $field.find('.cfs-wp-tag-selected').first();

        if (!$input.length || !$selected.length) {
            return;
        }

        var tags = get_tags($input.val());
        var remove_label = $selected.data('remove-label') || 'Remove';

        $selected.empty();

        $.each(tags, function(index, tag) {
            var $tag = $('<span />', {
                'class': 'cfs-wp-tag-selected-tag',
                text: tag
            });

            $('<button />', {
                type: 'button',
                'class': 'cfs-wp-tag-selected-remove',
                'data-tag-index': index,
                'aria-label': remove_label + ': ' + tag,
                text: '×'
            }).appendTo($tag);

            $tag.appendTo($selected);
        });

        $selected.prop('hidden', !tags.length);
    }

    function render_all_selected_tags() {
        $('.cfs-wp-tag-field').each(function() {
            render_selected_tags($(this));
        });
    }

    $(render_all_selected_tags);

    $(document)
        .off('input.atshiftCfsWpTagPopular change.atshiftCfsWpTagPopular', '.cfs-wp-tag-input')
        .on('input.atshiftCfsWpTagPopular change.atshiftCfsWpTagPopular', '.cfs-wp-tag-input', function() {
            render_selected_tags($(this).closest('.cfs-wp-tag-field'));
        });

    $(document)
        .off('click.atshiftCfsWpTagPopular', '.cfs-wp-tag-selected-remove')
        .on('click.atshiftCfsWpTagPopular', '.cfs-wp-tag-selected-remove', function(e) {
            e.preventDefault();

            var $button = $(this);
            var $field = $button.closest('.cfs-wp-tag-field');
            var $input = $field.find('.cfs-wp-tag-input').first();
            var tags = get_tags($input.val());
            var index = parseInt($button.attr('data-tag-index'), 10);

            if (!$input.length || isNaN(index)) {
                return;
            }

            tags.splice(index, 1);
            $input.val(tags.join(', ')).trigger('input').trigger('change').focus();
        });

    $(document)
        .off('click.atshiftCfsWpTagPopular', '.cfs-wp-tag-popular-toggle')
        .on('click.atshiftCfsWpTagPopular', '.cfs-wp-tag-popular-toggle', function(e) {
            e.preventDefault();

            var $toggle = $(this);
            var $panel = $toggle.closest('.cfs-wp-tag-popular').find('.cfs-wp-tag-popular-panel').first();
            var expanded = 'true' == $toggle.attr('aria-expanded');

            $toggle.attr('aria-expanded', expanded ? 'false' : 'true');
            $panel.prop('hidden', expanded);
        });

    $(document)
        .off('click.atshiftCfsWpTagPopular', '.cfs-wp-tag-popular-tag')
        .on('click.atshiftCfsWpTagPopular', '.cfs-wp-tag-popular-tag', function(e) {
            e.preventDefault();

            var $button = $(this);
            var tag_name = $.trim($button.data('tag-name') || $button.text());
            var $input = $button.closest('.cfs-wp-tag-field').find('.cfs-wp-tag-input').first();

            if (!$input.length) {
                $input = $button.closest('.field').find('input[type="text"]').first();
            }

            if (!tag_name || !$input.length) {
                return;
            }

            var tags = get_tags($input.val());
            var existing_tags = $.map(tags, function(tag) {
                return tag.toLowerCase();
            });

            if (-1 == $.inArray(tag_name.toLowerCase(), existing_tags)) {
                tags.push(tag_name);
            }

            $input.val(tags.join(', ')).trigger('input').trigger('change').focus();
        });
})(jQuery);
