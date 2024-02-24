<link rel="stylesheet" href="<?php bloginfo('template_url');?>/style.css" />
<link rel="stylesheet" href="<?php bloginfo('template_url');?>/list.css" />
<style>
#sidebar {
	float: none !important;
	width:auto !important;
	margin-left: 13px !important
}
</style>
<?php if(have_posts()): ?>
<?php while(have_posts()):the_post();  ?>
<div class="content">
  <div class="container" >
    <div class="row">
      <div class="col-md-9">
        <div class="part current" id="photo" data-title="<?php foreach((get_the_category()) as $category){echo $category->cat_name;}?>">
          <div class="breadcrumb">
            <?php $category = get_the_category(); if($category[0]){ echo '<a href="'.get_category_link($category[0]->term_id ).'">返回列表</a>'; } ?>
          </div>
          <h2 class=""><strong>
            <?php foreach((get_the_category()) as $category){echo $category->cat_name;}?>
            </strong></h2>
          <div class="items ">
            <div class="row">
              <main class="main">
              <article class="site-post">
              <div class="site-logo"> <img alt="<?php the_title(); ?>" src="<?php echo post_thumbnail_src(); ?>"> </div>
              <div class="site-info">
                <h1>
                  <?php the_title(); ?>
                </h1>
                <ul>
                  <li>更新日期：
                    <?php the_time('Y-n-j') ?>
                  </li>
                  <li>查看次数：
                    <?php get_post_views($post -> ID); ?>
                
                 <li> <?php the_tags('<li>站点标签：','','</li>'); ?>
      <h3> 关注微信公众号:<img src="https://s1.ax1x.com/2020/03/24/8ON1QU.jpg" height="10" width="10" />心科技，更多好东西等你探寻</h3>
                </ul>
              </div>
              
              <div class="site-go"> <a rel="nofollow" href="<?php echo get_post_meta($post->ID,"url_value",true);?>" target="_blank">立即前往</a> </div>
              <div class="site-content">
                <div class="post-ad">
                  <div class="site-entry">
                    <h4>详细介绍</h4>
               
                    <?php the_content(); ?>
                  </div>
                </div>
                </article>
                </main>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
          <?php endif; ?>
          
          
          
          <div class="part" id="search" data-title="相关推荐">
        <?php if( cs_get_option( 'i_works_tougao' ) ) { ?>
        <div class="breadcrumb"><a href="<?php echo cs_get_option( 'i_works_tougao' ); ?>"> 我要投稿</a> </div>
        <?php }?>
        <h2 class=""><strong>相关推荐</strong></h2>
        <div class="items ">
          <div class="row">
            <?php
$post_num = 4;
$exclude_id = $post->ID; 
$posttags = get_the_tags(); $i = 0;
$relatedhigh = stripslashes(get_option('strive_relatedhigh'));
if ( $posttags ) {
	$tags = ''; foreach ( $posttags as $tag ) $tags .= $tag->term_id . ','; 
	$args = array(
		'post_status' => 'publish',
		'tag__in' => explode(',', $tags), 
		'post__not_in' => explode(',', $exclude_id), 
		'caller_get_posts' => 1,
		'orderby' => 'comment_date', 
		'posts_per_page' => $post_num
	);
	query_posts($args);
	while( have_posts() ) { the_post(); ?>
            <div class="col-xs-6 col-sm-4 col-md-3">
              <div class="item">
                <?php if( cs_get_option( 'dawa_post_u' ) ) { ?>
                <a href="<?php the_permalink(); ?>"target="_blank" title="<?php the_title(); ?>">
                <?php }else{ ?>
                <a href="<?php echo get_post_meta($post->ID,"url_value",true);?>" rel="nofollow" target="_blank" title="<?php the_title(); ?>">
                <?php }?>
                <img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo post_thumbnail_src(); ?>&h=52&w=52&zc=1" alt="<?php the_title_attribute(); ?>">
                <h3>
                  <?php the_title_attribute(); ?>
                </h3>
                <p>
                  <?php if(has_excerpt()) the_excerpt(); else echo mb_strimwidth(strip_tags($post->post_content),0,59,'...'); ?>
                </p>
                </a> </div>
            </div>
            <?php
		$exclude_id .= ',' . $post->ID; $i ++;
	} wp_reset_query();
}
if ( $i < $post_num ) { 
	$cats = ''; foreach ( get_the_category() as $cat ) $cats .= $cat->cat_ID . ',';
	$args = array(
		'category__in' => explode(',', $cats), 
		'post__not_in' => explode(',', $exclude_id),
		'caller_get_posts' => 1,
		'orderby' => 'comment_date',
		'posts_per_page' => $post_num - $i
	);
	query_posts($args);
	while( have_posts() ) { the_post(); ?>
            <div class="col-xs-6 col-sm-4 col-md-3">
              <div class="item">
                <?php if( cs_get_option( 'dawa_post_u' ) ) { ?>
                <a href="<?php the_permalink(); ?>"target="_blank" title="<?php the_title(); ?>">
                <?php }else{ ?>
                <a href="<?php echo get_post_meta($post->ID,"url_value",true);?>" rel="nofollow" target="_blank" title="<?php the_title(); ?>">
                <?php }?>
                <img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo post_thumbnail_src(); ?>&h=52&w=52&zc=1" alt="<?php the_title_attribute(); ?>">
                <h3>
                  <?php the_title_attribute(); ?>
                </h3>
                <p>
                  <?php if(has_excerpt()) the_excerpt(); else echo mb_strimwidth(strip_tags($post->post_content),0,59,'...'); ?>
                </p>
                </a> </div>
            </div>
            <?php $i++;
	} wp_reset_query();
}
if ( $i  == 0 )  echo '<div class=\"r_title\">没有相关内容!</div>';?>
          </div>
        </div>
      </div>
       </div>
      
        
        <div class="col-md-3">
          <?php get_sidebar();?>
        </div>
      </div>
      
    </div>
  </div>
</div>
</div>
