<?php
// WP-CLI fixture for a disposable local WordPress installation only.
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || 'local' !== wp_get_environment_type() ) {
    exit( 'A disposable local WP-CLI environment is required.' );
}

wp_set_current_user( 1 );
$action = isset( $args[0] ) ? $args[0] : 'reset';
$key = 'atshift_loop_reorder_fixture';
$fixture = get_option( $key );
if ( 'read' === $action ) {
    echo wp_json_encode( CFS()->get( false, $fixture['post'] ), JSON_UNESCAPED_SLASHES ), "\n";
    return;
}
if ( 'cleanup' === $action ) {
    if ( $fixture ) {
        wp_delete_post( $fixture['post'], true );
        wp_delete_post( $fixture['group'], true );
        delete_option( $key );
    }
    return;
}
if ( ! $fixture ) {
    $fixture = [
        'group' => wp_insert_post( [
            'post_title' => 'Nested Loop regression fixture',
            'post_type' => ATSHIFT_CFS_FIELD_GROUP_POST_TYPE,
            'post_status' => 'publish',
        ] ),
        'post' => wp_insert_post( [
            'post_title' => 'Nested Loop regression test',
            'post_type' => 'post', 'post_status' => 'draft', 'post_author' => 1,
            'post_content' => '<!-- wp:paragraph --><p>Unchanged core content.</p><!-- /wp:paragraph -->',
        ] ),
    ];
    update_option( $key, $fixture );
}
$fields = [];
foreach ( [
    [ 7, 'parents', 'loop', 0 ],
    [ 12, 'parent_label', 'text', 7 ],
    [ 8, 'children', 'loop', 7 ],
    [ 9, 'child_text', 'text', 8 ],
    [ 10, 'grandchildren', 'loop', 8 ],
    [ 11, 'grandchild_text', 'text', 10 ],
    [ 20, 'independent', 'loop', 0 ],
    [ 21, 'control_text', 'text', 20 ],
] as $weight => $spec ) {
    $fields[] = [
        'id' => $spec[0], 'name' => $spec[1], 'label' => $spec[1],
        'type' => $spec[2], 'parent_id' => $spec[3], 'weight' => $weight,
        'notes' => '', 'options' => [ 'row_display' => 1 ],
    ];
}
update_option( ATSHIFT_CFS_NEXT_FIELD_ID_OPTION, max( 22, (int) get_option( ATSHIFT_CFS_NEXT_FIELD_ID_OPTION ) ) );
update_post_meta( $fixture['group'], 'cfs_fields', $fields );
update_post_meta( $fixture['group'], 'cfs_rules', [ 'post_ids' => [ 'operator' => [ '==' ], 'values' => [ $fixture['post'] ] ] ] );
update_post_meta( $fixture['group'], 'cfs_extras', [ 'order' => 0, 'context' => 'normal', 'hide_editor' => 0 ] );
CFS()->field_group->cache = [];
CFS()->api->cache = [];
$parents = [];
foreach ( [ 'A', 'B' ] as $label ) {
    $children = [];
    foreach ( [ 1, 2 ] as $number ) {
        $children[] = [
            'child_text' => $label . $number,
            'grandchildren' => [ [ 'grandchild_text' => $label . $number . '-deep' ] ],
        ];
    }
    $parents[] = [ 'parent_label' => $label, 'children' => $children ];
}
CFS()->save( [
    'parents' => $parents,
    'independent' => [ [ 'control_text' => 'CONTROL' ] ],
], [ 'ID' => $fixture['post'] ] );
echo wp_json_encode( $fixture ), "\n";
