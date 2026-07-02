<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_field_group
{
    public $cache;


    /*
    ================================================================
        Load all field groups
    ================================================================
    */
    public function load_field_groups() {
        global $wpdb;

        if ( isset( $this->cache['field_groups'] ) ) {
            return $this->cache['field_groups'];
        }

        $sql = "
        SELECT p.ID, p.post_title,
            m1.meta_value as fields,
            m2.meta_value AS rules,
            m3.meta_value AS extras
        FROM $wpdb->posts p
        INNER JOIN $wpdb->postmeta m1 ON m1.post_id = p.ID AND m1.meta_key = 'cfs_fields'
        INNER JOIN $wpdb->postmeta m2 ON m2.post_id = p.ID AND m2.meta_key = 'cfs_rules'
        INNER JOIN $wpdb->postmeta m3 ON m3.post_id = p.ID AND m3.meta_key = 'cfs_extras'
        WHERE p.post_status = 'publish'";
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query contains only WordPress table names and fixed meta keys.
        $results = $wpdb->get_results( $sql );

        $output = [];
        foreach ( $results as $result ) {
            $output[ $result->ID ] = [
                'title'     => $result->post_title,
                'fields'    => $this->safe_unserialize( $result->fields, [] ),
                'rules'     => $this->safe_unserialize( $result->rules, [] ),
                'extras'    => $this->safe_unserialize( $result->extras, [] )
            ];
        }

        $this->cache['field_groups'] = $output;

        return $output;
    }


    /*
    ================================================================
        Import field groups
    ================================================================
    */
    public function import( $options ) {
        global $wpdb;

        if ( ! empty( $options['import_code'] ) ) {

            // Collect stats
            $stats = [];

            // Get all existing field group names
            $existing_groups = $wpdb->get_col( "SELECT post_name FROM {$wpdb->posts} WHERE post_type = 'cfs'" );

            // Loop through field groups
            foreach ( $options['import_code'] as $group ) {

                // Make sure this field group doesn't exist
                if ( ! in_array( $group['post_name'], $existing_groups ) ) {

                    // Insert new post
                    $post_id = wp_insert_post( [
                        'post_title' => $group['post_title'],
                        'post_name' => $group['post_name'],
                        'post_type' => 'cfs',
                        'post_status' => 'publish',
                        'post_content' => '',
                        'post_content_filtered' => '',
                        'post_excerpt' => '',
                        'to_ping' => '',
                        'pinged' => '',
                    ] );

                    // Generate new field IDs
                    $field_id_mapping = [];
                    $next_field_id = (int) get_option( 'cfs_next_field_id' );
                    foreach ( $group['cfs_fields'] as $key => $data ) {

                        $id = $group['cfs_fields'][ $key ]['id'];
                        $parent_id = $group['cfs_fields'][ $key ]['parent_id'];
                        $field_id_mapping[ $id ] = $next_field_id;
                        $group['cfs_fields'][ $key ]['id'] = $next_field_id;
                        if ( 0 < (int) $parent_id ) {
                            $group['cfs_fields'][ $key ]['parent_id'] = $field_id_mapping[ $parent_id ];
                        }
                        $next_field_id++;
                    }

                    update_option( 'cfs_next_field_id', $next_field_id );
                    update_post_meta( $post_id, 'cfs_fields', $group['cfs_fields'] );
                    update_post_meta( $post_id, 'cfs_rules', $group['cfs_rules'] );
                    update_post_meta( $post_id, 'cfs_extras', $group['cfs_extras'] );

                    $stats['imported'][] = $group['post_title'];
                }
                else {
                    $stats['skipped'][] = $group['post_title'];
                }
            }

            $return = '';
            if ( ! empty( $stats['imported'] ) ) {
                $return .= '<div>' . esc_html__( 'Imported', 'atshift-fields-maintenance-for-custom-field-suite' ) . ': ' . esc_html( implode( ', ', $stats['imported'] ) ) . '</div>';
            }
            if ( ! empty( $stats['skipped'] ) ) {
                $return .= '<div>' . esc_html__( 'Skipped', 'atshift-fields-maintenance-for-custom-field-suite' ) . ': ' . esc_html( implode( ', ', $stats['skipped'] ) ) . '</div>';
            }
            return $return;
        }
        else {
            return '<div>' . esc_html__( 'Nothing to import', 'atshift-fields-maintenance-for-custom-field-suite' ) . '</div>';
        }
    }


    /*
    ================================================================
        Export field groups
    ================================================================
    */
    public function export( $options ) {
        global $wpdb;

        $post_ids = [];
        $field_groups = [];
        foreach ( (array) $options['field_groups'] as $post_id ) {
            $post_ids[] = absint( $post_id );
        }

        $post_ids = array_values( array_filter( $post_ids ) );
        if ( empty( $post_ids ) ) {
            return [];
        }

        $post_id_placeholders = implode( ',', array_fill( 0, count( $post_ids ), '%d' ) );

        $post_data = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title, post_name FROM {$wpdb->posts} WHERE post_type = %s AND ID IN ($post_id_placeholders)",
                array_merge( [ 'cfs' ], $post_ids )
            )
        );

        foreach ( $post_data as $row ) {
            $field_groups[ $row->ID ] = [
                'post_title' => $row->post_title,
                'post_name' => $row->post_name,
            ];
        }

        $meta_data = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND post_id IN ($post_id_placeholders)",
                array_merge( [ $wpdb->esc_like( 'cfs_' ) . '%' ], $post_ids )
            )
        );
        foreach ( $meta_data as $row ) {
            $value = $this->safe_unserialize( $row->meta_value, [] );
            $field_groups[ $row->post_id ][ $row->meta_key ] = $value;
        }

        // Strip out the field group keys
        $temp = [];
        foreach ( $field_groups as $field_group ) {
            $temp[] = $field_group;
        }
        $field_groups = $temp;

        return $field_groups;
    }


    /**
     * Save field group settings
     * @param array $params
     */
    function save( $params = [] ) {
        global $wpdb;

        $post_id = $params['post_id'];
        $params['rules'] = isset( $params['rules'] ) && is_array( $params['rules'] ) ? $params['rules'] : [];
        $params['rules']['operator'] = isset( $params['rules']['operator'] ) && is_array( $params['rules']['operator'] ) ? $params['rules']['operator'] : [];
        $params['extras'] = isset( $params['extras'] ) && is_array( $params['extras'] ) ? $params['extras'] : [];

        /*---------------------------------------------------------------------------------------------
            Save fields
        ---------------------------------------------------------------------------------------------*/

        $weight = 0;
        $prev_fields = [];
        $current_field_ids = [];
        $remapped_field_ids = [];
        $next_field_id = (int) get_option( 'cfs_next_field_id' );
        $existing_fields = get_post_meta( $post_id, 'cfs_fields', true );
        $other_field_ids = $this->get_field_ids_for_other_groups( $post_id );

        if ( ! empty( $existing_fields ) ) {
            foreach ( $existing_fields as $item ) {
                $prev_fields[ $item['id'] ] = $item['name'];
            }
        }

        $prepared_fields = [];
        $field_key_to_id = [];

        foreach ( $params['fields'] as $key => $field ) {

            // Sanitize the field
            $field = stripslashes_deep( $field );
            $field['id'] = isset( $field['id'] ) ? (int) $field['id'] : 0;
            $field['type'] = isset( $field['type'] ) && isset( atshift_fields_maintenance_for_custom_field_suite()->fields[ $field['type'] ] ) ? $field['type'] : 'text';
            $field['name'] = isset( $field['name'] ) ? $field['name'] : '';
            $field['label'] = isset( $field['label'] ) ? $field['label'] : '';
            $field['notes'] = isset( $field['notes'] ) ? $field['notes'] : '';

            // Allow for field customizations
            $field = atshift_fields_maintenance_for_custom_field_suite()->fields[ $field['type'] ]->pre_save_field( $field );
            $field['id'] = isset( $field['id'] ) ? (int) $field['id'] : 0;

            // Set the parent ID
            $field['parent_id'] = empty( $field['parent_id'] ) ? 0 : (int) $field['parent_id'];
            $field['key'] = isset( $field['key'] ) ? (string) $field['key'] : (string) $key;
            $field['parent_key'] = isset( $field['parent_key'] ) ? (string) $field['parent_key'] : '';

            // Save empty array for fields without options
            $field['options'] = empty( $field['options'] ) ? [] : $field['options'];
            if ( isset( $field['options']['conditional_value'] ) ) {
                $field['options']['conditional_value'] = sanitize_text_field( $field['options']['conditional_value'] );
            }

            if ( 0 < (int) $field['id'] && isset( $other_field_ids[ (int) $field['id'] ] ) ) {
                $field['remapped_from'] = (int) $field['id'];
                $field['id'] = 0;
            }

            // Use an existing ID if available
            if ( 0 < (int) $field['id'] ) {

                // We use this variable to check for deleted fields
                $current_field_ids[] = $field['id'];

                // Rename the postmeta key if necessary
                if ( isset( $prev_fields[ $field['id'] ] ) && $field['name'] != $prev_fields[ $field['id'] ] ) {
                    $wpdb->query(
                        $wpdb->prepare("
                            UPDATE {$wpdb->postmeta} m
                            INNER JOIN {$wpdb->prefix}cfs_values v ON v.meta_id = m.meta_id
                            SET meta_key = %s
                            WHERE v.field_id = %d",
                            $field['name'], $field['id']
                        )
                    );
                }
            }
            else {
                $field['id'] = $next_field_id;
                $next_field_id++;

                if ( isset( $field['remapped_from'] ) ) {
                    $remapped_field_ids[ (int) $field['remapped_from'] ] = (int) $field['id'];
                }
            }

            $field_key_to_id[ $field['key'] ] = (int) $field['id'];
            $prepared_fields[] = $field;
        }

        $valid_field_ids = array_fill_keys( array_map( 'intval', $field_key_to_id ), true );
        $field_types_by_id = [];

        foreach ( $prepared_fields as $field ) {
            $field_types_by_id[ (int) $field['id'] ] = $field['type'];
        }

        $new_fields = [];

        foreach ( $prepared_fields as $field ) {
            if ( '' !== $field['parent_key'] && isset( $field_key_to_id[ $field['parent_key'] ] ) ) {
                $field['parent_id'] = (int) $field_key_to_id[ $field['parent_key'] ];
            }

            if ( 0 < (int) $field['parent_id'] && empty( $valid_field_ids[ (int) $field['parent_id'] ] ) ) {
                $field['parent_id'] = 0;
            }

            if ( 0 < (int) $field['parent_id'] && isset( $field_types_by_id[ (int) $field['parent_id'] ] ) ) {
                $parent_type = $field_types_by_id[ (int) $field['parent_id'] ];

                if ( 'group' === $parent_type && in_array( $field['type'], [ 'tab', 'group', 'loop', 'accordion', 'conditional' ], true ) ) {
                    $field['parent_id'] = 0;
                }
                elseif ( 'accordion' === $parent_type && in_array( $field['type'], [ 'tab' ], true ) ) {
                    $field['parent_id'] = 0;
                }
                elseif ( 'conditional' === $parent_type && in_array( $field['type'], [ 'tab', 'conditional' ], true ) ) {
                    $field['parent_id'] = 0;
                }
            }

            $data = [
                'id'            => $field['id'],
                'name'          => $field['name'],
                'label'         => $field['label'],
                'type'          => $field['type'],
                'notes'         => $field['notes'],
                'parent_id'     => $field['parent_id'],
                'weight'        => $weight,
                'options'       => $field['options'],
            ];

            $new_fields[] = $data;

            $weight++;
        }

        // Save the fields
        update_post_meta( $post_id, 'cfs_fields', $new_fields );
        $this->migrate_remapped_field_values( $new_fields, $remapped_field_ids );

        // Update the field ID counter
        update_option( 'cfs_next_field_id', $next_field_id );

        // Remove values for deleted fields
        $deleted_field_ids = array_diff( array_keys( $prev_fields ), $current_field_ids );
        $deleted_field_ids = array_diff( $deleted_field_ids, array_keys( $remapped_field_ids ) );

        // Filter deleted field IDs before deleting meta
        $deleted_field_ids = apply_filters( 'cfs_deleted_field_ids', $deleted_field_ids );

        if ( 0 < count( $deleted_field_ids ) ) {
            $deleted_field_ids = array_values( array_filter( array_map( 'absint', $deleted_field_ids ) ) );
            if ( ! empty( $deleted_field_ids ) ) {
                $deleted_field_placeholders = implode( ',', array_fill( 0, count( $deleted_field_ids ), '%d' ) );
                $wpdb->query(
                    $wpdb->prepare(
                        "
                    DELETE v, m
                    FROM {$wpdb->prefix}cfs_values v
                    INNER JOIN {$wpdb->postmeta} m ON m.meta_id = v.meta_id
                    WHERE v.field_id IN ($deleted_field_placeholders)",
                        $deleted_field_ids
                    )
                );
            }
        }

        /*---------------------------------------------------------------------------------------------
            Save rules
        ---------------------------------------------------------------------------------------------*/

        $data = [];
        $rule_types = [ 'post_types', 'post_formats', 'user_roles', 'post_ids', 'term_ids', 'page_templates' ];

        foreach ( $rule_types as $type ) {
            if ( ! empty( $params['rules'][ $type ] ) ) {

                // Break apart the autocomplete string
                if ( 'post_ids' == $type ) {
                    $params['rules'][ $type ] = explode( ',', $params['rules'][ $type ] );
                }

                $data[ $type ] = [
                    'operator' => isset( $params['rules']['operator'][ $type ] ) ? $params['rules']['operator'][ $type ] : '==',
                    'values' => $params['rules'][ $type ],
                ];
            }
        }

        $data = apply_filters( 'cfs_save_field_group_rules', $data, $post_id );
        update_post_meta( $post_id, 'cfs_rules', $data );

        /*---------------------------------------------------------------------------------------------
            Save extras
        ---------------------------------------------------------------------------------------------*/

        update_post_meta( $post_id, 'cfs_extras', $params['extras'] );
    }


    private function get_field_ids_for_other_groups( $post_id ) {
        $field_ids = [];
        $field_groups = $this->load_field_groups();

        foreach ( $field_groups as $group_id => $group ) {
            if ( (int) $group_id === (int) $post_id ) {
                continue;
            }

            foreach ( (array) $group['fields'] as $field ) {
                if ( isset( $field['id'] ) && 0 < (int) $field['id'] ) {
                    $field_ids[ (int) $field['id'] ] = true;
                }
            }
        }

        return $field_ids;
    }


    private function migrate_remapped_field_values( $fields, $field_id_map ) {
        global $wpdb;

        if ( empty( $field_id_map ) ) {
            return;
        }

        $field_names = [];
        foreach ( $fields as $field ) {
            if ( ! empty( $field['name'] ) ) {
                $field_names[] = $field['name'];
            }
        }
        $field_names = array_values( array_unique( $field_names ) );

        if ( empty( $field_names ) ) {
            return;
        }

        $meta_placeholders = implode( ',', array_fill( 0, count( $field_names ), '%s' ) );

        foreach ( $field_id_map as $old_field_id => $new_field_id ) {
            $params = array_merge( [ (int) $new_field_id, (int) $old_field_id ], $field_names );
            // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Dynamic IN placeholders are generated from sanitized field names.
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}cfs_values v
                    INNER JOIN {$wpdb->postmeta} m ON m.meta_id = v.meta_id
                    SET v.field_id = %d
                    WHERE v.field_id = %d AND m.meta_key IN ($meta_placeholders)",
                    $params
                )
            );

            $params = array_merge( [ (int) $new_field_id, (int) $old_field_id ], $field_names );
            // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Dynamic IN placeholders are generated from sanitized field names.
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}cfs_values v
                    INNER JOIN {$wpdb->postmeta} m ON m.meta_id = v.meta_id
                    SET v.base_field_id = %d
                    WHERE v.base_field_id = %d AND m.meta_key IN ($meta_placeholders)",
                    $params
                )
            );
        }

        // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Dynamic IN placeholders are generated from sanitized field names.
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT v.meta_id, v.hierarchy
                FROM {$wpdb->prefix}cfs_values v
                INNER JOIN {$wpdb->postmeta} m ON m.meta_id = v.meta_id
                WHERE v.hierarchy <> '' AND m.meta_key IN ($meta_placeholders)",
                $field_names
            )
        );

        foreach ( $rows as $row ) {
            $parts = explode( ':', $row->hierarchy );
            foreach ( $parts as $index => $part ) {
                if ( 0 === $index % 2 && isset( $field_id_map[ (int) $part ] ) ) {
                    $parts[ $index ] = (string) $field_id_map[ (int) $part ];
                }
            }

            $hierarchy = implode( ':', $parts );
            if ( $hierarchy !== $row->hierarchy ) {
                $wpdb->update(
                    $wpdb->prefix . 'cfs_values',
                    [ 'hierarchy' => $hierarchy ],
                    [ 'meta_id' => (int) $row->meta_id ],
                    [ '%s' ],
                    [ '%d' ]
                );
            }
        }
    }


    private function safe_unserialize( $value, $default = false ) {
        if ( empty( $value ) ) {
            return $default;
        }

        $output = @unserialize( $value, [ 'allowed_classes' => false ] );
        return ( false === $output && 'b:0;' !== $value ) ? $default : $output;
    }
}

atshift_fields_maintenance_for_custom_field_suite()->field_group = new cfs_field_group();
