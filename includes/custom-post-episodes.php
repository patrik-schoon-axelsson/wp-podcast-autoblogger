<?php

require_once(__DIR__ . '/db.php');

function custom_post_type_episodes() {
    $labels = array(
        'name'               => _x( 'Episodes', 'post type general name'),
        'singular_name'      => _x( 'Episode', 'post type singular name'),
        'menu_name'          => _x( 'Episodes', 'admin menu'),
        'name_admin_bar'     => _x( 'Episode', 'add new on admin bar'),
        'add_new'            => _x( 'Add New', 'episode'),
        'add_new_item'       => __( 'Add New Episode'),
        'new_item'           => __( 'New Episode'),
        'edit_item'          => __( 'Edit Episode'),
        'view_item'          => __( 'View Episode'),
        'all_items'          => __( 'All Episodes'),
        'search_items'       => __( 'Search Episodes'),
        'parent_item_colon'  => __( 'Parent Episodes:'),
        'not_found'          => __( 'No episodes found.',),
        'not_found_in_trash' => __( 'No episodes found in Trash.')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'episodes' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
    );

    register_post_type( 'episode', $args );
    flush_rewrite_rules();
}

function register_custom_taxonomy_feeds() {
    $labels = array(
        'name'                       => _x('RSS Feeds', 'taxonomy general name'),
        'singular_name'              => _x('RSS Feed', 'taxonomy singular name'),
        'search_items'               => __('Search RSS Feeds'),
        'popular_items'              => __('Popular RSS Feeds'),
        'all_items'                  => __('All RSS Feeds'),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __('Edit Feed'),
        'update_item'                => __('Update Feed'),
        'add_new_item'               => __('Add New Feed'),
        'new_item_name'              => __('New Feed Name'),
        'separate_items_with_commas' => __('Separate RSS Feeds with commas'),
        'add_or_remove_items'        => __('Add or remove RSS Feeds'),
        'choose_from_most_used'      => __('Choose from the most used RSS Feeds'),
        'not_found'                  => __('No RSS Feeds found.'),
        'menu_name'                  => __('RSS Feeds'),
    );

    $args = array(
        'hierarchical'      => false, 
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'rss_feed'), 
    );
    
    register_taxonomy('rss_feed', 'episode', $args); 
    flush_rewrite_rules();
}

function get_or_create_term($term_name) {
    $term = term_exists($term_name, 'rss_feed');

    if ($term !== 0 && $term !== null) {
        return $term['term_id'];
    } else {
        $new_term = wp_insert_term($term_name, 'rss_feed');
        if (!is_wp_error($new_term)) {
            return $new_term['term_id'];
        } else {
            return false;
        }
    }
}

add_action('init', 'register_custom_taxonomy_feeds');

function add_custom_meta_field() {
    if (isset($post) && is_object($post)){
        $post_id = $post->ID;
        get_post_meta('episode_url', $post_id); 
    }
}

add_action( 'add_meta_boxes', 'add_custom_meta_field' );

function save_custom_meta_field( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['episode_id'] ) ) {
        $episode_id = sanitize_text_field( $_POST['episode_id'] );
        update_post_meta( $post_id, 'episode_id', $episode_id );
    }
}

add_action( 'save_post_episode', 'save_custom_meta_field' );