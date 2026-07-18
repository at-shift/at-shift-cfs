<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_ajax
{
    /**
     * Discover classic meta boxes registered for the selected post types.
     *
     * @param array $options
     * @return string A JSON results object
     */
    public function discover_meta_boxes( $options ) {
        global $post, $wp_meta_boxes;

        $requested_post_types = isset( $options['post_types'] ) ? (array) $options['post_types'] : [];
        $post_types = array_values( array_unique( array_filter( array_map( 'sanitize_key', $requested_post_types ) ) ) );
        $available_post_types = [];
        $meta_boxes = [];

        if ( empty( $post_types ) ) {
            return wp_json_encode( [
                'post_types' => [],
                'meta_boxes' => [],
            ] );
        }

        if ( ! function_exists( 'get_default_post_to_edit' ) ) {
            require_once ABSPATH . 'wp-admin/includes/post.php';
        }

        if ( ! function_exists( 'set_current_screen' ) ) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
            require_once ABSPATH . 'wp-admin/includes/screen.php';
        }

        if ( ! function_exists( 'add_meta_box' ) ) {
            require_once ABSPATH . 'wp-admin/includes/template.php';
        }

        foreach ( $post_types as $post_type ) {
            $post_type_object = get_post_type_object( $post_type );

            if (
                ! $post_type_object
                || in_array( $post_type, [ ATSHIFT_CFS_FIELD_GROUP_POST_TYPE, ATSHIFT_CFS_LEGACY_FIELD_GROUP_POST_TYPE, 'attachment', 'revision', 'nav_menu_item' ], true )
            ) {
                continue;
            }

            $sample_posts = get_posts( [
                'post_type'        => $post_type,
                'post_status'      => 'any',
                'posts_per_page'   => 1,
                'orderby'          => 'ID',
                'order'            => 'DESC',
                'suppress_filters' => false,
            ] );
            $sample_post = ! empty( $sample_posts ) ? $sample_posts[0] : get_default_post_to_edit( $post_type, false );

            if ( ! ( $sample_post instanceof WP_Post ) ) {
                continue;
            }

            unset( $wp_meta_boxes[ $post_type ] );
            $post = $sample_post;
            set_current_screen( $post_type );
            do_action( 'add_meta_boxes', $post_type, $sample_post );
            do_action( "add_meta_boxes_{$post_type}", $sample_post );

            $post_type_label = ! empty( $post_type_object->labels->singular_name )
                ? $post_type_object->labels->singular_name
                : $post_type;
            $available_post_types[ $post_type ] = $post_type_label;
            $screen_boxes = isset( $wp_meta_boxes[ $post_type ] ) && is_array( $wp_meta_boxes[ $post_type ] )
                ? $wp_meta_boxes[ $post_type ]
                : [];

            foreach ( $screen_boxes as $context => $priorities ) {
                if ( ! is_array( $priorities ) ) {
                    continue;
                }

                foreach ( $priorities as $priority => $boxes ) {
                    if ( ! is_array( $boxes ) ) {
                        continue;
                    }

                    foreach ( $boxes as $box ) {
                        if ( ! is_array( $box ) || empty( $box['id'] ) ) {
                            continue;
                        }

                        $box_id = sanitize_key( (string) $box['id'] );

                        if ( '' === $box_id ) {
                            continue;
                        }

                        if ( ! isset( $meta_boxes[ $box_id ] ) ) {
                            $recommended = ! $this->is_non_movable_meta_box( $box_id, $box );
                            $meta_boxes[ $box_id ] = [
                                'id'             => $box_id,
                                'title'          => wp_strip_all_tags( (string) ( $box['title'] ?? $box_id ) ),
                                'context'        => sanitize_key( (string) $context ),
                                'context_label'  => $this->get_meta_box_context_label( $context ),
                                'priority'       => sanitize_key( (string) $priority ),
                                'post_types'     => [],
                                'recommended'    => $recommended,
                                'recommendation' => $recommended ? '' : __( 'This meta box is not recommended to move.', 'atshift-fields-maintenance-for-custom-field-suite' ),
                            ];
                        }

                        $meta_boxes[ $box_id ]['post_types'][ $post_type ] = $post_type_label;
                    }
                }
            }
        }

        $post_type_count = count( $available_post_types );
        $meta_boxes = array_values( array_map( function( $meta_box ) use ( $post_type_count ) {
            $meta_box['post_types'] = array_values( $meta_box['post_types'] );
            $meta_box['available_on_all'] = $post_type_count > 0 && count( $meta_box['post_types'] ) === $post_type_count;
            return $meta_box;
        }, $meta_boxes ) );

        usort( $meta_boxes, function( $first, $second ) {
            if ( $first['recommended'] !== $second['recommended'] ) {
                return $first['recommended'] ? -1 : 1;
            }

            return strnatcasecmp( $first['title'], $second['title'] );
        } );

        return wp_json_encode( [
            'post_types' => $available_post_types,
            'meta_boxes' => $meta_boxes,
        ] );
    }


    private function is_non_movable_meta_box( $box_id, $box ) {
        $non_movable_ids = [
            'submitdiv',
            'categorydiv',
            'tagsdiv-post_tag',
            'postimagediv',
            'pageparentdiv',
            'formatdiv',
            'commentsdiv',
            'commentstatusdiv',
            'postexcerpt',
            'trackbacksdiv',
            'slugdiv',
            'authordiv',
            'postcustom',
            'revisionsdiv',
        ];
        $args = isset( $box['args'] ) && is_array( $box['args'] ) ? $box['args'] : [];

        return in_array( $box_id, $non_movable_ids, true )
            || 0 === strpos( $box_id, 'cfs_' )
            || ! empty( $args['taxonomy'] );
    }


    private function get_meta_box_context_label( $context ) {
        $labels = [
            'normal'   => __( 'Normal', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'advanced' => __( 'Normal', 'atshift-fields-maintenance-for-custom-field-suite' ),
            'side'     => __( 'Side', 'atshift-fields-maintenance-for-custom-field-suite' ),
        ];

        return $labels[ $context ] ?? (string) $context;
    }


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
            post_type NOT IN (%s, %s, 'attachment', 'revision', 'nav_menu_item') AND
            post_title LIKE %s
        ORDER BY post_type, post_title
        LIMIT 10",
                ATSHIFT_CFS_FIELD_GROUP_POST_TYPE,
                ATSHIFT_CFS_LEGACY_FIELD_GROUP_POST_TYPE,
                '%' . $wpdb->esc_like( $search ) . '%'
            )
        );

        $output = [];
        foreach ( $results as $result ) {
            $parent = '';
            $post_type_label = $result->post_type;
            $post_type_object = get_post_type_object( $result->post_type );

            if ( $post_type_object && ! empty( $post_type_object->labels->singular_name ) ) {
                $post_type_label = $post_type_object->labels->singular_name;
            }

            if (
                isset( $result->post_parent ) &&
                absint( $result->post_parent ) > 0 &&
                $parent = get_post( $result->post_parent )
            ) {
                $parent = "$parent->post_title >";
            }

            $output[] = [
                'id' => absint( $result->ID ),
                'text' => sprintf( '(%s) %s %s (#%d)', $post_type_label, $parent, $result->post_title, absint( $result->ID ) ),
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
        $wpdb->query(
            $wpdb->prepare(
                "
        DELETE p, m FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
        WHERE p.post_type IN (%s, %s)",
                ATSHIFT_CFS_FIELD_GROUP_POST_TYPE,
                ATSHIFT_CFS_LEGACY_FIELD_GROUP_POST_TYPE
            )
        );

        // Drop custom field values
        $sql = "
        DELETE v, m FROM {$wpdb->prefix}cfs_values v
        LEFT JOIN {$wpdb->postmeta} m ON m.meta_id = v.meta_id";
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table names are WordPress-generated and the query has no user input.
        $wpdb->query( $sql );

        // Drop tables
        $wpdb->query( "DROP TABLE {$wpdb->prefix}cfs_values" );
        $wpdb->query( "DROP TABLE {$wpdb->prefix}cfs_sessions" );
        delete_option( ATSHIFT_CFS_VERSION_OPTION );
        delete_option( ATSHIFT_CFS_LEGACY_VERSION_OPTION );
        delete_option( ATSHIFT_CFS_NEXT_FIELD_ID_OPTION );
        delete_option( ATSHIFT_CFS_LEGACY_NEXT_FIELD_ID_OPTION );
    }
}
