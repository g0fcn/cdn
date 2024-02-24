<?php
class WPJAM_Route extends WPJAM_Register{
	public function callback($wp){
		remove_action('template_redirect',	'redirect_canonical');

		$this->action	= $wp->query_vars['action'] ?? '';
		$callback		= $this->callback;

		if(!$callback && $this->model && method_exists($this->model, 'redirect')){
			$callback	= [$this->model, 'redirect'];
		}

		if($callback && is_callable($callback)){
			call_user_func($callback, $this->action, $this->name);
		}

		add_filter('template_include',	[$this, 'filter_template']);
	}

	public function filter_template($template){
		$file	= $this->action ? $this->action.'.php' : 'index.php';
		$file	= STYLESHEETPATH.'/template/'.$this->name.'/'.$file;
		$file	= apply_filters('wpjam_template', $file, $this->name, $this->action);

		return is_file($file) ? $file : $template;
	}

	public static function filter_determine_current_user($user_id){
		if(empty($user_id)){
			$wpjam_user	= wpjam_get_current_user();

			if($wpjam_user && !empty($wpjam_user['user_id'])){
				return $wpjam_user['user_id'];
			}
		}

		return $user_id;
	}

	public static function filter_current_commenter($commenter){
		$commenter['user_emails']	= [];

		$wpjam_user	= wpjam_get_current_user(false);

		if($wpjam_user){
			if(!empty($wpjam_user['user_email'])){
				$commenter['comment_author_email']	= $commenter['user_emails'][] = $wpjam_user['user_email'];
			}

			if(empty($commenter['comment_author'])){
				$commenter['comment_author']	= $wpjam_user['nickname'];
			}
		}

		return $commenter;
	}

	public static function filter_pre_avatar_data($args, $id_or_email){
		$user_id 	= 0;
		$avatarurl	= '';
		$email		= '';

		if(is_object($id_or_email) && isset($id_or_email->comment_ID)){
			$id_or_email	= get_comment($id_or_email);
		}

		if(is_numeric($id_or_email)){
			$user_id	= $id_or_email;
		}elseif(is_string($id_or_email)){
			$email		= $id_or_email;
		}elseif($id_or_email instanceof WP_User){
			$user_id	= $id_or_email->ID;
		}elseif($id_or_email instanceof WP_Post){
			$user_id	= $id_or_email->post_author;
		}elseif($id_or_email instanceof WP_Comment){
			$user_id	= $id_or_email->user_id;
			$email		= $id_or_email->comment_author_email;
			$avatarurl	= get_comment_meta($id_or_email->comment_ID, 'avatarurl', true);
		}

		if(!$avatarurl && $user_id){
			$avatarurl	= get_user_meta($user_id, 'avatarurl', true);
		}

		if($avatarurl){
			$url	= wpjam_get_thumbnail($avatarurl, [$args['width'], $args['height']]);

			return array_merge($args, ['found_avatar'=>true, 'url'=>$url,]);
		}

		if($user_id){
			$args['user_id']	= $user_id;
		}

		if($email){
			$args['email']		= $email;
		}

		return $args;
	}

	public static function filter_query_vars($query_vars){
		return ['module', 'action', 'term_id', ...$query_vars];
	}

	public static function filter_current_theme_supports($check, $args, $current){
		if($args){
			return is_array($current[0]) ? in_array($args[0], $current[0]) : false;
		}

		return $check;
	}

	public static function filter_script_loader_tag($tag, $handle, $src){
		if($handle == 'wpjam-ajax' && !is_login() && current_theme_supports('script', 'wpjam-ajax')){
			return '';
		}

		return $tag;
	}

	public static function on_parse_request($wp){
		$module	= $wp->query_vars['module'] ?? null;

		if($module){
			$object	= self::get($module) ?: new self($module);

			$object->callback($wp);
		}
	}

