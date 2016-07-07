<?php get_header(); ?>
<?php if (is_single()): ?>
<div class="franco_virtualtour-single">
	<iframe src="<?php echo esc_html(get_post_meta(get_the_ID(), 'streetview_url', true)); ?>" height="600" frameborder="0" width="100%"></iframe>
	<div class="info">
		<h3><span class="title"><?php the_title(); ?></span><span class="content"><?php echo trim($post->post_content) != '' ? ' - ' . $post->post_content : ''; ?></span><a href="<?php echo esc_html(get_post_meta(get_the_ID(), 'streetview_url', true)); ?>" class="btn right" target="_blank">Open in Google Maps</a></h3>
	</div>
</div>
<?php endif; ?>
<div id="primary" class="franco_virtualtour-index">
    <div id="content" role="main">
    <h2>Google Street View</h2>
    <?php $loop = new WP_Query(array('post_type' => 'franco_virtualtour', 'nopaging' => true)); ?>
    <?php $i = 0; while ($loop->have_posts()): $loop->the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header>
                <a href="<?php echo get_permalink(); ?>">
                	<?php the_post_thumbnail(array(320, 200)); ?><br>
                	<span class="title"><?php the_title(); ?></span><br>
                    <span class="content"><?php the_category(', '); ?></span>
                </a>
            </header>
        </article>
        <?php if (++$i % 3 == 0): ?><div style="height:0">&nbsp;</div><?php endif; ?>
    <?php endwhile; ?>
    </div>
</div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>
