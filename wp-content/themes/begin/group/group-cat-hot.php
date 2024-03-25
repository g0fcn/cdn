<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php if ( co_get_option( 'group_cat_hot' ) ) { ?>
	<?php $display_categories = explode( ',', co_get_option( 'group_cat_hot_id' ) ); foreach ( $display_categories as $category ) {
		$cat = ( co_get_option( 'group_no_cat_child' ) ) ? 'category' : 'category__in';
	?>

		<div class="betip line-group-cat-hot g-row g-line notext<?php if ( co_get_option( 'group_cat_hot_white' ) ) { ?> group-white<?php } ?>" <?php aos(); ?>>
			<div class="g-col">
				<div class="flexbox-grid">
					<div class="group-title" <?php aos_b(); ?>>
						<a href="<?php echo get_category_link( $category ); ?>" title="<?php _e( '更多', 'begin' ); ?>" rel="bookmark" <?php echo goal(); ?>>
							<?php if ( co_get_option( 'group_cat_hot_id' ) ) { ?>
								<h3><?php echo get_cat_name( $category ); ?></h3>
							<?php } else { ?>
								<h3>未分类</h3>
								<div class="group-des">公司主页 → 热门分类，输入分类ID</div>
							<?php } ?>
						</a>
						<?php if ( category_description( $category ) ) { ?>
							<div class="group-des"><?php echo category_description( $category ); ?></div>
						<?php } ?>
						<div class="clear"></div>
					</div>

					<div class="su-model-main">
						<div class="su-model-area">
							<div class="su-cat-model-img">
								<?php 
									$args = array(
										'post_type'      => 'post',
										'posts_per_page' => 2,
										'post_status'    => 'publish',
										$cat             => $category
									);
									$query = new WP_Query( $args );
								?>
								<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
									<div id="post-<?php the_ID(); ?>" class="su-model-item-img" <?php aos_a(); ?>>
										<figure class="thumbnail">
											<?php echo zm_thumbnail(); ?>
										</figure>
										<?php the_title( sprintf( '<h2 class="su-img-title"><a href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
										<p class="su-model-item-words over">
											<?php if ( has_excerpt('') ) {
													echo wp_trim_words( get_the_excerpt(), 28, '...' );
												} else {
													$content = get_the_content();
													$content = wp_strip_all_tags( str_replace( array('[',']' ),array('<','>' ), $content ) );
													echo wp_trim_words( $content, 30, '...' );
										        }
											?>
										</p>
									</div>

								<?php endwhile; endif; ?>
								<?php wp_reset_postdata(); ?>
							</div>

							<ul class="su-cat-model-list lic">
								<?php 
									$args = array(
										'post_type'      => 'post',
										'posts_per_page' => 12,
										'offset'         => 2,
										'post_status'    => 'publish',
										$cat             => $category
									);
									$s = 0;
									$query = new WP_Query( $args );
								?>
								<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); $s++; ?>
									<li id="post-<?php the_ID(); ?>" class="su-list-title high-<?php echo mt_rand(1, $s); ?><?php if ( ! co_get_option( 'group_cat_hot_date' ) ) { ?> no-listate<?php } ?>" <?php aos_a(); ?>>
										<?php the_title( sprintf( '<a class="srm" href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a>' ); ?>
										<?php if ( co_get_option( 'group_cat_hot_date' ) ) { ?>
											<span class="listate"><time datetime="<?php the_date('Y-m-d'); ?> <?php the_time('H:i:s'); ?>"></time><?php the_time('m/d'); ?></span>
										<?php } ?>
									</li>
								<?php endwhile; endif; ?>
								<?php wp_reset_postdata(); ?>
								<div class="clear"></div>

							</ul>
						</div>

						<div class="su-cat-model-hot">
							<h3 class="su-hot-title"><?php _e( '热', 'begin' ); ?></h3>
							<?php 
								$hotday = co_get_option( 'group_hot_day' );
								$args = array(
									'post_type'      => 'post',
									'posts_per_page' => '9',
									'post_status'    => 'publish',
									'meta_key'       => 'views',
									'orderby'        => 'meta_value_num',
									'order'          => 'date',
									$cat            => $category,
									'date_query'     => array(
										array(
											'after'     => $hotday . ' days ago',
											'inclusive' => true,
										),
									),
								);
								$i = 1;
								$query = new WP_Query( $args );
							?>

							<ul>
								<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
									<li id="post-<?php the_ID(); ?>" class="su-list-hot-title srm li-one-<?php echo $i; ?>" <?php aos_a(); ?>>
										<?php the_title( sprintf( '<span class="li-icon li-icon-' . $i . '">' . $i++ . '</span><a href="%s" rel="bookmark" ' . goal() . '>', esc_url( get_permalink() ) ), '</a>' ); ?>
									</li>
								<?php endwhile; endif; ?>
								<?php wp_reset_postdata(); ?>
								<div class="clear"></div>
							</ul>
						</div>
					</div>
					<div class="group-cat-img-more"><a href="<?php echo get_category_link( $category ); ?>" title="<?php _e( '更多', 'begin' ); ?>" rel="bookmark" <?php echo goal(); ?>><i class="be be-more"></i></a></div>
				</div>
			</div>
			<?php be_help( $text = '公司主页 → 热门分类' ); ?>
		</div>
	<?php } ?>
<?php } ?>