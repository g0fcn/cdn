<?php
header('content-type:application/json');
$arr = array (
		
		// 索引值 0  微信小程序友情链接	
		[
		      array(
				  'title'=>'APP工具',
				  'appid'=>'wx371fbdd9282d9707',
				  'url'=>'pages/index/index',
				  'img'=>'https://xkj.93665.xin/wp-content/uploads/2024/01/1705575315-pFFiBnI.png',
				  'introduce'=>'好像有很多资源'
			  ),
			  array(
				  'title'=>'巨魔源',
				  'appid'=>'wxd9d4822b94fc4e16',
				  'url'=>'pages/yueyu/yueyu',
				  'img'=>'https://xkj.93665.xin/wp-content/uploads/2024/01/2024012011463895.png',
				  'introduce'=>'好像有很多资源'
			  
			  )
		],
		
		// 索引值 1  QQ小程序友情链接 最多添加10个有限制
		[
		      //array('title'=>'枫瑞博客网','appid'=>'1109768965','url'=>'pages/index/index','img'=>'https://frbkw.com/wp-center/uploads/2019/11/1573174125-f10c4d43d461631.png','introduce'=>'一位只想找富婆的傻屌')
		    
		],

);

echo json_encode($arr);

    

?>