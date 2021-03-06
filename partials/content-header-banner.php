<?php
/**
 * Displays the header image with the title and subtitle overlay on each page.
 *
 * Because we are a child theme of the Bridge parent theme, we need to wrap our
 * contents within the parent theme's html.
 *
 * @package    FreshWeb_Church
 * @subpackage Partial
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Get page-specific header background image (via $class) and title/subtitle.
$banner_data = FW_Child_Sermon_Functions::get_sermon_header_banner_data();

$class    = $banner_data['class'];
$title    = $banner_data['title'];
$subtitle = $banner_data['subtitle'];

// The below page header structure leverages the parent theme's use of html/styles
// used on pages throughout site.
?>
<div class="vc_row wpb_row section vc_row-fluid
            child-page-header <?php echo $class; ?>" style="text-align:center;">
    <div class="full_section_inner clearfix">
        <div class="wpb_column vc_column_container vc_col-sm-12">
            <div class="vc_column-inner">
                <div class="wpb_wrapper">
                    <div class="wpb_text_column wpb_content_element">
                        <div class="wpb_wrapper">
                            <h2><?php echo $title; ?></h2>
                        </div>
                    </div>
                    <div class="wpb_text_column wpb_content_element">
                        <div class="wpb_wrapper">
                            <h3><?php echo $subtitle; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
