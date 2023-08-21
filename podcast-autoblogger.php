<?php
/**
 * Plugin Name:     Podcast Autoblogger
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A plugin for automatically subscribing your Wordpress-site to podcast RSS Feeds and automatically updating with posts for new episodes.
 * Author:          Patrik Schöön-Axelsson
 * Author URI:      https://www.schoonaxelsson.com
 * Text Domain:     podcast-autoblogger
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Podcast_Autoblogger
 */

require_once(plugin_dir_path( __FILE__ ) . 'includes/custom-post-episodes.php');
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';
require_once plugin_dir_path(__FILE__) . 'api-callbacks.php';

global $wpdb;

function initial_setup_db() {
    
    global $wpdb;

    $prefix = $wpdb->prefix;
    $table_name = $prefix . "pod_autoblog";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text NOT NULL,
        `feed_url` varchar(255) NOT NULL,
        `web_link` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
      )";

    dbDelta($sql);
}

function first_activation_setup() {
    initial_setup_db();
}

function feed_mgmt_admin_page() {
    add_menu_page(
        'Podcast Feeds',   
        'Podcast Feeds',   
        'manage_options',  
        'podcast-feed-management', 
        'render_admin_page', 
        'dashicons-microphone' 
    );
}


// JavaScript variable-injection for admin-page etc.

function enqueue_admin_js() {
    wp_enqueue_script('admin-page', plugin_dir_url( __FILE__ ) . 'admin/js/admin-page.js', NULL);

    $data = array(
        'restUrl' => get_rest_url(),
        'add_episodes_nonce' => wp_create_nonce('add_episodes_nonce'),
        'add_feed_nonce' => wp_create_nonce('add_feed_nonce'),
        'delete_feed_nonce' => wp_create_nonce('delete_feed_nonce')
    );

    
    wp_localize_script('admin-page', 'phpData', $data);
}

add_action('admin_enqueue_scripts', 'enqueue_admin_js');

add_action('rest_api_init', 'register_feed_table_endpoint');

add_action('rest_api_init', 'register_episodes_endpoint');

add_action('wp_ajax_parse_feed_episodes', 'parse_feed_episodes');

add_action('admin_menu', 'feed_mgmt_admin_page');

add_action( 'init', 'custom_post_type_episodes' );

register_activation_hook( __FILE__, 'first_activation_setup');