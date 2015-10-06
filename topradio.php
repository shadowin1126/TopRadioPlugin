<?php
/*
Plugin Name: TopRadio
Plugin URI: http://www.top-radio.org/
Description: TopRadio plugin for radio stations
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
				if ($row->country_id == $country) {
					$checkCountry = true;
				}
			}
/*
			if (!$checkCountry) {
				header('Location: http://top-radio.org'); // If not found will back to the root.
				exit;
			}

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
				if ((!$checkStation) && (substr($urlPath[1],0,2) != 'wp')  && ($urlPath[1] != 'genre')) {
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
	?>
	<!-- G+ Like button -->
	<script type="text/javascript">
	  (function() {
		var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
		po.src = "https://apis.google.com/js/plusone.js?publisherid=105610811389374160703";
		var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script>
	<!-- FB Like button -->
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.4&appId=1654102731501095";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	
	<!-- Ads -->
	<div class="row">
		<div class="row">
		<div class="small-12 columns">
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<!-- Top-Radio Top -->
			<ins class="adsbygoogle"
				 style="display:block"
				 data-ad-client="ca-pub-0047723350429793"
				 data-ad-slot="5303669051"
				 data-ad-format="auto"></ins>
			<script>
			(adsbygoogle = window.adsbygoogle || []).push({});
			</script>
		</div>
		</div>
	<br />
	<?php
	if (isset($data[1])) {
		?><div class="large-12 small-12 columns"><?
		$result = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE country_id = '$data[0]' AND tag = '$data[1]'" );
		if ($result) {

			//to get current station number in the database
			$stations = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE country_id = '$data[0]'" );
			$checkstation = 0;
			foreach ($stations as $station) {
				if ($station->tag != $data[1]) {
					$checkstation = $checkstation + 1;
				}
				else {
					$currentstation = $checkstation;
				}
			}

			//to get link for prev station and next station
			$country = $data[0];
			$count = $wpdb->num_rows;
			if ($currentstation == 0) {
				$prevstation = $count - 1;
				$nextstation = $currentstation + 1;
			}
			elseif ($currentstation == $count - 1) {
				$prevstation = $currentstation - 1;
				$nextstation = 0;
			}
			else {
				$prevstation = $currentstation - 1;
				$nextstation = $currentstation + 1;
			}
			$prev = "/".$country.'/'.$stations[$prevstation]->tag.'/';
			$next = "/".$country.'/'.$stations[$nextstation]->tag.'/';
			
			$tag = $result[0]->tag;
			$name = $result[0]->name;
			if ($result[0]->image) {
				$img = "/wp-content/uploads/logo/".$result[0]->image.".jpg";
			} else {
				$img = "/wp-content/uploads/logo/radio.jpg";
			}
			echo '<div class="row">';
			echo '<div class="medium-4 small-6 columns">';
			echo '<br />';
			echo "<img src=$img alt=$tag width='250' />";
			echo '<br /><br /></div>';
			echo '<div class="medium-8 small-6 columns">';
			echo '<div class="right">';
			echo "<a href=$prev class='tiny button'>prev station</a>";
			echo '<br /><br />';
			echo "<a href=$next class='tiny button'>next station</a>";
			echo '</div>';
			echo '</div></div>';
			echo '<p style="color:gray">posted by Adrian Foo | '.$result[0]->date_add.'</p>';
			echo '<br />';
			echo 'You are listening to <strong>'.$name.'</strong> from '.ucwords($result[0]->country);
			if ($result[0]->language) {
				echo ' aired in the '.ucwords($result[0]->language).' language';
			}
			echo '.';
			echo '<br /><br />';
			echo 'Here, you can listen to this radio station conveniently using your smart phone, iOS, iPhone, iPad, Android, Windows Phone or PC with an internet connection.';
			echo '<br /><br />';

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
					<embed type="application/x-shockwave-flash" src="/play/player2.swf" width="303" height="23" quality="high" allowscriptaccess="always" allowfullscreen="false" flashvars="autostart=true&amp;duration=99999&amp;file=<?= $stream ?>">
					<?
					/**
					<object width="300" height="23">
					<param name="movie" value="/play/player.swf">
					<param name="allowfullscreen" value="true">
					<param name="allowscriptaccess" value="always">
					<param name="wmode" value="transparent">
					<param name="flashvars" value="file=<?= $file ?>&amp;volume=100&amp;autostart=true&amp;repeat=always&amp;stretching=exactfit">
					<embed type="application/x-shockwave-flash" src="/play/player.swf" width="300" height="23" allowscriptaccess="always" allowfullscreen="true" wmode="transparent" flashvars="file=<?= $file ?>&amp;volume=100&amp;autostart=true&amp;repeat=always&amp;stretching=exactfit"></embed>
					</object>
					**/
					?>
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
				elseif ($result[0]->player == "5") {
					$stream = $result[0]->stream;
					?>
					<div id="movId" name="movId" align=center>

					<object id="MediaPlayer1" classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,0,02,902" standby="Loading Microsoft Windows Media Player components..." type="application/x-oleobject" width="400" height="300">
					  <param id="FileName" name="FileName" value="">
					  <param name="animationatStart" value="true">
					  <param name="transparentatStart" value="true">
					  <param name="autoStart" value="true">
					  <param name="showControls" value="true">
					  <param name="fullScreen" value="false">
					 <embed src="<?= $stream ?>" type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" name="MediaPlayer" autostart="1" showcontrols="1" width="400" height="45">
					</object>
					</div>
					<?
				}
			}
			
			//station remarks (description)
			if ($result[0]->remark) {
				echo '<br /><br />';
				echo '<div class="row">';
				echo '<div class="small-12 columns">';
				echo trim(preg_replace('/\s+/',' ', $result[0]->remark));			
				echo '</div></div>';
			}

			echo '<br /><br />';
			$homeUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/";
			$actualUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
			// g+ like button
			echo '<div class="row">';
			echo '<div class="small-8 columns">';
			echo '<div style="float: left;margin:2px"><g:plusone size="tall"></g:plusone></div>';
			// fb like button
			echo '<div style="float: left;margin:2px" class="fb-like" data-href=$actualUrl data-layout="box_count" data-action="like" data-show-faces="true" data-share="false"></div>';
			echo '<br /><br />';
			echo '</div></div>';
			echo '<div class="row">';
			echo '<div class="small-8 columns">';
			echo '<ul class="pricing-table">';
			echo '<li class="title">Station Information</li>';
			echo '<li class="bullet-item">Location : '.ucwords($result[0]->country).'</li>';
			if ($result[0]->language) {
				echo '<li class="bullet-item">Language : '.ucwords($result[0]->language).'</li>';
			}
			if ($result[0]->description) {
				echo '<li class="bullet-item">'.ucwords($result[0]->description).'</li>';
			}
			if ($result[0]->station_url) {
				$station = $result[0]->station_url;
				echo '<li class="bullet-item">Station url : '."<a href=$station>$station</a>".'</li>';
			}
			echo '</ul>';
			echo '</div></div>';
			echo '<br />';
			
			// fb feed
			echo '<div class="section group">';
			echo '<div class="small-12 columns">';
			if ($result[0]->fbid) {
			echo '<div class="medium-6 small-12 columns">';
				echo '<h4>'.$name.' Facebook Updates</h4>';
				echo '<br />';
				$fbid = $result[0]->fbid;
				echo '<div class="fbfeed">';
				echo do_shortcode('[custom-facebook-feed id='.$fbid.' num=3 textsize=13]');
				echo '</div>';
				echo '<br />';
				echo '</div>';
			}
			
			// twitter feed
			if ($result[0]->twitter) {
				echo '<div class="medium-6 small-12 columns">';
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
		$results = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE country_id = '$country'" );
		echo '<div class="row">';
		echo '<div class="small-12 columns">';
		echo '<h5>Stations</h5>';
		echo '</div></div>';
		echo '<hr>';
		foreach($results as $row) {
			if ($row->image) {
				$img = "/wp-content/uploads/logo/".$row->image.".jpg";
			} else {
				$img = "/wp-content/uploads/logo/radio.jpg";
			}
			echo '<div class="row">';
			echo '<div class="small-3 columns">';
			echo "<a href=$row->tag/><img src=$img alt='$row->tag'></a>";
			echo '</div>';
			echo '<div class="small-9 columns">';
			echo "<a href=$row->tag/>$row->name</a>";
			echo '<br />';
			if ($row->language) {
				echo "Language : ".ucwords($row->language);
				echo '<br />';
			}
			echo ucwords($row->description);
			echo '</div>';
			echo '</div>';
			echo '<hr>';
		}
	}
	?>
	<!-- Ads -->
	<div class="row">
		<div class="small-12 columns">
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<!-- Top-Radio Bottom -->
			<ins class="adsbygoogle"
				 style="display:block"
				 data-ad-client="ca-pub-0047723350429793"
				 data-ad-slot="2474627056"
				 data-ad-format="auto"></ins>
			<script>
			(adsbygoogle = window.adsbygoogle || []).push({});
			</script>
		</div>
	</div>
	</div>
	<?php
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
			if ($row->country_id == $country) {
				$checkCountry = true;
			}
		}
		if ($checkCountry) {
			$data = getLastPathSegment($_SERVER['REQUEST_URI']);
			$result = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE country_id = '$data[0]'" );
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
	if (($urlPath[1]) && ($urlPath[1] != '')) {
		if (($urlPath[2]) && ($urlPath[2] != '')) {
			$checkCountry = $wpdb->get_results( "SELECT country_id FROM radio_station_list WHERE country_id = '$urlPath[1]'" );

			$data = getLastPathSegment($_SERVER['REQUEST_URI']);
			// Individual stations page ie. /afghanistan/arman-fm/
			if ($checkCountry) {
				$result = $wpdb->get_results( "SELECT * FROM radio_station_list WHERE tag = '$data[1]' AND country_id = '$data[0]'" );
				$wp_query->post->post_title = $result[0]->name;
	
				$seo_title = 'Listen '.$result[0]->name.', '.ucwords($result[0]->country).' online for Android, iPhone, iPad, iOS and desktop PC.';
				$seo_desc = 'Listen to '.$result[0]->name.' online free streaming at TopRadio. '.trim(preg_replace('/\s+/',' ', $result[0]->remark));
				if ($result[0]->image) {
					$seo_image = 'http://top-radio.org/wp-content/uploads/logo-fb/'.$result[0]->image.'.jpg';
				}

				$keywords = array();

				$keywords[] = $result[0]->name;
				$keywords[] = $result[0]->country;
				$keywords[] = 'Radio Station';
				if ($result[0]->description) {
					$keywords[] = ucwords($result[0]->description);
				}
			}
			// Individual genres page ie. /genre/top-40/
			else {
				$seo_title = ucwords($data[1]).' Radio Stations Online Free Live Streaming.';
				$seo_desc = ucwords($data[1]).' AM and FM Radio Stations Online Free Streaming From All Around the World and Listen Now Online at Top-Radio.org';
				
				$keywords = array();

				$keywords[] = ucwords($data[1]);
				$keywords[] = 'Radio Station';
				$keywords[] = 'Free';
				$keywords[] = 'Online';
				$keywords[] = 'Live';
				$keywords[] = 'Stream';
				$keywords[] = 'AM';
				$keywords[] = 'FM';
			}
		}
		else {
			$checkCountry = $wpdb->get_results( "SELECT country_id FROM radio_station_list WHERE country_id = '$urlPath[1]'" );
			// Individual country page ie. /afghanistan/
			if ($checkCountry) {	
				$seo_title = ucwords($urlPath[1]).' Radio Stations Online Free Live Streaming';
				$seo_desc = 'Listen to over '.$wpdb->num_rows.' '.ucwords($urlPath[1]).' AM and FM Radio Stations online free streaming at TopRadio from your Android, iPhone, iPad, iOS and desktop PC.';
				$results = $wpdb->get_results( "SELECT name FROM radio_station_list WHERE country_id = '$urlPath[1]'" );
				$keywords = array();

				$keywords[] = ucwords($urlPath[1]);
				$keywords[] = 'Radio Station';
				$keywords[] = 'Free';
				$keywords[] = 'Online';
				$keywords[] = 'Live';
				$keywords[] = 'Stream';
				$keywords[] = 'AM';
				$keywords[] = 'FM';
			}
			// All other pages
			else {
				$seo_title = 'TopRadio Online Radio Free Live Streaming';
				$seo_desc = 'Listen to your favorite radio station at TopRadio. Your one place for every type of radio: music, news, sports, religious, business, pop, rock, jazz, classical, country, hip-hop, and much more.';
		
				$keywords = array();

				$keywords[] = 'TopRadio';
				$keywords[] = 'Radio Station';
				$keywords[] = 'Free';
				$keywords[] = 'Online';
				$keywords[] = 'Live';
				$keywords[] = 'Stream';
				$keywords[] = 'AM';
				$keywords[] = 'FM';
			}
		}
		$seo_keywords = '';
		$i = 0;
		foreach ($keywords as $keyword) {
			if ($keyword != '') {
				if ($i == 0) {
					$seo_keywords .= $keyword;
					$i = 1;
				}
				else {
					$seo_keywords .= ', '.$keyword;
				}
			}
		}
	}
	else {
		$wp_query->post->post_title = 'TopRadio';
	
		$seo_title = 'TopRadio: Listen to your favorite radio station from anywhere in the world';
		$seo_desc = 'Listen to your favorite radio station with TopRadio. Your one place for every type of radio: music, news, sports, religious, business, pop, rock, jazz, classical, country, hip-hop, and much more.';
	}
	if ((!$seo_image) || ($seo_image == '')) {
		$seo_image = "http://top-radio.org/wp-content/uploads/2015/05/TR01-500x500.png";
	}
	
	$topradio_seo = '
<!-- TopRadio SEO -->		
	
	<link rel="author" href="https://plus.google.com/u/0/105697456818218161068"/>

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
	<meta property="og:image" content="'.$seo_image.'" />
	';
//	$topradio_seo .= '<meta property="og:image" content="'.$thumbnail_url.'" />';

	$topradio_seo .= '
	<meta property="article:published_time" content="'.get_the_date('Y-m-d').'" />
	<meta property="article:modified_time" content="'.get_the_modified_date('Y-m-d').'" />

	<meta property="og:site_name" content="Top Radio"/>
	<meta property="fb:app_id" content="1654102731501095"/>
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
			__('#Top Ten Stations Widget', 'text_domain'), // Widget name
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
		echo '<h5>'. $title .'</h5>';

		// Display output
		global $wpdb;
/**
		if ($station1) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station1'");
			$img = '/wp-content/uploads/logo/'.$result[0]->image.'.jpg';
			$alt = $result[0]->name;
			$link = 'http://top-radio.org/'.$result[0]->country.'/'.$result[0]->tag.'/';
			echo  "<a href=$link><img src=$img alt='$alt' width='75'  /></a>";
		}
**/
		if ($station1) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station1'");
			$name = $result[0]->name;
			$link = '/'.$result[0]->country_id.'/'.$result[0]->tag.'/';
			echo  "<a href=$link>$name</a><br />";
		}
		if ($station2) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station2'");
			$name = $result[0]->name;
			$link = '/'.$result[0]->country_id.'/'.$result[0]->tag.'/';
			echo  "<a href=$link>$name</a><br />";
		}
		if ($station3) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station3'");
			$name = $result[0]->name;
			$link = '/'.$result[0]->country_id.'/'.$result[0]->tag.'/';
			echo  "<a href=$link>$name</a><br />";
		}
		if ($station4) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station4'");
			$name = $result[0]->name;
			$link = '/'.$result[0]->country_id.'/'.$result[0]->tag.'/';
			echo  "<a href=$link>$name</a><br />";
		}
		if ($station5) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station5'");
			$name = $result[0]->name;
			$link = '/'.$result[0]->country_id.'/'.$result[0]->tag.'/';
			echo  "<a href=$link>$name</a><br />";
		}
			if ($station6) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station6'");
			$name = $result[0]->name;
			$link = '/'.$result[0]->country_id.'/'.$result[0]->tag.'/';
			echo  "<a href=$link>$name</a><br />";
		}
		if ($station7) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station7'");
			$name = $result[0]->name;
			$link = '/'.$result[0]->country_id.'/'.$result[0]->tag.'/';
			echo  "<a href=$link>$name</a><br />";
		}
		if ($station8) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station8'");
			$name = $result[0]->name;
			$link = '/'.$result[0]->country_id.'/'.$result[0]->tag.'/';
			echo  "<a href=$link>$name</a><br />";
		}
		if ($station9) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station9'");
			$name = $result[0]->name;
			$link = '/'.$result[0]->country_id.'/'.$result[0]->tag.'/';
			echo  "<a href=$link>$name</a><br />";
		}
		if ($station10) {
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE name = '$station10'");
			$name = $result[0]->name;
			$link = '/'.$result[0]->country_id.'/'.$result[0]->tag.'/';
			echo  "<a href=$link>$name</a><br /><br />";
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

// Widget to list stations by country
// Creating the widget
class country_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'country_widget', // Base ID
			__('#Country Widget', 'text_domain'), // Widget name
			array( 'description' => __( 'TopRadio widget to list stations by country', 'text_domain' ), ) // Widget description
		);
	}

