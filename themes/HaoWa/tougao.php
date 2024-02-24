<?php 
/*
	template name: 发布作品
	description: template for mobantu.com mohtml theme 
*/
//if(!is_user_logged_in()){
  //  wp_safe_redirect(wp_login_url());
  //}
	if( isset($_POST['tougao_form']) && $_POST['tougao_form'] == 'send'){
    if ( isset($_COOKIE["tougao"]) && ( time() - $_COOKIE["tougao"] ) < 120 ){
        echo   '<p  align=center><input class="btnStyle" type="submit"   value="返回"   onclick="history.back()"></p>';
        wp_die('您投稿也太勤快了吧，先歇会儿！');
    }
    //表单变量初始化
    $name = isset( $_POST['tougao_authorname'] ) ? $_POST['tougao_authorname'] : '';
    $email = isset( $_POST['tougao_authoremail'] ) ? $_POST['tougao_authoremail'] : '';
    $blog = isset( $_POST['tougao_authorblog'] ) ? $_POST['tougao_authorblog']: '';
    $title = isset( $_POST['tougao_title'] ) ? $_POST['tougao_title'] : '';
    $tags = isset( $_POST['tougao_tags']) ? $_POST['tougao_tags'] : '';
    $category = isset( $_POST['cat'] ) ? (int)$_POST['cat'] : 0;
    $content = isset( $_POST['tougao_content'] ) ? $_POST['tougao_content'] : '';
    //表单项数据验证
    if ( empty($name) || strlen($name) > 100 ){
        echo   '<p  align=center><input class="btnStyle" type="submit"   value="返回"   onclick="history.back()"></p>';
        wp_die('图标必须填写，且不得超过100个长度');
    }
  /*  if ( empty($email) || strlen($email) > 60 || !preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)){
        echo   '<p  align=center><input class="btnStyle" type="submit"   value="返回"   onclick="history.back()"></p>';
        wp_die('邮箱必须填写，且不得超过60个长度，必须符合 Email 格式');
    }*/
    if ( empty($title) || strlen($title) > 100 ){
        echo   '<p  align=center><input class="btnStyle" type="submit"   value="返回"   onclick="history.back()"></p>';
        wp_die('标题必须填写，且不得超过100个长度');
    }
    if ( empty($content) || strlen($content) < 100){
        echo   '<p  align=center><input class="btnStyle" type="submit"   value="返回"   onclick="history.back()"></p>';
        wp_die('内容必须填写，且不得少于20个长度');
    }
    $tougao = array(
        'post_title' => $title,                //标题
        'post_content' => $content,            //内容
        'post_status' => 'pending',            //待审
        'tags_input' => $tags,                 //标签
        'post_category' => array($category)    //分类
    );
    //将文章插入数据库
    $status = wp_insert_post( $tougao );
    if ($status != 0){
        //将自定义域写入最新待审文章
        global $wpdb;
        $myposts = $wpdb->get_results("
            SELECT ID
            FROM $wpdb->posts
            WHERE post_status = 'pending'
            AND post_type = 'post'
            ORDER BY post_date DESC
        ");
        add_post_meta($myposts[0]->ID, 'author', $name);    //插入投稿人昵称的自定义域
        if ( !empty($blog)) add_post_meta($myposts[0]->ID, 'source', $blog);    //插入投稿人网址的自定义域
        
        setcookie("tougao", time(), time()+180);
        echo   '<p align="center"><a href="';bloginfo('siteurl');;echo '/" title="返回首页">返回首页</a></p>';
        wp_die('投稿成功！','投稿成功！');
    } else {
        echo   '<p  align=center><input class="btnStyle" type="submit"   value="返回"   onclick="history.back()"></p>';
        wp_die('投稿失败！','投稿失败！');
    }
}
?>
<link rel='stylesheet' id='kube-css'  href='<?php bloginfo('template_url');?>/pages/style.css?ver=4.4.5' type='text/css' media='all' />
<link href="https://cdn.bootcss.com/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<?php get_header()?>
<style>
.content .row {
	padding: 15px 25px 20px 25px;
}
.items p {
	line-height: 26px;
	font-size: 14px;
}
.items img {
	max-width: 100%;
}
.aligncenter {
	display: block;
	margin-left: auto;
	margin-right: auto;
}
.aligncenter p.wp-caption-text {
	display: block;
	margin-left: auto;
	margin-right: auto;
	text-align: center;
}
.red {
	color: #E91518
}
</style>
<div class="content">
  <div class="container">
    <div class="row">
      <div class="col-md-1 sidebar">
        <div class="content-sidebar">
          <dl id="goto">
            <dt style="top: 10.5px;"><span class="show-list"></span></dt>
          </dl>
        </div>
      </div>
      <div class="col-md-11">
        <div class="part current" id="web" data-title="<?php the_title();?>">
          <h2 class=""><strong>
            <?php the_title();?>
            </strong></h2>
          <div class="items">
            <div class="row">
              <div class="mainleft">
                <div class="article_container row box">
                  <div>
                    <form method="post" class="forms columnar" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                      <ul>
                        <li>
                          <label for="tougao_title" class="bold"><span class="red">*</span>网站标题:</label>
                          <input type="text" size="40" value="" name="tougao_title" class="form-control" />
                        </li>
                        <li>
                          <label for="tougao_authorname" class="bold"><span class="red">*</span>图标链接:</label>
                          <input type="text" size="40" value="" name="tougao_authorname"class="form-control" />
                        </li>
                        
                        <li>
                          <label for="tougao_authorblog" class="bold"><span class="red">*</span>您的网址:</label>
                          <input type="text" size="40" value="" name="tougao_authorblog"class="form-control" />
                        </li>
                        <li>
                          <label for="tougao_tags" class="bold"><span class="red">*</span>关键词:</label>
                          <input id="tags" type="text" size="40" value="" name="tougao_tags" class="form-control" value="多个标签请用英文逗号 , 分开" />
                        </li>
                        <li>
                          <label class="bold"><span class="red">*</span>选择文章分类:</label>
                          <?php wp_dropdown_categories('show_count=1&hierarchical=1'); ?>
                        </li>
                        <li>
                          <label for="tougao_content" class="bold"><span class="red">*</span>站点介绍:</label>
                          <textarea style=" height:100px; border:#ccc solid 1px;" rows="15" cols="55" class="form-control" name="tougao_content"></textarea>
                        </li>
                        <li class="push">
                          <input type="hidden" value="send" name="tougao_form" />
                          <input class="btn btn-primary" type="submit"  value="提交" />
                          <input class="btn btn-default" type="reset"  value="重填" />
                        </li>
                      </ul>
                    </form>
                  </div>
                </div>
              <style>
			  .push{ padding-top:20px}
			  [class^="icon-"]:before, [class*=" icon-"]:before {    line-height: 50px;}
			  .primary-menus .left-cont button {line-height: normal}
			  </style>
              </div>
            </div>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php get_footer();?>
