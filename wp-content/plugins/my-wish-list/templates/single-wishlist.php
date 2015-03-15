<?php
/**
 * The Template for displaying all single playlist posts.  Based on TwentyEleven.
 */

get_header(); ?>
		<div id="primary">
			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<nav id="nav-single">
						<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentyeleven' ); ?></h3>
						<span class="nav-previous"><?php previous_post_link( '%link', __( '<span class="meta-nav">&larr;</span> Previous', 'twentyeleven' ) ); ?></span>
						<span class="nav-next"><?php next_post_link( '%link', __( 'Next <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?></span>
					</nav><!-- #nav-single -->

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php if ( 'post' == get_post_type() ) : ?>
						<div class="entry-meta">
							<?php twentyeleven_posted_on(); ?>
						</div><!-- .entry-meta -->
						<?php endif; ?>
					</header><!-- .entry-header -->
					
					<div class="entry-content">
						<!-- custom wishlist output -->
						
						<?php 
						//get the metadata
						$wishlist_instructions_link = get_post_meta($post->ID,'wishlist_instructions_link',true);
						$wishlist_show_form = get_post_meta($post->ID,'wishlist_show_form',true);
						$wishlist_show_donor = get_post_meta($post->ID,'wishlist_show_donor',true);
						
						//thank you message for donors
						if(isset($_POST['wish_donor_thank_you'])) {
							echo '<div class="wishlist-thanks"> Thank you for pledging to get '.$_POST['wish_donor_donation'].' for us!  ';
							if($wishlist_instructions_link != '') {
								echo '<a href="'.$wishlist_instructions_link.'">Click here</a> for instructions on purchasing and sending the item.';
							}
							echo '</div>';
							unset($_POST['wish_donor_thank_you']);
						}
						
						?>
						
						<div class="wishlist-desc"><?php the_content();?></div>
					
						<?php 
						//output the list
						$wishmeta = get_post_meta(get_the_ID(), 'my_wishes', true);
						foreach($wishmeta as $i => $meta):
						?>
						<div class="wishlist-item">
							<h3><?php echo $meta['wishitem']; ?></h3>
									
							<div class="wishlist-image-form">
								<?php if($meta['wishdonorname'] != '' || $meta['wishdonoremail'] != ''): ?>
									<?php if($wishlist_show_donor == "show"): ?>
											<span class="wishlist-promised">Item Promised by <?php echo $meta['wishdonorname']; ?>!</span><br />
									<?php else: ?>
											<span class="wishlist-promised">Item Promised!</span><br />
									<?php endif; ?>
								<?php endif; ?>
									
								<?php if($meta['wishfilename'] != ''): ?>
									<?php $thumb = wp_get_attachment_image_src( $meta['wishfilename'], 'medium'); ?>
									<img src="<?php echo $thumb[0]; ?>" />
									<br />
								<?php endif; ?>
								
								<?php if($meta['wishdonorname'] == '' && $meta['wishdonoremail'] == '' && $wishlist_show_form == "show"): ?>
								I would like to get this item for you!  Tell me how!
								<form method="post" action="" class="wishlist-form">
									<table>
										<tr><td><span class="wishlist-form-label">Name:</span></td><td><input name="wishdonorname" type="text" class="wishlist-form-input" /></td></tr>
										<tr><td><span class="wishlist-form-label">Email:</span></td><td><input name="wishdonoremail" type="text" class="wishlist-form-input" /></td></tr>
										
										<tr><td colspan="2" class="wishlist-double-span">
										
										<input type="hidden" name="wishitem" value="<?php echo $meta['wishitem']; ?>" />
										<input type="hidden" name="post_id" value="<?php the_ID(); ?>" />
										<input type="hidden" name="wishindex" value="<?php echo $i; ?>" />
										<input type="hidden" name="wish_donor_add_update" value="1" />
										<input type="submit" value="Submit" class="wishlist-form-submit-button" />
										</td></tr>
									</table>
								</form>
								<?php endif; ?>
							</div>
									
							<?php if($meta['wishprice'] != ''): ?>
								<span class="wishlist-label">Price:</span> <?php echo $meta['wishprice']; ?><br />
							<?php endif; ?>
							
							<?php if($meta['wishsize'] != ''): ?>
								<span class="wishlist-label">Size:</span> <?php echo $meta['wishsize']; ?><br />
							<?php endif; ?>
							
							<?php if($meta['wishcolor'] != ''): ?>
								<span class="wishlist-label">Color:</span> <?php echo $meta['wishcolor']; ?><br />
							<?php endif; ?>
							
							<?php if($meta['wishstore'] != ''): ?>
								<span class="wishlist-label">Available at:</span> <?php echo $meta['wishstore']; ?><br />
							<?php endif; ?>
									
							<?php if($meta['wishlink'] != ''): ?>
								<?php 
									if(!stristr($meta['wishlink'], 'http://') && !stristr($meta['wishlink'], 'https://')) {
										$meta['wishlink'] = 'http://'.$meta['wishlink'];
									}
								?>
									<span class="wishlist-label">Link:</span> <a href="<?php echo $meta['wishlink']; ?>"><?php echo $meta['wishlink']; ?></a><br />
							<?php endif; ?>
								
								
						</div>
									
						<hr class="wishlist-hr" />
						<?php endforeach; ?>
						 
						<!-- /custom wishlist output -->
						
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
					</article>

					<?php //comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>