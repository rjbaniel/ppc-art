<?php
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

function ppcart__add_artist_meta_box_for_post() {
	add_meta_box( 'ppc-artist', 'Artist', 'ppcart__artist_meta_box', 'portfolio', 'side' );
}
add_action( 'add_meta_boxes_portfolio', 'ppcart__add_artist_meta_box_for_post' );

function ppcart__artist_meta_box( $post ) {
	wp_enqueue_script( 'awesomplete', get_stylesheet_directory_uri() . '/lib/awesomplete.js', array(), '1.0', true );
	wp_enqueue_style( 'awesomplete', get_stylesheet_directory_uri() . '/lib/awesomplete.css', array(), '1.0', 'screen' );
	wp_enqueue_script( 'ppcart-artist-metabox', get_stylesheet_directory_uri() . '/js/ppcart-artist-metabox.js', array(), '1.0', true );
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
	?>
	<label for="ppc-art">Artist name:</label>
	<input
		class="widefat awesomplete"
		name="ppcart-artist"
		id="ppc-art"
		type="text"
		value="<?php echo esc_attr( $current_artist_name ); ?>"
		<?php
		$artists = ppcart__get_artists_list();
		if ( $artists ) {
			echo ' data-list="' . $artists . '"';
		}
		?>
	>
	<?php
	wp_nonce_field( 'ppcart-save-artist', 'ppcart-artist-nonce' );
}

function ppcart__save_artist( $post_id ) {
	if ( get_post_type( $post_id ) != 'portfolio' )
		return; 

	if ( ! isset( $_POST['ppcart-artist'] ) )
		return;

	if ( isset( $_POST['ppcart-artist-nonce'] ) && ! wp_verify_nonce( $_POST['ppcart-artist-nonce'], 'ppcart-save-artist' ) )
		wp_die( 'Sorry, we weren\'t able to verify your request.' );

	$artists = ppcart__get_artists();
	$added_artist = '';
	foreach( $artists as $artist ) {
		if ( $artist->post_title == $_POST['ppcart-artist'] )
			$added_artist = $artist->ID;
	}

	update_post_meta( $post_id, 'ppcart-artist', $added_artist );
}
add_action( 'save_post', 'ppcart__save_artist' );

function ppcart__get_artists_list() {
	$artists = ppcart__get_artists();

	if ( ! is_array( $artists ) || empty( $artists ) )
		return '';

	$artist_names = array();
	foreach( $artists as $artist )
		$artist_names[] = $artist->post_title;
	$artists_list = implode( ', ', $artist_names);
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