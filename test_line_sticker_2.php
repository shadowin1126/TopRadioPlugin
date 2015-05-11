<?php
/*
Plugin Name: Test Line Sticker 2
Plugin URI: http://www.top-radio.org/
Description: Testing path link for different radio stations
Version: 1.0.0
Author: shadowin
Author URI: http://www.top-radio.org/
License: GPL2
*/

function seo_loader_init() {
	global $wpdb;
	$urlArr = parse_url($_SERVER['REQUEST_URI']);
	$urlPath = explode('/', $urlArr['path']);
	
	if (($urlPath[1]) && ($urlPath[1] != '')) {

		$results = $wpdb->get_results( "SELECT * FROM radio_station_list" );
		$country = $urlPath[1];
		$checkCountry = '';
		foreach($results as $row) {
			if ($row->country == $country) {
				$checkCountry = true;
			}
		}
		if ((!$checkCountry) && (substr($urlPath[1],0,2) != 'wp')) {
			header('Location: http://top-radio.org'); // If not found will back to the root.
			exit;
		}
/*
		$countries = array
		(
			'my' => 'malaysia',
			'th' => 'thailand',
		);

		if ($urlPath[1]) {
			$country = $urlPath[1];

			if (!in_array($country, $countries) && ($urlPath[1] != 'wp-admin')) {
				header('Location: http://top-radio.org'); // If not found will back to the root.
				exit;
			}
		}
*/
		if (($urlPath[2]) && ($urlPath[2] != '')) {
			$station = $urlPath[2];
			$checkStation = '';
			foreach($results as $row) {
				if ($row->tag == $station) {
					$checkStation = true;
				}
			}
			if ((!$checkStation) && (substr($urlPath[1],0,2) != 'wp')) {
				header('Location: /$urlPath[1]/');
				exit;
			}
		}
		
		add_filter( 'single_post_title', 'topradio_title',999,1);
		
		remove_action('wp_head', array( $GLOBALS['seo_ultimate'], 'template_head' ), 1 );
		add_action( 'wp_head', 'topradio_seo_meta', 1);
	}
}

function getLastPathSegment($url) {
$pathTest = parse_url($_SERVER['REQUEST_URI']); // test url data
$path = parse_url($url, PHP_URL_PATH); // to get the path from a whole URL
    $pathTrimmed = trim($path, '/'); // normalise with no leading or trailing slash
    $pathTokens = explode('/', $pathTrimmed); // get segments delimited by a slash
    if (substr($path, -1) !== '/') {
        array_pop($pathTokens);
    }
//    return end($pathTokens); // get the last segment
    return $pathTokens;
}


