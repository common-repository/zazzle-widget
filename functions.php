<?php
class EZP_Functions
{
    function get_path()
    {
        return dirname(__FILE__) . '/';
    }

    function get_url()
    {
        return WP_CONTENT_URL.'/plugins/'.basename(dirname(__FILE__)) . '/';
    }

    function add_styles()
    {
        $version = '0.1';
        $css = EZP_Functions::get_url() . 'css/zstore.css';
        wp_enqueue_style('ezp', $css, false, $version, 'screen');
    }

    function get_query_param_bool($parameter)
    {
        $value = $_GET[$parameter];
        return (isset($value) && (($value == 'true' || $value == 1)));
    }

    function get_search_term_from_post_tags()
    {
        $post_tags = get_the_tags();
        if($post_tags)
        {
            foreach($post_tags as $tag)
            {
                $search_term .= $tag->name . ' OR ';
            }
            $search_term = substr($search_term, 0, -4);
        }
        return $search_term;
    }
}
?>
