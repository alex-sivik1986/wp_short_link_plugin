<?php
defined('ABSPATH') or die;
if (!class_exists('ShortLink_List_Table')) {
    require_once(  dirname(__FILE__) .'/wp-list-links-table.php');
}

add_action('wp_ajax_shorten_url', array( 'LinkPages', 'shorten_url' ));
add_action('wp_ajax_nopriv_shorten_url', array( 'LinkPages', 'shorten_url' ));

class LinkPages
{
    public function show_page_html()
    {
        if(!current_user_can('manage_options')) { exit(); }
        self::form_converter();

        $list_table = new ShortLink_List_Table();
        $list_table->prepare_items();
        ?>
        <div class="wrap">

            <h2><?=__('Список всіх посилань')?></h2>
            <form id="list-links-form" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?=$list_table->search_box('Пошук', 'search_link'); ?>
                <?=$list_table->display(); ?>
            </form>
        </div>
        <?php
    }

    public function shorten_url()
    {
        if (isset($_POST['original'])) {
            $original = trim($_POST['original']);
            if (!class_exists('CreateShortUrl')) {
                require_once(dirname(__FILE__, 2) . '/includes/create-short.php');
            }
            $createShortUrl = new CreateShortUrl($original);
            $wp_list_table = new ShortLink_List_Table();
            $wp_list_table->ajax_response($createShortUrl->getResult());
            wp_die();
        }
    }

    public static function form_converter()
    {
?>
        <div class="wrap" id="short_page">
            <h2><?php echo get_admin_page_title() ?></h2>
            <form id="upload_form" method="POST">
                <table class="form-table" role="presentation">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="long">Введіть оригінальне посилання для скорочення</label></th>
                        <td>
                            <input type="text" class="regular-text" name="original" value="">
                            <button class="button button-primary" type="submit">Скоротити</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <div class="reload"><img src="<?= plugin_dir_url( dirname( __FILE__ ) ) ?>img/1494.gif"></div>
            <div class="error-message"></div>
            <div class="short-url"></div>
        </div>
        <?php
    }
}

