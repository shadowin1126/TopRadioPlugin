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
				echo '<table style="border:none" class="db-table">';
				echo '<tr>';
				echo '<th style="border:none">Station</th>';
				echo '<th style="border:none">Language</th>';
				echo '<th style="border:none">Description</th></tr>';
				echo '<tr><td style="border:none">',$result[0]->name,'</td>';
				echo '<td style="border:none">',$result[0]->language,'</td>';
				echo '<td style="border:none">',$result[0]->description,'</td>';
				echo '</tr>';
				echo '</table><br />';		
			}
		}
		else {
			$results = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE country = 'malaysia'" );
			echo '<table style="border:none">';
			echo '<tr>';
			echo '<th colspan="3" style="border:none;border-bottom:1px solid #BDBDBD"><h5>Stations</h5></th>';
			echo '</tr>';
			foreach($results as $row) {
				echo '<tr>';
				echo '<td style="border:none;float:left;margin-right:12px;padding:0;vertical-align:middle"><h5>',"<a href='http://top-radio.org/malaysia/$row->tag/'>$row->name</a>",'</h5></td>';
				echo '<td style="border:none;float:left;margin-right:12px;vertical-align:center">',"[".$row->language."]",'</td>';
				echo '</tr><tr>';
				echo '<td colspan="2" style="border:none;border-bottom:1px solid #E6E6E6;padding:0 0 20px 0;vertical-align:bottom">',$row->description,'</td>';
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
	
		$seo_title = $result[0]->name.' Station';
		$seo_desc = 'Listen to '.$result[0]->name.' on Top Radio.';
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
 	phpinfo();
}

add_action( 'init', 'seo_loader_init', 0 ); 
add_shortcode( 'shadowin', 'shadowin_func' );

?>