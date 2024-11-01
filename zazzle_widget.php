<?php
/*
Plugin Name: Zazzle Widget
Plugin URI: http://www.zazzletools.com/elegant-zazzle-plugin/
Description: A widget to display products from Zazzle
Author: Hupshee
Version: 2.0 
Author URI: http://www.zazzletools.com/ 

Installing
1. Upload files to wp-content/plugins/zazzle-widget
2. Activate it through the plugin management screen.
3. Go to Themes->Sidebar Widgets and drag and drop the widget to wherever you want to show it.
4. Configure the widget

Changelog
0.1 = First public release.
0.2 = Removed incorrect screenshot file.
0.3 = Removed incorrect screenshot file.
0.4 = Fixed bugs.
0.5 = Major upgrade: support for multiple product lines, multiple instances, and per post customization via custom fields.
0.6 = Fixed html bug.
0.7 = Improved display, customizable CSS.
0.8 = Filename change error.
0.9 = Increased number of possible products that can be shown to 20.
1.0 = Updated readme and FAQ text.
1.1 = Fixed bugs.
2.0 = Major udpate. Now includes support for all Zazzle domains and currencies. Improved display.
*/

/* License

   Zazzle Widget
   Copyright (C) 2010 zazzletools.com (hupshee@zazzletools.com)

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.

 */

// define some contants
if ( !defined('WP_CONTENT_URL') )
define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

// load library
if(!class_exists('EZP_Functions'))
{
    require(dirname(__FILE__) . '/functions.php');
}

class ZazzleWidget extends WP_Widget
{
    function ZazzleWidget()
    {
        $widget_ops = array(
            'classname'     => 'zazzle_widget', 
            'description'   => __("Zazzle Widget to showcase Zazzle products on your WordPress site.")
        );
        $control_ops = array('width' => 200, 'height' => 500);
        $this->WP_Widget('zazzle_widget', __('Zazzle Widget'), $widget_ops, $control_ops);
    }

    function widget($args, $instance)
    {
        // setup
        extract($args);

        // configuration parameters
        $widget_id          = esc_attr($instance['widget_id']);
        $widget_title       = esc_attr($instance['title']);
        $num_items          = esc_attr($instance['num_items']);
        $grid_cell_size     = esc_attr($instance['image_size']);
        $randomize          = esc_attr($instance['randomize']);
        $show_info          = esc_attr($instance['show_info']);
        $bg_color           = esc_attr($instance['bg_color']);
        $grid_width         = esc_attr($instance['grid_width']);
        $grid_cell_spacing  = esc_attr($instance['grid_cell_spacing']);
        $search_term        = esc_attr($instance['search_term']);
        $store_name         = esc_attr($instance['store_name']);
        $product_line       = esc_attr($instance['product_line']);
        $domain             = esc_attr($instance['domain']);
        $grid_cell_bg_color = $bg_color;
        $show_product_title = $show_info;

        // these parameters are not configurable
        $start_page = '1';
        $show_pagination = false;
        $show_sorting = false;
        $default_sort = 'popularity';
        $show_product_description = false;
        $show_product_creator = false;
        $show_product_price = false;
        $show_powered_by = false;

        // get post custom fields overrides
        $post_id = get_the_ID();
        $override_title = get_post_meta($post_id, $widget_id . '_title', true);
        if(!empty($override_title))
        {
            $widget_title = $override_title;
        }
        $override_store_name = get_post_meta($post_id, $widget_id . '_store_name', true);
        if(!empty($override_store_name))
        {
            $store_name = $override_store_name;
        }
        $override_product_line = get_post_meta($post_id, $widget_id . '_product_line', true);
        if(!empty($override_product_line))
        {
            $product_line = $override_product_line;
        }
        $override_search_term = get_post_meta($post_id, $widget_id . '_search_term', true);
        if(!empty($override_search_term))
        {
            $search_term = $override_search_term;
        }

        // if no store or search term provided, use post tags as search term
        if(empty($store_name) && empty($search_term))
        {
            $search_term = EZP_Functions::get_search_term_from_post_tags();
        }

        // set all store display options
        $_GET['contributorHandle']      = $store_name;
        $_GET['productLineId']          = $product_line;
        $_GET['productType']            = $product_type;
        $_GET['keywords']               = $search_term;
        $_GET['gridWidth']              = $grid_width;
        $_GET['gridCellSize']           = $grid_cell_size;
        $_GET['gridCellSpacing']        = $grid_cell_spacing;
        $_GET['gridCellBgColor']        = $grid_cell_bg_color;
        $_GET['showHowMany']            = $num_items;
        $_GET['randomize']              = $randomize;
        $_GET['startPage']              = $start_page;
        $_GET['showPagination']         = $show_pagination;
        $_GET['showSorting']            = $show_sorting;
        $_GET['defaultSort']            = $default_sort;
        $_GET['showProductDescription'] = $show_product_description;
        $_GET['showByLine']             = $show_product_creator;
        $_GET['showProductTitle']       = $show_product_title;
        $_GET['showProductPrice']       = $show_product_price;
        $_GET['showPoweredByZazzle']    = $show_powered_by;
        $_GET['domain']                 = $domain;

        // set the baseurl
        $url = get_permalink();
        $url .= (strpos($url, '?') === false)? '?' : '&';
        $_GET['baseUrl']                = $url;

        // get the data
        ob_start ();
        include (EZP_Functions::get_path() . 'zstore.php');
        $widget = ob_get_contents ();
        ob_end_clean ();

        ?>
            <?php echo $before_widget; ?>
            <?php echo $before_title . $widget_title . $after_title; ?> 


            <!-- Start of Zazzle Widget -->
            <?php echo $widget; ?>
            <!-- End of Zazzle Widget -->


            <?php echo $after_widget; ?>
            <?php
    }

