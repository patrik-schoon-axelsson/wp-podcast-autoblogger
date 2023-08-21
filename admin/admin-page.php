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
    <!-- Find the correct way to add alpine after evaluation. -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <div class="wrap aligncenter" x-data="app">
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
        <table class="widefat" x-data="{ table: [] }">
            <thead>
                <tr>
                    <th @click="table.sort((a, b) => a.id - b.id)">ID <span class="dashicons dashicons-arrow-down-alt2"></span></th>
                    <th @click="table.sort((a, b) => a.title.localeCompare(b.title));">Title <span class="dashicons dashicons-arrow-down-alt2"></span></th>
                    <th @click="table.sort((a, b) => a.description.localeCompare(b.description));">Description <span class="dashicons dashicons-arrow-down-alt2"></span></th>
                    <th>Feed URL <span class="dashicons dashicons-arrow-down-alt2"></span></th>
                    <th>Web Link <span class="dashicons dashicons-arrow-down-alt2"></span></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="row in table" x-init="fetch(phpData.restUrl+'podcast-autoblogger/v1/feeds').then(res => res.json()).then(res => table = res).catch(err => console.log(err)).finally(console.log('Done!'))" x-cloak>
                    <tr>
                        <td x-text="row.id"></td>
                        <td x-text="row.title"></td>
                        <td x-text="row.description"></td>
                        <td x-text="row.feed_url"></td>
                        <td x-text="row.web_link"></td>
                        <td><button class="<?php echo 'button-parser' ?> button-primary" @click="parse_feed(row.id)">Check for new episodes</button></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <?php
    add_action( 'admin_footer', 'admin_add_javascript' );
    
    function admin_add_javascript() { ?>
    <script>
        document.addEventListener('alpine:init', () => (
            Alpine.data('app', () => ({
                parse_feed: function (id) {
                    

                    let formData = new FormData();
                    let data = {
                        'id': id
                    };

                    formData.append('action', 'parse_feed_episodes',);
                    formData.append('id', id)
                    formData.append('add_episodes_nonce', phpData.add_episodes_nonce)

                    let options = {
                        method: 'POST',
                        body: formData
                    };

                    fetch(ajaxurl, options)
                    .then(res => res.json())
                    .then(res => console.log(res))
                    .catch(err => console.error(err.data));
                },
                msg: 'Podviewer Plugin'
                }))
        ))        
    </script>
    <?php
}
};