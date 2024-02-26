<?php 
function epd_download_page($msg, $pid=0){
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0<?php if ( zm_get_option( 'mobile_viewport' ) ) { ?>, maximum-scale=1.0, maximum-scale=0.0, user-scalable=no<?php } ?>">
<meta http-equiv="Cache-Control" content="no-transform" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
 <title>下载-<?php echo get_the_title( $pid ); ?><?php connector(); ?><?php bloginfo('name'); ?></title>
<meta name="keywords" content="<?php echo get_the_title($pid); ?>" />
<meta name="description" content="<?php echo get_the_title($pid); ?>-文件下载" />
<link rel="shortcut icon" href="<?php echo zm_get_option( 'favicon' ); ?>">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo zm_get_option( 'apple_icon' ); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/down.css" />
<link rel="stylesheet" href="<?php echo constant("erphpdown");?>static/erphpdown.css" type="text/css" />
<link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
<script src="<?php echo constant("erphpdown"); ?>static/erphpdown.js"></script>
<?php echo zm_get_option( 'ad_t' ); ?>
<?php echo zm_get_option( 'tongji_h' ); ?>
</head>
<body <?php body_class(); ?> ontouchstart="">
<?php wp_body_open(); ?>
<div id="page" class="hfeed site">
	<?php get_template_part( 'template/menu', 'index' ); ?>
	<nav class="bread">
		<div class="be-bread">
			<?php 
				$categories = get_the_category( $pid );
				echo '<span class="seat"></span><a class="crumbs" href="';
				echo home_url('/');
				echo '">';
				echo sprintf( __( '首页', 'begin' ) );
				echo "</a>";
				echo '<i class="be be-arrowright"></i>';
				if ( $categories ) {
					$category_link = get_category_link( $categories[0]->term_id );
					echo '<a href="' . $category_link . '" target="_blank">' . $categories[0]->name . '</a>';
				}
				echo '<i class="be be-arrowright"></i>';
				echo  '<a href="' . get_permalink( $pid ) . '" target="_blank">' . get_the_title( $pid ). '</a>';
				echo '<i class="be be-arrowright"></i>';
				echo sprintf( __( '下载', 'begin' ) );
				echo '</div>'
			 ?>
		</div>
	</nav>

	<div id="content" class="site-content">
		<div class="down-post">
			<div class="down-main erphpdown-main">
				<div class="down-header">
					<img src="<?php echo zm_get_option( 'down_header_img' ); ?>" alt="<?php echo get_the_title( $pid ); ?>" />
					<h1><a href="<?php echo get_permalink( $pid ); ?>" target="_blank"><?php echo get_the_title( $pid ); ?></a></h1>
					<div class="clear"></div>
				</div>
				<div id="erphpdown-download">
					<div class="msg">
						<?php echo $msg; ?>
					</div>
					<?php do_action('erphpdown_download_ad'); ?>
				</div>
			</div>

			<?php if ( zm_get_option('ad_down') == '' ) { ?>
			<?php } else { ?>
				<div class="down-tg">
					<?php echo stripslashes( zm_get_option( 'ad_down' ) ); ?>
					<div class="clear"></div>
				</div>
			<?php } ?>

			<div class="down-copyright">
				<strong>声明：</strong>
				<p><?php echo stripslashes( zm_get_option('down_explain') ); ?></p>
			</div>
		</div>
	</div>
<?php get_footer(); ?>
<?php 
    exit;
}

function epd_wait_page($pid=0){
    $erphp_url_front_vip = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-update-vip.php';
    if(get_option('erphp_url_front_vip')){
        $erphp_url_front_vip = get_option('erphp_url_front_vip');
    }
?>
    <html lang="zh-CN">
        <head>
            <meta charset="UTF-8" />
            <link rel="stylesheet" href="<?php echo constant("erphpdown");?>static/erphpdown.css" type="text/css" />
            <link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
            <title><?php _e("文件下载等待",'erphpdown')?> - <?php echo get_the_title($pid);?> - <?php bloginfo('name');?></title>
            <style>
            .loading{
                width: 80px;
                height: 40px;
                margin: 0 auto;
                margin-top:20px;
                margin-bottom: 40px;
            }
            .loading span{
                display: inline-block;
                width: 8px;
                height: 100%;
                border-radius: 4px;
                background: lightgreen;
                -webkit-animation: load 1s ease infinite;
            }
            @-webkit-keyframes load{
                0%,100%{
                    height: 40px;
                    background: lightgreen;
                }
                50%{
                    height: 70px;
                    margin: -15px 0;
                    background: lightblue;
                }
            }
            .loading span:nth-child(2){
                -webkit-animation-delay:0.2s;
            }
            .loading span:nth-child(3){
                -webkit-animation-delay:0.4s;
            }
            .loading span:nth-child(4){
                -webkit-animation-delay:0.6s;
            }
            .loading span:nth-child(5){
                -webkit-animation-delay:0.8s;
            }
            </style>
        </head>
        <body class="erphpdown-body">
            <div id="erphpdown-download">
                <div class="loading">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                </div>
                <div class="msg">
                    <p style="font-size: 15px;"><?php _e("下载即将开始，剩余等待时间...",'erphpdown')?><span id="time" style="color:#ff5f33"><?php echo get_option('erphp_free_wait');?></span>秒</p>
                    <a href="<?php echo $erphp_url_front_vip;?>" target="_blank" class="erphpdown-btn" style="color:green;margin-top:25px;background: lightgreen;"><?php _e("升级VIP，下载不用等待",'erphpdown')?></a>
                </div>
                <?php do_action('erphpdown_download_ad');?>
            </div>
            <script>
                var s = <?php echo get_option('erphp_free_wait');?>;  
                var Timer = document.getElementById("time");
                wppayCountdown();
                erphpTimer = setInterval(function(){ wppayCountdown() },1000);
                function wppayCountdown (){
                    Timer.innerHTML = s;
                    if( s == 0 ){
                        clearInterval(erphpTimer);
                        location.href=window.location.href+'&timekey=<?php echo md5($pid.get_option('erphpdown_downkey'));?>';
                    }else {
                        s--;
                    }
                }
            </script>
        </body>
    </html>
<?php 
    exit;  
}
