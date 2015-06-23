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
	$urlStr = (string) $urlArr;
	$urlPath = explode('/', $urlArr['path']);
	
	if (substr($urlPath[1],0,2) != 'wp') {
		if (($urlPath[1]) && ($urlPath[1] != '')) {

			$results = $wpdb->get_results( "SELECT * FROM radio_station_list" );
			$country = $urlPath[1];
			$checkCountry = '';
			foreach($results as $row) {
				if ($row->country == $country) {
					$checkCountry = true;
				}
			}
			/*
			if (!$checkCountry) {
				header('Location: http://top-radio.org'); // If not found will back to the root.
				exit;
			}
			*/
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
					header('Location: /'.$country.'/');
					exit;
				}
			}
			add_filter( 'single_post_title', 'topradio_title',999,1);
		
			remove_action('wp_head', array( $GLOBALS['seo_ultimate'], 'template_head' ), 1 );
			add_action( 'wp_head', 'topradio_seo_meta', 1);
		}
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
	if (isset($data[1])) {
		?><div class="12-small columns"><?
		$result = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE tag = '$data[1]'" );
		$name = $result[0]->name;
		if ($result) {

			if ($result[0]->stream) {
				if ($result[0]->player == "1") {
					$stream = $result[0]->stream;
					?><audio controls autoplay="autoplay" src=<?= $stream ?>>Your browser does not support the audio element.</audio><?
				}
				elseif ($result[0]->player == "2") {
					$stream = $result[0]->stream;
					$file = $result[0]->stream_file;
					?>
					<object width="300" height="23">
					<param name="movie" value="/play/player.swf">
					<param name="allowfullscreen" value="true">
					<param name="allowscriptaccess" value="always">
					<param name="wmode" value="transparent">
					<param name="flashvars" value="file=<?= $file ?>&amp;volume=100&amp;streamer=<?= $stream ?>&amp;autostart=true&amp;repeat=always&amp;stretching=exactfit">
					<embed type="application/x-shockwave-flash" src="/play/player.swf" width="300" height="23" allowscriptaccess="always" allowfullscreen="false" wmode="transparent" flashvars="file=<?= $file ?>&amp;volume=100&amp;streamer=<?= $stream ?>&amp;autostart=true&amp;repeat=always&amp;stretching=exactfit"></embed>
					</object>
					<?
				}
				elseif ($result[0]->player == "3") {
					$stream = $result[0]->stream;
					$file = $result[0]->stream_file;
					?>
					<object width="300" height="23">
					<param name="movie" value="http://mcot-web.mcot.net/radio/player.swf">
					<param name="allowfullscreen" value="true">
					<param name="allowscriptaccess" value="always">
					<param name="wmode" value="transparent">
					<param name="flashvars" value="file=<?= $file ?>&amp;volume=100&amp;streamer=<?= $stream ?>&amp;autostart=true&amp;repeat=always&amp;stretching=exactfit">
					<embed type="application/x-shockwave-flash" src="http://mcot-web.mcot.net/radio/player.swf" width="300" height="23" allowscriptaccess="always" allowfullscreen="true" wmode="transparent" flashvars="file=<?= $file ?>&amp;volume=100&amp;streamer=<?= $stream ?>&amp;autostart=true&amp;repeat=always&amp;stretching=exactfit"></embed>
					</object>
					<?
				}
				elseif ($result[0]->player == "4") {
					$stream = $result[0]->stream;
					?>
					<object>
					<embed src="/play/FlashMediaPlayback.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="false" flashvars="src=<?= $stream ?>&amp;autoPlay=true" height="30" width="280"></embed>
					</object>
					<?
				}
			}

			echo '<br /><br /><br />';
			echo '<div class="row">';
			echo '<div class="small-12 columns">';
			echo '<h4>Station Information</h4>';
			echo '</div></div>';
			echo '<br />';
			echo "Location : ".ucwords($result[0]->country);
			echo '<br />';
			echo "Language : ".ucwords($result[0]->language);
			echo '<br />';
			if ($result[0]->description) {
				echo ucwords($row->description);
			}
			if ($result[0]->station_url) {
				$station = $result[0]->station_url;
				echo "Station url : "."<a href=$station>$station</a>";
			}
			echo '<hr>';
			
			// fb feed
			echo '<link rel="stylesheet" type="text/css" href="/css/topradio.css">';
			echo '<div class="section group">';
			if ($result[0]->fbid) {
			echo '<div class="small-12 columns">';
			echo '<div class="medium-6 small-12 columns">';
//				echo '<div class="col span_1_of_2">';	//spans across two columns
				echo '<h4>'.$name.' Facebook Updates</h4>';
				echo '<br />';
				$fbid = $result[0]->fbid;
				echo '<div class="fbfeed">';
				echo do_shortcode('[fbf_page_feed pageID='.$fbid.' num="3" show_description="true" update="true" show_avatar="true" avatar_size="square" link_target_blank="true" feed_title="true" like_button="true" like_button_position="top"]');
				echo '</div>';
				echo '<br />';
				echo '</div>';
			}
			
			// twitter feed
			if ($result[0]->twitter) {
				echo '<div class="medium-6 small-12 columns">';
//				echo '<div class="col span_1_of_2">';
				echo '<h4>'.$name.' Latest Tweets</h4>';
				echo '<br />';
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
				echo '</div>';
			}
			echo '</div></div>';

		}
		?></div><?
	}
	else {
		$country = $data[0];
		$results = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE country = '$country'" );
		echo '<div class="row">';
		echo '<div class="small-12 columns">';
		echo '<h5>Stations</h5>';
		echo '</div></div>';
		echo '<hr>';
		foreach($results as $row) {
			$img = "http://top-radio.org/wp-content/uploads/logo/".$row->tag.".jpg";
			echo '<div class="row">';
			echo '<div class="small-3 columns">';
			echo "<a href=$row->tag><img src=$img alt='$row->name'></a>";
			echo '</div>';
			echo '<div class="small-9 columns">';
			echo "<a href=$row->tag>$row->name</a>";
			echo '<br />';
			echo "Language : ".ucwords($row->language);
			echo '<br />';
			echo ucwords($row->description);
			echo '</div>';
			echo '</div>';
			echo '<hr>';
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
	
		$results = $wpdb->get_results( "SELECT * FROM radio_station_list" );
		$country = $urlPath[1];
		$checkCountry = '';
		foreach($results as $row) {
			if ($row->country == $country) {
				$checkCountry = true;
			}
		}
		if ($checkCountry) {
			$data = getLastPathSegment($_SERVER['REQUEST_URI']);
			$result = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE country = '$data[0]'" );
			$title = ucwords($result[0]->country).' Radio Stations';
			return $title;
		}
	}
/*
	else {
		$title = "TopRadio: Listen to your favorite radio station from anywhere in the world";
		return $title;
	}
*/
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
		$results = $wpdb->get_results( "SELECT * FROM radio_station_list" );
		$country = $urlPath[1];
		$checkCountry = '';
		foreach($results as $row) {
			if ($row->country == $country) {
				$checkCountry = true;
			}
		}
		if ($checkCountry) {	
			$data = getLastPathSegment($_SERVER['REQUEST_URI']);
			$wp_query->post->post_title = ucwords($data[0]);
	
			$seo_title = ucwords($data[0]).' Radio Stations';
			$seo_desc = 'Listen to '.ucwords($data[0]).' Radio Stations on Top Radio.';
		}
		else {
			$seo_title = 'TopRadio'.ucwords($data[0]);
			$seo_desc = 'Listen to your favorite radio station with TopRadio. Your one place for every type of radio: music, news, sports, religious, business, pop, rock, jazz, classical, country, hip-hop, and much more.';
		}
	}
	else {
		$wp_query->post->post_title = 'TopRadio';
	
		$seo_title = 'TopRadio: Listen to your favorite radio station from anywhere in the world';
		$seo_desc = 'Listen to your favorite radio station with TopRadio. Your one place for every type of radio: music, news, sports, religious, business, pop, rock, jazz, classical, country, hip-hop, and much more.';
	}

	$keywords = array();

	$keywords[] = $result[0]->name;
	$keywords[] = ucwords($data[0]);
	$keywords[] = 'Radio Station';
	
	$seo_keywords = '';
	
	foreach ($keywords as $keyword) {
		if ($keyword != '') {
			$seo_keywords .= $keyword.', ';
		}
	}
	
	$topradio_seo = '