	public static function on_loaded(){
		foreach(self::get_active('rewrite_rule') as $rule){
			$rules	= is_array(current($rule)) ? $rule : [$rule];

			array_walk($rules, function($rule){
				if(is_array($rule)){
					$rule[0]	= $GLOBALS['wp_rewrite']->root.$rule[0];

					add_rewrite_rule(...$rule);
				}
			});
		}
	}
}

class WPJAM_JSON extends WPJAM_Register{
	private $response;

	public function response(){
		$this->response	= apply_filters('wpjam_pre_json', [], $this->args, $this->name);
		$this->response	= wpjam_throw_if_error($this->response);
		$this->response	= array_merge($this->response, [
			'errcode'		=> 0,
			'current_user'	=> wpjam_try('wpjam_get_current_user', $this->pull('auth'))
		]);

		if($_SERVER['REQUEST_METHOD'] != 'POST' && !str_ends_with($this->name, '.config')){
			$this->response	+= wp_array_slice_assoc($this, ['page_title', 'share_title', 'share_image']);
		}

		if($this->modules){
			$modules	= $this->modules;
			$modules	= is_callable($modules) ? call_user_func($modules, $this->name) : $modules;
			$modules	= wp_is_numeric_array($modules) ? $modules : [$modules];

			array_walk($modules, [$this, 'merge']);
		}else{
			$result		= null;
			$callback	= $this->pull('callback');

			if($callback){
				if(is_callable($callback)){
					$result	= wpjam_try($callback, $this->args, $this->name);
				}
			}elseif($this->template){
				if(is_file($this->template)){
					$result	= include $this->template;
				}
			}else{
				$result	= $this->args;
			}

			$this->merge($result, 'result');
		}

		$response	= apply_filters('wpjam_json', $this->response, $this->args, $this->name);

		if($_SERVER['REQUEST_METHOD'] != 'POST' && !str_ends_with($this->name, '.config')){
			foreach(['page_title', 'share_title']  as $title_key){
				if(empty($response[$title_key])){
					$response[$title_key]	= html_entity_decode(wp_get_document_title());
				}
			}

			if(!empty($response['share_image'])){
				$response['share_image']	= wpjam_get_thumbnail($response['share_image'], '500x400');
			}
		}

		return $response;
	}

	protected function merge($result, $type='module'){
		if($type !== 'result'){
			$result	= $this->parse_module($result);
		}

		wpjam_throw_if_error($result);

		if(is_array($result)){
			$attr	= array_filter(wp_array_slice_assoc($this->response, ['page_title', 'share_title', 'share_image']));

			$this->response	= array_merge($this->response, array_except($result, array_keys($attr)));
		}
	}

	public static function parse_module($module){
		$args	= array_get($module, 'args', []);
		$args	= is_array($args) ? $args : wpjam_parse_shortcode_attr(stripslashes_deep($args), 'module');
		$parser	= array_get($module, 'parser');

		if(!$parser){
			$type	= array_get($module, 'type');
			$parser	= wpjam_get_item('json_module_parser', $type);
		}

		return $parser ? wpjam_catch($parser, $args) : $args;
	}

	public static function redirect($action){
		if(!wpjam_doing_debug()){
			header('X-Content-Type-Options: nosniff');

			rest_send_cors_headers(false); 

			if('OPTIONS' === $_SERVER['REQUEST_METHOD']){
				status_header(403);
				exit;
			}

			$type	= wp_is_jsonp_request() ? 'javascript' : 'json';

			@header('Content-Type: application/'.$type.'; charset='.get_option('blog_charset'));
		}

		if(!str_starts_with($action, 'mag.')){
			return;
		}

		$name	= wpjam_remove_prefix($action, 'mag.');
		$name	= wpjam_remove_prefix($name, 'mag.');	// 兼容
		$name	= str_replace('/', '.', $name);
		$name	= apply_filters('wpjam_json_name', $name);

		wpjam_set_current_var('json', $name);

		$current_user	= wpjam_get_current_user();

		if($current_user && !empty($current_user['user_id'])){
			wp_set_current_user($current_user['user_id']);
		}

		do_action('wpjam_api', $name);

		$object	= self::get($name);

		if($object){
			if(is_wp_error($object)){
				self::send($object);
			}

			if($object->defaults){
				add_filter('wpjam_parameter_default', [$object, 'filter_parameter_default'], 10, 2);
			}

			self::send(wpjam_catch([$object, 'response']));
		}else{
			wp_die('接口未定义！', 'invalid_api');
		}
	}

