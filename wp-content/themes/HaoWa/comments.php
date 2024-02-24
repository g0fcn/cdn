<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');
	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			?>
			<p class="nocomments">必须输入密码，才能查看评论！</p>
			<?php
			return;
		}
	}
	/* This variable is for alternating comment background */
	$oddcomment = '';
?>
<!-- You can start editing here. -->
<?php if ('open' == $post->comment_status) : ?><section class="mainbox pd3040"><?php else : ?><?php endif; ?>
<?php if ($comments) : ?>

<div class="sidebartitle">
			            <h2>发布评论</h2>
			            <span class="commentsnum">
			            	共<?php comments_number('0', '1', '%' );?>条评论
			            </span>   
			        </div>
					<div id="comments" class="comments-area">

	<!--div class="navigation push-right">
		<div class="pagination">
        <?php paginate_comments_links(); ?></div>
	</div>	
	<div class="sidebartitle" style="margin-top: 20px;">
            <h2 id="comments-list-title">评论列表</h2>  
        </div-->			
	<ol class="comment-list">
	<?php wp_list_comments('type=comment&callback=weisay_comment&end-callback=weisay_end_comment&max_depth=23'); ?>
	</ol>
 <?php else : // this is displayed if there are no comments so far ?>
	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->
		<h3 id="comments"><?php //the_title(); ?>等您坐沙发呢！</h3>
	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<!--<p class="nocomments">报歉!评论已关闭.</p>-->
	<?php endif; ?>
	<?php endif; ?>
	<?php if ('open' == $post->comment_status) : ?>
	<!--div id="respond_box">
	<div id="respond">
		<h3>发表评论</h3>	
		<div class="cancel-comment-reply">
			<small><?php cancel_comment_reply_link(); ?></small>
		</div-->
		<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
		<p><?php print '您必须'; ?><a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"> [ 登录 ] </a>才能发表留言！</p>
    <?php else : ?>
     <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" class="commentform" id="commentform">
      <?php if ( $user_ID ) : ?>
      <p><?php print '当前登录：'; ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>&nbsp;&nbsp;<a href="<?php echo wp_logout_url(get_permalink()); ?>" title="退出"><?php print '[ 退出 ]'; ?></a></p>
	<?php elseif ( '' != $comment_author ): ?>
	<p><?php printf(__('欢迎回来 <strong>%s</strong>'), $comment_author); ?>
			<a href="javascript:toggleCommentAuthorInfo();" id="toggle-comment-author-info">[ 更改 ]</a></p>
			<script type="text/javascript" charset="utf-8">
				//<![CDATA[
				var changeMsg = "[ 更改 ]";
				var closeMsg = "[ 隐藏 ]";
				function toggleCommentAuthorInfo() {
					jQuery('#comment-author-info').slideToggle('slow', function(){
						if ( jQuery('#comment-author-info').css('display') == 'none' ) {
						jQuery('#toggle-comment-author-info').text(changeMsg);
						} else {
						jQuery('#toggle-comment-author-info').text(closeMsg);
				}
			});
		}
				jQuery(document).ready(function(){
					jQuery('#comment-author-info').hide();
				});
				//]]>
			</script>
	<?php endif; ?>
	
	<div id="comments" class="comments-area">
	<div id="respond" class="respond" role="form"> 
	<?php if ( ! $user_ID ): ?>
                        <p class="comment-form-author">
                            <input id="author" type="text" tabindex="2" value="<?php echo $comment_author; ?>" name="author" placeholder="昵称[必填]" required="">
                        </p>
                        <p class="comment-form-email">
                            <input id="email" type="text" tabindex="3" value="<?php echo $comment_author_email; ?>" name="email" placeholder="邮箱[必填]" required="">
                        </p>
                        <p class="comment-form-url">
                            <input id="url" type="text" tabindex="4" value="<?php echo $comment_author_url; ?>" name="url" placeholder="网址(可不填)">
                        </p>
                        <?php endif; ?>
                    <p class="comment-form-comment">
                            <textarea rows="3" id="comment" onkeydown="if(event.ctrlKey&amp;&amp;event.keyCode==13){document.getElementById('submit').click();return false};" placeholder="我等你到风景看透......" tabindex="1" name="comment"></textarea>
                        </p>
                    <p class="form-submit" role="group">
                        <input name="submit" type="submit" id="submit" class="submit" value="发表评论"> 
                    </p>
                    <input type="hidden" name="comment_post_ID" value="193" id="comment_post_ID">
<input type="hidden" name="comment_parent" id="comment_parent" value="0">
                </form>
                    </div>	
			<?php comment_id_fields(); ?>       
       		<script type="text/javascript">	//Crel+Enter
		//<![CDATA[
			jQuery(document).keypress(function(e){
				if(e.ctrlKey && e.which == 13 || e.which == 10) { 
					jQuery(".submit").click();
					document.body.focus();
				} else if (e.shiftKey && e.which==13 || e.which == 10) {
					jQuery(".submit").click();
				}          
			})
		// ]]>
		</script>
		<?php do_action('comment_form', $post->ID); ?>
    </form>
	<div class="clear"></div>
    <?php endif; // If registration required and not logged in ?>
  
  <?php endif; // if you delete this the sky will fall on your head ?>
  <?php if ('open' == $post->comment_status) : ?></section><?php else : ?><?php endif; ?>
  
  <script type="text/javascript">
  function ajacpload(){
$('#comment_pager a').click(function(){
    var wpurl=$(this).attr("href").split(/(\?|&)action=AjaxCommentsPage.*$/)[0];
    var commentPage = 1;
    if (/comment-page-/i.test(wpurl)) {
    commentPage = wpurl.split(/comment-page-/i)[1].split(/(\/|#|&).*$/)[0];
    } else if (/cpage=/i.test(wpurl)) {
    commentPage = wpurl.split(/cpage=/)[1].split(/(\/|#|&).*$/)[0];
    };
    //alert(commentPage);//获取页数
    var postId =$('#cp_post_id').text();
	//alert(postId);//获取postid
    var url = wpurl.split(/#.*$/)[0];
    url += /\?/i.test(wpurl) ? '&' : '?';
    url += 'action=AjaxCommentsPage&post=' + postId + '&page=' + commentPage;        
    //alert(url);//看看传入参数是否正确
    $.ajax({
    url:url,
    type: 'GET',
    beforeSend: function() {
    document.body.style.cursor = 'wait';
    var C=0.7;//修改下面的选择器，评论列表div的id，分页部分的id
    $('#thecomments,#comment_pager').css({opacity:C,MozOpacity:C,KhtmlOpacity:C,filter:'alpha(opacity=' + C * 100 + ')'});
    var loading='Loading';
    $('#comment_pager').html(loading);
    },
    error: function(request) {
        alert(request.responseText);
    },
    success:function(data){
    var responses=data.split('');
    $('#thecomments').html(responses[0]);
    $('#comment_pager').html(responses[1]);
    var C=1; //修改下面的选择器，评论列表div的id，分页部分的id
    $('#thecomments,#comment_pager').css({opacity:C,MozOpacity:C,KhtmlOpacity:C,filter:'alpha(opacity=' + C * 100 + ')'});
    $('#cmploading').remove();
    document.body.style.cursor = 'auto';
    ajacpload();//自身重载一次
	//single_js();//需要重载的js，注意
	$body.animate( { scrollTop: $('#comment_header').offset().top - 200}, 1000);
        }//返回评论列表顶部
    });    
    return false;
    });
}
  </script>