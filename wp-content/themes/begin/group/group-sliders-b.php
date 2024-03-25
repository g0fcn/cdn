<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php if ( co_get_option( 'group_slides_b' ) ) { ?>
	<?php 
		$addslides = ( array ) co_get_option( 'group_slides_b_add' );
		foreach ( $addslides as $items ) {
	?>

		<div class="g-row g-line group-slider-text<?php if ( ! empty( $items['line_white'] ) ) { ?> group-white<?php } ?>" <?php aos(); ?>>
			<div class="g-col">
				<div class="owl-carousel slides-text<?php if ( ! empty( $items['group_slides_b_r'] ) ) { ?> slider-r<?php } ?>">
					<?php 
						if ( ! empty( $items['group_slides_b_item'] ) ) {
							foreach ( $items['group_slides_b_item'] as $slides ) { ?>

								<div class="slides-item slides-center">
									<div class="slides-content">
										<div class="slides-item-text<?php if ( ! empty( $slides['group_slides_b_ret'] ) ) { ?> ret<?php } ?>">
											<div class="slides-item-title"><?php echo $slides['group_slides_b_title']; ?></div>
											<?php if ( ! empty( $slides['group_slides_b_des'] ) ) { ?>
												<div class="slides-item-des"><?php echo wpautop( $slides['group_slides_b_des'] ); ?></div>
											<?php } ?>

											<?php if ( ! empty( $slides['group_slides_b_btn'] ) ) { ?>
												<div class="slides-item-btn"><a href="<?php echo $slides['group_slides_b_btn_url']; ?>" rel="bookmark" <?php echo goal(); ?> class="slides-button"><?php echo $slides['group_slides_b_btn']; ?></a></div>
											<?php } ?>
										</div>
									</div>

									<?php if ( ! empty( $slides['group_slides_b_img'] ) ) { ?>
										<div class="slides-img">
											<div class="slides-item-img"><img src="<?php echo $slides['group_slides_b_img']; ?>" alt="<?php echo $slides['group_slides_b_title']; ?>"></div>
										</div>
									<?php } ?>
								</div>
							<?php }
						}
					?>
				</div>

				<div class="lazy-img ajax-owl-loading">
					<?php 
						if ( ! empty( $items['group_slides_b_item'] ) ) {
							foreach ( $items['group_slides_b_item'] as $slides ) { ?>
								<div class="slides-item slides-center">
									<div class="slides-content">
										<div class="slides-item-text<?php if ( ! empty( $slides['group_slides_b_ret'] ) ) { ?> ret<?php } ?>">
											<div class="slides-item-title"><?php echo $slides['group_slides_b_title']; ?></div>
											<?php if ( ! empty( $slides['group_slides_b_des'] ) ) { ?>
												<div class="slides-item-des"><?php echo wpautop( $slides['group_slides_b_des'] ); ?></div>
											<?php } ?>

											<?php if ( ! empty( $slides['group_slides_b_btn'] ) ) { ?>
												<div class="slides-item-btn"><a href="<?php echo $slides['group_slides_b_btn_url']; ?>" rel="bookmark" <?php echo goal(); ?> class="slides-button"><?php echo $slides['group_slides_b_btn']; ?></a></div>
											<?php } ?>
										</div>
									</div>
									<?php if ( ! empty( $slides['group_slides_b_img'] ) ) { ?>
										<div class="slides-img">
											<div class="slides-item-img"><img src="<?php echo $slides['group_slides_b_img']; ?>" alt="<?php echo $slides['group_slides_b_title']; ?>"></div>
										</div>
									<?php } ?>
								</div>
								<?php break; ?>
							<?php }
						}
					?>
				</div>

				<?php be_help( $text = '公司主页 → 幻灯图文' ); ?>
			</div>
		</div>
	<?php } ?>
<?php } ?>