	public static function send($data=[], $status_code=null){
		$data	= wpjam_parse_error($data);
		$result	= self::encode($data);

		if(!headers_sent() && !wpjam_doing_debug()){
			if(!is_null($status_code)){
				status_header($status_code);
			}

			if(wp_is_jsonp_request()){
				$result	= '/**/' . $_GET['_jsonp'] . '(' . $result . ')';

				$type	= 'javascript';
			}else{
				$type	= 'json';
			}

			@header('Content-Type: application/'.$type.'; charset='.get_option('blog_charset'));
		}

		echo $result;

		exit;
	}

	public static function encode($data){
		return wp_json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	public static function decode($json, $assoc=true){
		$json	= wpjam_strip_control_characters($json);

		if(!$json){
			return new WP_Error('json_decode_error', 'JSON 内容不能为空！');
		}

		$result	= json_decode($json, $assoc);

		if(is_null($result)){
			$result	= json_decode(stripslashes($json), $assoc);

			if(is_null($result)){
				if(wpjam_doing_debug()){
					print_r(json_last_error());
					print_r(json_last_error_msg());
				}
				trigger_error('json_decode_error '. json_last_error_msg()."\n".var_export($json,true));
				return new WP_Error('json_decode_error', json_last_error_msg());
			}
		}

		return $result;
	}

	public static function get_current($output='name'){
		$name	= wpjam_get_current_var('json');

		return $output == 'object' ? self::get($json) : $name;
	}

	public static function get_rewrite_rule(){
		return [
			['api/([^/]+)/(.*?)\.json?$',	['module'=>'json', 'action'=>'mag.$matches[1].$matches[2]'], 'top'],
			['api/([^/]+)\.json?$', 		'index.php?module=json&action=$matches[1]', 'top'],
		];
	}

	public static function get_defaults(){
		return [
			'post.list'		=> ['modules'=>['WPJAM_Posts', 'json_modules_callback']],
			'post.calendar'	=> ['modules'=>['WPJAM_Posts', 'json_modules_callback']],
			'post.get'		=> ['modules'=>['WPJAM_Posts', 'json_modules_callback']],
			'media.upload'	=> ['modules'=>['type'=>'media', 'args'=>['media'=>'media']]],
			'site.config'	=> ['modules'=>['type'=>'config']],
		];
	}

	public static function __callStatic($method, $args){
		if(in_array($method, ['parse_post_list_module', 'parse_post_get_module'])){
			$args	= $args[0] ?? [];
			$action	= str_replace(['parse_post_', '_module'], '', $method);

			return self::parse_module(['type'=>'post_type', 'args'=>array_merge($args, ['action'=>$action])]);
		}
	}
}

class WPJAM_Config{
	public static function register(...$args){
		$group	= count($args) >= 3 ? array_shift($args) : '';
		$group 	= $group ? $group.':config' : 'config';
		$args	= filter_null($args);

		if($args){
			if(is_array($args[0]) || count($args) == 1){
				$args	= is_callable($args[0]) ? ['callback'=>$args[0]] : (is_array($args[0]) ? $args[0] : [$args[0] => null]);
			}else{
				$args	= is_callable($args[1]) ? ['name'=>$args[0], 'callback'=>$args[1]] : [$args[0]=>$args[1]];
			}

			wpjam_add_item($group, $args);
		}
	}

