<?php
/*
Name: 链接跳转
URI: https://mp.weixin.qq.com/s/e9jU49ASszsY95TrmT34TA
Description: 链接跳转扩展支持设置跳转规则来实现链接跳转。
Version: 2.0
*/
class WPJAM_Redirect{
	public static function __callStatic($method, $args){
		$handler	= wpjam_get_handler([
			'primary_key'	=> 'id',
			'option_name'	=> 'wpjam-links',
			'items_field'	=> 'redirects',
			'max_items'		=> 50
		]);

		return wpjam_call_handler($handler, $method, ...$args);
	}

	public static function get_actions(){
		return [
			'add'		=> ['title'=>'新建',	'dismiss'=>true],
			'edit'		=> ['title'=>'编辑'],
			'delete'	=> ['title'=>'删除',	'direct'=>true, 	'confirm'=>true,	'bulk'=>true],
			'set'		=> ['title'=>'设置',	'overall'=>true,	'class'=>'button-primary',	'value_callback'=>[self::class, 'get_setting'], 'callback'=>[self::class, 'update_setting']]
		];
	}

	public static function get_fields($action_key='', $id=0){
		if($action_key == 'set'){
			return [
				'redirect_view'	=> ['type'=>'view',		'value'=>'默认只在404页面支持跳转，开启下面开关后，所有页面都支持跳转'],
				'redirect_all'	=> ['type'=>'checkbox',	'class'=>'switch',	'label'=>'所有页面都支持跳转'],
			];
		}

		return [
			'type'			=> ['title'=>'匹配设置',	'type'=>'checkbox',	'class'=>'switch',	'label'=>'使用正则匹配'],
			'request'		=> ['title'=>'原地址',	'type'=>'url',	'required',	'show_admin_column'=>true],
			'destination'	=> ['title'=>'目标地址',	'type'=>'url',	'required',	'show_admin_column'=>true],
		];
	}

	public static function get_list_table(){
		return [
			'title'		=> '跳转规则',
			'plural'	=> 'redirects',
			'singular'	=> 'redirect',
			'model'		=> self::class,
		];
	}

	public static function on_template_redirect(){
		$url	= wpjam_get_current_page_url();

		if(is_404()){
			if(strpos($url, 'feed/atom/') !== false){
				wp_redirect(str_replace('feed/atom/', '', $url), 301);
				exit;
			}

			if(!get_option('page_comments') && strpos($url, 'comment-page-') !== false){
				wp_redirect(preg_replace('/comment-page-(.*)\//', '',  $url), 301);
				exit;
			}

			if(strpos($url, 'page/') !== false){
				wp_redirect(preg_replace('/page\/(.*)\//', '',  $url), 301);
				exit;
			}
		}

		if(is_404() || self::get_setting('redirect_all')){
			foreach(self::parse_items() as $redirect){
				$type			= $redirect['type']	?? 0;
				$request		= $redirect['request'] ?? '';
				$destination	= $redirect['destination'] ?? '';

				if($request && $destination){
					$request	= set_url_scheme($request);

					if($type == 1){
						$replaced	= preg_replace('#'.$request.'#', $destination, $url);

						if($replaced && $replaced != $url){
							wp_redirect($replaced, 301);
							exit;
						}
					}else{
						if($request == $url){
							wp_redirect($destination, 301);
							exit;
						}
					}
				}
			}
		}
	}

	public static function add_hooks(){
		$redirects	= wpjam_get_setting('wpjam-links', 'redirects');

		if($redirects === null){
			wpjam_update_setting('wpjam-links', 'redirects', (get_option('301-redirects') ?: []));

			delete_option('301-redirects');
		}

		add_action('template_redirect', [self::class, 'on_template_redirect'], 99);
	}
}

wpjam_add_menu_page('redirects', [
	'plugin_page'	=> 'wpjam-links',
	'title'			=> '链接跳转',
	'function'		=> 'list',
	'summary'		=> __FILE__,
	'list_table'	=> 'WPJAM_Redirect',
	'hooks'			=> ['WPJAM_Redirect', 'add_hooks']
]);
