<?php

require(dirname(__FILE__,5) . '/wp-load.php');

function frontend_wp_enqueue_scripts()
{
    wp_enqueue_script(
        'script-users',
        plugins_url('/assets/front/js/script.js', dirname(__FILE__, 1)),
        array('jquery'),
        '1.0',
        true);
    wp_enqueue_style(
        'style-users',
        plugins_url('/assets/front/css/styles.css', dirname(__FILE__, 1))
    );
    wp_localize_script(
        'script-users',
        'ajax_short',
        admin_url('admin-ajax.php')
    );
}

function user_short_shortcode(): string
{
    return '
        <main class="links_box">
            <form class="user_short_form" id="short_form" method="POST">
                <label for="original_link">Введіть оригінальне посилання для скорочення</label>
                <input id="original_link" class="wp-filter-search" name="original" type="text" />
            <div class="error-message"></div>
            <div class="reload"><img src="' . plugin_dir_url(dirname(__FILE__)) . 'img/icons8-spinner.gif"></div>
            <div class="short-url"></div>
                <div class="full-width">
                    <button type="submit">Скоротити</button>
                </div>
            </form>
        </main>';
}

function short_url_user()
{
    if (isset($_POST['original'])) {
        $original = trim($_POST['original']);
        if (!class_exists('CreateShortUrl')) {
            require_once(dirname(__FILE__, 2) . '/includes/create-short.php');
        }
        $createShortUrl = new CreateShortUrl($original);
        wp_send_json($createShortUrl->getResult());
        wp_die();
    }
}

add_action( 'wp_enqueue_scripts', 'frontend_wp_enqueue_scripts' );
add_action('wp_ajax_short_url_user', 'short_url_user');
add_action('wp_ajax_nopriv_short_url_user', 'short_url_user');



