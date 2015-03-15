<?php get_header(); ?>

<div id="page">

	<div id="content">
		<h1 class="pagetitle">Search Results for &ldquo;<?php the_search_query(); ?>&rdquo;</h1>

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post">
				<h2 id="post-<?php the_ID(); ?>" class="title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<small class="byline"><?php the_time('l, F jS, Y') ?></small>
				<div class="entry"><?php the_excerpt(); ?></div>
				<p class="meta">Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
			</div>

		<?php endwhile; ?>

	<?php else : ?>

		<p>No posts found. Try a different search?</p>
<!-- hidden since we already have one in the sidebar
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
-->

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>