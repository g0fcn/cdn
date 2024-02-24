<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK SETTINGS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$settings           = array(
  'menu_title' => '主题选项',
  'menu_type'       => 'menu', // menu, submenu, options, theme, etc.
  'menu_slug'       => 'cs-framework',
  'ajax_save'       => false,
  'show_reset_all'  => false,
  'framework_title' => 'Nice <small>by Wazhuti.com</small>',
);

// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options        = array();

// ----------------------------------------
// a option section for options overview  -
// ----------------------------------------
$options[]      = array(
  'name'        => 'overwiew',
  'title'       => '常规选项',
  'icon'        => 'fa fa-star',
  'fields'      => array(
 
	 
 		// Favicon和Logo设置
		 array(
		  'type'    => 'notice',
		  'class'   => 'info',
		  'content' => 'Favicon和Logo设置',
		),  
  
		// 自定义收藏站标		
        array(
          'id'      => 'i_favicon_icon',
          'type'    => 'upload',
          'title'   => 'Favicon图标',
		  'add_title' => '添加favicon',
          'default' => get_template_directory_uri()."/images/favicon.ico",
          'help'      => '建议制作一张400x400的png图像, 然后等比缩小到你想转换的ico尺寸,最后通过网上的工具转换成ico图标格式.',
        ),			
		
		
		// Logo设置
        array(
          'id'      => 'i_logo_image',
          'type'    => 'upload',
          'title'   => 'Logo设置',
		  'add_title' => '添加Logo',
          'help'      => '262 x 35 pixels',
          'default' => get_template_directory_uri()."/images/logo.png",
		 'info'      => '其它位置请手动替换主题文件夹images/logo.png文件',

        ),	
		
		
		/*    array(
      'id'      => 'color_picker_1',
      'type'    => 'color_picker',
      'title'   => '整体颜色选择',
      'default' => '#24b727',
    ),*/
	
	
	 array(
      'id'      => 'checkbox_1',
      'type'    => 'checkbox',
      'title'   => '深空灰',
      'label'   => '是否开启灰度模式',
    ),
	
	 
	
		 	// 首页开启公告栏
		array(
          'id'    	  => 'dawa_post_u',
          'type'      => 'switcher',
          'title'     => '开启详情页',
		  'help'      => '注意：只显示在主页',
        ),  
		
		
			// 首页开启图标
		array(
          'id'    	  => 'dawa_ico_u',
          'type'      => 'switcher',
          'title'     => '开启链接图标',
		  'help'      => '注意：只显示在主页',
        ),  
		
		
			// Logo设置
        array(
          'id'      => 'i_bg_image',
          'type'    => 'upload',
          'title'   => '顶部背景图片',
		  'add_title' => '添加图片',

        ),	
		
		
		
		
		  array(
      'id'      => 'i_works_tougao',
      'type'  => 'text',
      'title'   => '我要投稿链接',
	   'default' => '#',

    ),
	
	/*	// 网站标题渐变动画
        array(
          'id'      => 'i_logo_color',
          'type'    => 'switcher',
          'title'   => '网站标题渐变动画',
          'dependency' => array( 'i_symbol_i_text', '==', 'true' ),
        ),		
		
		// 自定义皮肤
        array(
          'id'        => 'i_skin',
          'type'      => 'select',
          'title'     => '自定义皮肤',
          'options'   => array(
          'i_skin01' => '酷黑',
          'i_skin02' => '清新',
          'i_skin03' => '复古',
          ),
          'default'   => 'i_skin01',
          'help'      => '皮肤随版本更新而增加，另可定制个人专属皮肤',
        ),	
	
		// 开启前端换肤功能
		array(
          'id'    	  => 'i_switcher',
          'type'      => 'switcher',
          'title'     => '开启前端换肤',
          'label'     => '假如此项没开启，换肤小工具会失效；一旦开启，自定义皮肤将失效',
          'help'      => '开启后默认显示第一套皮肤，关于修改默认皮肤请看使用说明',
        ),
	  array(
              'id'    => 'dawa_co',
              'type'  => 'text',
              'title' => '企业名称',
            ),
			
		*/	
 
 
 		// Favicon和Logo设置
		 array(
		  'type'    => 'notice',
		  'class'   => 'info',
		  'content' => '主体列表',
		),  
		
		
		  array(
      'id'      => 'i_works_id',
      'type'  => 'text',
      'title'   => '首页模块分类列表',
	   'default' => '1,2,3',
	   'info'      => '请填空分类ID，列表按分类ID顺序排列用英文","间隔',

    ),


		  array(
      'id'      => 'i_works_nums',
      'type'    => 'number',
      'title'   => '首页模块文章显示数量',
      'default' => '12',
    ),

	// Favicon和Logo设置
		 array(
		  'type'    => 'notice',
		  'class'   => 'info',
		  'content' => '热门推荐',
		),  
		
		
		 	// 首页开启公告栏
		array(
          'id'    	  => 'dawa_hotcat_u',
          'type'      => 'switcher',
          'title'     => '开启',
		  'help'      => '注意：只显示在主页',
        ),  
		
		  array(
      'id'      => 'i_hot_nums',
      'type'    => 'number',
      'title'   => '首页模块文章显示数量',
      'default' => '12',
    ),
	
	
	  array(
          'id'             => 'select_1',
          'type'           => 'select',
          'title'          => '热门推荐分类',
          'options'        => 'categories',
          'default_option' => 'Select a tag'
        ),
		
			//基本信息
		 array(
		  'type'    => 'notice',
		  'class'   => 'info',
		  'content' => '底部信息',		
		),  
		
		
		// 描述	
		array(
		  'id'      => 'dawa_foot_tx',
		  'type'    => 'textarea',
		  'title'   => '描述',
		  'default' => '本网站的网址数氢来源于互联网搜索引擎和热心网友投稿，如有冒犯请直接联系热友网友或追踪互联网搜索引擎，特此声明。',
		),		
		
  ),
);

 

