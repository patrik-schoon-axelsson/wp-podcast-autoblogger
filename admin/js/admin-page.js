jQuery(document).ready(function($) {
    console.log(phpData);
    	    
        function parse_feed(id) {
            

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
       
        }
        
        $(".button-parser").on('click', function () {
            
            let id = $(this).data('feedIdentity');
            parse_feed(id);
            
        })
    
});