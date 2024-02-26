<?php
header('content-type:application/json');
$arr = array (
		// 索引值 0 首页金刚区域 ，其中类型type分为以下情况
        // 1.xs_default默认值跳二级
        // 2.xs_tab跳转底部导航
        // 3.xs_program跳转小程序
        // 4.xs_business跳转业务域名请在route填写网站地址
        // 5.xs_wxvideo_activity 打开视频号视频
        // 6.xs_wxvideo_reserve_live 打开预约视频号直播
        // 7.xs_wxvideo_live 打开视频号直播
        // 8.xs_bilibli跳转b站小程序，appid参数是AV号，但b站目前只有显式的BV号。获取视频的号，在PC端的播放页面打开F12输入aid即可获得
		[
		    array(
				'title'=>'搜索',
				'type'=>'xs_tap',
				'appid'=>'',
				'route'=>'/pages/search/search',
				'himg'=>'../../static/index/dstrict-2.svg'
			),
		    array(
				'title'=>'友情',
				'type'=>'xs_default',
				'appid'=>'',
				'route'=>'../friend/friend',
				'himg'=>'../../static/index/dstrict-1.svg'
			),
			array(
				'title'=>'视频号',
				'type'=>'xs_wxvideo_activity',
				'appid'=>'sphZH1MCAzuup7O',//视频号 id，以“sph”开头的id，可在视频号助手获取
				'route'=>'export/mnkjln',//视频 feedId 就是视频链接
				'himg'=>'../../static/data/ad_video.svg'
			),
				array(
				'title'=>'B站',
				'type'=>'xs_bilibli',
				'appid'=>'294890',
				'route'=>'pages/index/index',
				'himg'=>'../../static/data/renking-8.svg'
			)
// 			array(
// 				'title'=>'商店',
// 				'type'=>'xs_program',
// 				'appid'=>'wx0f2bd3c9f8970c64',
// 				'route'=>'pages/index/index',
// 				'himg'=>'../../static/data/renking-8.svg'
// 			)
		    
		],
		

);

echo json_encode($arr);

    

?>