// ----------------------------------------
// 导航  -
// ----------------------------------------
$options[]      = array(
  'name'        => 'dh',
  'title'       => '基础选项',
  'icon'        => 'fa fa-cube',
  'fields'      => array(
  
  
  		
		// 分页设置
		 array(
		  'type'    => 'notice',
		  'class'   => 'info',
		  'content' => '基础信息',
		), 		
		
		
		 array(
          'id'      => 'dawa_wxname',
          'type'    => 'text',
          'title'   => '微信名称',
        ),	
		
		  array(
          'id'      => 'dawa_wxnb',
          'type'    => 'text',
          'title'   => '微信号',
        ),	
		
		
		  array(
          'id'      => 'dawa_weixin',
          'type'    => 'upload',
          'title'   => '微信二维码192*192',
		  'default' => get_template_directory_uri()."/images/qrcode.jpg",

        ),	
		
		
		  array(
          'id'      => 'dawa_weixin2',
          'type'    => 'upload',
          'title'   => '微信二维码112*112',
		  'default' => get_template_directory_uri()."/images/wechat.jpg",

        ),	
		
		  array(
          'id'      => 'dawa_wxad',
          'type'    => 'text',
          'title'   => '微信推广内容',
        ),	
		
		
		 array(
              'id'    => 'dawa_qq',
              'type'  => 'text',
              'title' => 'QQ号',
            ),
			
			
		  array(
              'id'    => 'dawa_weibo',
              'type'  => 'text',
              'title' => '微博URL',
            ),
			
		
			  array(
              'id'    => 'dawa_beian',
              'type'  => 'text',
              'title' => '备案号',
            ),
			
  
   ),
); 
// ----------------------------------------
// 顶部选项  -
// ----------------------------------------
$options[]      = array(
  'name'        => 'about',
  'title'       => '顶部选项',
  'icon'        => 'fa fa-arrows-h',
  'fields'      => array(
  
  		
		// 分页设置
		 array(
		  'type'    => 'notice',
		  'class'   => 'info',
		  'content' => '设置选项',
		), 		
		
		
		
			// 首页开启公告栏
		array(
          'id'    	  => 'dawa_header_u',
          'type'      => 'switcher',
          'title'     => '开启',
		  'help'      => '注意：只显示在主页',
        ),  
		
	
		
		 array(
              'id'    => 'dawa_header_ad',
              'type'  => 'wysiwyg',
              'title' => '导航栏链接',
			  'info'      => '支持Font Awesome 字体图标',
			        'default' => '<a href="#" class="add_fav" target="_blank"><i class="icon-heart"></i>Ctrl+D收藏导航</a><a href=#" target="_blank"><i class="icon-pen-01"></i>联系我们</a><a href="#" target="_blank">&nbsp;翻译工具</a>',
 'settings' => array(
            'textarea_rows' => 1,
            'tinymce'       => false,
            'media_buttons' => false,
          )
            ),


			// 首页开启公告栏
		array(
          'id'    	  => 'dawa_hoc_u',
          'type'      => 'switcher',
          'title'     => '开启',
		  'help'      => '注意：只显示在主页',
        ),  
		
		 array(
              'id'    => 'dawa_hoc',
              'type'  => 'wysiwyg',
              'title' => '特别推荐',
			  'info'      => '支持Font Awesome 字体图标',
			        'default' => '<a href="#" target="_blank"><i><img src="https://image.uisdc.com/wp-content/uploads/2015/05/dh-pic-4.png" alt="学动效"></i>学动效</a><a href="#" target="_blank"><i><img src="https://image.uisdc.com/wp-content/uploads/2015/05/dh-pic-6.png" alt="学C4D"></i>学C4D</a><a href="#/" target="_blank"><i><img src="https://image.uisdc.com/wp-content/uploads/2018/09/nav-dkt-new2018.jpg" alt="优设大课堂"></i>优设大课堂</a>',
           'settings' => array(
            'textarea_rows' => 1,
            'tinymce'       => false,
            'media_buttons' => false,
          )
            ),
			
			// 首页开启公告栏
		array(
          'id'    	  => 'dawa_hot_u',
          'type'      => 'switcher',
          'title'     => '开启',
		  'help'      => '注意：只显示在主页',
        ),  
		
		 array(
              'id'    => 'dawa_hot_ad',
              'type'  => 'wysiwyg',
              'title' => '热门公告',
			  	'info'      => '支持Font Awesome 字体图标',
			        'default' => '<a href="#" target="_blank"><i class="icon-fire"></i> 自学PS、AI、C4D！找设计灵感！用酷炫神器！尽在UiiiUiii.com</a><a class="hide" href="#" target="_blank"><i class="icon-fire"></i> 自学设计灵感！用酷炫神器！尽在UiiiUiii.com</a>',

            'settings' => array(
            'textarea_rows' => 1,
            'tinymce'       => false,
            'media_buttons' => false,
			
          )
            ),
		 
		 
		 	// 首页开启公告栏
		array(
          'id'    	  => 'dawa_follow_u',
          'type'      => 'switcher',
          'title'     => '开启',
		  'help'      => '注意：只显示在主页',
        ),  
		
		 array(
              'id'    => 'dawa_follow_ad',
              'type'  => 'wysiwyg',
              'title' => '大家正在关注',
			  'info'      => '支持Font Awesome 字体图标',
			        'default' => '<a href="#" target="_blank">今天设计圈有啥新鲜事</a> <a href="#" target="_blank">设计师完全自学指南</a> <a target="_blank">热点风云榜</a> <a  href="#" target="_blank">高质量免费图库</a> <a class="hide" href="#" target="_blank">PS抠图神器</a> <a class="hide" href="#" target="_blank">网页配色基础</a> <a class="hide" href="#" target="_blank">纹理打包下载</a> <a class="hide" href="#" target="_blank">响应式设计原则</a>',

            'settings' => array(
            'textarea_rows' => 1,
            'tinymce'       => false,
            'media_buttons' => false,
          )
            ),
		 
  
   ),
);

 
  // ----------------------------------------
