  <?php get_header(); ?>

<div id="page">
<table style="background:url(http://2.bp.blogspot.com/_Toi-rh0Nm00/SwUefw_jGXI/AAAAAAAADkw/lgYJrD6LInU/s1600/contentbg.jpg) repeat-y;" width="760" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td>
	<div id="content">
	<?php if (have_posts()): the_post(); ?>

		<div class="post" id="post-<?php the_ID(); ?>">
			<h1 class="title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h1>
			<h3 class="byline">Posted by <strong><?php the_author() ?> </strong> on <?php the_time('l') ?> <?php the_time('M j, Y') ?></h3>
			<div class="entry">
				<?php the_content('Read the rest of this entry &raquo;'); ?>
			</div>
			<p class="meta">Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
		</div>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<h2 class="title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<h3 class="byline"><?php the_time('F jS, Y') ?></h3>
				<div class="entry">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>
				<p class="meta">Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
			</div>

		<?php endwhile; ?>

	<?php else: ?>
		
		<div class="post">
			<h2 class="title">Not Found!</h2>
			<div class="entry">
				<p>Sorry, but you are looking for something that isn't here.</p>
			</div>
		</div>

	<?php endif; ?>
	</div>
	<!-- end div#content -->

	<?php get_sidebar(); ?>
	</td></tr>
	</table>
	
</div> 

<!-- end div#page -->

<?php get_footer(); ?>