    function update($new_instance, $old_instance)
    {
        if (!isset($new_instance['submit'])) 
        {
            return false;
        }
        $instance = $old_instance;

        $instance['widget_id'] = strip_tags($new_instance['widget_id']);
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['num_items'] = intval($new_instance['num_items']);
        $instance['image_size'] = strip_tags($new_instance['image_size']);
        $instance['randomize'] = intval($new_instance['randomize']);
        $instance['show_info'] = intval($new_instance['show_info']);
        $instance['bg_color'] = strip_tags($new_instance['bg_color']);
        $instance['grid_width'] = strip_tags($new_instance['grid_width']);
        $instance['grid_cell_spacing'] = strip_tags($new_instance['grid_cell_spacing']);
        $instance['search_term'] = strip_tags($new_instance['search_term']);
        $instance['store_name'] = strip_tags($new_instance['store_name']);
        $instance['product_line'] = strip_tags($new_instance['product_line']);
        $instance['domain'] = strip_tags($new_instance['domain']);

        return $instance;
    }

    function form($instance)
    {
        // defaults
        $instance = wp_parse_args( (array) $instance, array(
        'widget_id'         => 'zw1',
        'title'             => 'My Zazzle Products',
        'num_items'         => 3,
        'image_size'        => 'medium',
        'randomize'         => 0,
        'show_info'         => 0,
        'bg_color'          => 'FFFFFF',
        'grid_width'        => '300',
        'grid_cell_spacing' => '9',
        'search_term'       => '',
        'store_name'        => '',
        'product_line'      => '',
        'domain'            => 'com'
        ) );

        $widget_id          = esc_attr($instance['widget_id']);
        $title              = esc_attr($instance['title']);
        $num_items          = intval($instance['num_items']);
        $image_size         = esc_attr($instance['image_size']);
        $randomize          = intval($instance['randomize']);
        $show_info          = intval($instance['show_info']);
        $bg_color           = esc_attr($instance['bg_color']);
        $grid_width         = esc_attr($instance['grid_width']);
        $grid_cell_spacing  = esc_attr($instance['grid_cell_spacing']);
        $search_term        = esc_attr($instance['search_term']);
        $store_name         = esc_attr($instance['store_name']);
        $product_line       = esc_attr($instance['product_line']);
        $domain             = esc_attr($instance['domain']);

        ?>


            <p>
            <label for = "<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
                <input  class   = "widefat" 
                        id      = "<?php echo $this->get_field_id('title'); ?>" 
                        name    = "<?php echo $this->get_field_name('title'); ?>"
                        type    = "text" 
                        value   = "<?php echo $title; ?>" />
            </label>
            </p>

            <p>
            <label for = "<?php echo $this->get_field_id('store_name'); ?>"><?php _e('Store Name:'); ?>
                <input  class   = "widefat" 
                        id      = "<?php echo $this->get_field_id('store_name'); ?>" 
                        name    = "<?php echo $this->get_field_name('store_name'); ?>"
                        type    = "text" 
                        value   = "<?php echo $store_name; ?>" />
            </label>
            </p>

            <p>
            <label for = "<?php echo $this->get_field_id('domain'); ?>"><?php _e('Domain:'); ?>
                <input  class   = "widefat" 
                        id      = "<?php echo $this->get_field_id('domain'); ?>" 
                        name    = "<?php echo $this->get_field_name('domain'); ?>"
                        type    = "text" 
                        value   = "<?php echo $domain; ?>" />
            </label>
            </p>
            <p>

            <label for = "<?php echo $this->get_field_id('product_line'); ?>"><?php _e('Product Line:'); ?>
                <input  class   = "widefat" 
                        id      = "<?php echo $this->get_field_id('product_line'); ?>" 
                        name    = "<?php echo $this->get_field_name('product_line'); ?>"
                        type    = "text" 
                        value   = "<?php echo $product_line; ?>" />
            </label>
            </p>

            <p>
            <label for = "<?php echo $this->get_field_id('search_term'); ?>"><?php _e('Search Term:'); ?>
                <input  class   = "widefat" 
                        id      = "<?php echo $this->get_field_id('search_term'); ?>" 
                        name    = "<?php echo $this->get_field_name('search_term'); ?>"
                        type    = "text" 
                        value   = "<?php echo $search_term; ?>" />
            </label>
            </p>


            <p>
            <label for = "<?php echo $this->get_field_id('num_items'); ?>"><?php _e('Number of Items:'); ?>
                <select id      = "<?php echo $this->get_field_id('num_items'); ?>" 
                        name    = "<?php echo $this->get_field_name('num_items'); ?>" >
                        <?php for ( $i = 1; $i <= 20; ++$i ) 
                            echo "<option value='$i' " . selected($i, $num_items) . ">$i</option>"; ?>
                </select>
            </label>
            </p>

            <p>
            <label for = "<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Image Size:'); ?>
                <select id      = "<?php echo $this->get_field_id('image_size'); ?>"
                        name    = "<?php echo $this->get_field_name('image_size'); ?>" >
                            <?php echo "<option value='tiny'" . selected('tiny', $image_size) . '>Tiny</option>'; ?>
                            <?php echo "<option value='small'" . selected('small', $image_size) . '>Small</option>'; ?>
                            <?php echo "<option value='medium'" . selected('medium', $image_size) . '>Medium</option>'; ?>
                            <?php echo "<option value='large'" . selected('large', $image_size) . '>Large</option>'; ?>
                            <?php echo "<option value='huge'" . selected('huge', $image_size) . '>Huge</option>'; ?>
                </select>
            </label>
            </p>

            <p>
            <label for = "<?php echo $this->get_field_id('randomize'); ?>"><?php _e('Randomize Order:'); ?>
                <input  id      = "<?php echo $this->get_field_id('randomize'); ?>" 
                        name    = "<?php echo $this->get_field_name('randomize'); ?>" 
                        type    = "checkbox" 
                        value   = "1" <?php echo $randomize? 'checked' : ''; ?> /> 
            </label>
            </p>

            <p>
            <label for = "<?php echo $this->get_field_id('show_info'); ?>"><?php _e('Show Product Info:'); ?>
                <input  id      = "<?php echo $this->get_field_id('show_info'); ?>" 
                        name    = "<?php echo $this->get_field_name('show_info'); ?>" 
                        type    = "checkbox" 
                        value   = "1" <?php echo $show_info? 'checked' : ''; ?> /> 
            </label>
            </p>

            <p>
            <label for = "<?php echo $this->get_field_id('bg_color'); ?>"><?php _e('Background Color:'); ?>
                <input  class   = "widefat" 
                        id      = "<?php echo $this->get_field_id('bg_color'); ?>" 
                        name    = "<?php echo $this->get_field_name('bg_color'); ?>"
                        type    = "text" 
                        value   = "<?php echo $bg_color; ?>" />
            </label>
            </p>

            <p>
            <label for = "<?php echo $this->get_field_id('grid_width'); ?>"><?php _e('Grid Width:'); ?>
                <input  class   = "widefat" 
                        id      = "<?php echo $this->get_field_id('grid_width'); ?>" 
                        name    = "<?php echo $this->get_field_name('grid_width'); ?>"
                        type    = "text" 
                        value   = "<?php echo $grid_width; ?>" />
            </label>
            </p>

            <p>
            <label for = "<?php echo $this->get_field_id('grid_cell_spacing'); ?>"><?php _e('Grid Cell Spacing:'); ?>
                <input  class   = "widefat" 
                        id      = "<?php echo $this->get_field_id('grid_cell_spacing'); ?>" 
                        name    = "<?php echo $this->get_field_name('grid_cell_spacing'); ?>"
                        type    = "text" 
                        value   = "<?php echo $grid_cell_spacing; ?>" />
            </label>
            </p>

            <p>
            <label for = "<?php echo $this->get_field_id('widget_id'); ?>"><?php _e('Widget Id:'); ?>
                <input  class   = "widefat" 
                        id      = "<?php echo $this->get_field_id('widget_id'); ?>" 
                        name    = "<?php echo $this->get_field_name('widget_id'); ?>"
                        type    = "text" 
                        value   = "<?php echo $widget_id; ?>" />
            </label>
            </p>

            <p>
            Visit <a href="http://www.zazzletools.com/zazzle-widget/">zazzletools.com</a> for advanced usage such as multiple instances and custom fields.
            </p>

            <input  type    = "hidden" 
                    id      = "<?php echo $this->get_field_id('submit'); ?>" 
                    name    = "<?php echo $this->get_field_name('submit'); ?>" 
                    value   = "1" />
            <?php
    }

}

add_action('wp_print_styles', array('EZP_Functions', 'add_styles'));
add_action('widgets_init', create_function('', 'return register_widget("ZazzleWidget");'));

?>
