<?php
/**
 * Plugin Name: Custom API
 * Plugin URI: http://chrushingit.com
 * Description: Crushing it!
 * Version: 1.0
 * Author: Art Vandelay
 * Author URI: http://watch-learn.com
 */

function wl_catalog() {
	$args = [
		'numberposts' => 500,
		'post_type' => 'catalog'
	];
	$posts = get_posts($args);

	$data = [];
	$i = 0;

	//This is a customized object for the catalog slider (El swiper slider)
	foreach($posts as $post) {
		$data[$i]['id'] = $post->ID;
		$data[$i]['title'] = empty($post->title) ? $post->post_title : $post->title;
		$data[$i]['slug'] = $post->post_name;
		$data[$i]['src'] = empty($post->image) ? '' : explode(',',$post->image)[0];
		$data[$i]['thumb'] = empty($post->image) ? '' : explode(',',$post->image)[0];
		$data[$i]['subHtml'] = '<div class="lightGallery-captions"><h4>'.$data[$i]['title'].'</h4></div>';
		$data[$i]['date'] = get_post_datetime( $post->ID, $field = 'modified', $source = 'local' )->format('Y-m-d H:i:s');	

		$i++;
	}
        $data = array_reverse($data);
	return $data;
}
function wl_blog($slug = 3) {
	$args = [
		'numberposts' => $slug['slug'],
		'post_type' => 'blog'
	];
	$posts = get_posts($args);

	$data = [];
	$i = 0;

	foreach($posts as $post) {
	$data[$i]['id'] = $post->ID;
	$data[$i]['title'] = empty($post->title) ? $post->post_title : $post->title;
        $data[$i]['slug'] = $post->post_name;
        $data[$i]['image'] = empty($post->image) ? '' : explode(',',$post->image)[0];
        $data[$i]['content'] = apply_filters('the_content', $post->post_content); 
        $data[$i]['author'] = get_the_author_meta('display_name', $post->post_author);
	$data[$i]['date'] = get_post_datetime( $post->ID, $field = 'modified', $source = 'local' )->format('Y-m-d H:i:s');	

	$i++;
	}

	return $data;
}
 
function wl_single_blog($slug) {
	$args = [
		'ID' => $slug['slug'],
		'post_type' => 'blog'
	];
	$post = get_post($slug['slug']);
	$data = [];
 	$data['id'] = $post->ID;
	$data['title'] = empty($post->title) ? $post->post_title : $post->title;
        $data['slug'] = $post->post_name;
        $data['image'] = empty($post->image) ? '' : explode(',',$post->image)[0];
        $data['content'] = apply_filters('the_content', $post->post_content); 
        $data['author'] = get_the_author_meta('display_name', $post->post_author);
	$data['date'] = get_post_datetime( $post->ID, $field = 'modified', $source = 'local' )->format('Y-m-d H:i:s');	
        
	return $data;
}

function wl_posts() {
	$args = [
		'numberposts' => 99999,
		'post_type' => 'post'
	];

	$posts = get_posts($args);

	$data = [];
	$i = 0;

	foreach($posts as $post) {
		$data[$i]['id'] = $post->ID;
		$data[$i]['title'] = $post->post_title;
		$data[$i]['content'] = $post->post_content;
		$data[$i]['slug'] = $post->post_name;
		$data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
		$data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($post->ID, 'medium');
		$data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($post->ID, 'large');
		$i++;
	}

	return $data;
}

function wl_post( $slug ) {
	$args = [
		'name' => $slug['slug'],
		'post_type' => 'post'
	];

	$post = get_posts($args);

	$data['id'] = $post[0]->ID;
	$data['title'] = $post[0]->post_title;
	$data['content'] = $post[0]->post_content;
	$data['slug'] = $post[0]->post_name;
	$data['featured_image']['thumbnail'] = get_the_post_thumbnail_url($post[0]->ID, 'thumbnail');
	$data['featured_image']['medium'] = get_the_post_thumbnail_url($post[0]->ID, 'medium');
	$data['featured_image']['large'] = get_the_post_thumbnail_url($post[0]->ID, 'large');

	return $data;
}


// Used in this video https://www.youtube.com/watch?v=76sJL9fd12Y
function wl_product() {
	$args = [
		'numberposts' => 99999,
		'post_type' => 'product'
	];
	$posts = get_posts($args);

	$data = [];
	$i = 0;

	foreach($posts as $post) {
		$data[$i]['id'] = $post->ID;
		$data[$i]['title'] = empty($post->title) ? $post->post_title : $post->title;
        $data[$i]['slug'] = $post->post_name;
        $data[$i]['_description'] = $post->_description;
        
		if(!empty(explode( ',',$post->image)[0]))
			$data[$i]['image1'] = wp_get_attachment_image_src( explode( ',',$post->image)[0], 'original' );
		if(!empty(explode( ',',$post->image)[1]))
			$data[$i]['image2'] = wp_get_attachment_image_src( explode( ',',$post->image)[1], 'original' );
        
        // $data[$i]['price'] = get_field('price', $post->ID);
		$i++;
	}

	return $data;
}

