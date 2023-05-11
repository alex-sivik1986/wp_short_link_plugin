<?php
defined('ABSPATH') or die;

class RedirectShortLink
{
    public static function redirect_user()
    {
        global $wp_query;

        if($wp_query->is_404())
        {
            $data = self::findSQLLink($wp_query->query["pagename"]);
            if(is_array($data))
            {
                wp_redirect($data['link_original']);
                exit();
            }
        }
    }

    private static function findSQLLink($data)
    {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}short_link WHERE link_short = %s", $data ), ARRAY_A );
    }

}