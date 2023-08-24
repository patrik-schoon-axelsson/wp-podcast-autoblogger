<?php

require_once('db.php');

global $wpdb;

function cron_check_for_episodes() {
    $feeds = get_podcast_feeds();
    $nonce = wp_create_nonce('add_episodes_nonce');

    foreach($feed as $feeds) {

        $data = array("body" => array(
            'action' => 'parse_feed_episodes',
            'add_episodes_nonce' => $nonce,
            'id' => $feed['id']
        ));
        wp_remote_post( admin_url('admin-ajax.php'), $data);
    }
}