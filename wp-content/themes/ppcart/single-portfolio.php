<?php get_header(); ?>
<?php $mts_options = get_option(MTS_THEME_NAME); ?>
	<div class="content ss-full-width post-single-content">
		<article id="post-<?php the_ID(); ?>" <?php post_class('g post'); ?> itemscope itemtype="http://schema.org/BlogPosting">
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
					<div class="single_post">
						<?php
						$post_id = get_the_ID();
						if ( get_post_meta( $post_id, 'ppcart-show-image', true ) ) {
							$single_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
							$single_image = $single_image[0];
							$single_image_url = bfi_thumb( $single_image, array( 'width' => '1090', 'height' => '450', 'crop' => true ) );
							
							if ( $single_image_url )
								echo '<img src="'.$single_image_url.'" width="1090" height="450" class="single_featured">';
						}
						?>
						
						<header class="post-header">
							<h1 class="single-post-title" itemprop="headline"><?php the_title(); ?></h1>
						</header><!--.headline_area-->

						<div class="post-content" itemprop="articleBody">
							<?php the_content(); ?>
						</div>
					</div><!--.post-content box mark-links-->

					<?php wp_link_pages(array('before' => '<div class="pagination">', 'after' => '</div>', 'link_before'  => '<span class="current"><span class="currenttext">', 'link_after' => '</span></span>', 'next_or_number' => 'next_and_number', 'nextpagelink' => __('Next','mythemeshop'), 'previouspagelink' => __('Previous','mythemeshop'), 'pagelink' => '%','echo' => 1 )); ?>
					
					<?php 
					$artist_id = get_post_meta( $post_id, 'ppcart-artist', true );
					if ( $artist_id ) :
						$artist = get_post( $artist_id );
					?>
						<div class="postauthor">
							<h4 style="padding-left: 0; margin-bottom: 1rem;">
								<?php
								_e('About The Artist: ', 'ppc-art');
								echo esc_html( $artist->post_title );
								?>
							</h4>

							<?php
							$artist_image = wp_get_attachment_image_src( get_post_thumbnail_id( $artist_id ), 'thumbnail' );
							if ( $artist_image ) : ?>
								<img class="avatar" src="<?php echo esc_url( $artist_image[0] ); ?>">
							<?php endif; ?>

							<?php echo apply_filters( 'the_content', $artist->post_content ); ?>
						</div>
					<?php endif; ?>
			<?php endwhile; /* end loop */ ?>
		</article>
	</div>
<?php get_footer(); ?>