<!-- TopRadio SEO -->		
	
	<link rel="author" href="https://plus.google.com/u/0/102188650451887878557" />

	<link rel="canonical" href="http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'" />

	<meta name="title" content="'.$seo_title.'" />
	<meta name="entry-title" content="'.$seo_title.'" />
	<meta name="description" content="'.$seo_desc.'" />
	<meta name="keywords" content="'.$seo_keywords.'" />
	<meta name="url" content="http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="'.$seo_title.'" />
	<meta property="og:description" content="'.$seo_desc.'" />
	<meta property="og:url" content="http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'" />
	';
//	$topradio_seo .= '<meta property="og:image" content="'.$thumbnail_url.'" />';

	$topradio_seo .= '
	<meta property="article:published_time" content="'.get_the_date('Y-m-d').'" />
	<meta property="article:modified_time" content="'.get_the_modified_date('Y-m-d').'" />

	<meta property="og:site_name" content="Top Radio"/>
	<meta property="fb:app_id" content="1432179603751975"/>
	<meta name="twitter:card" content="summary"/>
	<meta name="twitter:site" content="@onlinetopradio"/>
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

// Top 10 radio station widget
// Creating the widget
class my_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'my_widget', // Base ID
			__('Hello World Widget', 'text_domain'), // Widget name
			array( 'description' => __( 'Sample widget based on Top Radio Tutorial', 'text_domain' ), ) // Widget description
		);
	}

	// Front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
