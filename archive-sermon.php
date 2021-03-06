<?php
/**
 * Displays the paginated list of sermons sorted by date.
 *
 * WordPress invokes this file when the user selects a date from the 
 *     http://your-church-domain/2 sermons/dates/ page.
 *
 * WordPress loads this file with a url similar to:
 *     http://your-church-domain/2016/10/?post_type=sermon
 * where the date is the date selected by the user.
 *
 * @package    FreshWeb_Church
 * @subpackage Page
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 *
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Redirect user to home page if the FreshWeb Studio Sermons plugin is not activated.
FW_Child_Common_Functions::redirect_if_sermons_not_activated();

/**
 * Returns the text used in the header of this page. This text overlays the background
 * header image. This method is called via an apply_filter() from the method referenced
 * by 'see' below.
 *
 * @since 1.1.0
 * @see   FW_Child_Sermon_Functions::get_sermon_header_banner_data()
 *
 * @param  array $data Header text components.
 * @return array       Modified text.
 */
function fw_child_sermon_header_banner_data( $data ) {

    $archive_dates = FW_Child_Common_Functions::get_month_archive_date_from_permalink();

    $data['subtitle'] = ( ! empty( $archive_dates ) )
        ? 'on ' . $archive_dates['month_name'] . ' ' . $archive_dates['year']
        : 'Browse by date';

    return $data;

}
// Make method above available via apply_filter().
add_filter( 'fw_child_sermon_header_banner_data', 'fw_child_sermon_header_banner_data' );

?>

<?php get_header(); ?>

<div class="full_width">

    <div class="full_width_inner">

        <section class="fw-child-sermon-section">

            <?php

            // Display header banner with page title.
            get_template_part( FW_CHILD_THEME_PARTIALS_DIR . '/content-header-banner' );

            // Display sermon navigation icons.
            FW_Child_Common_Functions::wrap_template_part(
                 FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-navigation'
            );

            // Display sermons.
            FW_Child_Common_Functions::wrap_template_part(
                 FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-loop'
            );

            // Display pagination.
            FW_Child_Common_Functions::wrap_template_part(
                 FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-pagination'
            );

            ?>

        </section>

    </div>

</div>

<?php get_footer(); ?>
