<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php 
	$s_f_t     = get_post_meta(get_the_ID(), 's_f_t', true);
	$s_f_e     = get_post_meta(get_the_ID(), 's_f_e', true);
	$s_f_n_a   = get_post_meta(get_the_ID(), 's_f_n_a', true);
	$s_f_n_a_l = get_post_meta(get_the_ID(), 's_f_n_a_l', true);
	$s_f_n_b   = get_post_meta(get_the_ID(), 's_f_n_b', true);
	$s_f_n_b_l = get_post_meta(get_the_ID(), 's_f_n_b_l', true);
?>
<?php if ( get_post_meta( get_the_ID(), 's_f_e', true ) ) { ?>
<div class="g-row show-grey">
	<div class="g-col">
		<div class="section-box">
			<div class="group-title" <?php aos_a(); ?>>
				<h3><?php echo $s_f_t; ?></h3>
				<div class="clear"></div>
			</div>
				<div class="group-contact">
					<div class="group-contact-main single-content" <?php aos_a(); ?>>
						<?php echo $s_f_e; ?>
					</div>
					<div class="clear"></div>
					<div class="group-contact-more" <?php aos_a(); ?>>
						<span class="group-more">
							<a href="<?php echo $s_f_n_a_l; ?>" target="_blank" rel="bookmark"><?php echo $s_f_n_a; ?></a>
						</span>
						<span class="group-phone"><a href="<?php echo $s_f_n_b_l; ?>" rel="bookmark" target="_blank"><?php echo $s_f_n_b; ?></a></span>
						<div class="clear"></div>
					</div>
				</div>
			<?php wp_reset_query(); ?>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
</div>
<?php } ?>