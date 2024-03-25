<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php if ( co_get_option( 'group_slides_a' ) ) { ?>
	<?php 
		$addslides = ( array ) co_get_option( 'group_slides_a_add' );
		foreach ( $addslides as $items ) {
	?>

		<div class="g-row g-line group-slider-text<?php if ( ! empty( $items['line_white'] ) ) { ?> group-white<?php } ?>" <?php aos(); ?>>
			<div class="g-col">
				<div class="owl-carousel slides-text<?php if ( ! empty( $items['group_slides_a_r'] ) ) { ?> slider-r<?php } ?>">
					<?php 
						if ( ! empty( $items['group_slides_a_item'] ) ) {
							foreach ( $items['group_slides_a_item'] as $slides ) { ?>

								<div class="slides-item slides-center">
									<div class="slides-content">
										<div class="slides-item-text<?php if ( ! empty( $slides['group_slides_a_ret'] ) ) { ?> ret<?php } ?>">
											<div class="slides-item-title"><?php echo $slides['group_slides_a_title']; ?></div>
											<?php if ( ! empty( $slides['group_slides_a_des'] ) ) { ?>
												<div class="slides-item-des"><?php echo wpautop( $slides['group_slides_a_des'] ); ?></div>
											<?php } ?>

											<?php if ( ! empty( $slides['group_slides_a_btn'] ) ) { ?>
												<div class="slides-item-btn"><a href="<?php echo $slides['group_slides_a_btn_url']; ?>" rel="bookmark" <?php echo goal(); ?> class="slides-button"><?php echo $slides['group_slides_a_btn']; ?></a></div>
											<?php } ?>
										</div>
									</div>

									<?php if ( ! empty( $slides['group_slides_a_img'] ) ) { ?>
										<div class="slides-img">
											<div class="slides-item-img"><img src="<?php echo $slides['group_slides_a_img']; ?>" alt="<?php echo $slides['group_slides_a_title']; ?>"></div>
										</div>
									<?php } ?>
								</div>
							<?php }
						}
					?>
				</div>

				<div class="lazy-img ajax-owl-loading">
					<?php 
						if ( ! empty( $items['group_slides_a_item'] ) ) {
							foreach ( $items['group_slides_a_item'] as $slides ) { ?>
								<div class="slides-item slides-center">
									<div class="slides-content">
										<div class="slides-item-text<?php if ( ! empty( $slides['group_slides_a_ret'] ) ) { ?> ret<?php } ?>">
											<div class="slides-item-title"><?php echo $slides['group_slides_a_title']; ?></div>
											<?php if ( ! empty( $slides['group_slides_a_des'] ) ) { ?>
												<div class="slides-item-des"><?php echo wpautop( $slides['group_slides_a_des'] ); ?></div>
											<?php } ?>

											<?php if ( ! empty( $slides['group_slides_a_btn'] ) ) { ?>
												<div class="slides-item-btn"><a href="<?php echo $slides['group_slides_a_btn_url']; ?>" rel="bookmark" <?php echo goal(); ?> class="slides-button"><?php echo $slides['group_slides_a_btn']; ?></a></div>
											<?php } ?>
										</div>
									</div>
									<?php if ( ! empty( $slides['group_slides_a_img'] ) ) { ?>
										<div class="slides-img">
											<div class="slides-item-img"><img src="<?php echo $slides['group_slides_a_img']; ?>" alt="<?php echo $slides['group_slides_a_title']; ?>"></div>
										</div>
									<?php } ?>
								</div>
								<?php break; ?>
							<?php }
						}
					?>
				</div>

				<?php be_help( $text = '公司主页 → 图文幻灯' ); ?>
			</div>
		</div>
	<?php } ?>
<?php } ?>