<?php

require_once plugin_dir_path(__FILE__) . '../includes/db.php';

function render_admin_page() {
    global $wpdb;

    $prefix = $wpdb->prefix;
    $table_name = $prefix . "pod_autoblog";
    $table_data = get_podcast_feeds();
    
    if (isset($_POST['submit_podcast_feed']) && check_admin_referer('podfeed-submission-nonce')) {
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_text_field($_POST['description']);
        $feed_url = sanitize_text_field($_POST['feed_url']);
        $web_link = sanitize_text_field($_POST['web_link']);

        $data = array(
            'title' => $title,
            'description' => $description,
            'feed_url' => $feed_url,
            'web_link' => $web_link,
        );

        $wpdb->insert($table_name, $data);
        echo "<h3>New Feed Added!</h3>";
    }

    ?>
    <div class="wrap aligncenter" x-data="{ msg: 'Podviewer Plugin' }">
        <h1 x-text="msg" style="text-align: center"></h1>

        <!-- Podcast feed form -->
        <form method="post" class="form-table">
        <?php wp_nonce_field('podfeed-submission-nonce'); ?>
            <label for="title" class="form-field">Title:</label><br>
            <input type="text" name="title" id="title" class="regular-text" required><br>
            <label for="description" class="form-field">Description:</label><br>
            <textarea name="description" id="description" class="regular-text" required></textarea><br>
            <label for="feed_url" class="form-field">Feed URL:</label><br>
            <input type="text" name="feed_url" class="regular-text" id="feed_url" required><br>
            <label for="web_link" class="form-field">Web Link:</label><br>
            <input type="text" name="web_link" class="regular-text" id="web_link" required><br>
            <br>        
            <input type="submit" name="submit_podcast_feed"  class="button-primary" value="Add Podcast Feed">
        </form>
        <hr>
        <h1>Podcast Feeds</h1>
        <table class="widefat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Feed URL</th>
                    <th>Web Link</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($table_data as $row) : ?>
                    <tr>
                        <td><?php echo esc_html($row['id']); ?></td>
                        <td><?php echo esc_html($row['title']); ?></td>
                        <td><?php echo esc_html($row['description']); ?></td>
                        <td><?php echo esc_html($row['feed_url']); ?></td>
                        <td><?php echo esc_html($row['web_link']); ?></td>
                        <td><button class="<?php echo 'button-parser' ?> button-primary" data-feed-identity="<?php echo $row['id'] ?>">Check for new episodes</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    add_action( 'admin_footer', 'admin_add_javascript' );
    
    function admin_add_javascript() { ?>
    <!-- Find the correct way to add alpine after evaluation. -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
	<script type="text/javascript" >
    	jQuery(document).ready(function($) {
    	    
    	    function parse_feed(id) {
    	        

                let formData = new FormData();
        		let data = {
        			
        			'id': id
        		};

                formData.append('action', 'parse_feed_episodes',);
                formData.append('id', id)

                let options = {
                    method: 'POST',
                    body: formData
                };

                fetch(ajaxurl, options)
                .then(res => res.json())
                .then(res => console.log(res))
                .catch(err => console.error(err.data));
           
    	    }
    	    
    	    $(".button-parser").on('click', function () {
    	        
    	        let id = $(this).data('feedIdentity');
    	        parse_feed(id);
    	        
    	    })
    	});
	</script> <?php
}
};