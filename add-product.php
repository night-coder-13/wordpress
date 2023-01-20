<?php
/**
 * Template Name: Add New Product Template
 *
 */
get_header();

$args = [
	'taxonomy' => 'product_cat',
	'hide_empty' => false,
];
$Product_type = get_terms($args);


?>

	<form action="#" method="post" id="product-form">
		<label for="">Product Type:
			<select name="product_type" id="product-category">
				<option data-slug=""value=""></option>
				<?php foreach ( $Product_type  as $item ) { ?>

					<option  value="<?php echo $item->term_id; ?>">
						<?php echo $item->name; ?>
					</option>
					<?php
				}?>
			</select>
		</label>
		<br>
    	<input type="file" name="img[]" multiple>
		<br>
		<b>OR</b>
		<br>
		<input type="text" name="dir" placeholder="path dirctory">
		<br>
		<button name="submit" type="submit">Submit</button>
	</form>

<?php

function AddPost($image_url , $post_title){
	
	// Get the path to the upload directory.
	$wp_upload_dir = wp_upload_dir();

	// download to temp dir
	$temp_file = download_url( $image_url );

	if( is_wp_error( $temp_file ) ) {
		return false;
	}

	// move the temp file into the uploads directory
	$file = array(
		'name'     => basename( $image_url ),
		'type'     => mime_content_type( $temp_file ),
		'tmp_name' => $temp_file,
		'size'     => filesize( $temp_file ),
	);
	$sideload = wp_handle_sideload(
		$file,
		array(
			'test_form'   => false // no needs to check 'action' parameter
		)
	);

	if( ! empty( $sideload[ 'error' ] ) ) {
		// you may return error message if you want
		return false;
	}

	// Create post object
	$my_post = array(
		'post_title'    => preg_replace( '/\.[^.]+$/', '', basename( $image_url ) ),
		'taxonomy'    => 'product_cat',
		'post_content'  => '',
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_category' => array( 8,39 ),
		'post_type' => 'product'
	);

	// Insert the post into the database
	$parent_post_id =wp_insert_post( $my_post );
	// The ID of the post this attachment is for.
	// $parent_post_id = 0;

	// Prepare an array of post data for the attachment.
	$attachment = array(
		'guid'           => $wp_upload_dir['url'] . '/' . basename( $image_url ), 
		'post_mime_type' => 'image/jpeg',
		'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $image_url ) ),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	// Insert the attachment.
	$attach_id = wp_insert_attachment( $attachment, $wp_upload_dir['url'] . '/' . basename( $image_url ), $parent_post_id );

	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	$attach_data = wp_generate_attachment_metadata( $attach_id, $wp_upload_dir['url'] );
	wp_update_attachment_metadata( $attach_id, $attach_data );


	add_post_meta($parent_post_id , 'image' , $attach_id);


	global $wpdb;      
	$wp_term_relationships = array(
		'object_id'  =>  $parent_post_id,
		'term_taxonomy_id' =>  4,
		'term_order' =>  0
	);
			
	$formats_values = array( '%s', '%d' );

	$wpdb->insert( 'wp_term_relationships' , $wp_term_relationships, $formats_values );

}

if(isset($_POST['submit'])){
	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	$image_string = str_replace('&','|',str_replace('%5B%5D','', file_get_contents('php://input')));
	$image_array = explode('|', $image_string);
	$images=[];
	$i=0;
	$dirction='';
	foreach($image_array as $item){
		if(strstr($item , 'dir')){
			$dirction=str_replace('dir=','',strstr( $item ,'dir'));
		}
	}
	foreach($image_array as $item){
		if(strstr($item , 'img')){
			$images[$i]=urldecode('http://localhost/change-name/' . $dirction .'/'. str_replace('img=','',$item));
			// AddPost($images[$i]);
			$i++;
		}
	}

}

?>

<!-- get_footer(); -->
