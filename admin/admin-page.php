<?php

require_once plugin_dir_path(__FILE__) . '../includes/db.php';

function render_admin_page() {

    ?>
    
    <div class="wrap aligncenter" x-data="app">
        <h1 x-text="msg" style="text-align: center"></h1>

        <!-- Podcast feed form -->
        <form x-data="{feedUrl: ''}" @submit.prevent="addNewFeed(feedUrl)" class="form-table">
            <?php wp_nonce_field('podfeed-submission-nonce'); ?>
            <label for="feed_url" class="form-field">Feed URL:</label><br>
            <input type="text" name="feed_url" class="regular-text" id="feed_url" required x-model="feedUrl"><br>
            <br>        
            <input type="submit" name="submit_podcast_feed"  class="button-primary" value="Add Podcast Feed">
        </form>
        <hr>
        <h1>Podcast Feeds</h1>
        <table class="widefat">
            <thead>
                <tr>
                    <th>Delete Feed</th>
                    <th @click="table.sort((a, b) => a.title.localeCompare(b.title));">Title <span class="dashicons dashicons-arrow-down-alt2"></span></th>
                    <th @click="table.sort((a, b) => a.description.localeCompare(b.description));">Description <span class="dashicons dashicons-arrow-down-alt2"></span></th>
                    <th>Feed URL <span class="dashicons dashicons-arrow-down-alt2"></span></th>
                    <th>Web Link <span class="dashicons dashicons-arrow-down-alt2"></span></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="row in table" x-init="fetch(phpData.restUrl+'podcast-autoblogger/v1/feeds').then(res => res.json()).then(res => table = res).catch(err => console.log(err)).finally(console.log('Done!'))" x-cloak>
                    <tr x-data="{ modal: '#modal-target-'+row.id }">
                        <td>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" :data-bs-target="modal">
                              Delete <span class="dashicons dashicons-database-remove delete-feed"></span>
                        </button>
                      </td>
                        <td x-text="row.title"></td>
                        <td x-html="row.description"></td>
                        <td x-text="row.feed_url"></td>
                        <td x-text="row.web_link"></td>
                        <td><button class="<?php echo 'button-parser' ?> button-primary" @click="parse_feed(row.id)">Check for new episodes</button></td>
                        <template x-teleport="body">
                        <div class="modal fade" :id="modal.substring(1)" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Confirm Deletion</h5>
                                  <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  Are you sure you want to delete the feed <b x-text="row.title"></b>? This will delete the feed and all Episode posts in the database associated with this feed.
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                  <button type="button" class="btn btn-danger" @click="deleteFeed(row.id)" data-bs-dismiss="modal">Delete Feed</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </template>
                    </tr>
                </template>
            </tbody>
        </table>
        <div id="modal-wrap">
        </div>
    <?php
}