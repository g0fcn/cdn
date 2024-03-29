<?php
/*
Template Name: 近期留言
*/
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php get_header(); ?>
<style type="text/css">
#primary {
	width: 100%;
}
</style>

	<div id="primary" class="content-area">
		<main id="main" class="be-main site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" class="post-item post">
					<div id="message" class="message-page gaimg">
						<ul>
							<?php 
							$no_comments = false;
							$avatar_size = 96;
							$comments_query = new WP_Comment_Query();
							$comments = $comments_query->query( array_merge( array( 'number' => 66, 'type' => 'comments', 'status' => 'approve', 'author__not_in' => 1 ) ) );
							if ( $comments ) : foreach ( $comments as $comment ) : ?>

							<li class="bkc load">
								<a href="<?php echo get_permalink($comment->comment_post_ID); ?>#anchor-comment-<?php echo $comment->comment_ID; ?>" title="发表在：<?php echo get_the_title($comment->comment_post_ID); ?>" rel="external nofollow">
									<?php if (zm_get_option('cache_avatar')) { ?>
										<?php echo begin_avatar( $comment->comment_author_email, $avatar_size, '', get_comment_author( $comment->comment_ID ) ); ?>
									<?php } else { ?>
										<?php if ( !zm_get_option( 'avatar_load' ) ) {
											echo get_avatar( $comment->comment_author_email, $avatar_size, '', get_comment_author( $comment->comment_ID ) );
										} else {
											echo '<img class="avatar photo" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" alt="'. get_comment_author( $comment->comment_ID ) .'" width="30" height="30" data-original="' . preg_replace(array('/^.+(src=)(\"|\')/i', '/(\"|\')\sclass=(\"|\').+$/i'), array('', ''), get_avatar( $comment->comment_author_email, $avatar_size, '', get_comment_author( $comment->comment_ID ) )) . '" />';
										} ?>
									<?php } ?>
									<span class="comment_author"><strong><?php echo get_comment_author( $comment->comment_ID ); ?></strong></span>
									<?php echo convert_smilies($comment->comment_content); ?>
								</a>
							</li>

							<?php endforeach; else : ?>
								<li><?php _e('暂无留言', 'begin'); ?></li>
								<?php $no_comments = true;
							endif; ?>
						</ul>
					</div><!-- #message -->
				</article><!-- #page -->
			<?php endwhile;?>
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>