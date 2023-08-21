<?php

require_once plugin_dir_path(__FILE__) . '../includes/db.php';

function render_admin_page() {

    ?>
    
    <div class="wrap aligncenter" x-data="app">
        <h1 x-text="msg" style="text-align: center"></h1>

        <!-- Podcast feed form -->
        <form @submit.prevent="addNewFeed()" class="form-table">
            <?php wp_nonce_field('podfeed-submission-nonce'); ?>
            <label for="feed_url" class="form-field">Feed URL:</label><br>
            <input type="text" name="feed_url" class="regular-text" id="feed_url" required><br>
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
}