function wl_archive($slug) {
        global $wpdb;      
	$sql = "SELECT * FROM `wp_term_relationships` WHERE `term_taxonomy_id`=".$slug['slug'];
	$posts = $wpdb->get_results($sql);
		
	$data = [];
	$i = 0;

	foreach($posts as $post) {
		
		$post = get_post($post->object_id);

		$data[$i]['id'] = $post->ID;
		$data[$i]['title'] = empty($post->title) ? $post->post_title : $post->title;
        $data[$i]['slug'] = $post->post_name;
        $data[$i]['_description'] = $post->_description;
        $data[$i]['hashtag'] = $post->hashtag;
		if(!empty(explode( ',',$post->image)[0]))
        	$data[$i]['image1'] = wp_get_attachment_image_src( explode( ',',$post->image)[0], 'original' );
		if(!empty(explode( ',',$post->image)[1]))
        	$data[$i]['image2'] = wp_get_attachment_image_src( explode( ',',$post->image)[1], 'original' );

		$i++;
	}
        return $data;
	
}
// Used in this video https://www.youtube.com/watch?v=76sJL9fd12Y
function wl_single_product($slug) {
	$args = [
		'ID' => $slug['slug'],
		'post_type' => 'product'
	];
	$post = get_post($slug['slug']);
	$data = [];
		$data['id'] = $post->ID;
		$data['title'] = empty($post->title) ? $post->post_title : $post->title;
        	$data['slug'] = $post->post_name;
        	$data['_description'] = $post->_description;
        	$data['hashtag'] = $post->hashtag;
		if(!empty(explode( ',',$post->image)[0]))
        	$data['image1'] = wp_get_attachment_image_src( explode( ',',$post->image)[0], 'original' );
		if(!empty(explode( ',',$post->image)[1]))
        	$data['image2'] = wp_get_attachment_image_src( explode( ',',$post->image)[1], 'original' );
        
	return $data;
}

// taxonomy
function wl_product_cats() {
	$args = [
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
	];
	$posts = get_terms($args);

	$data = [];
	$i = 0;

	foreach($posts as $post) {
		// print_r($post);
		$data[$i]['id'] = $post->term_id;
		$data[$i]['name'] = $post->name;
		$thumbnail = get_term_meta($post->term_id)['thumbnail'];
		if(!empty($thumbnail[0]))
        	$data[$i]['img'] = wp_get_attachment_image_src( $thumbnail[0], 'thumbnail' );

        
        // $data[$i]['price'] = get_field('price', $post->ID);
		$i++;
	}

	return $data;
}
function wl_product_cat($slug) {
	$args = [
		'term_taxonomy_id' => $slug['slug'],
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
	];
	$posts = get_terms($args);

	$data = [];
	$i = 0;

	foreach($posts as $post) {
		// print_r($post);
		$data['id'] = $post->term_id;
		$data['name'] = $post->name;
		$data['description'] = $post->description;
		$thumbnail = get_term_meta($post->term_id)['thumbnail'];
		$thumbnail = explode( ',',$thumbnail[0]);
		if(!empty($thumbnail[0]))
        	$data['img'] = wp_get_attachment_image_src( $thumbnail[0], 'original' );
		if(!empty($thumbnail[1]))
        	$data['img_bg'] = wp_get_attachment_image_src( $thumbnail[1], 'original' );

		$i++;
	}

	return $data;
}

add_action('rest_api_init', function() {
	register_rest_route('wl/v1', 'posts', [
		'methods' => 'GET',
		'callback' => 'wl_posts',
	]);

	register_rest_route( 'wl/v1', 'posts/(?P<slug>[a-zA-Z0-9-]+)', array(
		'methods' => 'GET',
		'callback' => 'wl_post',
    ) );
    // catalog
    register_rest_route('wl/v1', 'catalog', [
	'methods' => 'GET',
	'callback' => 'wl_catalog',
     ]);
	
    
    // blog
    register_rest_route('wl/v1', 'blog', [
	'methods' => 'GET',
	'callback' => 'wl_blog',
    ]);

    register_rest_route('wl/v1', 'blog/(?P<slug>[a-zA-Z0-9-]+)', [
	'methods' => 'GET',
	'callback' => 'wl_blog',
    ]);

    register_rest_route('wl/v1', 'single_blog/(?P<slug>[a-zA-Z0-9-]+)', [
	'methods' => 'GET',
	'callback' => 'wl_single_blog',
    ]);

    // product
    register_rest_route('wl/v1', 'product', [
		'methods' => 'GET',
		'callback' => 'wl_product',
	]);
    
    register_rest_route('wl/v1', 'product/(?P<slug>[a-zA-Z0-9-]+)', [
		'methods' => 'GET',
		'callback' => 'wl_single_product',
	]);

    // category (taxonomy)
    register_rest_route('wl/v1', 'product_cat', [
		'methods' => 'GET',
		'callback' => 'wl_product_cats',
	]);
    register_rest_route('wl/v1', 'product_cat/(?P<slug>[a-zA-Z0-9-]+)', [
		'methods' => 'GET',
		'callback' => 'wl_product_cat',
	]);
	// archive
    register_rest_route('wl/v1', 'archive/(?P<slug>[a-zA-Z0-9-]+)', [
		'methods' => 'GET',
		'callback' => 'wl_archive',
	]);
});