// Front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

// Before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] .'<h5>'. $title .'</h5>'. $args['after_title'];

// Display output
		global $wpdb;
		$data = getLastPathSegment($_SERVER['REQUEST_URI']);
		
		if (isset($data[1])) {
			$countryID = ucwords($data[0]);
			$results = $wpdb->get_results("select * FROM radio_station_list WHERE country_id = '$countryID'");
			$country = $results[0]->country;
			echo "<h5>$country</h5>";
			foreach( $results as $result ) {
				$station = $result->name;
				$link = '/'.$result->country_id.'/'.$result->tag.'/';
				echo  "<a href=$link>$station</a><br />";
			}
			echo '<br />';
		}
		echo $args['after_widget'];
	}

// Backend
	public function form( $instance ) {
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
		<?php
	}
} // Class country_widget ends here

// Widget to display social links of a station
// Creating the widget
class social_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'social_widget', // Base ID
			__('#Social Widget', 'text_domain'), // Widget name
			array( 'description' => __( 'TopRadio widget to display social links of a station', 'text_domain' ), ) // Widget description
		);
	}

// Front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

// Before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] .'<h5>'. $title .'</h5>'. $args['after_title'];

// Display output
		global $wpdb;
		$data = getLastPathSegment($_SERVER['REQUEST_URI']);
		
		if (isset($data[1])) {
		
			$result = $wpdb->get_results("select * FROM radio_station_list WHERE tag = '$data[1]'");
			$name = $result[0]->name." Social Links";
			$fb = $result[0]->fb;
			$tw = $result[0]->twitter;
			if (($fb != "") || ($tw != "")) {
				echo "<h5>$name</h5>";
				if ($fb != "") {
					echo  "<a href=$fb>Facebook</a><br />";
				}
				if ($tw != "") {
					echo  "<a href=$tw>Twitter</a><br />";
				}
			}
			echo '<br />';
		}
		echo $args['after_widget'];
	}

// Backend
	public function form( $instance ) {
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
		<?php
	}
} // Class country_widget ends here

// Register and load the widget
function load_widget() {
    register_widget( 'my_widget' );
    register_widget( 'country_widget' );
    register_widget( 'social_widget' );
}
add_action( 'widgets_init', 'load_widget' );

add_action( 'init', 'seo_loader_init', 0 ); 
add_shortcode( 'shadowin', 'shadowin_func' );

?>