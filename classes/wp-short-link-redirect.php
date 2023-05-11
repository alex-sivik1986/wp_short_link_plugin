<?php
defined('ABSPATH') or die;

class RedirectShortLink
{
    public static function redirect_user()
    {
        global $wp_query;
        if($wp_query->is_404())
        {
            $result = $wp_query->query;
            $value = '';
            if(is_array($result) and !array_key_exists('attachment',$result) and count($result) <= 2)
            {
                $value = implode("", $result);
            }

            $data = self::findSQLLink($value);
            if(is_array($data) && !empty($data))
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