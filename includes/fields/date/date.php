<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class cfs_date extends cfs_field
{

    function __construct() {
        $this->name = 'date';
        $this->label = __( 'Date', 'at-shift-cfs' );
    }


    function input_head( $field = null ) {
        global $wp_locale;

        $this->load_assets();

        $weekdays = array_values( $wp_locale->weekday );
        $weekdays[] = $weekdays[0];
        $weekdays_short = array_values( $wp_locale->weekday_abbrev );
        $weekdays_short[] = $weekdays_short[0];
        $weekdays_min = [];
        foreach ( $wp_locale->weekday as $weekday ) {
            $weekdays_min[] = $wp_locale->get_weekday_initial( $weekday );
        }
        $weekdays_min[] = $weekdays_min[0];

        $months = [];
        $months_short = [];
        foreach ( range( 1, 12 ) as $month_number ) {
            $month = $wp_locale->get_month( zeroise( $month_number, 2 ) );
            $months[] = $month;
            $months_short[] = $wp_locale->get_month_abbrev( $month );
        }

        $locale = str_replace( '_', '-', get_user_locale() );
        $date_locale = [
            'days'        => $weekdays,
            'daysShort'   => $weekdays_short,
            'daysMin'     => $weekdays_min,
            'months'      => $months,
            'monthsShort' => $months_short,
            'today'       => __( 'Today', 'at-shift-cfs' ),
            'clear'       => _x( 'Clear', 'date picker', 'at-shift-cfs' ),
        ];
    ?>
        <?php ob_start(); ?>
        (function($) {
            var dateLocale = <?php echo wp_json_encode( $date_locale ); ?>;
            var userLocale = <?php echo wp_json_encode( $locale ); ?>;

            $(function() {
                $.fn.datepicker.dates.cfs = dateLocale;

                $(document).on('cfs/ready', '.cfs_add_field', function() {
                    $('.cfs_date:not(.ready)').init_date();
                });
                $('.cfs_date').init_date();
            });

            $.fn.init_date = function() {
                this.each(function() {
                    //$(this).find('input.date').datetime();
                    $(this).find('input.date').datepicker({
                        format: 'yyyy-mm-dd',
                        language: 'cfs',
                        weekStart: <?php echo absint( get_option( 'start_of_week', 0 ) ); ?>,
                        titleFormatter: function(year, month) {
                            if ('undefined' !== typeof Intl && Intl.DateTimeFormat) {
                                var locales = [userLocale, userLocale.split('-')[0]];
                                for (var i = 0; i < locales.length; i++) {
                                    try {
                                        return new Intl.DateTimeFormat(locales[i], {
                                            year: 'numeric',
                                            month: 'long'
                                        }).format(new Date(year, month, 1));
                                    }
                                    catch (error) {
                                        // Try the base language before using the legacy fallback.
                                    }
                                }
                            }
                            return dateLocale.months[month] + ' ' + year;
                        },
                        todayHighlight: true,
                        autoclose: true,
                        clearBtn: true
                    });
                    $(this).addClass('ready');
                });
            };
        })(jQuery);
        <?php wp_add_inline_script( 'bootstrap-datepicker', ob_get_clean() ); ?>
    <?php
    }


    function load_assets() {
        wp_enqueue_style( 'bootstrap-datepicker', esc_url( CFS_URL . '/includes/fields/date/datepicker.css' ), [], CFS_VERSION );
        wp_register_script( 'bootstrap-datepicker', esc_url( CFS_URL . '/includes/fields/date/bootstrap-datepicker.js' ), [ 'jquery' ], CFS_VERSION, true );
        wp_enqueue_script( 'bootstrap-datepicker' );
    }
}