// 底部选项  -
// ----------------------------------------
$options[]      = array(
  'name'        => 'footen',
  'title'       => '底部选项',
  'icon'        => 'fa fa-calendar',
  'fields'      => array(
  
  		
		// 分页设置
		 array(
		  'type'    => 'notice',
		  'class'   => 'info',
		  'content' => '底部选项',
		), 		
		
		
		
			// 首页开启公告栏
		array(
          'id'    	  => 'dawa_footer_u',
          'type'      => 'switcher',
          'title'     => '开启',
		  'help'      => '注意：只显示在主页',
        ),  
		
	
		
		 array(
              'id'    => 'dawa_footer_ad',
              'type'  => 'wysiwyg',
              'title' => '第一栏',
			  'info'      => '支持Font Awesome 字体图标',
			        'default' => '
              <h2><a href="#" target="_blank">设计师神器</a></h2>
              <div class="cont"> 
              <a href="#" target="_blank" title="玲珑小法器myRuler，屏幕测量工具"><i class="icon-ruler"></i></a>
               <a href="#" target="_blank" title="切图标记外挂神器ASSISTOR"><i class="icon-assister"></i></a> 
               <a href="#" target="_blank" title="边玩边学！帮你轻松掌握钢笔工具的游戏酷站"><i class="icon-pen"></i></a> 
               <a href="#" title="PS 参考线插件GuideGuide下载及使用说明"><i class="icon-guide"></i></a> 
               <a href="#" target="_blank" title="解放设计师的切图神器Slicy"><i class="icon-slice"></i></a>
                <a href="#" target="_blank" title="神器推荐：PS磨皮优化滤镜Portraiture2"><i class="icon-ps"></i></a> 
                <a href="#" target="_blank" title="神器推荐：spoon！让你在win7系统也可开IE6测试"><i class="icon-win"></i></a>
                 <a href="#" target="_blank" title="推荐：全能取色神器pixeur"><i class="icon-pick"></i></a>
                  <a href="#" target="_blank" title="20个设计师必备工具推荐"><i class="icon-tool"></i></a>
                   </div>',
 'settings' => array(
            'textarea_rows' => 1,
            'tinymce'       => false,
            'media_buttons' => false,
          )
            ),

	// 分页设置
		 array(
		  'type'    => 'notice',
		  'class'   => 'info',
		  'content' => '底部选项',
		), 	   
		
		 array(
          'id'      => 'dawa_flist',
          'type'    => 'text',
          'title'   => '标题名称',
		  'help'    => '默认为自定义栏目，填写标题后即开启分类调用功能.',

        ),	
		    
        array(
            'id' => 'i_alerts_nums',
            'type' => 'number',
            'title' => '显示数量',
            'default' => '12',
        ) ,
        array(
            'id' => 'alerts_1',
            'type' => 'select',
            'title' => '栏目分类',
            'options' => 'categories',
            'default_option' => 'Select a tag'
        ) ,
		
		
		
		
		 array(
              'id'    => 'dawa_foot_list',
              'type'  => 'wysiwyg',
              'title' => '自定义第二栏',
			  'info'      => '支持Font Awesome 字体图标',
			        'default' => '<div class="widget widget-product">
              <h2><a href="#" target="_blank">大产品 小细节</a></h2>
              <div class="cont">
                <ul>
                  <li><a href="#" >「派利是」与「發紅包」有什么区别？</a></li>
                  <li><a href="#" >具体到时分的行车路线推荐，是不是更具参考价值呢？</a></li>
                  <li><a href="#" >当抽象的捐赠金额变成可量化的公益项目，会打动你么？</a></li>
                  <li><a href="#" >怎样用最便宜的预算，来一场「说走就走」的旅行？</a></li>
                  <li><a href="#" >对重度知乎患者超友好的「下一个回答」</a></li>
                  <li><a href="#" >你好，小猪佩琦～</a></li>
                  <li><a href="#" >每当听雨的时候，你在想着谁？</a></li>
                  <li><a href="#" >「新浪微博」的「编辑记录」为什么是对所有人可见呢？</a></li>
                  <li><a href="#" >在「讯飞输入法」输入「时间」二字，查看隐藏惊喜哦～</a></li>
                </ul>
              </div>
            </div>',
           'settings' => array(
            'textarea_rows' => 1,
            'tinymce'       => false,
            'media_buttons' => false,
          )
            ),
			
   ),
);