function getFromDatabase() {
	global $wpdb;
	
	$data = getLastPathSegment($_SERVER['REQUEST_URI']);
	if ($data[0] == 'malaysia') {
		if (isset($data[1])) {
			$result = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE tag = '$data[1]'" );
			if ($result) {

				if ($result[0]->stream) {
					$stream = $result[0]->stream;
					?><audio controls autoplay="autoplay" src=<?= $stream ?> type="live">Your browser does not support the audio element.</audio><?
				}

				?>
				<object width="300" height="23">
				<param name="movie" value="/play/player.swf">
				<param name="allowfullscreen" value="true">
				<param name="allowscriptaccess" value="always">
				<param name="wmode" value="transparent">
				<param name="flashvars" value="file=chill.sdp&volume=100&streamer=rtmp://real3.atimemedia.com/live&autostart=true&repeat=always&stretching=exactfit">
				<embed type="application/x-shockwave-flash" src="/play/player.swf" width="300" height="23" allowscriptaccess="always" allowfullscreen="false" wmode="transparent" flashvars="file=chill.sdp&volume=100&streamer=rtmp://real3.atimemedia.com/live&autostart=true&repeat=always&stretching=exactfit"></embed>
				</object>
				<?
				
				
//				echo do_shortcode('[videojs flash="rtmp://real6.atimemedia.com:1935/live/efm.sdp" type="rtmp" width="300" height="23" autoplay="true"]');

				echo '<br /><br /><br />';
				echo '<table style="border:none" class="db-table">';
				echo '<tr>';
				echo '<th style="border:none;float:left;width:300px;text-align:center">Station Information</th></tr>';
				echo '<tr><td style="border:none;float:left;width:300px;text-align:center">',"Location : ".ucwords($result[0]->country),'</td></tr>';
				echo '<tr><td style="border:none;float:left;width:300px;text-align:center">',"Language : ".ucwords($result[0]->language),'</td></tr>';
				echo '<tr><td style="border:none;float:left;width:300px;text-align:center">',"Genre : ".ucwords($result[0]->description),'</td>';
				echo '</tr>';
				echo '</table><br />';
				
				if ($result[0]->fbid) {
					$fbid = $result[0]->fbid;
					echo do_shortcode('[fbf_page_feed pageID='.$fbid.' num="3" show_description="true" update="true" show_avatar="true" avatar_size="square" link_target_blank="true" feed_title="true" like_button="true" like_button_position="top"]');
				}
				echo '<br />';
				
				// twitter feed
				if ($result[0]->twitter) {
					$twID = explode('/', $result[0]->twitter);
					?><a class="twitter-timeline" data-widget-id="596354424148131840" data-screen-name=<?= $twID[3] ?>>Tweets by @<?= $twID[3] ?></a>
					<script>!function(d,s,id){
						var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';
						if(!d.getElementById(id)){
							js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";
							fjs.parentNode.insertBefore(js,fjs);
						}
					}
					(document,"script","twitter-wjs");</script><?
				}

			}
		}
		else {
			$results = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE country = 'malaysia'" );
			echo '<table style="border:none">';
			echo '<tr>';
			echo '<th colspan="3" style="border:none;border-bottom:1px solid #BDBDBD"><h5>Stations</h5></th>';
			echo '</tr>';
			foreach($results as $row) {
				$img = "http://top-radio.org/wp-content/uploads/logo/".$row->tag.".jpg";
				echo '<tr>';
				echo '<td rowspan="3" style="border:none;padding:0;width:100px;vertical-align:middle">','<img src="'.$img.'" alt="" style="width:80px; height:auto;">','</td>';
				echo '<td style="border:none;float:left;padding:0"><h4>',"<a href='http://top-radio.org/malaysia/$row->tag/'>$row->name</a>",'</h4></td>';
				echo '</tr><tr>';
				echo '<td style="border:none;padding:0;color:#848484">',"Language : ".ucwords($row->language),'</td>';
				echo '</tr><tr>';
				echo '<td style="border:none;padding:0 0 20px 0;color:#848484">',ucwords($row->description),'</td>';
				echo '</tr><tr>';
				echo '<td colspan="3" style="border:none;border-bottom:1px solid #E6E6E6"></td>';
				echo '</tr>';
			}
			echo '</table><br />';

/*
		if (isset($data[1])) {
			$result = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE tag = '$data[1]'" );
			if ($result) {
				echo $result[0]->name;
				echo " [".$result[0]->language."]";
				echo "<br />";
				echo $result[0]->description;
				echo "<br />";
				echo "<br /><br />";
			}
		}
		else {
			$results = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE country = 'malaysia'" );

			foreach($results as $row) {
				echo "<strong><a href='http://top-radio.org/malaysia/$row->tag/'>$row->name</a></strong>";
				echo " [".$row->language."]";
				echo "<br />";
				echo "<font-size: 12px>$row->description";
				echo "<br />";
				echo "<br /><br />";
			}
		}	
*/
		}

	}
}

