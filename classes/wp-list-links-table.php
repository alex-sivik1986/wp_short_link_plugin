<?php
defined('ABSPATH') or die;
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class ShortLink_List_Table extends WP_List_Table {

    public function prepare_items() {
        global $wpdb;
        $this->process_bulk_action();

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $table_name = $wpdb->prefix . 'short_link';

        $per_page = 25;
        $current_page = $this->get_pagenum();
        $offset = ( $current_page - 1 ) * $per_page;

        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

        if ( isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
            $items = $this->get_table_search(wp_unslash($_REQUEST['s']));
            $total_items = count($items);
        } else {
            $items = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT $per_page OFFSET $offset", ARRAY_A );
        }

        $primary  = 'id';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);
        usort($items, array(&$this, 'usort_reorder'));

        // Set pagination
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ) );

        $this->items = $items;
    }

    private function get_table_search( $search = '' ) {
        global $wpdb;

        $table = $wpdb->prefix . 'short_link';

        return $wpdb->get_results(
            "SELECT * from {$table} WHERE link_original Like '%{$search}%' OR link_short Like '%{$search}%'",
            ARRAY_A
        );
    }

    public function ajax_response( $data = [] ) {
        $this->prepare_items();

        ob_start();
        if ( ! empty( $_REQUEST['no_placeholder'] ) ) {
            $this->display_rows();
        } else {
            $this->display_rows_or_placeholder();
        }

        $rows = ob_get_clean();
        $response = array( 'rows' => $rows );
        if ( isset( $this->_pagination_args['total_items'] ) ) {
            $response['total_items_i18n'] = sprintf(
            /* translators: Number of items. */
                _n( '%s item', '%s items', $this->_pagination_args['total_items'] ),
                number_format_i18n( $this->_pagination_args['total_items'] )
            );
        }
        if ( isset( $this->_pagination_args['total_pages'] ) ) {
            $response['total_pages']      = $this->_pagination_args['total_pages'];
            $response['total_pages_i18n'] = number_format_i18n( $this->_pagination_args['total_pages'] );
        }

        die( wp_json_encode( array_merge($response, $data) ) );
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'link_original':
            case 'link_short':
            case 'created_at':
            default:
                return $item[$column_name];
        }
    }

    protected function get_sortable_columns(): array
    {
        return array(
            'link_original' => array('link_original', false),
            'link_short'   => array('link_short', false),
            'created_at'   => array('created_at', true)
        );
    }

    function usort_reorder($a, $b)
    {
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'created_at';

        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'desc';
        $result = strcmp($a[$orderby], $b[$orderby]);
        return ($order === 'asc') ? $result : -$result;
    }

    public function get_columns()
    {
        return [
            'cb' => '<input type="checkbox" />',
            'link_original' => 'Повне посилання',
            'link_short' => 'Скорочене посилання',
            'created_at' => 'Дата створення',
        ];
    }

    protected function column_cb( $item ) {
        return sprintf(
            '<label class="screen-reader-text" for="link_' . $item['id'] . '">' . sprintf( __( 'Select %s' ), $item['link_short'] ) . '</label>'
            . "<input type='checkbox' name='link[]' id='link_{$item['id']}' value='{$item['id']}' />"
        );
    }

    public function get_hidden_columns(): array
    {
        return array();
    }

    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Видалити'
        );
        return $actions;
    }

    public function process_bulk_action() {

        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];
            if ( ! wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Nope! Security check failed!' );
        }

        $action = $this->current_action();

        switch ( $action ) {
            case 'delete':
                global $wpdb;
                $table_name = $wpdb->prefix . 'short_link';
                if ('delete' === $this->current_action()) {
                    $id_s = isset($_REQUEST['link']) ? $_REQUEST['link'] : array();
                    if (is_array($id_s)) $id_s = implode(',', $id_s);
                    if (!empty($id_s)) {
                        $wpdb->query("DELETE FROM $table_name WHERE id IN($id_s)");
                    }
                }

                break;
            default:
                break;
        }
        return;

    }
}
