// State management and AJAX for admin-page.php
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
        addNewFeed: function (feedUrl) {
                     

                let formData = new FormData();
                let data = {
                    'feed_url': feedUrl
                };
    
                formData.append('action', 'add_rss_feed',);
                formData.append('feed_url', feedUrl)
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
            msg: 'Podviewer Plugin',
        })
        ))
    )