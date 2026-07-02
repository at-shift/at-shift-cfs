<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php wp_add_inline_script( 'atshift-cfs-fields', atshift_cfs_capture_output( function() { ?>
(function($) {
    $(function() {
        $('.tablenav.top, .search-box').hide();
        $('.subsubsub').append($('#attribution').html());
    });
})(jQuery);
<?php } ) ); ?>

<div id="attribution" class="hidden">
    <li> | If you enjoy CFS, also check out <a href="https://facetwp.com/?cfs=1" target="_blank">FacetWP</a> <span class="dashicons dashicons-thumbs-up"></span></li>
</div>
