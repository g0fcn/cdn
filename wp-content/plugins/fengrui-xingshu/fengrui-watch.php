<?php
header('content-type:application/json');
$arr = array (
		// 索引值 0 焦点文章 首页最多显示3个 其他的需要点击查看更多列表
		[
		    array(
				'title'=>'我们都受过伤，却有了更好的人生',
				'describe'=>'这个是一个不与人见面，也能活得很好的年代，反而有点想与人相遇',
				'hurl'=>'https://mp.weixi#n.qq.com/s/vlSW13zQKU2qx_heN1bVlQ',
				'himg'=>'https://cdn.frbkw.com/wp-center/uploads/2020/01/1578381069-2700deb7f6ed633.jpg'
			),
		   
		    array(
				'title'=>'毕业一年半的我在一家互联网公司渡过了三次疲倦期',
				'describe'=>'你要储蓄你的可爱，眷顾你的善良，变得勇敢，当这个',
				'hurl'=>'https://mp.weixi#n.qq.com/s/4_I-rlb8EYddvxpydBNVGg',
				'himg'=>'https://cdn.frbkw.com//wp-center/uploads/2019/11/1574145236-4446896d67ce3ae.jpg'
			),
		    
		],
		

);

echo json_encode($arr);

    

?>