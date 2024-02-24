<?php if( cs_get_option( 'dawa_footer_u' ) ) { ?>
<div class="footer-widgets">
  <div class="container">
    <div class="row">
      <div class="col-md-1"></div>
      <div class="col-md-11">
        <div class="row footer-widgets-items">
          <div class="col-sm-6 col-md-3">
            <div class="widget widget-sjs"> <?php echo cs_get_option( 'dawa_footer_ad' ); ?> </div>
          </div>
         

          <div class="col-sm-6 col-md-3">
            <?php if( cs_get_option( 'dawa_flist' ) ) { ?>
            <?php $cid = cs_get_option( 'alerts_1' ); $img_id = 1; include( 'f-list.php' ); ?>
            <?php }else{ ?>
            <?php echo cs_get_option( 'dawa_foot_list' ); ?>
            <?php }?>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="widget widget-wechat">
              <h2>官方公众号</h2>
              <div class="cont">
                <div class="wechat-img"><img src="<?php echo cs_get_option( 'dawa_weixin' ); ?>" alt="wechat"></div>
                <p>扫描二维码：<span><?php echo cs_get_option( 'dawa_wxnb' ); ?></span></p>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="widget widget-site ">
              <h2>已收录人气网站</h2>
              <div class="cont">
                <div class="count"><strong>
                  <?php $count_posts = wp_count_posts(); echo $published_posts = $count_posts->publish;?>
                  </strong>个 </div>
                <p><i class="icon-time"></i>苦心ios导航更新日期
                  <?php the_modified_time('Y年n月j日'); ?>
                </p>
                <h4>还有更赞的 <a href="tencent://message/?uin=<?php echo cs_get_option( 'dawa_qq' ); ?>" target="_blank"><i class="icon-pen-01"></i> 我来推荐</a> </h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php }?>
<div class="footer-copyright ">
  <div class="container">
    <p><?php echo cs_get_option( 'dawa_foot_tx' ); ?></p>
    <div class="footerlink">
      <ul>
        <?php wp_list_bookmarks('title_li=&categorize=0&show_images=0'); ?>
      </ul>
    </div>
    <style>
	.footerlink ul li{display:inline-block; margin-right:10px;margin-bottom: 8px;}
	.footerlink a{ color:#dff2ff}
	</style>
    <p>Copyright <?php echo comicpress_copyright();?> <a href="<?php bloginfo('siteurl');?>/"><strong>
      <?php bloginfo('name');?>
      </strong></a> <a href="https://beian.miit.gov.cn" target="_blank"><?php echo cs_get_option( 'dawa_beian' ); ?></a></p><?php $tongji = cs_get_option( 'i_js_tongji' ); ?>
<?php if( ! empty( $tongji ) ){ echo '<script>'.$tongji.'</script>';}else{				echo' ';			} ?>
  </div>
</div>
<div class="fixed-bar">
  <?php if( cs_get_option( 'dawa_weibo' ) ) { ?>
  <a class="fixed-weibo" href="<?php echo cs_get_option( 'dawa_weibo' ); ?>" target="_blank"><span><i class="icon-sina"></i></span></a>
  <?php }?>
  <?php if( cs_get_option( 'dawa_wxad' ) ) { ?>
  <a class="fixed-wechat" href="#"><span><i class="icon-wechat"></i></span>
  <div class="wechat_div">
    <div class="thumb"> <img src="<?php echo cs_get_option( 'dawa_weixin2' ); ?>" alt="wechat"> </div>
    <div class="wechat_info">
      <h3><?php echo cs_get_option( 'dawa_wxname' ); ?></h3>
      <h4><?php echo cs_get_option( 'dawa_wxad' ); ?></h4>
      <p>微信号：<?php echo cs_get_option( 'dawa_wxnb' ); ?></p>
    </div>
  </div>
  </a>
  <?php }?>
  <div id="back-top"><a href="#" alt="返回顶部"><span><i class="icon-up"></i></span></a></div>
</div>

<!-- 引入插件js -->
<?php wp_footer(); ?>
<script src="<?php bloginfo('template_url');?>/js/hao.js" charset="utf-8"></script>
</body></html>