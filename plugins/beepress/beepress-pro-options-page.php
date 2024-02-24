<?php include_once('beepress-utils.php');?>
<?php
$utils = new BeePressUtils();
$pass = $utils->check_license();
?>
<div class="container-fluid">
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item active">
			<a class="nav-link active" data-toggle="tab" href="#regular" role="tab">常规</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#rule" role="tab">采集规则配置</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#auto" role="tab">同步(非自动)</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#about" role="tab">关于&帮助&用户协议</a>
		</li>
	</ul>
	<div class="tab-content">
		<input type="text" hidden id="request_url" value="<?php echo admin_url( 'admin-ajax.php' );?>">
		<div class="tab-pane" id="rule" role="tabpanel">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">说明</th>
					<td>
						<p>规则是针对文章设置的，而非列表</p>
						<p>默认支持公众号文章，其他站点（平台）需自行添加采集规则</p>
						<p style="color: red;">采集规则代写：30元／条，欢迎咨询</p>
						<p><a href="http://xingyue.artizen.me/2017/01/08/beepress-faqs/" target="_blank">配置教程</a></p>
					</td>
				</tr>
				<th scope="row">
					规则
				</th>
				<td>
					<button id="add-rule-setting" type="button" class="btn btn-success btn-small" aria-label="Left Align">
						添加
					</button><br><br>
					<div id="rule-panel">
						<?php
						$ruleSettings = get_option('beepress_rule_settings', array());
						?>
						<?php foreach ($ruleSettings as $ruleSetting):?>
						<div class="panel panel-default rule-setting-panel">
							<div class="panel-body">
								<table class="form-table">
									<tr valign="top">
										<th scope="row">网站地址</th>
										<td>
											<input type="url" style="width:400px" class="site-url" value="<?php echo $ruleSetting['site'];?>" placeholder="网站域名，http或https开头">
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">标题</th>
										<td>
											<input type="text" style="width:400px" class="title-rule" value="<?php echo $ruleSetting['titleRule'];?>" placeholder="标签（如h1）、类名（.class-name）、ID（#id-name）">
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">内容</th>
										<td>
											<input type="text" style="width:400px" class="content-rule" value="<?php echo $ruleSetting['contentRule'];?>" placeholder="标签（如h1）、类名（.class-name）、ID（#id-name）">
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">图片</th>
										<td>
											<input type="text" style="width:400px" class="img-rule" value="<?php echo $ruleSetting['imgRule'];?>" placeholder="标签（如h1）、类名（.class-name）、ID（#id-name）">
											<p>如果图片是lazy-load模式，请在后面指定图片属性名称，用 | 隔开，如img|data-src，默认为src</p>
										</td>
									<tr valign="top">
										<th scope="row">目标网站编码(默认当作UTF8处理，可不填写)</th>
										<td>
											<input type="text" style="width:400px" class="encode-rule" value="<?php echo $ruleSetting['encodeRule'];?>" placeholder="如gbk、utf8">
										</td>
									</tr>
								</table>
								<button type="button" class="delete-rule-btn btn btn-danger btn-sm">
									删除
								</button>
							</div>
						</div>
						<?php endforeach;?>
					</div>
				</td>
			</table>
			<button class="btn btn-primary" id="save-rule-setting">保存</button>
		</div>
		<div class="tab-pane" id="about" role="tabpanel">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">公众号</th>
					<td>
						<h5>关注我的公众号，第一时间获得插件更新消息</h5>
						<img width="20%" src="https://i.loli.net/2018/11/30/5c00943dcba24.png">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">用户协议</th>
					<td>
						<h5>采集功能</h5>
						<p>
							该插件为用户提供一个迁移内容的便捷方式，用户需确保持有或获得迁移内容的版权承担因内容版权问题而产生的一切责任
						</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">其他</th>
					<td>
						更多问题请访问 <a href="http://xingyue.artizen.me/2017/01/08/beepress-faqs/" target="_blank">FAQs 页面</a>
					</td>
				</tr>
			</table>
		</div>
		<div class="tab-pane" id="auto">

			<h3>小蜜蜂公众号文章助手（PC 软件，非本插件）同步功能（不推荐使用）</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">状态</th>
					<td>
						<input class="form-check-input" type="radio" name="syncpress_push_status" value="open" <?php echo get_option('syncpress_push_status', 'open') == 'open' ? 'checked' : '';?>> 开启推送
						<input class="form-check-input" type="radio" <?php echo get_option('syncpress_push_status', 'open') == 'close' ? 'checked' : '';?> name="syncpress_push_status" value="close"> 关闭推送
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Token</th>
					<td><input id="syncpress_push_token" style="width:300px" placeholder="需额外购买 Token 开通" type="text" name="syncpress_push_token" value="<?php echo esc_attr( get_option('syncpress_push_token') );?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">
						公众号同步规则设置
					</th>
					<td>
						<button id="add-account-setting" type="button" class="btn btn-success btn-small" aria-label="Left Align">
							添加
						</button><br><br>
						<?php
							$pushSettings = get_option('syncpress_push_settings', array());
							$pushSettingArr['wechat'] = $pushSettings;
							$pushSettingArr['token'] = get_option('syncpress_push_token');
							$pushSettingArr['homeurl'] = home_url();
							$pushSettingString = esc_html(json_encode($pushSettingArr));
						?>
						<?php
						$cats = get_categories(array(
							'hide_empty' => false,
							'order' => 'ASC',
							'orderby' => 'id'
						));
						$cateArr = array();
						foreach ($cats as $cate) {
							$cateArr[] = $cate->cat_ID.','.$cate->cat_name;
						}
						$cateStr = implode('|', $cateArr);


						// 获取所有用户
						$users = get_users(array(
							'fields' => array('ID', 'user_nicename', 'display_name')
						));
						$userArr = array();
						foreach ($users as $user) {
							$userArr[] = $user->ID . ',' . $user->user_nicename;
						}

						$userStr = implode('|', $userArr);
						?>
						<input hidden id="push-setting-str" value="<?php echo $pushSettingString;?>">
						<div style="display: none;" data-cates="<?php echo $cateStr;?>" id="cate-str"></div>
						<div style="display: none;" data-users="<?php echo $userStr;?>" id="user-str"></div>
						<div id="setting-panel">
							<?php foreach ($pushSettings as $setting):?>
								<div class="panel panel-default account-setting-panel">
									<div class="panel-body">
										<table class="form-table">
											<tr valign="top">
												<th scope="row">公众号名称</th>
												<td><input style="width:200px" class="account-name" placeholder="公众号名称" type="text" name="account_name_<?php echo $setting['account_id'];?>" value="<?php echo $setting['account_name'];?>" /></td>
											</tr>
											<tr valign="top">
												<th scope="row">公众号微信号(助手填写 Biz)</th>
												<td><input class="account-id" style="width:200px" placeholder="微信号 or Biz" type="text" name="account_id_<?php echo $setting['account_id'];?>" value="<?php echo $setting['account_id'];?>" /></td>
											</tr>
											<tr valign="top">
												<th scope="row">助手同步</th>
												<?php
												$isHelper = isset($setting['is_helper']) ? $setting['is_helper'] : 'yes';
												?>
												<td>
													<input <?php if ($isHelper == 'no') echo 'checked';?> class="is-helper" type="radio" name="is_helper_<?php echo $setting['account_id'];?>" value="no" > 否
													<input <?php if ($isHelper == 'yes') echo 'checked';?> class="is-helper" type="radio" name="is_helper_<?php echo $setting['account_id'];?>" value="yes"> 是
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">作者</th>
												<td>
													<select class="custom-select post-author" name="post_author_<?php echo $setting['account_id'];?>">
														<?php foreach ($users as $user):?>
															<option value="<?php echo $user->ID;?>" <?php if(isset($setting['post_author']) && $user->ID == $setting['post_author']) echo "selected";?> ><?php echo $user->user_nicename . '(' . $user->display_name . ')';?></option>
														<?php endforeach;?>
													</select>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">指定分类</th>
												<td>
													<?php foreach ($cats as $cat):?>
														<input name="cat_ids[]" type="checkbox" <?php if (in_array($cat->cat_ID, isset($setting['cat_ids']) ? $setting['cat_ids'] : array(1))) echo 'checked';?> value="<?php echo $cat->cat_ID;?>"><?php echo $cat->cat_name;?>
													<?php endforeach;?>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">文章状态</th>
												<td>
													<?php
													$postStatus = isset($setting['post_status']) ? $setting['post_status'] : 'publish';
													?>
													<input type="radio" <?php if($postStatus == 'publish') echo 'checked';?> class="post-status" name="post-status_<?php echo $setting['account_id'];?>" value="publish"> 直接发布
													<input type="radio" <?php if($postStatus == 'pending') echo 'checked';?> class="post-status" name="post-status_<?php echo $setting['account_id'];?>" value="pending"> 待审核
													<input type="radio" <?php if($postStatus == 'draft') echo 'checked';?> class="post-status" name="post-status_<?php echo $setting['account_id'];?>" value="draft">  草稿
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">移除文中的链接</th>
												<?php
												$removeOuterlink = isset($setting['remove_outerlink']) ? $setting['remove_outerlink'] : 'no';
												?>
												<td>
													<input <?php if ($removeOuterlink == 'no') echo 'checked';?> class="remove-oueterlink" type="radio" name="remove_outerlink_<?php echo $setting['account_id'];?>" value="no" > 否
													<input <?php if ($removeOuterlink == 'keepcontent') echo 'checked';?> class="remove-oueterlink" type="radio" name="remove_outerlink_<?php echo $setting['account_id'];?>" value="keepcontent"> 移除链接，保留内容
													<input <?php if ($removeOuterlink == 'all') echo 'checked';?> class="remove-oueterlink" type="radio" name="remove_outerlink_<?php echo $setting['account_id'];?>" value="all"> 移除链接和内容
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">去除指定位置图片</th>
												<?php
												$removeImages = isset($setting['remove_images']) ? $setting['remove_images'] : array();
												?>
												<td>
													<input type="checkbox" value="1" name="remove_specified_image[]" <?php if (in_array(1, $removeImages)) echo 'checked';?>> 第1
													<input type="checkbox" value="2" name="remove_specified_image[]" <?php if (in_array(2, $removeImages)) echo 'checked';?>> 第2
													<input type="checkbox" value="3" name="remove_specified_image[]" <?php if (in_array(3, $removeImages)) echo 'checked';?>> 第3
													<input type="checkbox" value="4" name="remove_specified_image[]" <?php if (in_array(4, $removeImages)) echo 'checked';?>> 第4<br><br>
													<input type="checkbox" value="-1" name="remove_specified_image[]" <?php if (in_array(-1, $removeImages)) echo 'checked';?>> 倒数第1
													<input type="checkbox" value="-2" name="remove_specified_image[]" <?php if (in_array(-2, $removeImages)) echo 'checked';?>> 倒数第2
													<input type="checkbox" value="-3" name="remove_specified_image[]" <?php if (in_array(-3, $removeImages)) echo 'checked';?>> 倒数第3
													<input type="checkbox" value="-4" name="remove_specified_image[]" <?php if (in_array(-4, $removeImages)) echo 'checked';?>> 倒数第4<br>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">关键词替换</th>
												<td>
													<textarea name="keywords_replace_rule" cols="80" rows="8" placeholder="在此输入关键词替换规则，每行一条规则，规则格式：关键词=替换后的关键词"><?php if(isset($setting['keywords_replace_rule'])) echo $setting['keywords_replace_rule'];?></textarea><br>
													如：<br>
													windows=mac<br>
													乔布斯=盖茨<br>
												</td>
											</tr>
										</table>
										<button type="button" class="delete-setting-btn btn btn-danger btn-sm right">
											删除
										</button>
									</div>
								</div>
							<?php endforeach;?>
						</div>
					</td>
				</tr>
			</table>
			开通后请点击【复制配置】按钮，会自动将配置复制到剪贴板
			<br>
			<br>
			<button class="btn btn-primary" id="save-syncpress-setting">保存</button>
			<button class="btn btn-success" id="copy-syncpress-setting" data-clipboard-text="<?php echo $pushSettingString;?>">复制配置</button>
		</div>
		<div class="tab-pane active" id="regular" role="tabpanel">
			<form method="post" action="options.php">
				<?php settings_fields( 'beepress-option-group' ); ?>
				<?php do_settings_sections( 'beepress-option-group' ); ?>
				<h3>授权</h3>
				<table class="form-table">
					<tr valign="top">
						<td>
							<p>
								插件价格：原价<del>¥100</del>，现仅需 ¥80，最多绑 3 个域名，并且支持一年更新服务，联系微信 <strong style="color: red;">always-bee</strong> 进行购买，备注：蜜蜂采集<br>
								此价格包含了 蜜蜂采集 和 蜜蜂百宝箱 的功能，购买前请先试用，并遵循使用规则，不得使用本插件用于任何非法活动。
							</p>
							<p>
								1. 曾经购买过的老用户请联系开发者授权，可获得 1 年的更新服务权益
							</p>
							<p>
								2. 如果您曾经打赏支持过开发者（无论多少），在现有优惠基础上再享有 8 折优惠，即 ¥64，感谢你们
							</p>
							<p>
								所有购买用户享有按定价的 8 折<strong>续费权益</strong>
							</p>

							<p>您的站点地址(请复制后向开发者兑换授权码): <?php echo site_url();?></p>
							<h4>其它付费服务：</h4>
							<p>采集规则代写，30／条，设置好后，可以<strong>手动</strong>粘贴指定网站的文章链接进行采集，<strong>非自动采集</strong></p>
							<h3>独立开发维护不易，欢迎购买支持我继续完善插件</h3>
							<h4>若发现存在插件使用者侵犯了您的权益，请联系插件开发者，在确认之后将侵权行为进行限制，为了插件的长期发展，请插件用户务必合理合法使用本插件</h4>
						</td>
					</tr>
				</table>
				<h3>文章默认采集规则</h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">文章发布时间</th>
						<td>
							<?php
							$bp_post_time = get_option('bp_post_time');
							?>
							<select class="custom-select" name="bp_post_time">
								<option <?php echo $bp_post_time == 'original_time' ? 'selected' : '';?>  value="original_time">原文时间(仅对公众号生效)</option>
								<option <?php echo $bp_post_time == 'current_time' ? 'selected' : '';?> value="current_time">当前时间</option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">文章状态</th>
						<td>
							<?php
							$bp_post_status = get_option('bp_post_status');
							?>
							<select class="custom-select" name="bp_post_status">
								<option <?php echo $bp_post_status == 'publish' ? 'selected' : '';?> value="publish">直接发布</option>
								<option <?php echo $bp_post_status == 'pending' ? 'selected' : '';?> value="pending">待审</option>
								<option <?php echo $bp_post_status == 'draft' ? 'selected' : '';?> value="draft">草稿</option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">保留版权信息</th>
						<td>
							<input class="form-check-input" type="radio" name="bp_keep_copyright" value="no" <?php echo get_option('bp_keep_copyright', 'no') == 'no' ? 'checked' : '';?>> 去除
							<input class="form-check-input" type="radio" <?php echo get_option('bp_keep_copyright', 'no') == 'yes' ? 'checked' : '';?> name="bp_keep_copyright" value="yes"> 保留
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">版权声明位置</th>
						<td>
							<input <?php echo get_option('bp_copyright_position', 'bottom') == 'top' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_copyright_position" value="top" > 文章开头
							<input <?php echo get_option('bp_copyright_position', 'bottom') == 'bottom' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_copyright_position" value="bottom"> 文章结尾
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">移除文中的链接</th>
						<td>
							<input <?php echo get_option('bp_remove_outerlink', 'no') == 'no' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_remove_outerlink" value="no" > 否
							<input <?php echo get_option('bp_remove_outerlink', 'no') == 'keepcontent' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_remove_outerlink" value="keepcontent"> 移除链接，保留内容
							<input <?php echo get_option('bp_remove_outerlink', 'no') == 'all' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_remove_outerlink" value="all"> 移除链接和内容
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">是否开启反防盗链(实验性，可能会造成百度统计等失效)</th>
						<td>
							<input <?php echo get_option('bp_anti_protected_link', 'no') == 'yes' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_anti_protected_link" value="yes" > 是
							<input <?php echo get_option('bp_anti_protected_link', 'no') == 'no' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_anti_protected_link" value="no"> 否
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">设置特色图片（即Featured Image、缩略图、封面图）</th>
						<td>
							<input <?php echo get_option('bp_featured_image', 'yes') == 'yes' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_featured_image" value="yes" > 是
							<input <?php echo get_option('bp_featured_image', 'yes') == 'no' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_featured_image" value="no"> 否
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">是否下载图片</th>
						<td>
							<input <?php echo get_option('bp_download_image', 'yes') == 'yes' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_download_image" value="yes" > 是
							<input <?php echo get_option('bp_download_image', 'yes') == 'no' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_download_image" value="no"> 否
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">图片保存路径</th>
						<td>
							<input <?php echo get_option('bp_image_path', 'abs') == 'abs' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_image_path" value="abs" > 绝对路径
							<input <?php echo get_option('bp_image_path', 'abs') == 'rel' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_image_path" value="rel"> 相对路径
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">图片居中</th>
						<td>
							<input <?php echo get_option('bp_image_centered', 'no') == 'yes' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_image_centered" value="yes" > 是
							<input <?php echo get_option('bp_image_centered', 'no') == 'no' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_image_centered" value="no"> 否
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">自定义图片 Title 和 Alt 属性值</th>
						<td><input style="width:300px" placeholder="默认为文章标题" type="text" name="bp_image_title_alt" value="<?php echo esc_attr( get_option('bp_image_title_alt') );?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">图片文件名称前缀，可添加关键词，有利于 SEO</th>
						<td><input style="width:400px" placeholder="输入关键词，尽量简短，最好不要包含中文，建议拼音或英文" type="text" name="bp_image_name_prefix" value="<?php echo esc_attr( get_option('bp_image_name_prefix') );?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">标题关键词</th>
						<td>
							<input style="width:400px" name="bp_title_before" placeholder="在此处输入您要添加的内容（标题开头）" value="<?php echo get_option('bp_title_before', '');?>"><br>
							<input style="width:400px" name="bp_title_after" placeholder="在此处输入您要添加的内容（标题结尾）" value="<?php echo get_option('bp_title_after', '');?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">内容插入(普通文本或者HTML)</th>
						<td>
							<textarea cols="100" rows="10" id="" name="bp_content_before" placeholder="在此处输入您要添加的内容（文章开头）"><?php echo get_option('bp_content_before', '');?></textarea><br>
							<textarea cols="100" rows="10" id="" name="bp_content_after" placeholder="在此处输入您要添加的内容（文章末尾）"><?php echo get_option('bp_content_after', '');?></textarea>
						</td>
					</tr>
				</table>
				<h3>通用配置</h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">关键词替换库</th>
						<td>
							<textarea cols="100" rows="10" id="" name="bp_keywords_lib" placeholder="在此处输入您的关键词替换规则，每行一条规则"><?php echo get_option('bp_keywords_lib', '');?></textarea>
							<p>示例(等号左侧为原词，右侧为替换后的词)：</p>
							<p>比尔盖茨=乔布斯</p>
							<p>windows=mac</p>
						</td>
					</tr>
				</table>
				<h3>授权</h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">授权码</th>
						<td>
							<input style="width:400px" name="bp_license_code" placeholder="在此输入授权码" value="<?php echo get_option('bp_license_code', '');?>">
						</td>
					</tr>
				</table>
				<!-- <h3>内容推荐(当前版本已经移除该功能，如有需要，请自行到插件市场下载 先荐)</h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">关闭推荐功能</th>
						<td>
							<input <?php echo get_option('bp_close_xianjian', 'no') != 'no' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_close_xianjian" value="yes" > 是
							<input <?php echo get_option('bp_close_xianjian', 'no') != 'yes' ? 'checked' : '';?> class="form-check-input" type="radio" name="bp_close_xianjian" value="no"> 否
						</td>
					</tr>
				</table> -->
<!--				--><?php //if($pass):?>
<!--				<h3>其他</h3>-->
<!--				<table class="form-table">-->
<!--					<tr valign="top">-->
<!--						<th scope="row">隐藏赞助商</th>-->
<!--						<td>-->
<!--							<input --><?php //echo get_option('bp_close_sponsor', 'no') == 'yes' ? 'checked' : '';?><!-- class="form-check-input" type="radio" name="bp_close_sponsor" value="yes" > 是-->
<!--							<input --><?php //echo get_option('bp_close_sponsor', 'no') == 'no' ? 'checked' : '';?><!-- class="form-check-input" type="radio" name="bp_close_sponsor" value="no"> 否-->
<!--						</td>-->
<!--					</tr>-->
<!--				</table>-->
<!--				--><?php //endif;?>
				<?php submit_button(); ?>
		</div>
	</div>
</div>
