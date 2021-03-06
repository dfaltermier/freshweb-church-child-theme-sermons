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

    /**
     * Define how long we want our sermon excerpt to be in number of words.
     *
     * @since 1.1.0
     */
    const SERMON_EXCERPT_LENGTH = 70;

    function __construct() {

        // Set our own excerpt length.
        add_filter( 'excerpt_length', array( $this, 'set_excerpt_length' ), 2000 );

    }

    /**
     * Return how long we want our sermon excerpts to be in number of words.
     *
     * @since  1.1.0
     * @return int   Number of words.
     */
    public function set_excerpt_length() {

        return self::SERMON_EXCERPT_LENGTH;

    }

    /**
     * Returns true if the FreshWeb Studio Sermons plugin is activated.
     *
     * Use this method before attempting to display any Sermon-specific pages.
     * This will eliminate broken pages when the plugin is not activated.
     *
     * FW_SERMONS_IS_ACTIVATED is globally defined in the plugin just for
     * this detection purpose.
     *
     * @since  1.1.0
     * @return bool   True if plugin is installed.
     */
    public static function is_sermons_plugin_activated() {

        return defined( 'FW_SERMONS_IS_ACTIVATED' ) ? FW_SERMONS_IS_ACTIVATED : false;

    }

    /**
     * Redirects user to the home page if the FreshWeb Studio Sermons
     * plugin is not activated.
     *
     * @since  1.1.0
     */
    public static function redirect_if_sermons_not_activated() {

        if ( ! self::is_sermons_plugin_activated() ) {

            /*
             * A 302 termpoary redirect will keep browsers checking for the sermons
             * page when they come back online.
             */
            $http_status_code = 302;
            $location = home_url();

            wp_safe_redirect( $location, $http_status_code );

        }

    }

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
     * Create an excerpt from the given content.
     *
     * @since  1.1.4
     * @param  string  $content      Source text.
     * @param  bool    $do_truncate  Truncate SERMON_EXCERPT_LENGTH words. Default: true.
     * @return string            Created excerpt.
     */
    public static function create_an_excerpt( $content, $do_truncate = true ) {

        $excerpt = wp_strip_all_tags( $content );
        $excerpt = wptexturize( $excerpt );

        if ( $do_truncate ) {
            $excerpt = wp_trim_words( $excerpt, self::SERMON_EXCERPT_LENGTH );
        }

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
