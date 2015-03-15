<?php get_header(); ?>

<div id="page">

<table style="background:url(http://2.bp.blogspot.com/_Toi-rh0Nm00/SwUefw_jGXI/AAAAAAAADkw/lgYJrD6LInU/s1600/contentbg.jpg) repeat-y;" width="760" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td>

	<div id="content">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<h1 class="title"><?php the_title(); ?></h1>
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			</div>
		</div>
		<?php endwhile; endif; ?>
		<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
	</div>
	<!-- end div#content -->

<?php get_sidebar(); ?>

</td></tr>
	</table>

</div>
<!-- end div#page -->

<?php get_footer(); ?>