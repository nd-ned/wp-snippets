<?php
//top posts counter
function NAMESPACE_popular_posts($post_id) {
	$count_key = 'popular_posts';
	$count = get_post_meta($post_id, $count_key, true);
	if ($count == '') {
		$count = 0;
		delete_post_meta($post_id, $count_key);
		add_post_meta($post_id, $count_key, '0');
	} else {
		$count++;
		update_post_meta($post_id, $count_key, $count);
	}
}
function NAMESPACE_track_posts($post_id) {
	if (!is_single()) return;
	if (empty($post_id)) {
		global $post;
		$post_id = $post->ID;
	}
	NAMESPACE_popular_posts($post_id);
}
add_action('wp_head', 'NAMESPACE_track_posts');

//create custom endpoint
function NAMESPACE_register_endpoint() {
    //endpont's route
	register_rest_route('NAMESPACE/v1', '/topposts', array(
		'methods' => 'GET',
		'callback' => 'NAMESPACE_get_top_posts',
		) );
}
add_action( 'rest_api_init', 'NAMESPACE_register_endpoint');

function NAMESPACE_get_top_posts ($request) {
	$popular = new WP_Query(array('posts_per_page'=>4, 'meta_key'=>'popular_posts', 'orderby'=>'meta_value_num', 'order'=>'DESC'));
	$posts = $popular->get_posts();
    //route output
	foreach( $posts as $post ) {
	    $imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), "medium" );
	    $imgsrc = $imgsrc[0];
	    $output[] = array(
	    'id' => $post->ID, 
	    'title' => $post->post_title, 
	    'count' => $post->popular_posts, 
	    'link' => get_permalink($post), 
	    'image_src' => $imgsrc
	    );
	 }   
	return rest_ensure_response($output);
}