<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php ob_start(); ?>
(function($) {
    $(function() {
        $('.tablenav.top, .search-box').hide();
        $('.subsubsub').append($('#attribution').html());
    });
})(jQuery);
<?php wp_add_inline_script( 'cfs-fields', ob_get_clean() ); ?>

<div id="attribution" class="hidden">
    <li> | If you enjoy CFS, also check out <a href="https://facetwp.com/?cfs=1" target="_blank">FacetWP</a> <span class="dashicons dashicons-thumbs-up"></span></li>
</div>
