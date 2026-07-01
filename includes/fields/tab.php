<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_tab extends cfs_field
{

    function __construct() {
        $this->name = 'tab';
        $this->label = __( 'Tab', 'at-shift-cfs' );
    }


    // Prevent tabs from inheriting the parent field HTML
    function html( $field ) {

    }


    // Prevent tabs from inheriting the parent options HTML
    function options_html( $key, $field ) {

    }


    // Tab handling javascript
    function input_head( $field = null ) {
    ?>
        <script>
        (function($) {
            function activate_first_tabs($context) {
                $context.find('.cfs-tabs').each(function(){
                    var $tabs = $(this);

                    if (!$tabs.children('.cfs-tab.active').length) {
                        $tabs.children('.cfs-tab:first').click();
                    }
                });
            }

            function get_tab_key($element) {
                return $element.attr('data-tab-key') || $element.attr('rel');
            }

            $(document).on('click', '.cfs-tab', function() {
                var tab = get_tab_key($(this)),
                    $tabs = $(this).closest('.cfs-tabs'),
                    $context = $tabs.parent();
                $tabs.children('.cfs-tab').removeClass('active');
                $context.children('.cfs-tab-content').removeClass('active');
                $(this).addClass('active');
                $context.children('.cfs-tab-content').filter(function() {
                    return get_tab_key($(this)) === tab;
                }).addClass('active');
            });

            $(function() {
                activate_first_tabs($(document));
            });

            $(document).on('cfs/ready', function(event) {
                activate_first_tabs($(event.target).closest('.cfs_input'));
            });
        })(jQuery);
        </script>
    <?php
    }
}