	public static function get($group=''){
		$group	= is_array($group) ? array_get($group, 'group') : $group;
		$group 	= $group ? $group.':config' : 'config';

		return array_reduce(wpjam_get_items($group), fn($config, $item) => array_merge($config, self::parse($item)), []);
	}

	public static function parse($item){
		if(!empty($item['callback'])){
			$name	= $item['name'] ?? '';
			$args	= $item['args'] ?? [];
			$args	= $args ?: ($name ? [$name] : []);
			$value	= call_user_func($item['callback'], ...$args);
			$item	= $name ? [$name => $value] : (is_array($value) ? $value : []);
		}

		return $item;
	}
}

class WPJAM_Source{
	public static function register($name, $callback, $query_args=['source_id']){
		if(!wpjam_get_items('source')){
			add_filter('wpjam_pre_json', [self::class, 'filter_pre_json']);
		}

		return wpjam_add_item('source', $name, ['callback'=>$callback, 'query_args'=>$query_args]);
	}

	public static function filter_pre_json($pre){
		$name	= wpjam_get_parameter('source');
		$source	= $name ? wpjam_get_item('source', $name) : null;

		if($source){
			$query_data	= wpjam_generate_query_data($source['query_args'], '');

			call_user_func($source['callback'], $query_data);
		}

		return $pre;
	}
}

class WPJAM_Activation{
	public static function register($callback, $hook=null){
		$actives	= get_option('wpjam-actives', []);
		$actives[]	= $hook ? compact('hook', 'callback') : $callback;

		update_option('wpjam-actives', $actives);
	}

	public static function on_plugins_loaded(){
		$reset		= false;
		$actives	= get_option('wpjam-actives', null);

		if($actives && is_array($actives)){
			foreach($actives as $active){
				if(is_array($active) && isset($active['hook'])){
					$hook	= $active['hook'];
					$active	= $active['callback'];
				}else{
					$hook	= 'wp_loaded';
				}

				add_action($hook, $active);
			}

			$reset	= true;
		}elseif(is_null($actives)){
			$reset	= true;
		}

		if($reset){
			update_option('wpjam-actives', []);
		}
	}
}

class WPJAM_JWT{
	public static function generate($payload, $key='', $header=[]){
		if(is_array($payload)){
			$header	= wp_parse_args($header, [
				'alg'	=> 'HS256',
				'typ'	=> 'JWT'
			]);

			if($header['alg'] == 'HS256'){
				$header		= base64_urlencode(wpjam_json_encode($header));
				$payload	= base64_urlencode(wpjam_json_encode($payload));
				$jwt		= $header.'.'.$payload;
				$key		= $key ?: wp_salt();

				return $jwt.'.'.base64_urlencode(hash_hmac('sha256', $jwt, $key, true));
			}
		}

		return false;
	}

	public static function verify($token, $key=''){
		$tokens	= $token ? explode('.', $token) : [];

		if(count($tokens) != 3){
			return false;
		}

		list($header, $payload, $sign) = $tokens;

		$jwt		= $header.'.'.$payload;
		$key		= $key ?: wp_salt();
		$header		= wpjam_json_decode(base64_urldecode($header));
		$payload	= wpjam_json_decode(base64_urldecode($payload));

		if(empty($header['alg']) || $header['alg'] != 'HS256'){
			return false;
		}

		if(!hash_equals(base64_urlencode(hash_hmac('sha256', $jwt, $key, true)), $sign)){
			return false;
		}

		//签发时间大于当前服务器时间验证失败
		if(isset($payload['iat']) && $payload['iat'] > time()){
			return false;
		}

		//该nbf时间之前不接收处理该Token
		if(isset($payload['nbf']) && $payload['nbf'] > time()){
			return false;
		}

		//过期时间小于当前服务器时间验证失败
		if(empty($payload['exp']) || $payload['exp'] < time()){
			return false;
		}

		return $payload;
	}

