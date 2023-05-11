<?php
/*
 * Plugin Name:       Short Link
 * Description:       Створює короткі посилання
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Oleksandr Sivodedov
 */

defined('ABSPATH') or die;
require_once(  dirname(__FILE__) .'/classes/wp-short-link-db.php');
require_once(  dirname(__FILE__) .'/classes/class-admin-page.php');
require_once(  dirname(__FILE__) .'/classes/wp-short-link-redirect.php');
require_once(  dirname(__FILE__) .'/includes/shortcode.php');

if(!class_exists('ShortLink'))
{
    class ShortLink
    {
        public static function activation(): void
        {
            $sql = new SqlTable();
            $sql->create_table_links();
        }

        public static function menu_load()
        {
            $page_title = 'Створення коротких посилань';
            $menu_title = 'Short links';
            $capability ='manage_options';
            $menu_slug = 'wp-short-link';
            $function = array('LinkPages', 'show_page_html' );
            $icon_url = 'dashicons-format-links';
            $position = 9;
            add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        }

        public static function script_work()
        {
            wp_enqueue_script( 'script-work', plugins_url( 'assets/admin/js/builder.js',  __FILE__)  );
            wp_enqueue_style( 'styles-work', plugins_url( 'assets/admin/css/styles.css',  __FILE__)  );
            //wp_localize_script( 'script-work', 'ajax_short', plugins_url( 'includes/create-short.php',  __FILE__)  );
        }

        public static function deactivation()
        {

        }
    }

    register_activation_hook(__FILE__, array( 'ShortLink', 'activation' ));

    add_action( 'admin_menu', array( 'ShortLink', 'menu_load' ) );
    add_action('admin_enqueue_scripts', array('ShortLink','script_work'));
    add_shortcode('short_links', 'user_short_shortcode');
    add_action('template_redirect', array('RedirectShortLink', 'redirect_user'));

    register_deactivation_hook(__FILE__, array( 'ShortLink', 'deactivation' ));
}