// ------------------------------
// SEO选项                       -
// ------------------------------

$options[]      = array(
  'name'        => 'ad',
  'title'       => '广告添加',
  'icon'        => 'fa fa-bookmark',
  'fields'      => array(

 				
				//基本信息
		 array(
		  'type'    => 'notice',
		  'class'   => 'info',
		  'content' => '首页广告信息',		
		),  
		
	
			
			
			
		 array(
			  'id'     => 'i_ad_home',
              'type'  => 'wysiwyg',
			  'title' => '<h4>首页底部广告位</h4>',
			        'default' => '<a href="#" target="_blank"><img src="https://image.uisdc.com/wp-content/uploads/2018/08/uisdc-draw-180825.png" alt="img"></a><a class="hide" href="#" target="_blank"><img src="https://image.uisdc.com/wp-content/uploads/2018/08/uisdc-dkt-ixd08022.jpg" alt="img"></a>',
           'settings' => array(
            'textarea_rows' => 1,
            'tinymce'       => false,
            'media_buttons' => false,
          )
            ),
		
		 array(
			  'id'     => 'i_ad_home_txt',
              'type'  => 'wysiwyg',
			  'title' => '<h4>首页底部文字广告</h4>',
			        'default' => ' <h3> <span>学设计</span> <span>在这里</span> </h3>
          <p>2012年成立至今 一直专注于设计师的学习成长交流</p>
          <p>优设网是国内极具人气的设计平台</p>',
           'settings' => array(
            'textarea_rows' => 1,
            'tinymce'       => false,
            'media_buttons' => false,
          )
            ),
			
				//基本信息
		 array(
		  'type'    => 'notice',
		  'class'   => 'info',
		  'content' => '分类底部信息',		
		),  
		
			
			 array(
			  'id'     => 'i_ad_cat',
              'type'  => 'wysiwyg',
			  'title' => '<h4>分类底部广告位</h4>',
			        'default' => ' <a href="#" target="_blank"><img src="https://image.uisdc.com/wp-content/uploads/2018/08/uisdc-draw-180825.png" alt="img"></a><a class="hide" href="#" target="_blank"><img src="https://image.uisdc.com/wp-content/uploads/2018/08/uisdc-dkt-ixd08022.jpg" alt="img"></a>',
           'settings' => array(
            'textarea_rows' => 1,
            'tinymce'       => false,
            'media_buttons' => false,
          )
            ),
             array(
			  'id'     => 'i_ad_home',
              'type'  => 'wysiwyg',
			  'title' => '<h4>首页底部广告位</h4>',
			        'default' => '<a href="#" target="_blank"><img src="https://image.uisdc.com/wp-content/uploads/2018/08/uisdc-draw-180825.png" alt="img"></a><a class="hide" href="#" target="_blank"><img src="https://image.uisdc.com/wp-content/uploads/2018/08/uisdc-dkt-ixd08022.jpg" alt="img"></a>',
           'settings' => array(
            'textarea_rows' => 1,
            'tinymce'       => false,
            'media_buttons' => false,
          )
            ),
				 array(
			  'id'     => 'i_ad_cat_txt',
              'type'  => 'wysiwyg',
			  'title' => '<h4>分类底部文字广告</h4>',
			        'default' => ' <h3> <span>学设计</span> <span>在这里</span> </h3>
          <p>2012年成立至今 一直专注于设计师的学习成长交流</p>
          <p>优设网是国内极具人气的设计平台</p>',
           'settings' => array(
            'textarea_rows' => 1,
            'tinymce'       => false,
            'media_buttons' => false,
          )
            ),
			
	
				
   ),
);
		