	public static function get($key='access_token', $required=false){
		$headers	= getallheaders();

		if(isset($headers['Authorization']) && str_starts_with($headers['Authorization'], 'Bearer')){
			return trim(wpjam_remove_prefix($headers['Authorization'], 'Bearer'));
		}else{
			return wpjam_get_parameter($key, ['required'=>$required]);
		}
	}
}

class WPJAM_Error extends WPJAM_Model{
	public static function get_handler(){
		return wpjam_get_handler('wpjam_errors', [
			'option_name'	=> 'wpjam_errors',
			'primary_key'	=> 'errcode',
			'primary_title'	=> '代码',
		]);
	}

	public static function filter($data){
		$error	= self::get($data['errcode']);

		if($error){
			$data['errmsg']	= $error['errmsg'];

			if(!empty($error['show_modal'])){
				if(!empty($error['modal']['title']) && !empty($error['modal']['content'])){
					$data['modal']	= $error['modal'];
				}
			}
		}else{
			if(empty($data['errmsg'])){
				$item	= self::get_setting($data['errcode']);

				if($item){
					$data['errmsg']	= $item['message'];

					if($item['modal']){
						$data['modal']	= $item['modal'];
					}
				}
			}
		}

		return $data;
	}

	public static function parse($data){
		if(is_wp_error($data)){
			$errdata	= $data->get_error_data();
			$data		= [
				'errcode'	=> $data->get_error_code(),
				'errmsg'	=> $data->get_error_message(),
			];

			if($errdata){
				$errdata	= is_array($errdata) ? $errdata : ['errdata'=>$errdata];
				$data 		= $data + $errdata;
			}
		}else{
			if($data === true){
				return ['errcode'=>0];
			}elseif($data === false || is_null($data)){
				return ['errcode'=>'-1', 'errmsg'=>'系统数据错误或者回调函数返回错误'];
			}elseif(is_array($data)){
				if(!$data || !wp_is_numeric_array($data)){
					$data	= wp_parse_args($data, ['errcode'=>0]);
				}
			}
		}

		if(!empty($data['errcode'])){
			$data	= self::filter($data);
		}

		return $data;
	}

	public static function add_setting($code, $message, $modal=[]){
		$object = wpjam_get_items_object('error');

		if(!$object->get_items()){
			add_action('wp_error_added', [self::class, 'on_wp_error_added'], 10, 4);
		}

		$object->add_item($code, ['message'=>$message, 'modal'=>$modal]);
	}

	public static function get_setting($code){
		$object = wpjam_get_items_object('error');
		$item	= $object->get_item($code);

		if($item && $item['message']){
			return $object->get_item($code);
		}
	}

	public static function convert($message, $title='', $code=0){
		$modal	= [];

		if(is_wp_error($message)){
			$wp_error	= $message;
		}else{
			if($code){
				if($title){
					$modal	= ['title'=>$title, 'message'=>$message];
				}
			}else{
				$code	= $title;

				if(!$code){
					if(is_scalar($message) && self::get_setting($message)){
						$code		= $message;
						$message	= '';
					}else{
						$code		= 'error';
					}
				}
			}
		}

		$wp_error	= new WP_Error($code, $message);

		if($modal){
			$wp_error->add_data(['modal'=>$modal]);
		}

		return $wp_error;
	}

	public static function wp_die_handler($message, $title='', $code=0){
		wpjam_send_json(self::convert($message, $title, $code));
	}

