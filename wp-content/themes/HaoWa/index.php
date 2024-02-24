<?php get_header(); ?>
<div class="content">
  <div class="container">
    <div class="row">
      <div class="col-md-1 sidebar">
        <div class="content-sidebar ">
          <dl id="goto">
          </dl>
        </div>
      </div>
      <div class="col-md-11">
               <?php if( cs_get_option( 'dawa_hotcat_u' ) ) { ?>
                <?php $cid = cs_get_option( 'select_1' ); $img_id = 1; include( 'parts/index-new.php' ); ?>
                <?php }?>
        <!--Start-->
        <?php 
		$nums = cs_get_option( 'i_works_nums' ); 
		$display_categories = explode(',', cs_get_option( 'i_works_id' ) ); 
		foreach ($display_categories as $category) { 
			query_posts( array(
				'showposts' => 1,
				'cat' => $category,
				'post_not_in' => $do_not_duplicate
				)
			);
		?>
        <?php while (have_posts()) : the_post(); ?>
        <div class="part" id="<?php $cat= single_cat_title('', false);
echo get_cat_ID($cat); ?>" data-title="<?php single_cat_title(); ?>">
          <h2 class="has_link "><strong>
            <?php single_cat_title(); ?>
            </strong><a href="<?php echo get_category_link($category);?>" target="_blank">查看更多 &gt;</a></h2>
          <?php endwhile; ?>
          <div class="items ">
            <div class="row">
              <?php
					query_posts( array(
						'showposts' => $nums,
						'cat' => $category,
						'post_not_in' => $do_not_duplicate
						)
		 			);
					?>
              <?php while (have_posts()) : the_post(); ?>
              <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="item">
        <?php if( cs_get_option( 'dawa_post_u' ) ) { ?>
         <a href="<?php the_permalink(); ?>"target="_blank" title="<?php the_title(); ?>">
		<?php }else{ ?>
		         <a href="<?php echo get_post_meta($post->ID,"url_value",true);?>" rel="nofollow" target="_blank" title="<?php the_title(); ?>">
        <?php }?>
         <img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo post_thumbnail_src(); ?>&h=52&w=52&zc=1">
                  <h3 style="color:<?php echo get_post_meta($post->ID,"tit_value",true);?>">
                    <?php the_title(); ?>
                  </h3>
                  <p><?php if(has_excerpt()) the_excerpt(); else echo mb_strimwidth(strip_tags($post->post_content),0,59,'...'); ?></p>
                  </a> 
                 <?php if( cs_get_option( 'dawa_ico_u' ) ) { ?> <a class="npcink-a" target="_blank" rel="nofollow"  href="<?php echo get_post_meta($post->ID,"url_value",true);?>"></a><?php }?>
                  </div>
              </div>
              <?php endwhile; ?>
            </div>
          </div>
        </div>
        <?php } ?>
        <!--end-->
        <div class="content-words"><?php echo cs_get_option( 'i_ad_home_txt' ); ?>       
        </div>
        <div class="content-linkto "> <?php echo cs_get_option( 'i_ad_home' ); ?></div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>