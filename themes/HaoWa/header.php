<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title><?php echo _title(); ?></title>
<?php wp_head(); ?>
<?php if( cs_get_option( 'i_favicon_icon' ) ) { ?>
<link rel="shortcut icon" href="<?php echo cs_get_option( 'i_favicon_icon', '' ); ?>" type="image/x-icon" >
<?php }else{ ?>
<link rel="Shortcut Icon" href="<?php bloginfo('template_url');?>/images/favicon.ico" type="image/x-icon" />
<?php }?>
<link rel='stylesheet' href='<?php bloginfo('template_url');?>/css/fontello.css' />
<link rel="stylesheet" href="<?php bloginfo('template_url');?>/styles.css" />
<link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<?php if( cs_get_option( 'i_bg_image' ) ) { ?>
<style>
.header {
    background: #3295d9 url(<?php echo cs_get_option( 'i_bg_image', '' ); ?>);
    background-size: 100%;
}
</style>
<?php }?>
<body class="body-home body-website">
<div class="header ">
  <div class="container">
    <h1 class="logo"> <strong> <a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"> <img src="<?php echo cs_get_option( 'i_logo_image', '' ); ?>" alt="<?php bloginfo('name'); ?>"> </a> </strong>
    
    <span class="arrow"><i class="icon-play-circled"></i><em></em></span>
     <?php if(function_exists('wp_nav_menu')) { wp_nav_menu(array(
						'theme_location'=>'top-menu',
						'container' => false, 
						'walker' => new wp_bootstrap_navwalker(),
						'depth' => 1,
						'items_wrap' => ' <ul class="subnav">%3$s</ul>'
					)); } ?>
     </h1>
    <div class="primary-menus">
      <ul class="selects">
        <li> 常用 </li>
        <li data-target="uiii-search"> <span>站内搜索</span> </li>
        <li class="current" data-target="baidu-search"> <span>百度</span> </li>
    
      </ul>
      <div class="cont">
              <?php if( cs_get_option( 'dawa_header_u' ) ) { ?>
        <div class="right-link"> 
       <?php echo cs_get_option( 'dawa_header_ad' ); ?>
          </div>
           <?php }?>
        <div class="left-cont">
           <form class="search" id="baidu-search" action="https://www.baidu.com/s" method="get" target="_blank">
            <input type="text" name="wd" class="s" value="<?php bloginfo('name'); ?>">
            <button type="submit" name="submit" class="btn">百度一下</button>
          </form>
		  
		  <form class="search hidden " id="uiii-search" action="<?php bloginfo('url');?>" method="get" target="_blank">
            <input type="text" name="s" class="s" value="站内搜索">
            <button type="submit" class="btn">站内搜索</button>
          </form>				
        </div>
      </div>
    </div>
  </div>
</div>
<div class="header-bar">
  <div class="container">
    <div class="bar-left">
    	 <div class="menu"><ul>
<li><a></a></li>
<li><a></a></li>
</ul></div>
      </ul>
    </div>
	<ul class="">
         <?php if(function_exists('wp_nav_menu')) { wp_nav_menu(array(
						'theme_location'=>'header-menu',
						'container' => false, 
						'walker' => new wp_bootstrap_navwalker(),
						'depth' => 2,
						'items_wrap' => '<ul class="site-nav">%3$s</ul>'
					)); } ?>
      </ul>
    </div>
	
     <?php if( cs_get_option( 'dawa_hoc_u' ) ) { ?><div class="bar-right "> <span>特别推荐</span> <?php echo cs_get_option( 'dawa_hoc' ); ?></div><?php }?>
  </div>
</div> 
<div style="text-align:center;">

		</div>
 <div class="header-recommend">
  <div class="container">
    <?php if( cs_get_option( 'dawa_follow_u' ) ) { ?> <div class="focus "> <span>大家正在关注：</span><?php echo cs_get_option( 'dawa_follow_ad' ); ?></div><?php }?>
    <?php if( cs_get_option( 'dawa_hot_u' ) ) { ?> <div class="link "><?php echo cs_get_option( 'dawa_hot_ad' ); ?> </div><?php }?>
  </div>
</div>
<div align="center">
</div>