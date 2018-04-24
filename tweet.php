<?php
// requires, https://github.com/abraham/twitteroauth
require_once  dirname( __FILE__ ) ."/twitteroauth-master/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
define('CONSUMER_KEY', '');
define('CONSUMER_SECRET', '');
define('ACCESS_TOKEN', '');
define('ACCESS_TOKEN_SECRET', '');


//view

function twitter_push($connection, $status){
	$post_tweets = $connection->post("statuses/update", ["status" => $status]);

}

//model

function twitter_post(){
date_default_timezone_set("UTC") ;
$sysdate = date( 'm-d', time() );
$systime = date( 'H:i:s', time() - 5);  // add -5 seconds due to lag
//channel can be dynamic as well, static for now
$endpoint = "https://www.siriusxm.com/metadata/pdt/en-us/json/channels/altnation/timestamp/".$sysdate.'-'.$systime;
$ch = curl_init( $endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json'
	
	)                                                                       
);      
$result = curl_exec($ch);
curl_close($ch);
return $result ;
}

//controller

$pdt_json = twitter_post();
if( sizeof( $pdt_json  ) > 0 ){
$pdt_json =  json_decode( $pdt_json, true);

	if( isset( $pdt_json['channelMetadataResponse']['metaData'] )){
		$status = $pdt_json['channelMetadataResponse']['metaData']['currentEvent']['artists']['name']; //text for your tweet.
		$artist = file_get_contents( 'artist.txt' );

		if( !( $artist === $status )  ){
			//send
			///if diff set & post to twitter
			file_put_contents( 'artist.txt', $status );

			$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
			$status = str_replace(" ","",$status );
			$status = str_replace("-","",$status );
			$status = str_replace(".","",$status );
			$status = str_replace("/","",$status );
			$status = str_replace("@altnation","",$status );
			twitter_push($connection, $status . ' airs on http://siriusxm.us/AltNation');
			//print_r( $connection );
			echo "string";
		}		

	}

}


?>