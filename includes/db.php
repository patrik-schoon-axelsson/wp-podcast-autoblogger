<?php

function get_podcast_feeds() {
    global $wpdb;
    $prefix = $wpdb->prefix;

    $table_name = $prefix . "pod_autoblog";

    $query = "SELECT * FROM $table_name";
    $results = $wpdb->get_results($query, ARRAY_A);
    
    return $results;
}

function get_single_podcast_feed($id) {

    global $wpdb;
    $prefix = $wpdb->prefix;

    $table_name = $prefix . "pod_autoblog";
    
    $query = "SELECT * FROM `wpoz_pod_autoblog` WHERE `id` = $id";
    $results = $wpdb->get_row($query, ARRAY_A);
    
    return $results;
}