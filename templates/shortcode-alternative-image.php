<?php 
$eai_featured_image = get_post_meta( get_the_ID(), 'eai-featured-image' );

if ( ! empty( $eai_featured_image ) ) : ?>
	<div class="edd_download_image">
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<img src="<?php echo $eai_featured_image[0]; ?>" />
		</a>
	</div>
<?php elseif ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ) : ?>
	<div class="edd_download_image">
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php echo get_the_post_thumbnail( get_the_ID(), 'thumbnail' ); ?>
		</a>
	</div>
<?php endif; ?>