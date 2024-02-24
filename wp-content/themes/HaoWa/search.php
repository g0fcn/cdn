<?php get_header();?>
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
       <div class="part" id="hot" data-title="搜索结果">
          <h2 class="has_link "><strong>搜索结果</strong><a >以下为搜索结果</a></h2>
          <div class="items ">
            <div class="row">
                 <?php if(have_posts()): ?>
      <?php while(have_posts()):the_post();  ?>
              <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="item"> <a href="<?php if( cs_get_option( 'dawa_post_u' ) ) { ?><?php the_permalink(); ?><?php }else{ ?><?php echo get_post_meta($post->ID,"url_value",true);?><?php }?>" rel="nofollow"  target="_blank"  title="<?php the_title(); ?>"> <img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo post_thumbnail_src(); ?>&h=52&w=52&zc=1" alt="<?php the_title(); ?>">
                  <h3><?php the_title_attribute(); ?></h3>
                  <p><?php if(has_excerpt()) the_excerpt(); else echo mb_strimwidth(strip_tags($post->post_content),0,59,'...'); ?></p>
                  </a> </div>
              </div>
              
 <?php endwhile ?>
      <?php endif ?>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>