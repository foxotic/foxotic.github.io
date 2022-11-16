<?php $format = get_post_format(); ?>

<div class="featured-card-outer">
	<div class="featured-card">

		<div class="featured-card-image-wrapper">
			<div class="featured-card-image" style="background-image:url('<?php if ( has_post_thumbnail() ): ?><?php the_post_thumbnail_url('incolor-medium'); ?><?php else: ?><?php echo esc_url( get_template_directory_uri() ); ?>/img/thumb-medium.png<?php endif; ?>');">
				<a class="featured-card-link" href="<?php the_permalink(); ?>"></a>
				<?php if ( has_post_format('video') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fas fa-play"></i></span>'; ?>
				<?php if ( has_post_format('audio') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fas fa-volume-up"></i></span>'; ?>
				<?php if ( is_sticky() ) echo'<span class="thumb-icon small"><i class="fas fa-star"></i></span>'; ?>
			</div>
			<?php if ( comments_open() && ( get_theme_mod( 'comment-count', 'on' ) =='on' ) ): ?>
				<a class="card-comments" href="<?php comments_link(); ?>"><i class="fas fa-comment"></i><span><?php comments_number( '0', '1', '%' ); ?></span></a>
			<?php endif; ?>
		</div>
		
		<div class="featured-card-content">
			<div class="featured-card-category"><?php the_category(' / '); ?></div>
			<h2 class="featured-card-title">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>
		</div>
		
	</div>
</div>