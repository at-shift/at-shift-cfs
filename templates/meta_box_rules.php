<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


global $post, $wpdb, $wp_roles;

$equals_text = __( 'equals', 'at-shift-cfs' );
$not_equals_text = __( 'is not', 'at-shift-cfs' );
$rules = (array) get_post_meta( $post->ID, 'cfs_rules', true );

// Populate rules if empty
$rule_types = [
    'post_types',
    'post_formats',
    'user_roles',
    'post_ids',
    'term_ids',
    'page_templates'
];

foreach ( $rule_types as $type ) {
    if ( ! isset( $rules[ $type ] ) ) {
        $rules[ $type ] = [ 'operator' => [ '==' ], 'values' => [] ];
    }
}

// Post types
$post_types = [];
$types = get_post_types();
foreach ( $types as $post_type ) {
    if ( ! in_array( $post_type, [ 'cfs', 'attachment', 'revision', 'nav_menu_item' ] ) ) {
        $post_types[ $post_type ] = $post_type;
    }
}

// Post formats
$post_formats = [];
if ( current_theme_supports( 'post-formats' ) ) {
    $post_formats = [ 'standard' => 'Standard' ];
    $post_formats_slugs = get_theme_support( 'post-formats' );

    if ( is_array( $post_formats_slugs[0] ) ) {
        foreach ( $post_formats_slugs[0] as $post_format ) {
            $post_formats[ $post_format ] = get_post_format_string( $post_format );
        }
    }
}

// User roles
foreach ( $wp_roles->roles as $key => $role ) {
    $user_roles[ $key ] = $key;
}

// Post IDs
$post_ids = [];
$json_posts = [];

if ( ! empty( $rules['post_ids']['values'] ) ) {
    $post_in = array_values( array_filter( array_map( 'absint', (array) $rules['post_ids']['values'] ) ) );

    $results = [];
    if ( ! empty( $post_in ) ) {
        $post_in_placeholders = implode( ',', array_fill( 0, count( $post_in ), '%d' ) );
        $sql = $wpdb->prepare(
            "
        SELECT ID, post_type, post_title, post_parent
        FROM $wpdb->posts
        WHERE ID IN ($post_in_placeholders)
        ORDER BY post_type, post_title",
            $post_in
        );
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql is prepared above with sanitized post IDs.
        $results = $wpdb->get_results( $sql );
    }

    foreach ( $results as $result ) {
        $parent = '';

        if (
            isset( $result->post_parent ) &&
            absint( $result->post_parent ) > 0 &&
            $parent = get_post( $result->post_parent )
        ) {
            $parent = "$parent->post_title >";
        }

        $json_posts[] = [ 'id' => $result->ID, 'text' => "($result->post_type) $parent $result->post_title (#$result->ID)" ];
        $post_ids[] = $result->ID;
    }
}

// Term IDs
$sql = "
SELECT t.term_id, t.name, tt.taxonomy
FROM $wpdb->terms t
INNER JOIN $wpdb->term_taxonomy tt ON tt.term_id = t.term_id AND tt.taxonomy != 'post_tag'
ORDER BY tt.parent, tt.taxonomy, t.name";
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query contains only WordPress table names and fixed taxonomy filtering.
$results = $wpdb->get_results( $sql );

foreach ( $results as $result ) {
    $term_ids[ $result->term_id ] = "($result->taxonomy) $result->name";
}

// Page templates
$page_templates = [];
$templates = get_page_templates();

foreach ( $templates as $template_name => $filename ) {
    $page_templates[ $filename ] = $template_name;
}

?>

<script>
(function($) {
    $(function() {
        var cfs_nonce = '<?php echo esc_js( wp_create_nonce( 'cfs_admin_nonce' ) ); ?>';

        $('.select2').select2({
            placeholder: '<?php esc_html_e( 'Leave blank to skip this rule', 'at-shift-cfs' ); ?>'
        });

        $('.select2-ajax').select2({
            multiple: true,
            placeholder: '<?php esc_html_e( 'Leave blank to skip this rule', 'at-shift-cfs' ); ?>',
            minimumInputLength: 2,
            ajax: {
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: function(term, page) {
                    return {
                        q: term,
                        action: 'cfs_ajax_handler',
                        action_type: 'search_posts',
                        nonce: cfs_nonce
                    }
                },
                results: function(data, page) {
                    return { results: data };
                }
            },
            initSelection: function(element, callback) {
                var data = [];
                var post_ids = <?php echo wp_json_encode( $json_posts ); ?>;
                $(post_ids).each(function(idx, val) {
                    data.push({ id: val.id, text: val.text });
                });
                callback(data);
            }
        });
    });
})(jQuery);
</script>

