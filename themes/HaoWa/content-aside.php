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
  <div class="container">
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
              <div class="site-info list">
                <h1>
                  <?php the_title(); ?>
                </h1>
                <ul>
                  <li>发布时间：
                    <?php the_time('Y-n-j') ?>
                    <h3> 关注微信公众号:<img src="https://s1.ax1x.com/2020/03/24/8ON1QU.jpg" height="10" width="10" />心科技，更多好东西等你探寻</h3>
                  </li>
                </ul>
              </div>
              <div class="site-content">
                <div class="post-ad">
                  <div class="site-entry">
                    <?php the_content(); ?>
                    <div class="bdsharebuttonbox"><a href="#" class="bds_more" data-cmd="more"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_mshare" data-cmd="mshare" title="分享到一键分享"></a><a href="#" class="bds_douban" data-cmd="douban" title="分享到豆瓣网"></a><a href="#" class="bds_tieba" data-cmd="tieba" title="分享到百度贴吧"></a><a href="#" class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a><a href="#" class="bds_huaban" data-cmd="huaban" title="分享到花瓣"></a><a href="#" class="bds_copy" data-cmd="copy" title="分享到复制网址"></a></div>
                    <script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script> 
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
          <div class="items ">
            <div class="row">
              <?php

		$prev_post = get_previous_post(true);//与当前文章同分类的上一篇文章
		$next_post = get_next_post(true);//与当前文章同分类的下一篇文章
	?>
              <?php if (!empty( $prev_post )): ?>
              <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="item ricle"> <a href="<?php echo get_permalink( $prev_post->ID ); ?>" target="_blank">
                  <h3><?php echo $prev_post->post_title; ?></h3>
                  <p>
                    <?php if(has_excerpt()) the_excerpt(); else echo mb_strimwidth(strip_tags($prev_post->post_content),0,59,'...'); ?>
                  </p>
                  </a> </div>
              </div>
              <?php endif; ?>
              <?php if (!empty( $next_post )): ?>
              <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="item ricle"> <a href="<?php echo get_permalink( $next_post->ID ); ?>" target="_blank">
                  <h3><?php echo $next_post->post_title; ?></h3>
                  <p>
                    <?php if(has_excerpt()) the_excerpt(); else echo mb_strimwidth(strip_tags($next_post->post_content),0,59,'...'); ?>
                  </p>
                  </a> </div>
              </div>
              <?php endif; ?>
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