	public static function on_wp_error_added($code, $message, $data, $wp_error){
		if($code && (!$message || is_array($message)) && count($wp_error->get_error_messages($code)) <= 1){
			$item	= self::get_setting($code);

			if(is_array($code)){
				trigger_error(var_export($code, true));
			}

			if($item){
				if($item['modal']){
					$data	= is_array($data) ? $data : [];
					$data	= array_merge($data, ['modal'=>$item['modal']]);
				}

				if(is_callable($item['message'])){
					$message	= call_user_func($item['message'], $message, $code);
				}else{
					$message	= is_array($message) ? sprintf($item['message'], ...$message) : $item['message'];
				}
			}elseif(str_starts_with($code, 'invalid_')){
				$msg	= is_array($message) ? implode($message) : '';
				$name	= wpjam_remove_prefix($code, 'invalid_');

				if($name == 'parameter'){
					$message	= $msg ? '无效的参数：'.$msg.'。' : '参数错误。';
				}elseif($name == 'callback'){
					$message	= '无效的回调函数'.($msg ? '：' : '').$msg.'。';
				}else{
					$map	= [
						'id'			=> ' ID',
						'appsecret'		=> '密钥',
						'post'			=> '文章',
						'term'			=> '分类',
						'user_id'		=> '用户 ID',
						'user'			=> '用户',
						'comment_type'	=> '评论类型',
						'comment_id'	=> '评论 ID',
						'comment'		=> '评论',
						'type'			=> '类型',
						'signup_type'	=> '登录方式',
						'email'			=> '邮箱地址',
						'data_type'		=> '数据类型',
						'submit_button'	=> '提交按钮',
						'qrcode'		=> '二维码',
					];

					$message	= '无效的'.$msg.($map[$name] ?? ' '.ucwords($name));
				}
			}elseif(str_starts_with($code, 'illegal_')){
				$name	= wpjam_remove_prefix($code, 'illegal_');
				$map	= [
					'access_token'	=> 'Access Token ',
					'refresh_token'	=> 'Refresh Token ',
					'verify_code'	=> '验证码',
				];

				$message	= ($map[$name] ?? ucwords($name).' ').'无效或已过期。';
			}elseif(str_ends_with($code, '_occupied')){
				$name	= wpjam_remove_postfix($code, '_occupied');
				$map	= [
					'phone'		=> '手机号码',
					'email'		=> '邮箱地址',
					'nickname'	=> '昵称',
				];

				$message	= ($map[$name] ?? ucwords($name).' ').'已被其他账号使用。';
			}

			if($message){
				$wp_error->remove($code);
				$wp_error->add($code, $message, $data);
			}
		}
	}

	public static function filter_wp_die_handler(){
		return [self::class, 'wp_die_handler'];
	}

	public static function callback($args, $code){
		if($code == 'undefined_method'){
			if(count($args) >= 2){
				return sprintf('「%s」%s未定义', ...$args);
			}elseif(count($args) == 1){
				return sprintf('%s方法未定义', ...$args);
			}
		}elseif($code == 'quota_exceeded'){
			if(count($args) >= 2){
				return sprintf('%s超过上限：%s', ...$args);
			}elseif(count($args) == 1){
				return sprintf('%s超过上限', ...$args);
			}else{
				return '超过上限';
			}
		}
	}
}

class WPJAM_Exception extends Exception{
	private $errcode	= '';

	public function __construct($errmsg, $errcode=null, Throwable $previous=null){
		if(is_array($errmsg)){
			$errmsg	= new WP_Error($errcode, $errmsg);
		}

		if(is_wp_error($errmsg)){
			$errcode	= $errmsg->get_error_code();
			$errmsg		= $errmsg->get_error_message();
		}else{
			$errcode	= $errcode ?: 'error';
		}

		$this->errcode	= $errcode;

		parent::__construct($errmsg, (is_numeric($errcode) ? (int)$errcode : 1), $previous);
	}

	public function get_error_code(){
		return $this->errcode;
	}

	public function get_error_message(){
		return $this->getMessage();
	}

	public function get_wp_error(){
		return new WP_Error($this->errcode, $this->getMessage());
	}
}

class WPJAM_Parameter extends WPJAM_Singleton{
	protected $_input;

