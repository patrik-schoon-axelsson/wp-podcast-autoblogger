<?php

// I guess this is API & Ajax callbacks, will have to check what the correct best practice is here eventually.

require_once plugin_dir_path(__FILE__) . 'includes/db.php';
require_once plugin_dir_path(__FILE__) . 'includes/parser.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-post-episodes.php';

// REST API FUNCTIONS

function register_feed_table_endpoint() {
    register_rest_route('podcast-autoblogger/v1', '/feeds', array(
        'methods' => 'GET',
        'callback' => 'get_podcast_feeds',
        'permission_callback' => '__return_true',
    ));
}

function register_episodes_endpoint() {
    register_rest_route('podcast-autoblogger/v1', '/episodes/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_episodes_by_id'
    ));
} 


// AJAX FUNCTIONS

function delete_rss_feed() {
    if (isset($_POST['delete_feed_nonce']) && wp_verify_nonce($_POST['delete_feed_nonce'], 'delete_feed_nonce')) {
        global $wpdb;
        $id = $_POST['id'];
        $prefix = $wpdb->prefix;
        $table_name = $prefix . "pod_autoblog";

        $wpdb::delete($table_name, array('ID' => $id));

        wp_send_json(array("msg" => "Deleted feed with ID: $id"));
    } else {
        wp_send_json_error('Invalid nonce', 403);
    }
}

function add_rss_feed() {
    
    if (isset($_POST['add_feed_nonce']) && wp_verify_nonce($_POST['add_feed_nonce'], 'add_feed_nonce')) {
    global $wpdb;

    $feed_url = sanitize_text_field($_POST['feed_url']);
    $prefix = $wpdb->prefix;
    $table_name = $prefix . "pod_autoblog";
    
    
        $parsed_data = parse_simplexml_to_array($_POST['feed_url']);
        
        $data = array(
            'title' => $parsed_data['title'],
            'description' => $parsed_data['description'],
            'feed_url' => $feed_url,
        );

        $new_row = $wpdb->insert($table_name, $data);    

        if(!$new_row) {
            wp_send_json_error(array('error' => "Could not write new row to custom table $table_name"), 500);
        } else {

            $new_feeds = get_podcast_feeds();

            wp_send_json( array(
                "msg" => "Successfully added $title to database!",
                "feeds" => $new_feeds
            ), 200);
        }

    } else {
        wp_send_json_error('Invalid nonce', 403);
    }
}


function eps_to_cpt_episodes($eps) {
    global $wpdb;

    $query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'episode' AND post_status = 'publish'";
    $db_results = $wpdb->get_results($query, ARRAY_A);
    $titles = array_column($db_results, 'post_title');

    $res = array_filter($eps, function ($i) use ($titles) {
        return !in_array($i['title'], $titles);
    });

    // Cleanup indexes in the filtered array.
    $res = array_values($res);
    
    return $res;
}

function parse_feed_episodes() {
    if (isset($_POST['add_episodes_nonce']) && wp_verify_nonce($_POST['add_episodes_nonce'], 'add_episodes_nonce')) {
     
    $id = $_POST['id'];
    $feed = get_single_podcast_feed($id);
    $title = $feed['title'];
    
    $parsed_data = parse_simplexml_to_array($feed['feed_url']);
    
    if($parsed_data == false){
        wp_send_json_error(array(
            "error" => "Error parsing the url of $title, please revalidate that the feed_url link is valid RSS.", 
            "feed_url" => $feed['feed_url'],
            "feed_id" => $feed['id']),
            500, JSON_UNESCAPED_SLASHES);
      wp_die();  
    };
    
    $new_episodes = eps_to_cpt_episodes($parsed_data['items']);
    $res = [];
    
    foreach($new_episodes as $episode) {

        $Q = new WP_Query(array('post_type' => 'episode', 'title' => $episode['title']));

        if($Q -> have_posts()){
            // If there already is a post that matches the title, we just proceed to the next item in the loop.
            // I should probably expand this validation at a later date to include checking the health of pseudo-foreignkeys with the Feeds.
            wp_reset_postdata();
        } else {
            $ep = wp_insert_post(array(
                'post_title' => $episode['title'],
                'post_content' => $episode['description'],
                'post_date' => $episode['pubDate'],
                'post_type' => 'episode',
                'post_status' => 'publish'
            ));
            
            if ($ep) {
                // Sanitizing and clean-up. Sanitize title is needed because we want this to be the URL slug.
                $tag = sanitize_title($parsed_data['title']);

                $term = term_exists($tag, 'feed');

                if ($term !== 0 && $term !== null) {
                    
                } else {
                    
                    wp_insert_term($tag, 'feed', array('description' => $parsed_data['description']));
                }

                update_post_meta($ep, 'episode_url', $episode['url']);
                update_post_meta($ep, 'feed_id', $id);
                wp_set_post_terms($ep, $tag, 'rss_feed', true);
                $res[] = array('id' => $ep, 'title' => $episode['title']);
            } 
        }
    }

    if(empty($res)) {
        $res[] = array("msg" => 'No new episodes found in feed: ' . $title);
    }

    wp_send_json($res, JSON_UNESCAPED_SLASHES);
     
    } else {
    // Nonce is invalid, return an error response
    wp_send_json_error('Invalid nonce', 403);
    }
    wp_die();
}