function topradio_title($title) {
	global $wpdb, $topradio;
	
	$urlArr = parse_url($_SERVER['REQUEST_URI']);
	$urlPath = explode('/', $urlArr['path']);
	
	if (($urlPath[2]) && ($urlPath[2] != '')) {
	
		$data = getLastPathSegment($_SERVER['REQUEST_URI']);
		$result = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE tag = '$data[1]'" );
		$title = $result[0]->name.' Station';
		return $title;
		
	}
	elseif (($urlPath[1]) && ($urlPath[1] != '')) {
	
		$data = getLastPathSegment($_SERVER['REQUEST_URI']);
		$result = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE country = '$data[0]'" );
		$title = ucwords($result[0]->country).' Radio Stations';
		return $title;
	}
}

function topradio_seo_meta() {
	global $seo_ultimate, $post_pages, $post_desc, $post_keywords, $post_menu, $meta_desc, $wp_query, $page, $post, $wpdb;
	global $wp_post_meta;
	
	$urlArr = parse_url($_SERVER['REQUEST_URI']);
	$urlPath = explode('/', $urlArr['path']);
	
	if (($urlPath[2]) && ($urlPath[2] != '')) {
	
		$data = getLastPathSegment($_SERVER['REQUEST_URI']);
		$result = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE tag = '$data[1]'" );
		$wp_query->post->post_title = $result[0]->name;
	
		$seo_title = 'Listen '.$result[0]->name.' '.ucwords($result[0]->country).' online for Android, iPhone, iPad, iOS and desktop PC.';
		$seo_desc = 'Listen to '.$result[0]->name.' Top Radio.';
	}
	
	elseif (($urlPath[1]) && ($urlPath[1] != '')) {
	
		$data = getLastPathSegment($_SERVER['REQUEST_URI']);
		$wp_query->post->post_title = ucwords($data[0]);
	
		$seo_title = ucwords($data[0]).' Radio Stations';
		$seo_desc = 'Listen to '.ucwords($data[0]).' Radio Stations on Top Radio.';
	}

	$keywords = array();

	$keywords[] = $result[0]->name;
	$keywords[] = ucwords($data[0]);
	$keywords[] = 'Radio Station';
	
	$seo_keywords = '';
	
	foreach ($keywords as $keyword) {
		$seo_keywords .= $keyword.', ';
	}
	
	$topradio_seo = '
<!-- TopRadio SEO -->		
	
	<link rel="author" href="https://plus.google.com/u/0/110449270534612866446/" />

	<link rel="canonical" href="https://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'" />

	<meta name="title" content="'.$seo_title.'" />
	<meta name="entry-title" content="'.$seo_title.'" />
	<meta name="description" content="'.$seo_desc.'" />
	<meta name="keywords" content="'.$seo_keywords.'" />
	<meta name="url" content="https://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="'.$seo_title.'" />
	<meta property="og:description" content="'.$seo_desc.'" />
	<meta property="og:url" content="https://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'" />
	';
//	$topradio_seo .= '<meta property="og:image" content="'.$thumbnail_url.'" />';

	$topradio_seo .= '
	<meta property="article:published_time" content="'.get_the_date('Y-m-d').'" />
	<meta property="article:modified_time" content="'.get_the_modified_date('Y-m-d').'" />

	<meta property="og:site_name" content="Top Radio"/>
	<meta property="fb:app_id" content="531406790246098"/>
	<meta name="twitter:card" content="summary"/>
	<meta name="twitter:site" content="@topradio"/>
	<meta name="google-site-verification" content="7HwbOdRomrvwyGKJ-BfbtzNRpEVXsFLEQyK0LGpzBEU"/>
	<meta name="msvalidate.01" content="D9BB6B9AEFB09CF845BD7F04295BCA91"/>

	';


	$topradio_seo .= '
<!-- TopRadio SEO -->
			';
	echo $topradio_seo;
	

}

//[shadowin]
function shadowin_func( $atts ) {
// $data = getLastPathSegment($_SERVER['REQUEST_URI']);
// print_r($data);
 	getFromDatabase();
}

add_action( 'init', 'seo_loader_init', 0 ); 
add_shortcode( 'shadowin', 'shadowin_func' );

?>