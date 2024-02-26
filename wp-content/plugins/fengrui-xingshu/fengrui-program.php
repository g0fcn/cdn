<?php
header('content-type:application/json');
$arr = array (
		
		// 索引值 0  微信小程序友情链接	
		[
		      array(
				  'appid'=>'wxd9d4822b94fc4e16',//小程序id
				  'url'=>'pages/yueyu/yueyu',//跳转小程序的页面路径
				  'img'=>'https://xkj.93665.xin/wp-content/uploads/2024/01/2024012011463895.png'//图片地址
			  )
		]

);

echo json_encode($arr);

    

?>