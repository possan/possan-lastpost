<?php
/*
Plugin Name: Last post javascript widget
Plugin URI: http://possan.se
Description: A plugin that provides an embeddable script containing the last post
Version: 0.1
Author: Per-Olov Jernberg
Author URI: http://possan.se
*/


function possan_lastpost_render_post_by_id( $id ){
	$blog_id = 1; // TODO: fix this
	if( !headers_sent() )
		header("Content-type: text/javascript");

	
	echo "// render post by id: #".$id."\n";
	$post = get_post( $id );
	// print_r( $post );

	$link = get_permalink( $id );

	$html = "<h1><a href=\"".$link."\">".$post->post_title."</a></h1>\n".$post->post_content;

	echo "document.write(".json_encode($html).");";
}

function possan_lastpost_activate()
{
  	add_rewrite_rule( 'lastpost\.js$', 'index.php?possan_lastpost=1', 'top' );
	add_rewrite_rule( '.+/post\.js$', 'index.php?possan_lastpost=2', 'top' );
	global $wp_rewrite;
	$wp_rewrite->flush_rules(); 
}

function possan_lastpost_init()
{
}

function possan_lastpost_query_vars( $query_vars )
{
    $query_vars[] = 'possan_lastpost';
    return $query_vars;
}

function possan_lastpost_parse_request( &$wp )
{
    if ( array_key_exists( 'possan_lastpost', $wp->query_vars ) ) {
	// echo "Hello from my plug";
	// print_r($wp);
	$arg = $wp->query_vars["possan_lastpost"];
	if( $arg == 1 ){
		$last = wp_get_recent_posts( '1');
		$last_id = $last['0']['ID'];
		possan_lastpost_render_post_by_id( $last_id );
	}
	else if( $arg == 2 ) { 
		$url = $wp->request;
		$url = substr( $url, 0, strlen( $url )-8 );
		$id = url_to_postid( $url );
		possan_lastpost_render_post_by_id( $id );
	}
	
        exit();
    }
    return;
}

register_activation_hook( __FILE__, 'possan_lastpost_activate' );
add_action( 'init', 'possan_lastpost_init' );
add_filter( 'query_vars', 'possan_lastpost_query_vars' );
add_action( 'parse_request', 'possan_lastpost_parse_request' );

?>
