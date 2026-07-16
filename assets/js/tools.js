(function($) {
    $(function() {
        var $tools = $('.atshift-cfs-tools');
        var settings = window.AtshiftCFSTools || {};

        if (!$tools.length) {
            return;
        }

        $tools.find('.nav-tab').on('click', function(e) {
            var tab = $(this).attr('rel');

            e.preventDefault();
            $tools.find('.tab-content').removeClass('active');
            $tools.find('.nav-tab').removeClass('nav-tab-active');
            $tools.find('.tab-content.' + tab).addClass('active');
            $(this).addClass('nav-tab-active');
        });

        $tools.find('#button-export').on('click', function() {
            var groups = $tools.find('#export-field-groups').val();

            if (groups && groups.length) {
                $.post(ajaxurl, {
                    action: 'atshift_cfs_ajax_handler',
                    action_type: 'export',
                    nonce: settings.nonce,
                    field_groups: groups
                },
                function(response) {
                    $tools.find('#export-output').val(response);
                    $tools.find('#export-area').show();
                });
            }
        });

        $tools.find('#button-import').on('click', function() {
            $.post(ajaxurl, {
                action: 'atshift_cfs_ajax_handler',
                action_type: 'import',
                nonce: settings.nonce,
                import_code: $tools.find('#import-code').val()
            },
            function(response) {
                $tools.find('#import-message').html(response);
            });
        });

        $tools.find('#button-reset').on('click', function() {
            if (confirm(settings.resetConfirmMessage || 'Are you sure?')) {
                $.post(ajaxurl, {
                    action: 'atshift_cfs_ajax_handler',
                    action_type: 'reset',
                    nonce: settings.nonce
                },
                function(response) {
                    window.location.replace(response);
                });
            }
        });
    });
})(jQuery);
