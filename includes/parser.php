<?php 

function parse_simplexml_to_array($url) {
    
    $feeds = simplexml_load_file($url);
    
    // First of all, check if $url is valid XML at all.
    if($feeds == false){
        wp_send_json_error(array(
            "error" => "Error parsing the url of $title, please revalidate that the feed_url link is valid RSS.", 
            "feed_url" => $url),
            500, JSON_UNESCAPED_SLASHES);
      wp_die();  
    };
    
    $data = array();
    $items = array();
    
    $data["title"] = $feeds -> channel -> title[0]->__toString();
    $data["description"] = $feeds -> channel -> description[0]->__toString();
    
    foreach($feeds -> channel -> item as $item) {
            
        $episode = array(
            "title" => $item -> title[0]->__toString(),
            "description" => $item -> description[0]->__toString(),
            "pubDate" => Date('Y-m-d\TH:M:s', strtotime($item -> pubDate[0]->__toString())),
            "url" => $item -> enclosure[0] -> attributes() -> url ->__toString()
            );
    
        $items[] = $episode;
    }
    
    $data["items"] = $items;
    return $data;
} 