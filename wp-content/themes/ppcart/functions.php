<?php

function ppcart__init() {
	require_once( __DIR__ . '/includes/navigation.php' );
	wp_enqueue_style( 'ppacart', get_stylesheet_uri(), array( 'stylesheet', 'responsive' ), '1.0', 'all' );
}
add_action( 'init', 'ppcart__init' );

function ppcart__register_artist_cpt() {
	$args = array(
		'label'				=> 'Artists',
		'hierarchical'		=> true,
		'description'		=> 'A custom post type for artists on the PPC Art website.',
		'public'			=> true,
		'menu_icon'			=> 'dashicons-admin-users',
		'has_archive'		=> false,
		'supports'			 => array( 'title', 'editor', 'thumbnail', 'excerpt' )
	);

	register_post_type( 'artist', $args );	
}
add_action( 'init', 'ppcart__register_artist_cpt' );

function ppcart__add_meta_box_for_portfolio() {
	add_meta_box( 'ppc-artist', 'Customize', 'ppcart__portfolio_meta_box', 'portfolio', 'side', 'low' );
}
add_action( 'add_meta_boxes_portfolio', 'ppcart__add_meta_box_for_portfolio' );

function ppcart__portfolio_meta_box( $post ) {
	wp_enqueue_script( 'awesomplete', get_stylesheet_directory_uri() . '/lib/awesomplete.js', array(), '1.0', true );
	wp_enqueue_style( 'awesomplete', get_stylesheet_directory_uri() . '/lib/awesomplete.css', array(), '1.0', 'screen' );
	wp_localize_script(
		'ppcart-artist-metabox',
		'ajax_object',
		array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
	);
	$current_value = get_post_meta( $post->ID, 'ppcart-artist', true );
	$current_artist_name = '';
	if ( $current_value ) {
		$current_artist = get_post( $current_value );
		$current_artist_name = $current_artist->post_title;
	}

	$show_image = '';
	if ( get_post_meta( $post->ID, 'ppcart-show-image', true ) )
		$show_image = 'checked';

	?>
	<label for="ppcart-artist">Artist name:</label>
	<input
		class="widefat awesomplete"
		name="ppcart-artist"
		id="ppcart-artist"
		type="text"
		value="<?php echo esc_attr( $current_artist_name ); ?>"
		<?php
		$artists = ppcart__get_artists_list();
		if ( $artists ) {
			echo ' data-list="' . $artists . '"';
		}
		?>
	>

	<br><br>

	<input type="checkbox" id="ppcart-show-image" name="ppcart-show-image" <?php echo esc_attr( $show_image ); ?>>
	<label for="ppcart-show-image">Show featured image on portfolio item page?</label>

	<?php
	wp_nonce_field( 'ppcart-save-portfolio', 'ppcart-portfolio-nonce' );
}

function ppcart__save_portfolio( $post_id ) {
	if ( get_post_type( $post_id ) != 'portfolio' )
		return; 

	if ( ! isset( $_POST['ppcart-artist'] ) && ! isset( $_POST['ppcart-show-image'] ) )
		return;

	if ( isset( $_POST['ppcart-portfolio-nonce'] ) && ! wp_verify_nonce( $_POST['ppcart-portfolio-nonce'], 'ppcart-save-portfolio' ) )
		wp_die( 'Sorry, we weren\'t able to verify your request.' );

	$artists = ppcart__get_artists();
	$added_artist = '';
	foreach( $artists as $artist ) {
		if ( $artist->post_title == $_POST['ppcart-artist'] )
			$added_artist = $artist->ID;
	}
	update_post_meta( $post_id, 'ppcart-artist', $added_artist );

	$show_image = isset( $_POST['ppcart-show-image'] );
	update_post_meta( $post_id, 'ppcart-show-image', $show_image);
}
add_action( 'save_post', 'ppcart__save_portfolio' );

function ppcart__get_artists_list() {
	$artists = ppcart__get_artists();

	if ( ! is_array( $artists ) || empty( $artists ) )
		return '';

	$artist_names = array();
	foreach( $artists as $artist )
		$artist_names[] = $artist->post_title;
	$artists_list = implode( ', ', $artist_names);
	if ( count( $artist_names ) == 1 ) {
		$artists_list .= ',';
	}
	return $artists_list;
}

function ppcart__get_artists() {
	$args = array(
		'post_type'	=>		'artist',
		'posts_per_page'	=> 0,
	);
	$artists_query = new WP_Query( $args );
	if ( empty( $artists_query ) ) {
		return '';
	}

	return $artists_query->posts;
}