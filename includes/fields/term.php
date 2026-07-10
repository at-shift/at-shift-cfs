<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Atshift_CFS_term extends Atshift_CFS_field
{

    function __construct() {
        $this->name = 'term';
        $this->label = __( 'Term', 'atshift-fields-maintenance-for-custom-field-suite' );
    }


    function html( $field ) {
        global $wpdb;

        $selected_posts = [];
        $available_posts = [];

        $taxonomies = [];
        if ( ! empty( $field->options['taxonomies'] ) ) {
            foreach ( $field->options['taxonomies'] as $taxonomy ) {
                $taxonomies[] = $taxonomy;
            }
        }
        else {
            $taxonomies = get_taxonomies( [ 'public' => true ] );
        }

        $args = [
            'taxonomy'   => $taxonomies,
            'hide_empty' => false,
            'fields'     => 'ids',
            'orderby'    => 'name',
            'order'      => 'ASC'
        ];

        $args = atshift_cfs_apply_filters_compat( 'cfs_field_term_query_args', 'atshift_cfs_field_term_query_args', $args, [ 'field' => $field ] );

        $query = get_terms( $args );

        foreach ( $query as $term_id ) {
            $term = get_term( $term_id );
            $available_posts[] = (object) [
                'term_id'  => $term->term_id,
                'taxonomy' => $term->taxonomy,
                'name'     => $term->name,
            ];
        }

        $field_value = $this->normalize_ids( $field->value );
        $field->value = implode( ',', $field_value );

        if ( ! empty( $field_value ) ) {
            $field_value_placeholders = implode( ',', array_fill( 0, count( $field_value ), '%d' ) );
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT term_id, name FROM $wpdb->terms WHERE term_id IN ($field_value_placeholders) ORDER BY FIELD(term_id,$field_value_placeholders)",
                    array_merge( $field_value, $field_value )
                )
            );
            foreach ( $results as $result ) {
                $selected_posts[ $result->term_id ] = $result;
            }
        }
    ?>
        <div class="filter_posts">
            <input type="text" class="cfs_filter_input" autocomplete="off" placeholder="<?php esc_attr_e( 'Search terms', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" />
        </div>

        <div class="available_posts post_list">
        <?php foreach ( $available_posts as $term ) : ?>
            <?php $class = ( isset( $selected_posts[ $term->term_id ] ) ) ? 'used' : ''; ?>
            <div rel="<?php echo absint( $term->term_id ); ?>" class="<?php echo esc_attr( $class ); ?>" title="<?php echo esc_attr( $term->name ); ?>"><?php echo wp_kses_post( $this->display_label( $term->name, $term->term_id, $field ) ); ?></div>
        <?php endforeach; ?>
        </div>

        <div class="selected_posts post_list">
        <?php foreach ( $selected_posts as $term ) : ?>
            <div rel="<?php echo absint( $term->term_id ); ?>"><span class="remove"></span><?php echo wp_kses_post( $this->display_label( $term->name, $term->term_id, $field ) ); ?></div>
        <?php endforeach; ?>
        </div>
        <div class="clear"></div>
        <input type="hidden" name="<?php echo esc_attr( $field->input_name ); ?>" class="<?php echo esc_attr( $field->input_class ); ?>" value="<?php echo esc_attr( $field->value ); ?>" />
    <?php
    }


    function options_html( $key, $field ) {
        $args = [ 'public' => true ];
        $choices = atshift_cfs_apply_filters_compat( 'cfs_field_term_taxonomies', 'atshift_cfs_field_term_taxonomies', get_taxonomies( $args ) );

    ?>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e('Taxonomies', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <p class="description"><?php esc_html_e('Limit terms to the following taxonomies', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></p>
            </td>
            <td>
                <?php
                    atshift_fields_maintenance_for_custom_field_suite()->create_field( [
                        'type'          => 'select',
                        'input_name'    => "cfs[fields][$key][options][taxonomies]",
                        'options'       => [ 'multiple' => '1', 'choices' => $choices ],
                        'value'         => $this->get_option( $field, 'taxonomies' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
            <td class="label">
                <label><?php esc_html_e( 'Limits', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></label>
                <div class="cfs_tooltip">?
                    <div class="tooltip_inner"><?php esc_html_e( 'Minimum sets the fewest terms that can be selected. Maximum sets the most terms that can be selected.', 'atshift-fields-maintenance-for-custom-field-suite' ); ?></div>
                </div>
            </td>
            <td>
                <input type="text" name="cfs[fields][<?php echo absint( $key ); ?>][options][limit_min]" value="<?php echo esc_attr( $this->get_option( $field, 'limit_min' ) ); ?>" placeholder="<?php esc_attr_e( 'Minimum', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" style="width:80px" />
                <input type="text" name="cfs[fields][<?php echo absint( $key ); ?>][options][limit_max]" value="<?php echo esc_attr( $this->get_option( $field, 'limit_max' ) ); ?>" placeholder="<?php esc_attr_e( 'Maximum', 'atshift-fields-maintenance-for-custom-field-suite' ); ?>" style="width:80px" />
            </td>
        </tr>
    <?php
    }


    function input_head( $field = null ) {
    ?>
        <?php wp_add_inline_script( 'atshift-cfs-validation', atshift_cfs_capture_output( function() { ?>
        (function($) {
            update_term_values = function(field) {
                var term_ids = [];
                field.find('.selected_posts div').each(function(idx) {
                    term_ids[idx] = $(this).attr('rel');
                });
                field.find('input.term').val(term_ids.join(','));
            }

            $(function() {
                $(document).on('cfs/ready', '.cfs_add_field', function() {
                    $('.cfs_term:not(.ready)').init_term();
                });
                $('.cfs_term').init_term();

                // add selected post
                $(document).on('click', '.cfs_term .available_posts div', function() {
                    var parent = $(this).closest('.field');
                    var term_id = $(this).attr('rel');
                    var html = $(this).html();
                    $(this).addClass('used');
                    parent.find('.selected_posts').append('<div rel="'+term_id+'"><span class="remove"></span>'+html+'</div>');
                    update_term_values(parent);
                });

                // remove selected post
                $(document).on('click', '.cfs_term .selected_posts .remove', function() {
                    var div = $(this).parent();
                    var parent = div.closest('.field');
                    var term_id = div.attr('rel');
                    parent.find('.available_posts div[rel='+term_id+']').removeClass('used');
                    div.remove();
                    update_term_values(parent);
                });

                // filter posts
                $(document).on('keyup', '.cfs_term .cfs_filter_input', function() {
                    var input = $(this).val();
                    var parent = $(this).closest('.field');
                    var regex = new RegExp(input, 'i');
                    parent.find('.available_posts div:not(.used)').each(function() {
                        if (-1 < $(this).html().search(regex)) {
                            $(this).removeClass('hidden');
                        }
                        else {
                            $(this).addClass('hidden');
                        }
                    });
                });
            });

            $.fn.init_term = function() {
                this.each(function() {
                    var $this = $(this);
                    $this.addClass('ready');

                    // sortable
                    $this.find('.selected_posts').sortable({
                        axis: 'y',
                        update: function(event, ui) {
                            var parent = $(this).closest('.field');
                            update_term_values(parent);
                        }
                    });
                });
            }
        })(jQuery);
        <?php } ) ); ?>
    <?php
    }


    function display_label( $label, $term_id, $field ) {
        $label = apply_filters( 'cfs_term_display', $label, $term_id, $field );
        return apply_filters( 'atshift_cfs_term_display', $label, $term_id, $field );
    }


    function prepare_value( $value, $field = null ) {
        return $value;
    }


    function format_value_for_input( $value, $field = null ) {
        return empty( $value ) ? '' : implode( ',', $value );
    }


    function pre_save( $value, $field = null ) {
        if ( ! empty( $value ) ) {

            // Inside a loop, the value is $value[0]
            $value = (array) $value;

            // The raw input saves a comma-separated string
            $first_value = isset( $value[0] ) ? (string) $value[0] : '';
            if ( false !== strpos( $first_value, ',' ) ) {
                return $this->normalize_ids( $first_value );
            }

            return $this->normalize_ids( $value );
        }

        return [];
    }


    function normalize_ids( $value ) {
        if ( null === $value || '' === $value ) {
            return [];
        }
        $value = is_array( $value ) ? $value : explode( ',', $value );
        $value = array_map( 'absint', $value );
        return array_values( array_filter( $value ) );
    }
}
