<div class="widget widget-product">
  <h2><a href="<?php echo get_category_link( $cid ); ?>" target="_blank"><?php echo cs_get_option( 'dawa_flist' ); ?></a></h2>
  <div class="cont">
    <ul>
      <?php if ( cs_get_option('i_alerts_nums') ) { $picposts = stripslashes(cs_get_option('i_alerts_nums')); } else { $picposts = 10; } ?>
      <?php query_posts( array( 'cat'=>$cid, 'ignore_sticky_posts'=>true, 'posts_per_page'=>$picposts ) ); while( have_posts() ): the_post(); ?>
      <li><a href="<?php the_permalink() ?>" target="_blank" >
        <?php the_title(); ?>
        </a></li>
      <?php endwhile; ?>
    </ul>
  </div>
</div>
