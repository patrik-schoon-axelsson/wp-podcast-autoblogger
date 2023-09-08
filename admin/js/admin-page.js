// State management and AJAX for admin-page.php
document.addEventListener('alpine:init', () => (
    Alpine.data('app', () => ({
        table: [],
        notifications: [],
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
        addNewFeed: function (feedUrl) {
                let formData = new FormData();
                let data = {
                    'feed_url': feedUrl
                };
    
                formData.append('action', 'add_rss_feed',);
                formData.append('feed_url', feedUrl)
                formData.append('add_feed_nonce', phpData.add_feed_nonce)
    
                let options = {
                    method: 'POST',
                    body: formData
                };
    
                fetch(ajaxurl, options)
                .then(res => res.json())
                .then(res => this.table = res.feeds)
                .catch(err => console.error(err.data));
            },
        deleteFeed: function(id) {
                let formData = new FormData();
                
    
                formData.append('action', 'delete_rss_feed',);
                formData.append('feed_id', id);
                formData.append('delete_feed_nonce', phpData.delete_feed_nonce)
    
                let options = {
                    method: 'POST',
                    body: formData
                };
    
                fetch(ajaxurl, options)
                .then(res => res.json())
                .then(res => this.notifications.push(res))
                .catch(err => console.error(err.data));
                console.log(this.notifications);
                // Update the table after deletion.
                this.table = this.table.filter(i => i['id'] !== id);
                
            },
            msg: 'Podviewer Plugin',
        })
        ))
    )