	public function get_value($name, $args){
		$this->args	= $args;

		$value	= $this->get_by_name($name);
		$value	??= $this->get_fallback($name);
		$result	= $this->validate_value($value, $name);

		if(is_wp_error($result)){
			return $this->send === false ? $result : wpjam_send_json($result);
		}

		return $this->sanitize_value($value);
	}

	protected function get_by_name($name){
		$method	= $this->method ?: 'GET';
		$method	= strtoupper($method);

		if($method == 'POST'){
			$data	= $_POST;
		}elseif($method == 'REQUEST'){
			$data	= $_REQUEST;
		}else{
			$data	= $_GET;
		}

		if($name){
			if(isset($data[$name])){
				return wp_unslash($data[$name]);
			}else{
				if($_POST || $method == 'GET'){
					return null;
				}
			}
		}else{
			if($data || in_array($method, ['GET', 'REQUEST'])){
				return wp_unslash($data);
			}
		}

		if(is_null($this->_input)){
			$input	= file_get_contents('php://input');
			$input	= is_string($input) ? @wpjam_json_decode($input) : $input;

			$this->_input	= is_array($input) ? $input : [];
		}

		return $name ? ($this->_input[$name] ?? null) : $this->_input;
	}

	protected function get_fallback($name){
		if($this->fallback){
			foreach(array_filter((array)$this->fallback) as $fallback){
				$value	= $this->get_by_name($fallback);

				if(!is_null($value)){
					return $value;
				}
			}
		}

		return $this->default ?? apply_filters('wpjam_parameter_default', null, $name);	// 内部底层接口，不建议直接使用
	}

	protected function validate_value($value, $name=''){
		if($this->required){
			if(is_null($value)){
				return new WP_Error('missing_parameter', '缺少参数：'.$name);
			}
		}

		if($this->length){
			if(is_numeric($this->length) && mb_strlen($value) < $this->length){
				return new WP_Error('invalid_parameter', [$name]);
			}
		}

		$result	= parent::validate_value($value);

		if($result === false){
			return new WP_Error('invalid_parameter', [$name]);
		}elseif(is_wp_error($result)){
			return $result;
		}

		return true;
	}

	protected function sanitize_value($value){
		if($this->type == 'int' && !is_null($value)){
			return (int)$value;
		}

		return parent::sanitize_value($value);
	}
}

class WPJAM_Data_Parameter extends WPJAM_Parameter{
	protected $_data;

	protected function get_by_name($name){
		if($name && isset($_GET[$name])){
			return wp_unslash($_GET[$name]);
		}

		if(is_null($this->_data)){
			$this->_data	= $this->sandbox(function(){
				$args		= ['sanitize_callback'=>'wp_parse_args', 'default'=>[]];
				$data		= wpjam_get_post_parameter('data', $args);
				$defaults	= wpjam_get_post_parameter('defaults', $args);

				return merge_deep($defaults, $data);
			});
		}

		if($name){
			return $this->_data[$name] ?? null;
		}

		return $this->_data;
	}
}

class WPJAM_Request extends WPJAM_Singleton{
	public function request($url, $args=[], $err_args=[], &$headers=null){
		$this->args	= wp_parse_args($args, [
			'body'			=> [],
			'headers'		=> [],
			'sslverify'		=> false,
			'blocking'		=> true,	// 如果不需要立刻知道结果，可以设置为 false
			'stream'		=> false,	// 如果是保存远程的文件，这里需要设置为 true
			'filename'		=> null,	// 设置保存下来文件的路径和名字
			// 'headers'	=> ['Accept-Encoding'=>'gzip;'],	//使用压缩传输数据
			// 'compress'	=> false,
		]);

		if($this->method){
			$this->method	= strtoupper($this->method);
		}else{
			$this->method	= $this->body ? 'POST' : 'GET';
		}

		if($this->method == 'GET'){
			$response	= wp_remote_get($url, $this->args);
		}elseif($this->method == 'FILE'){
			$response	= (new WP_Http_Curl())->request($url, wp_parse_args($this->args, [
				'method'			=> $this->body ? 'POST' : 'GET',
				'sslcertificates'	=> ABSPATH.WPINC.'/certificates/ca-bundle.crt',
				'user-agent'		=> 'WordPress',
				'decompress'		=> true,
			]));
		}else{
			$encode_required	= $this->pull('json_encode_required', $this->pull('need_json_encode'));

			if($encode_required){
				if(is_array($this->body)){
					$this->body	= $this->body ?: new stdClass;
					$this->body	= wpjam_json_encode($this->body);
				}

				if($this->method == 'POST' && empty($this->headers['Content-Type'])){
					$this->headers	+= ['Content-Type'=>'application/json'];
				}
			}

			$response	= wp_remote_request($url, $this->args);
		}

		if(wpjam_doing_debug()){
			print_r($response);
		}

		if(is_wp_error($response)){
			trigger_error($url."\n".$response->get_error_code().' : '.$response->get_error_message()."\n".var_export($this->body,true));
			return $response;
		}

		$errcode	= $response['response']['code'] ?? 0;

		if($errcode && $errcode != 200){
			return new WP_Error($errcode, '远程服务器错误：'.$errcode.' - '.$response['response']['message']);
		}

		if(!$this->blocking){
			return true;
		}

		$this->url	= $url;
		$body		= $response['body'];
		$headers	= $response['headers'];

		return $this->decode($body, $headers, $err_args);
	}