<table>
    <tr>
        <td class="label cfs-rule-label">
            <label><?php esc_html_e( 'Post Types', 'at-shift-cfs' ); ?></label>
        </td>
        <td class="cfs-rule-operator">
            <?php
                CFS()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][post_types]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['post_types']['operator'],
                ] );
            ?>
        </td>
        <td class="cfs-rule-value">
            <?php
                CFS()->create_field( [
                    'type' => 'select',
                    'input_class' => 'select2',
                    'input_name' => "cfs[rules][post_types]",
                    'options' => [ 'multiple' => '1', 'choices' => $post_types ],
                    'value' => $rules['post_types']['values'],
                ] );
            ?>
        </td>
    </tr>
    <?php if ( current_theme_supports( 'post-formats' ) && count( $post_formats ) ) : ?>
        <tr>
            <td class="label cfs-rule-label">
                <label><?php esc_html_e( 'Post Formats', 'at-shift-cfs' ); ?></label>
            </td>
            <td class="cfs-rule-operator">
                <?php
                CFS()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][post_formats]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['post_formats']['operator'],
                ] );
                ?>
            </td>
            <td class="cfs-rule-value">
                <?php
                CFS()->create_field( [
                    'type' => 'select',
                    'input_class' => 'select2',
                    'input_name' => "cfs[rules][post_formats]",
                    'options' => [ 'multiple' => '1', 'choices' => $post_formats ],
                    'value' => $rules['post_formats']['values'],
                ] );
                ?>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td class="label cfs-rule-label">
            <label><?php esc_html_e( 'User Roles', 'at-shift-cfs' ); ?></label>
        </td>
        <td class="cfs-rule-operator">
            <?php
                CFS()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][user_roles]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['user_roles']['operator'],
                ] );
            ?>
        </td>
        <td class="cfs-rule-value">
            <?php
                CFS()->create_field( [
                    'type' => 'select',
                    'input_class' => 'select2',
                    'input_name' => "cfs[rules][user_roles]",
                    'options' => [ 'multiple' => '1', 'choices' => $user_roles ],
                    'value' => $rules['user_roles']['values'],
                ] );
            ?>
        </td>
    </tr>
    <tr>
        <td class="label cfs-rule-label">
            <label><?php esc_html_e('Posts', 'at-shift-cfs' ); ?></label>
        </td>
        <td class="cfs-rule-operator">
            <?php
                CFS()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][post_ids]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['post_ids']['operator'],
                ] );
            ?>
        </td>
        <td class="cfs-rule-value">
            <input type="hidden" name="cfs[rules][post_ids]" class="select2-ajax" value="<?php echo esc_attr( implode( ',', array_map( 'absint', $post_ids ) ) ); ?>" style="width:99.95%" />
        </td>
    </tr>
    <tr>
        <td class="label cfs-rule-label">
            <label><?php esc_html_e( 'Taxonomy Terms', 'at-shift-cfs' ); ?></label>
        </td>
        <td class="cfs-rule-operator">
            <?php
                CFS()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][term_ids]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['term_ids']['operator'],
                ] );
            ?>
        </td>
        <td class="cfs-rule-value">
            <?php
                CFS()->create_field( [
                    'type' => 'select',
                    'input_class' => 'select2',
                    'input_name' => "cfs[rules][term_ids]",
                    'options' => [ 'multiple' => '1', 'choices' => $term_ids ],
                    'value' => $rules['term_ids']['values'],
                ] );
            ?>
        </td>
    </tr>
    <tr>
        <td class="label cfs-rule-label">
            <label><?php esc_html_e( 'Page Templates', 'at-shift-cfs' ); ?></label>
        </td>
        <td class="cfs-rule-operator">
            <?php
                CFS()->create_field( [
                    'type' => 'select',
                    'input_name' => "cfs[rules][operator][page_templates]",
                    'options' => [
                        'choices' => [
                            '==' => $equals_text,
                            '!=' => $not_equals_text,
                        ],
                        'force_single' => true,
                    ],
                    'value' => $rules['page_templates']['operator'],
                ] );
            ?>
        </td>
        <td class="cfs-rule-value">
            <?php
                CFS()->create_field( [
                    'type' => 'select',
                    'input_class' => 'select2',
                    'input_name' => "cfs[rules][page_templates]",
                    'options' => [ 'multiple' => '1', 'choices' => $page_templates ],
                    'value' => $rules['page_templates']['values'],
                ] );
            ?>
        </td>
    </tr>
</table>