//		$station = $wpdb->get_results( "SELECT ID, name FROM radio_station_list");

		$station1 = $instance['station1'];
		$station2 = $instance['station2'];
		$station3 = $instance['station3'];
		$station4 = $instance['station4'];
		$station5 = $instance['station5'];
		$station6 = $instance['station6'];
		$station7 = $instance['station7'];
		$station8 = $instance['station8'];
		$station9 = $instance['station9'];
		$station10 = $instance['station10'];
		// Before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];

		// Display output
		global $wpdb;
		if ($station1) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station1'");
			$img = 'http://top-radio.org/wp-content/uploads/logo/'.$result[0]->tag.'.jpg';
			$alt = $result[0]->name;
			$link = 'http://top-radio.org/'.$result[0]->country.'/'.$result[0]->tag.'/';
			echo  "<a href=$link><img src=$img alt='$alt' width='75'  /></a> ";
		}
		if ($station2) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station2'");
			$img = 'http://top-radio.org/wp-content/uploads/logo/'.$result[0]->tag.'.jpg';
			$alt = $result[0]->name;
			$link = 'http://top-radio.org/'.$result[0]->country.'/'.$result[0]->tag.'/';
			echo  "<a href=$link><img src=$img alt='$alt' width='75'  /></a><br />";
		}
		if ($station3) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station3'");
			$img = 'http://top-radio.org/wp-content/uploads/logo/'.$result[0]->tag.'.jpg';
			$alt = $result[0]->name;
			$link = 'http://top-radio.org/'.$result[0]->country.'/'.$result[0]->tag.'/';
			echo  "<a href=$link><img src=$img alt='$alt' width='75'  /></a>";
		}
		if ($station4) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station4'");
			$img = 'http://top-radio.org/wp-content/uploads/logo/'.$result[0]->tag.'.jpg';
			$alt = $result[0]->name;
			$link = 'http://top-radio.org/'.$result[0]->country.'/'.$result[0]->tag.'/';
			echo  "<a href=$link><img src=$img alt='$alt' width='75'  /></a><br />";
		}
		if ($station5) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station5'");
			$img = 'http://top-radio.org/wp-content/uploads/logo/'.$result[0]->tag.'.jpg';
			$alt = $result[0]->name;
			$link = 'http://top-radio.org/'.$result[0]->country.'/'.$result[0]->tag.'/';
			echo  "<a href=$link><img src=$img alt='$alt' width='75'  /></a>";
		}
			if ($station6) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station6'");
			$img = 'http://top-radio.org/wp-content/uploads/logo/'.$result[0]->tag.'.jpg';
			$alt = $result[0]->name;
			$link = 'http://top-radio.org/'.$result[0]->country.'/'.$result[0]->tag.'/';
			echo  "<a href=$link><img src=$img alt='$alt' width='75'  /></a><br />";
		}
		if ($station7) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station7'");
			$img = 'http://top-radio.org/wp-content/uploads/logo/'.$result[0]->tag.'.jpg';
			$alt = $result[0]->name;
			$link = 'http://top-radio.org/'.$result[0]->country.'/'.$result[0]->tag.'/';
			echo  "<a href=$link><img src=$img alt='$alt' width='75'  /></a>";
		}
		if ($station8) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station8'");
			$img = 'http://top-radio.org/wp-content/uploads/logo/'.$result[0]->tag.'.jpg';
			$alt = $result[0]->name;
			$link = 'http://top-radio.org/'.$result[0]->country.'/'.$result[0]->tag.'/';
			echo  "<a href=$link><img src=$img alt='$alt' width='75'  /></a><br />";
		}
		if ($station9) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station9'");
			$img = 'http://top-radio.org/wp-content/uploads/logo/'.$result[0]->tag.'.jpg';
			$alt = $result[0]->name;
			$link = 'http://top-radio.org/'.$result[0]->country.'/'.$result[0]->tag.'/';
			echo  "<a href=$link><img src=$img alt='$alt' width='75'  /></a>";
		}
		if ($station10) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station10'");
			$img = 'http://top-radio.org/wp-content/uploads/logo/'.$result[0]->tag.'.jpg';
			$alt = $result[0]->name;
			$link = 'http://top-radio.org/'.$result[0]->country.'/'.$result[0]->tag.'/';
			echo  "<a href=$link><img src=$img alt='$alt' width='75'  /></a><br />";
		}
		echo $args['after_widget'];
	}

	// Backend
	public function form( $instance ) {
		global $wpdb;
		$results = $wpdb->get_results("select * FROM radio_station_list");
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		// Admin Form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'station1' ); ?>"><?php _e( 'Station 1:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'station1' ); ?>" name="<?php echo $this->get_field_name( 'station1' ); ?>" class="widefat">
			<?
			foreach( $results as $result ) {
			?>
				<option <?php if ( $result->name == $instance['station1'] ) echo 'selected="selected"'; ?>><?= $result->name ?></option>
			<?
			}
			?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'station2' ); ?>"><?php _e( 'Station 2:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'station2' ); ?>" name="<?php echo $this->get_field_name( 'station2' ); ?>" class="widefat">
			<?
			foreach( $results as $result ) {
			?>
				<option <?php if ( $result->name == $instance['station2'] ) echo 'selected="selected"'; ?>><?= $result->name ?></option>
			<?
			}
			?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'station3' ); ?>"><?php _e( 'Station 3:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'station3' ); ?>" name="<?php echo $this->get_field_name( 'station3' ); ?>" class="widefat">
			<?
			foreach( $results as $result ) {
			?>
				<option <?php if ( $result->name == $instance['station3'] ) echo 'selected="selected"'; ?>><?= $result->name ?></option>
			<?
			}
			?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'station4' ); ?>"><?php _e( 'Station 4:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'station4' ); ?>" name="<?php echo $this->get_field_name( 'station4' ); ?>" class="widefat">
			<?
			foreach( $results as $result ) {
			?>
				<option <?php if ( $result->name == $instance['station4'] ) echo 'selected="selected"'; ?>><?= $result->name ?></option>
			<?
			}
			?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'station5' ); ?>"><?php _e( 'Station 5:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'station5' ); ?>" name="<?php echo $this->get_field_name( 'station5' ); ?>" class="widefat">
			<?
			foreach( $results as $result ) {
			?>
				<option <?php if ( $result->name == $instance['station5'] ) echo 'selected="selected"'; ?>><?= $result->name ?></option>
			<?
			}
			?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'station6' ); ?>"><?php _e( 'Station 6:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'station6' ); ?>" name="<?php echo $this->get_field_name( 'station6' ); ?>" class="widefat">
			<?
			foreach( $results as $result ) {
			?>
				<option <?php if ( $result->name == $instance['station5'] ) echo 'selected="selected"'; ?>><?= $result->name ?></option>
			<?
			}
			?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'station7' ); ?>"><?php _e( 'Station 7:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'station7' ); ?>" name="<?php echo $this->get_field_name( 'station7' ); ?>" class="widefat">
			<?
			foreach( $results as $result ) {
			?>
				<option <?php if ( $result->name == $instance['station5'] ) echo 'selected="selected"'; ?>><?= $result->name ?></option>
			<?
			}
			?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'station8' ); ?>"><?php _e( 'Station 8:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'station8' ); ?>" name="<?php echo $this->get_field_name( 'station8' ); ?>" class="widefat">
			<?
			foreach( $results as $result ) {
			?>
				<option <?php if ( $result->name == $instance['station5'] ) echo 'selected="selected"'; ?>><?= $result->name ?></option>
			<?
			}
			?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'station9' ); ?>"><?php _e( 'Station 9:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'station9' ); ?>" name="<?php echo $this->get_field_name( 'station9' ); ?>" class="widefat">
			<?
			foreach( $results as $result ) {
			?>
				<option <?php if ( $result->name == $instance['station5'] ) echo 'selected="selected"'; ?>><?= $result->name ?></option>
			<?
			}
			?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'station10' ); ?>"><?php _e( 'Station 10:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'station10' ); ?>" name="<?php echo $this->get_field_name( 'station10' ); ?>" class="widefat">
			<?
			foreach( $results as $result ) {
			?>
				<option <?php if ( $result->name == $instance['station5'] ) echo 'selected="selected"'; ?>><?= $result->name ?></option>
			<?
			}
			?>
		</select>
		</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['station1'] = $new_instance['station1'];
		$instance['station2'] = $new_instance['station2'];
		$instance['station3'] = $new_instance['station3'];
		$instance['station4'] = $new_instance['station4'];
		$instance['station5'] = $new_instance['station5'];
		$instance['station6'] = $new_instance['station6'];
		$instance['station7'] = $new_instance['station7'];
		$instance['station8'] = $new_instance['station8'];
		$instance['station9'] = $new_instance['station9'];
		$instance['station10'] = $new_instance['station10'];
		return $instance;
	}
} // Class my_widget ends here

// Register and load the widget
function load_widget() {
    register_widget( 'my_widget' );
}
add_action( 'widgets_init', 'load_widget' );

add_action( 'init', 'seo_loader_init', 0 ); 
add_shortcode( 'shadowin', 'shadowin_func' );

?>