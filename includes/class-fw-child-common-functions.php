<?php
/**
 * Common utility methods used throughout the theme.
 *
 * @package    FreshWeb_Church
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 *
 * This file incorporates portions of code from the Maranatha Church Theme
 * (https://churchthemes.com/themes/maranatha). The original code is 
 * copyright (c) 2015, churchthemes.com and is distributed under the terms
 * of the GNU GPL license 2.0 or later 
 * (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html).
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class wrapper for all methods.
 *
 * @since 1.1.0
 */
class FW_Child_Common_Functions {

    function __construct() { }

    /**
     * Wrap a template part.
     *
     * This is a wrapper around the WordPress get_template_part() function.
     * It wraps the given template file contents with the row tags used by
     * Visual Composer in the [parent] Bridge theme. Using this ensures
     * that our rows are formatted and styled consistently with the rest
     * of the theme.
     *
     * @since 1.1.0
     *
     * @param string $slug The slug name for the generic template.
     * @param string $name Optional. The name of the specialized template.
     */
    public static function wrap_template_part( $slug, $name = null ) {
        ?>

        <div class="vc_row wpb_row section vc_row-fluid grid_section">
            <div class="section_inner clearfix">
                <div class="section_inner_margin clearfix">
                    <div class="wpb_column vc_column_container vc_col-sm-12">
                        <div class="vc_column-inner">
                            <div class="wpb_wrapper">
                                <?php get_template_part( $slug, $name ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
    } 

    /**
     * Get/set $paged.
     *
     * For use in templates that can be used as static front page.
     * get_query_var( 'paged' ) returns nothing on front page, but get_query_var( 'page' ) does.
     * This returns and sets globally $paged so that the query and pagination work.
     *
     * @since  1.1.0
     * @global int $paged  Current Page number.
     * @return int         Current page number.
     */
    public static function get_current_page_number() {

        global $paged;

        // Use paged if given; otherwise page; otherwise 1
        $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );

        return $paged;

    }

    /**
     * Returns the given excerpt cleaned and sanitized.
     *
     * @since  1.1.0
     * @param  string  $excerpt  Optional. Post or page excerpt. Default: current post excerpt.
     * @return string            Sanitized excerpt.
     */
    public static function get_the_excerpt( $excerpt = null ) {

        $excerpt = isset( $excerpt ) ? $excerpt : get_the_excerpt();
        $excerpt = wp_strip_all_tags( $excerpt );
        $excerpt = wptexturize( $excerpt );
        return $excerpt;

    }

    /**
     * Returns the given excerpt cleaned, sanitized and trimmed to the given
     * number of words.
     *
     * @since  1.1.0
     * @param  string  $excerpt          Optional. Post or page excerpt. Default: current post excerpt.
     * @param  int     $number_of_words  Optional. Truncate excerpt to this length in words.
     * @return string                    Excerpt.
     */
    public static function get_trimmed_excerpt( $excerpt = null , $number_of_words = 55 ) {

        $excerpt = isset( $excerpt ) ? $excerpt : get_the_excerpt();
        $excerpt = self::get_the_excerpt( $excerpt );
        $excerpt = wp_trim_words( $excerpt, $number_of_words );
        return $excerpt;

    }

    /**
     * Return a date string as 'start - end' (e.g.: October 6, 2013 - July 7, 2016)
     *
     * @since  1.1.0
     * @param  int     $start_date   Epoch seconds.
     * @param  int     $end_date     Epoch seconds.
     * @param  string  $data_format  Date string format.
     * @return string                Formatted date string.
     */
    public static function create_date_range_string( $start_date, $end_date, $date_format ) {

        if ( $start_date === $end_date ) {

            return date_i18n( $date_format, $start_date );

        }

        $start_date_format = $date_format;
        $end_date_format   = $date_format;

        // Date formats
        // Make compact range of "June 1 - 5, 2015 if using "F j, Y" format (month and year removed from earliest date as not to be redundant)
        // If year is same but month different, becomes "June 30 - July 1, 2015"

        if ( ( 'F j, Y' === $date_format ) && ( date_i18n( 'Y', $start_date ) === date_i18n( 'Y', $end_date ) ) ) {

            // Remove year from start date
            $start_date_format = 'F j';

            // Months and year is same
            // Remove month from latest date
            if ( date_i18n( 'F', $start_date ) === date_i18n( 'F', $end_date) ) {
                $end_date_format = 'j, Y';
            }

        }

        $start_date_formatted = date_i18n( $start_date_format, $start_date );
        $end_date_formatted   = date_i18n( $end_date_format, $end_date );

        $date_string = $start_date_formatted . ' - ' . $end_date_formatted;

        return $date_string;

    }

    /**
     * Check if string is a URL.
     *
     * @since  1.1.0
     * @param  string $string  String to check for URL format
     * @return bool            True if string is URL
     */
    public static function is_url( $string ) {

        return preg_match( '/^(http(s*)):\/\//i', $string ) ? true : false;

    }

    /**
     * Check if URL is local.
     *
     * @since  1.1.0
     * @param  string $url  URL to test
     * @return bool         True if URL is local
     */
    public static function is_local_url( $url ) {

        return ( self::is_url( $url ) && preg_match( '/^' . preg_quote( home_url(), '/' ) . '/', $url ) ) ? true : false;

    }

    /**
     * Return date year and month from the current permalink.
     *
     * @since  1.1.0
     * @return array Sermon date components (e.g.: year, month index and name). See data structure below.
     */
    public static function get_month_archive_date_from_permalink() {
    
        global $wp;

        $request = $wp->request;  // Returns the 'year/month' from the uri path. e.g.: '2016/10'
        $matches = array();
        $dates   = array();

        if ( preg_match( '/([0-9]{4})\/([0-9]{1,2})/', $request, $matches ) ) {

            $dates['year']        = $matches[1];
            $dates['month_index'] = $matches[2];
            $dates['month_name']  = date( 'F', mktime( 0, 0, 0, $dates['month_index'] + 1, 0 ) );
        
        }

        return $dates;

    }

}
