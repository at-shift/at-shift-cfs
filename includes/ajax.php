<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_ajax
{
    /**
     * Search posts (in the Placement Rules area)
     * @param array $options
     * @return string A JSON results object
     */
    public function search_posts( $options ) {
        global $wpdb;

        $search = isset( $options['q'] ) ? sanitize_text_field( wp_unslash( $options['q'] ) ) : '';

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "
        SELECT ID, post_type, post_title, post_parent
        FROM $wpdb->posts
        WHERE
            post_status IN ('publish', 'private') AND
            post_type NOT IN ('cfs', 'attachment', 'revision', 'nav_menu_item') AND
            post_title LIKE %s
        ORDER BY post_type, post_title
        LIMIT 10",
                '%' . $wpdb->esc_like( $search ) . '%'
            )
        );

        $output = [];
        foreach ( $results as $result ) {
            $parent = '';

            if (
                isset( $result->post_parent ) &&
                absint( $result->post_parent ) > 0 &&
                $parent = get_post( $result->post_parent )
            ) {
                $parent = "$parent->post_title >";
            }

            $output[] = [
                'id' => absint( $result->ID ),
                'text' => "($result->post_type) $parent $result->post_title (#$result->ID)"
            ];
        }
        return wp_json_encode( $output );
    }


    /**
     * Remove all traces of CFS
     */
    public function reset() {
        global $wpdb;

        // Drop field groups
        $sql = "
        DELETE p, m FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
        WHERE p.post_type = 'cfs'";
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table names are WordPress-generated and the query has no user input.
        $wpdb->query( $sql );

        // Drop custom field values
        $sql = "
        DELETE v, m FROM {$wpdb->prefix}cfs_values v
        LEFT JOIN {$wpdb->postmeta} m ON m.meta_id = v.meta_id";
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table names are WordPress-generated and the query has no user input.
        $wpdb->query( $sql );

        // Drop tables
        $wpdb->query( "DROP TABLE {$wpdb->prefix}cfs_values" );
        $wpdb->query( "DROP TABLE {$wpdb->prefix}cfs_sessions" );
        delete_option( 'cfs_version' );
        delete_option( 'cfs_next_field_id' );
    }
}
