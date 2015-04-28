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
		if ((!$checkCountry) && ($urlPath[1] != 'wp-admin')) {
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
			if (!$checkStation) {
				header('Location: /$urlPath[1]/');
				exit;
			}
		}
		
		add_filter( 'single_post_title', 'topradio_title',999,1);
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
				echo "ID"."  "."Name"."<br><br>";
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
			echo "ID"."  "."Name"."<br><br>";
			foreach($results as $row) {
				echo "<strong><a href='http://top-radio.org/malaysia/$row->tag/'>$row->name</a></strong>";
				echo " [".$row->language."]";
				echo "<br />";
				echo "<font-size: 12px>$row->description";
				echo "<br />";
				echo "<br /><br />";
			}
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


//[shadowin]
function shadowin_func( $atts ) {
// $data = getLastPathSegment($_SERVER['REQUEST_URI']);
// print_r($data);
 	getFromDatabase();
 	phpinfo();
}

add_action( 'init', 'seo_loader_init', 0 ); 
add_shortcode( 'shadowin', 'shadowin_func' );

?>