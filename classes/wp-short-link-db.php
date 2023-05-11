<?php
defined('ABSPATH') or die;

class SqlTable
{
    public function create_table_links(): void
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();
        $create_table_sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}short_link(
                id bigint(20) unsigned NOT NULL auto_increment,
                link_original varchar(1000) NOT NULL,
                link_short varchar(10) NOT NULL,
                created_at timestamp NOT NULL default CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                INDEX link_short_index (link_short)
            ) $charset_collate";

        dbDelta($create_table_sql);
    }
}