// ------------------------------
// SEO选项                       -
// ------------------------------

$options[]      = array(
  'name'        => 'seo',
  'title'       => 'SEO选项',
  'icon'        => 'fa fa-bug',
  'fields'      => array(

 				
				//基本信息
		 array(
		  'type'    => 'notice',
		  'class'   => 'info',
		  'content' => '基本信息',		
		),  
  
	// 首页开启公告栏
		array(
          'id'    	  => 'i_catseo_u',
          'type'      => 'switcher',
          'title'     => '分类标题自定义',
		  'help'      => '注意：只显示在主页',
        ),  
  
  
  
		// 关键词  
		array(
		  'id'      => 'dawa_keywords',
		  'type'    => 'textarea',
		  'title'   => '关键字',
		  'help'    => '标识页面是关于什么的关键词，通常在搜索引擎中使用',
		),
		
	
		// 描述	
		array(
		  'id'      => 'dawa_description',
		  'type'    => 'textarea',
		  'title'   => '描述',
		  'help'    => '页面的简短描述',
		),		
		
	
  ),
);


// ------------------------------
// 代码                      -
// ------------------------------

$options[]      = array(
  'name'        => 'code',
  'title'       => '定义代码',
  'icon'        => 'fa fa-code',
  'fields'      => array(

			// 自定义CSS
			array(
			  'id'     => 'i_css',
			  'type'   => 'textarea',
			  'before' => '<h4>自定义CSS</h4>',
			  'after'  => '<p class="cs-text-muted">注意：无需写入<strong>&lt;style></strong>标签。</p>',
			),	
			
			// 自定义javascript
			array(
			  'id'     => 'i_js',
			  'type'   => 'textarea',
			  'before' => '<h4>自定义javascript</h4>',
			  'after'  => '<p class="cs-text-muted">注意：无需写入<strong>&lt;script></strong>标签。</p>',
			),

			// 统计代码
			array(
			  'id'     => 'i_js_tongji',
			  'type'   => 'textarea',
			  'before' => '<h4>统计代码</h4>',
			  'after'  => '<p class="cs-text-muted">注意：无需写入<strong>&lt;script></strong>标签。',
			),				
	
  ),
);


// ------------------------------
// 备份                       -
// ------------------------------
$options[]   = array(
  'name'     => 'advanced',
  'title'    => '备份选项',
  'icon'     => 'fa fa-shield',
  'fields'   => array(

    array(
      'type'    => 'notice',
      'class'   => 'warning',
      'content' => '您可以保存当前的选项，下载一个备份和导入.',
    ),

	// 备份		
    array(
      'type'    => 'backup',
    ),

  )
);



CSFramework::instance( $settings, $options );