	public function decode($body, $headers, $err_args=[]){
		$disposition	= $headers['content-disposition'] ?? '';
		$content_type	= $headers['content-type'] ?? '';
		$content_type	= is_array($content_type) ? implode(' ', $content_type) : $content_type;

		if($disposition && strpos($disposition, 'attachment;') !== false){
			if(!$this->stream){
				if(!$content_type){
					$content_type	= finfo_buffer(finfo_open(), $body, FILEINFO_MIME_TYPE);
				}

				return 'data:'.$content_type.';base64, '.base64_encode($body);
			}

			return $body;
		}else{
			if($content_type && strpos($content_type, '/json')){
				$decode_required	= true;
			}else{
				$decode_required	= $this->pull('json_decode_required', $this->pull('need_json_decode', ($this->stream ? false : true)));
			}

			if($decode_required){
				if($this->stream){
					$body	= file_get_contents($this->filename);
				}

				if(empty($body)){
					trigger_error(var_export($body, true).var_export($headers, true));
				}else{
					$body	= wpjam_json_decode($body);

					if(is_wp_error($body)){
						return $body;
					}
				}
			}
		}

		if(is_array($body)){
			$err_args	= wp_parse_args($err_args,  [
				'errcode'	=> 'errcode',
				'errmsg'	=> 'errmsg',
				'detail'	=> 'detail',
				'success'	=> '0',
			]);

			$errcode	= array_pull($body, $err_args['errcode']);
		
			if($errcode && $errcode != $err_args['success']){
				$errmsg	= array_pull($body, $err_args['errmsg']);
				$detail	= array_pull($body, $err_args['detail']);
				$detail	= is_null($detail) ? array_filter($body) : $detail;

				if(apply_filters('wpjam_http_response_error_debug', true, $errcode, $errmsg, $detail)){
					trigger_error($this->url."\n".$errcode.' : '.$errmsg."\n".($detail ? var_export($detail,true)."\n" : '').var_export($this->body,true));
				}

				return new WP_Error($errcode, $errmsg, $detail);
			}
		}

		return $body;
	}
}

class WPJAM_API{
	public static function __callStatic($method, $args){
		$function	= 'wpjam_'.$method;

		if(function_exists($function)){
			return call_user_func($function, ...$args);
		}
	}

	public static function get_apis(){	// 兼容
		return WPJAM_JSON::get_registereds();
	}
}
