<?php
class WPJAM_Attr extends WPJAM_Args{
	public function __toString(){
		return (string)$this->render();
	}

	public function jsonSerialize(){
		return $this->render();
	}

	public function attr(...$args){
		if(count($args) >= 2 || is_array($args[0])){
			$update	= is_array($args[0]) ? $args[0] : [$args[0]=>$args[1]];

			foreach($update as $k => $v){
				$this->$k	= $v;
			}

			return $this;
		}

		return $this->{$args[0]};
	}

	public function remove_attr($key){
		return $this->delete_arg($key);
	}

	public function val(...$args){
		return $args ? $this->attr('value', $args[0]) : $this->value;
	}

	public function data(...$args){
		$data	= $this->data ?: [];

		foreach($this->get_args() as $k => $v){
			if(str_starts_with($k, 'data-')){
				$data[$k]	= $v;
			}
		}

		if(!$args){
			return $data;
		}elseif(count($args) == 1 && is_scalar($args[0])){
			return $data[$args[0]] ?? null;
		}else{
			$update	= is_array($args[0]) ? $args[0] : [$args[0]=>$args[1]];

			foreach($update as $k => $v){
				$data[$k]	= $v;

				$this->remove_attr('data-'.$k);
			}

			return $this->attr('data', $data);
		}
	}

	protected function class($action='', ...$args){
		$class	= wp_parse_list($this->class ?: []);

		if(!$action){
			return $class;
		}

		$name	= $args[0];

		if($action == 'has'){
			return in_array($name, $class);
		}

		$names	= wp_parse_list($name);
		
		if($action == 'add'){
			$class	= array_merge($class, $names);
		}elseif($action == 'remove'){
			$class	= array_diff($class, $names);
		}elseif($action == 'toggle'){
			$added	= array_diff($names, $class);
			$class	= array_diff($class, $names);
			$class	= array_merge($class, $added);
		}

		return $this->attr('class', $class);
	}

	public function has_class($name){
		return $this->class('has', $name);
	}

	public function add_class($name){
		return $this->class('add', $name);
	}

	public function remove_class(...$args){
		return $args ? $this->class('remove', $args[0]) : $this->attr('class', []);
	}

	public function toggle_class($name){
		return $this->class('toggle', $name);
	}

	public function render(){
		$attr	= [];
		$args	= self::process($this->get_args());
		$data	= array_pull($args, 'data') ?: [];
		$value	= array_pull($args, 'value');
		$style	= array_pull($args, 'style');
		$class	= array_pull($args, 'class') ?: [];
		$class	= wp_parse_list($class);
		$class	= array_merge($class, array_keys(array_filter(wp_array_slice_assoc($args, ['readonly', 'disabled']))));

		if($class){
			$attr['class']	= implode(' ', array_unique(array_filter($class)));
		}

		if($style){
			$style	= (array)$style;

			foreach($style as $k => &$v){
				$v	= $v ? ((is_numeric($k) ? '' : $k.':').rtrim($v, ';')) : '';
			}

			$attr['style']	= implode(' ', array_unique(array_filter($style)));
		}

		if(isset($value)){
			$attr['value']	= $value;
		}

		foreach($args as $key => $value){
			if(str_ends_with($key, '_callback') || str_starts_with($key, '_') || is_blank($value)){
				continue;
			}

			if(str_starts_with($key, 'data-')){
				$data[$key]	= $value;
			}elseif(is_scalar($value)){
				$attr[$key]	= $value;
			}else{
				trigger_error($key.' '.var_export($value, true));
			}
		}

		$items		= array_map(fn($k, $v) => $k.'="'.esc_attr($v).'"', array_keys($attr), $attr);
		$items[]	= $data ? self::create($data, 'data') : '';

		return ' '.implode(' ', array_filter($items));
	}

	public static function is_bool($attr){
		return in_array($attr, ['allowfullscreen', 'allowpaymentrequest', 'allowusermedia', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled', 'download', 'formnovalidate', 'hidden', 'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open', 'playsinline', 'readonly', 'required', 'reversed', 'selected', 'typemustmatch']);
	}

	public static function process($args){
		$parsed	= [];

		foreach($args as $key => $value){
			$key	= strtolower(trim($key));

			if(is_numeric($key)){
				$key	= $value = strtolower(trim($value));

				if(!self::is_bool($key)){
					continue;
				}
			}else{
				if(self::is_bool($key)){
					if(!$value){
						continue;
					}

					$value	= $key;
				}
			}

			$parsed[$key]	= $value;
		}

		return $parsed;
	}

	public static function create($attr, $type=''){
		$attr	= ($attr && is_string($attr)) ? shortcode_parse_atts($attr) : wpjam_array($attr);

		return $type == 'data' ? new WPJAM_Data_Attr($attr) : new WPJAM_Attr($attr);
	}
}

class WPJAM_Data_Attr extends WPJAM_Attr{
	public function render(){
		$items	= [];

		foreach($this as $key => $value){
			if(isset($value) && $value !== false){
				if(is_scalar($value)){
					$value	= esc_attr($value);
				}else{
					$value	= $key == 'data' ? http_build_query($value) : wpjam_json_encode($value);
				}

				$key		= str_starts_with($key, 'data-') ? $key : 'data-'.$key;
				$items[]	= $key.'=\''.$value.'\'';
			}
		}

		return $items ? implode(' ', $items) : '';
	}
}

class WPJAM_Tag extends WPJAM_Attr{
	protected $tag		= '';
	protected $text		= '';
	protected $_before	= [];
	protected $_after	= [];
	protected $_prepend	= [];
	protected $_append	= [];

	public function __construct($tag='', $attr=[], $text=''){
		$this->tag	= $tag;
		$this->args	= ($attr && (wp_is_numeric_array($attr) || !is_array($attr))) ? ['class'=>$attr] : $attr;

		if($text && is_array($text)){
			$this->text(...$text);
		}elseif($text || is_numeric($text)){
			$this->text	= $text;
		}
	}

	public function __call($method, $args){
		if(in_array($method, ['text', 'tag', 'before', 'after', 'prepend', 'append'])){
			if($args){
				if(count($args) > 1){
					$value	= is_array($args[1])? new self(...$args) : new self($args[1], ($args[2] ?? []), $args[0]);
				}else{
					$value	= $args[0];
				}

				if($value || in_array($method, ['text', 'tag'])){
					if($method == 'text'){
						$this->text	= (string)$value;
					}elseif($method == 'tag'){
						$this->tag	= $value;
					}elseif(in_array($method, ['before', 'prepend'])){
						array_unshift($this->{'_'.$method}, $value);
					}elseif(in_array($method, ['after', 'append'])){
						array_push($this->{'_'.$method}, $value);
					}
				}	

				return $this;
			}else{
				if($method == 'text'){
					return $this->text;
				}elseif($method == 'tag'){
					return $this->tag;
				}else{
					return $this->{'_'.$method};
				}
			}
		}elseif(in_array($method, ['insert_before', 'insert_after', 'append_to', 'prepend_to'])){
			$method	= str_replace(['insert_', '_to'], '', $method);

			return call_user_func([$args[0], $method], $this);
		}

		return parent::__call($method, $args);
	}

	public function render($type=''){
		if($type){
			return $this->{'_'.$type} ? implode('', $this->{'_'.$type}) : '';
		}

		if($this->tag == 'a'){
			if(is_null($this->href)){
				$this->href	= 'javascript:;';
			}
		}elseif($this->tag == 'img'){
			if(is_null($this->title)){
				$this->title	= $this->alt;
			}
		}

		$single	= $this->is_single($this->tag);
		$result	= $this->render('before');

		if($this->tag){
			$result	.= '<'.$this->tag.parent::render();
			$result	.= $single ? ' />' : '>';
		}

		if(!$single){
			$result	.= $this->render('prepend');
			$result	.= (string)$this->text;
			$result	.= $this->render('append');

			if($this->tag){
				$result	.= '</'.$this->tag.'>';
			}
		}

		return $result	.= $this->render('after');
	}

	public function wrap($tag, ...$args){
		if($tag){
			if(str_contains($tag, '></')){
				if(preg_match('/<(\w+)([^>]+)>/', sprintf($tag, ...$args), $matches)){
					$tag	= $matches[1];
					$attr	= shortcode_parse_atts($matches[2]);
				}else{
					$tag	= '';
				}
			}else{
				$attr	= $args[0] ?? [];
			}
		}

		if($tag){
			if($attr && (!is_array($attr) || wp_is_numeric_array($attr))){
				$attr	= ['class'=>$attr];
			}

			$this->text	= clone($this);
			$this->tag	= $tag;
			$this->args	= $attr;

			foreach(['before', 'after', 'prepend', 'append'] as $type){
				$this->{'_'.$type}	= [];
			}	
		}

		return $this;
	}

	public static function is_single($tag){
		return $tag && in_array($tag, ['area', 'base', 'basefont', 'br', 'col', 'command', 'embed', 'frame', 'hr', 'img', 'input', 'isindex', 'link', 'meta', 'param', 'source', 'track', 'wbr']);
	}
}

class WPJAM_Array extends WPJAM_Args{
	public function exists($key){
		return $this->get($key) !== null;
	}

	public function get($key, $default=null){
		return $this->get_arg($key, $default);
	}

	public function set($key, $value){
		return $this->update_arg($key, $value);
	}

	public function add(...$args){
		if(count($args) >= 2){
			if(!$this->exists($args[0])){
				return $this->set(...$args);
			}
		}elseif($args){
			$this->args[]	= $value;
		}

		return $this;
	}

	public function delete($key){
		return $this->delete_arg($key);
	}

	public function merge($data){
		$this->args	= merge_deep($this->get_args(), $data);

		return $this;
	}

	public function filter($callback){
		$this->args	= filter_deep($this->get_args(), $callback);

		return $this;
	}
}

class WPJAM_Compare extends WPJAM_Singleton{
	public function compare($value, $args){
		$this->args	= $args;

		$strict		= (bool)$this->strict;
		$value2		= $this->value;
		$key		= $this->key;
		$compare	= $this->compare;
		$swap		= $this->swap;

		if($key){
			$item	= $value;

			if(is_object($item)){
				$value	= $item->{$key} ?? null;
			}elseif(is_array($item)){
				$value	= $item[$key] ?? null;
			}else{
				$value	= null;
			}

			if($this->callable && is_callable($value)){
				return call_user_func($value, $value2, $item);
			}

			if(is_array($value)){
				$swap	= true;
			}
		}

		if(is_null($compare) && isset($this->if_null) && is_null($value)){
			return $this->if_null;
		}

		if($swap){
			[$value, $value2]	= [$value2, $value];
		}

		$value2	= self::parse($value2, $compare, $value);

		if($compare == '='){
			if($strict){
				return $value === $value2;
			}else{
				return $value == $value2;
			}
		}elseif($compare == '!='){
			if($strict){
				return $value !== $value2;
			}else{
				return $value != $value2;
			}
		}elseif($compare == '>'){
			return $value > $value2;
		}elseif($compare == '>='){
			return $value >= $value2;
		}elseif($compare == '<'){
			return $value < $value2;
		}elseif($compare == '<='){
			return $value <= $value2;
		}elseif($compare == 'IN'){
			if(is_array($value)){
				foreach($value as $v){
					if(!in_array($v, $value2, $strict)){
						return false;
					}
				}

				return true;
			}else{
				return in_array($value, $value2, $strict);
			}
		}elseif($compare == 'NOT IN'){
			if(is_array($value)){
				foreach($value as $v){
					if(in_array($v, $value2, $strict)){
						return false;
					}
				}

				return true;
			}else{
				return !in_array($value, $value2, $strict);
			}
		}elseif($compare == 'BETWEEN'){
			return $value > $value2[0] && $value < $value2[1];
		}elseif($compare == 'NOT BETWEEN'){
			return $value < $value2[0] || $value > $value2[1];
		}

		return false;
	}

	public function match($item, $args=[], $operator='AND'){
		$operator	= strtoupper($operator);

		if(!in_array($operator, ['AND', 'OR', 'NOT'], true)){
			return false;
		}

		foreach($args as $key => $value){
			$value	= wpjam_is_assoc_array($value) ? $value : ['value'=>$value];
			$value	= wp_parse_args($value, ['key'=>$key]);
			$match	= $this->compare($item, $value);

			if($match){
				if('OR' === $operator){
					return true;
				}elseif('NOT' === $operator){
					return false;
				}
			}else{
				if('AND' === $operator){
					return false;
				}
			}
		}

		if('AND' === $operator || 'NOT' === $operator){
			return true;
		}elseif('OR' === $operator){
			return false;
		}
	}

	public static function parse($value, &$compare=null, $value1=null){
		$compare	= $compare ? strtoupper($compare) : (is_array($value) ? 'IN' : '=');

		if(in_array($compare, ['IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'])){
			$value	= wp_parse_list($value);

			if(!is_array($value1) && count($value) == 1){
				$value		= current($value);
				$compare	= in_array($compare, ['IN', 'BETWEEN']) ? '=' : '!=';
			}
		}else{
			if(is_string($value)){
				$value	= trim($value);
			}
		}

		return $value;
	}
}

class WPJAM_User_Agent extends WPJAM_Args{
	private $user_agent;

	public function __construct($user_agent=null){
		$this->user_agent	= $user_agent ?? wpjam_get_user_agent();
	}

	public function get_parsed(){
		if(!$this->args){
			$this->parse_os();
			$this->parse_browser();
			$this->parse_app();
		}

		return wp_array_slice_assoc($this, ['os', 'device', 'app', 'browser', 'os_version', 'browser_version', 'app_version']);
	}

	public function parse_os(){
		$this->os			= 'unknown';
		$this->device		= '';
		$this->os_version	= 0;

		foreach([
			['iPhone',			'iOS',	'iPhone'],
			['iPad',			'iOS',	'iPad'],
			['iPod',			'iOS',	'iPod'],
			['Android',			'Android'],
			['Windows NT',		'Windows'],
			['Macintosh',		'Macintosh'],
			['Windows Phone',	'Windows Phone'],
			['BlackBerry',		'BlackBerry'],
			['BB10',			'BlackBerry'],
			['Symbian',			'Symbian'],
		] as $rule){
			if(stripos($this->user_agent, $rule[0])){
				$this->os	= $rule[1];

				if(isset($rule[2])){
					$this->device	= $rule[2];
				}

				break;
			}
		}

		if($this->os == 'iOS'){
			if(preg_match('/OS (.*?) like Mac OS X[\)]{1}/i', $this->user_agent, $matches)){
				$this->os_version	= (float)(trim(str_replace('_', '.', $matches[1])));
			}
		}elseif($this->os == 'Android'){
			if(preg_match('/Android ([0-9\.]{1,}?); (.*?) Build\/(.*?)[\)\s;]{1}/i', $this->user_agent, $matches)){
				if(!empty($matches[1]) && !empty($matches[2])){
					$this->os_version	= trim($matches[1]);

					if(strpos($matches[2],';')!==false){
						$this->device	= substr($matches[2], strpos($matches[2],';')+1, strlen($matches[2])-strpos($matches[2],';'));
					}else{
						$this->device	= $matches[2];
					}

					$this->device	= trim($this->device);
					// $build	= trim($matches[3]);
				}
			}
		}
	}

	public function parse_browser(){
		$this->browser 			= '';
		$this->browser_version	= 0;

		foreach([
			['lynx',	'/lynx\/([\d\.]+)/i'],
			['safari',	'/version\/([\d\.]+).*safari/i'],
			['edge',	'/edge\/([\d\.]+)/i'],
			['chrome',	'/chrome\/([\d\.]+)/i'],
			['firefox',	'/firefox\/([\d\.]+)/i'],
			['opera', 	'/(?:opera|opr).([\d\.]+)/i'],
			['ie',		'/msie ([\d\.]+)/i'],
			['ie',		'/rv:([\d.]+) like Gecko/i'],
			// ['Gecko',	'gecko'],

		] as $rule){
			if(preg_match($rule[1], $this->user_agent, $matches)){
				$this->browser 	= $rule[0];
				$this->browser_version	= (float)(trim($matches[1]));

				break;
			}
		}
	}

	public function parse_app(){
		$referer	= $_SERVER['HTTP_REFERER'] ?? '';
		$this->app 		= '';
		$this->app_version	= 0;

		if(strpos($this->user_agent, 'MicroMessenger') !== false){
			if(strpos($referer, 'https://servicewechat.com') !== false){
				$this->app	= 'weapp';
			}else{
				$this->app	= 'weixin';
			}

			if(preg_match('/MicroMessenger\/(.*?)\s/', $this->user_agent, $matches)){
				$this->app_version = (float)$matches[1];
			}

			// if(preg_match('/NetType\/(.*?)\s/', $this->user_agent, $matches)){
			// 	$net_type = $matches[1];
			// }
		}elseif(strpos($this->user_agent, 'ToutiaoMicroApp') !== false 
			|| strpos($referer, 'https://tmaservice.developer.toutiao.com') !== false
		){
			$this->app	= 'bytedance';
		}
	}
}

class WPJAM_File{
	protected $value;
	protected $type;

	public function __construct($value='', $type=''){
		$this->value	= $value;
		$this->type		= $type ?: (is_numeric($value) ? 'id' : 'url');
	}

	public function __get($key){
		if($key == $this->type){
			return $this->value;
		}elseif($this->type == 'id'){
			return wpjam_get_attachment_value($this->value, $key);
		}elseif(in_array($this->type, ['url', 'file', 'path'])){
			return self::convert($this->value, $this->type, $key);
		}
	}

	public function __isset($key){
		return $this->$key !== null;
	}

	public static function convert($value, $from='path', $to='file'){
		$dir	= wp_get_upload_dir();

		if($from == 'path'){
			$path	= $value;
		}else{
			if($from == 'url'){
				$value	= parse_url($value, PHP_URL_PATH);
				$base	= parse_url($dir['baseurl'], PHP_URL_PATH);
			}elseif($from == 'file'){
				$base	= $dir['basedir'];
			}

			if(!str_starts_with($value, $base)){
				return null;
			}

			$path	= wpjam_remove_prefix($value, $base);
		}

		if($to == 'path'){
			return $path;
		}elseif($to == 'file'){
			return $dir['basedir'].$path;
		}elseif($to == 'url'){
			return $dir['baseurl'].$path;
		}elseif($to == 'size'){
			$file	= $dir['basedir'].$path;
			$size	= file_exists($file) ? wp_getimagesize($file) : [];

			if($size){
				return ['width'=>$size[0], 'height'=>$size[1]];
			}
		}

		$id		= self::get_id_by_meta($path);

		return $id ? wpjam_get_attachment_value($id, $to) : null;
	}

	public static function get_id_by_meta($value, $key='_wp_attached_file'){
		if($key == '_wp_attached_file'){
			$value	= ltrim($value, '/');
		}

		$meta	= wpjam_get_by_meta('post', $key, $value);

		if($meta){
			$id	= current($meta)['post_id'];

			if(get_post_type($id) == 'attachment'){
				return $id;
			}
		}

		return '';
	}

	public static function add_to_media($file, $post_id=0){
		if(is_array($file)){
			$upload	= $file;
			$file	= $upload['file'] ?? '';
			$url	= $upload['url'] ?? '';
			$type	= $upload['type'] ?? '';
		}else{
			$url	= $type = '';
		}

		if(!$file){
			return;
		}

		$path	= self::convert($file, 'file', 'path');
		$id		= self::get_id_by_meta($path);

		if($id){
			return $id;
		}

		$url	= $url ?: self::convert($file, 'file', 'url');
		$type	= $type ?: mime_content_type($file);

		if(!$url){
			return;
		}

		require_once ABSPATH.'wp-admin/includes/image.php';

		$title		= preg_replace('/\.[^.]+$/', '', wp_basename($file));
		$content	= '';
		$image_meta	= wp_read_image_metadata($file);

		if($image_meta ) {
			if(trim($image_meta['title']) && ! is_numeric(sanitize_title($image_meta['title']))){
				$title	= $image_meta['title'];
			}

			if(trim($image_meta['caption'])){
				$content	= $image_meta['caption'];
			}
		}

		$id	= wp_insert_attachment([
			'post_title'		=> $title,
			'post_content'		=> $content,
			'post_parent'		=> $post_id,
			'post_mime_type'	=> $type,
			'guid'				=> $url,
		], $file, $post_id, true);

		if(!is_wp_error($id)){
			wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $file));
		}

		return $id;
	}

	public static function parse_args($name, $media=true, $post_id=0){
		if(is_array($name)){
			$args	= wp_parse_args($name, [
				'name'		=> '',
				'media'		=> false,
				'post_id'	=> 0,
			]);
		}else{
			$args	= [
				'name'		=> $name,
				'media'		=> $media,
				'post_id'	=> $post_id,
			];
		}

		if(empty($args['field'])){
			$args['field']	= $args['media'] ? 'id' : 'file';
		}

		return $args;
	}
}

class WPJAM_Image extends WPJAM_File{
	public function is_valid(){
		if($this->type == 'url'){
			$ext_types	= wp_get_ext_types();
			$img_exts	= $ext_types['image'];
			$img_parts	= explode('?', $this->value);
			$img_url	= wpjam_remove_postfix($img_parts[0], '#');

			return preg_match('/\.('.implode('|', $img_exts).')$/i', $img_url);
		}elseif($this->type == 'file'){
			return !empty($this->size);
		}elseif($this->type == 'id'){
			return wp_attachment_is_image($this->value);
		}
	}

	public function get_size(){
		$size	= $this->size ?: [];
		$size	= apply_filters('wpjam_image_size', $size, $this->value, $this->type);

		if($size){
			$size = array_map('intval', $size);

			$size['orientation']	= $size['height'] > $size['width'] ? 'portrait' : 'landscape';
		}

		return $size;
	}

	public function get_thumbnail(...$args){
		if($this->url){
			$img_url	= wpjam_zh_urlencode($this->url);	// 中文名
			$img_url	= remove_query_arg(['orientation', 'width', 'height'], $img_url);

			return apply_filters('wpjam_thumbnail', $img_url, self::parse_thumbnail_args(...$args));
		}

		return '';
	}

	public function parse_query(){
		$query	= parse_url($this->url, PHP_URL_QUERY);
		$query	= wp_parse_args($query);

		foreach(['width', 'height'] as $key){
			if(isset($query[$key])){
				$query[$key]	= (int)$query[$key];
			}
		}

		return $query;
	}

	public static function parse_thumbnail_args(...$args){
		$args_num	= count($args);

		if($args_num == 0){
			// 1. 无参数
			return [];
		}elseif($args_num == 1){
			// 2. ['width'=>100, 'height'=>100]	// 这个为最标准版本
			// 3. [100,100]
			// 4. 100x100
			// 5. 100

			return self::parse_size($args[0]);
		}else{
			if(is_numeric($args[0])){
				// 6. 100, 100, $crop=1

				return [
					'width'		=> $args[0] ?? 0,
					'height'	=> $args[1] ?? 0,
					'crop'		=> $args[2] ?? 1
				];
			}else{
				// 7.【100,100], $crop=1

				return array_merge(self::parse_size($args[0]), ['crop'=>$args[1] ?? 1]);
			}
		}
	}

	// $size, $ratio
	// $size, $ratio, [$max_width, $max_height]
	// $size, [$max_width, $max_height]
	public static function parse_size($size, ...$args){
		$ratio		= 1;
		$max_width	= $max_height = 0;

		if($args){
			if(!is_array($args[0])){
				$ratio	= array_shift($args);
			}

			$max	= array_shift($args);

			if(is_array($max)){
				$max_width	= array_shift($max);
				$max_height	= array_shift($max);
			}
		}

		if(is_array($size)){
			if(wp_is_numeric_array($size)){
				$size	= ['width'=>($size[0] ?? 0), 'height'=>($size[1] ?? 0)];
			}

			$size['width']	= !empty($size['width']) ? ((int)$size['width'])*$ratio : 0;
			$size['height']	= !empty($size['height']) ? ((int)$size['height'])*$ratio : 0;
			$size['crop']	= $size['crop'] ?? ($size['width'] && $size['height']);
		}else{
			$size	= str_replace(['*','X'], 'x', $size);

			if(strpos($size, 'x') !== false){
				$size	= explode('x', $size);
				$width	= $size[0];
				$height	= $size[1];
				$crop	= true;
			}elseif(is_numeric($size)){
				$width	= $size;
				$height	= 0;
			}elseif($size == 'thumb' || $size == 'thumbnail'){
				$width	= get_option('thumbnail_size_w') ?: 100;
				$height = get_option('thumbnail_size_h') ?: 100;
				$crop	= get_option('thumbnail_crop');
			}elseif($size == 'medium'){
				$width	= get_option('medium_size_w') ?: 300;
				$height	= get_option('medium_size_h') ?: 300;
				$crop	= false;
			}else{
				if($size == 'medium_large'){
					$width	= get_option('medium_large_size_w');
					$height	= get_option('medium_large_size_h');
					$crop	= false;
				}elseif($size == 'large'){
					$width	= get_option('large_size_w') ?: 1024;
					$height	= get_option('large_size_h') ?: 1024;
					$crop	= false;
				}else{
					$sizes = wp_get_additional_image_sizes();

					if(isset($sizes[$size])){
						$width	= $sizes[$size]['width'];
						$height	= $sizes[$size]['height'];
						$crop	= $sizes[$size]['crop'];
					}else{
						$width	= $height = 0;
					}
				}

				if($width && !empty($GLOBALS['content_width'])){
					$width	= min($GLOBALS['content_width'] * $ratio, $width);
				}
			}

			$size	= [
				'crop'		=> $crop ?? ($width && $height),
				'width'		=> (int)$width * $ratio,
				'height'	=> (int)$height * $ratio
			];
		}

		if($max_width && $max_height){
			if($size['width'] && $size['height']){
				list($size['width'], $size['height'])	= wp_constrain_dimensions($size['width'], $size['height'], $max_width, $max_height);
			}elseif($size['width']){
				$size['width']	= $size['width'] < $max_width ? $size['width'] : $max_width;
			}else{
				$size['height']	= $size['height'] < $max_height ? $size['height'] : $max_height;
			}
		}

		return $size;
	}
}

class WPJAM_Crypt extends WPJAM_Args{
	public function __construct(...$args){
		if($args && is_string($args[0])){
			$key	= $args[0];
			$args	= $args[1] ?? [];
			$args	= array_merge($args, ['key'=>$key]);
		}else{
			$args	= $args[0] ?? [];
		}

		$this->args	= wp_parse_args($args, [
			'method'	=> 'aes-256-cbc',
			'key'		=> '',
			'iv'		=> '',
			'options'	=> OPENSSL_ZERO_PADDING,	
		]);
	}

	public function encrypt($text){
		if($this->pad == 'weixin' && $this->appid){
			$text 	= $this->pad($text, 'weixin', $this->appid);
		}

		if($this->options == OPENSSL_ZERO_PADDING && $this->block_size){
			$text	= $this->pad($text, 'pkcs7', $this->block_size);
		}

		return openssl_encrypt($text, $this->method, $this->key, $this->options, $this->iv);
	}

	public function decrypt($text){
		$text	= openssl_decrypt($text, $this->method, $this->key, $this->options, $this->iv);

		if($this->options == OPENSSL_ZERO_PADDING && $this->block_size){
			$text	= $this->unpad($text, 'pkcs7', $this->block_size);
		}

		if($this->pad == 'weixin' && $this->appid){
			$text 	= $this->unpad($text, 'weixin', $this->appid);
		}

		return $text;
	}

	public static function pad($text, $method, ...$args){
		if($method == 'pkcs7'){
			$pad	= $args[0] - (strlen($text) % $args[0]);

			return $text.str_repeat(chr($pad), $pad);
		}elseif($method == 'weixin'){
			return wp_generate_password(16, false).pack("N", strlen($text)).$text.$args[0];
		}

		return $text;
	}

	public static function unpad($text, $method, ...$args){
		if($method == 'pkcs7'){
			$pad	= ord(substr($text, -1));

			if($pad < 1 || $pad > $args[0]){
				$pad	= 0;
			}

			return substr($text, 0, -1 * $pad);
		}elseif($method == 'weixin'){
			$text	= substr($text, 16);
			$length	= (unpack("N", substr($text, 0, 4)))[1];

			if(substr($text, $length + 4) != $args[0]){
				return new WP_Error('invalid_appid', 'Appid 校验错误');
			}

			return substr($text, 4, $length);
		}

		return $text;
	}

	public static function generate_signature(...$args){
		sort($args, SORT_STRING);

		return sha1(implode($args));
	}

	public static function weixin_pad($text, $appid){
		return self::pad($text, 'weixin', $appid);
	}

	public static function weixin_unpad($text, &$appid){
		$text		= substr($text, 16, strlen($text));
		$len_list	= unpack("N", substr($text, 0, 4));
		$text_len	= $len_list[1];
		$appid		= substr($text, $text_len + 4);

		return substr($text, 4, $text_len);
	}

	public static function generate_weixin_signature($token, &$ts='', &$nonce='', $encrypt_msg=''){
		$ts		= $ts ?: time();
		$nonce	= $nonce ?: wp_generate_password(8, false);

		return self::generate_signature($token, $ts, $nonce, $encrypt_msg);
	}
}

class WPJAM_Updater extends WPJAM_Args{
	public function get_data($file){
		$key		= 'update_'.$this->plural.':'.$this->hostname;
		$response	= get_transient($key);

		if($response === false){
			$response	= wpjam_remote_request($this->update_url);	// https://api.wordpress.org/plugins/update-check/1.1/

			if(!is_wp_error($response)){
				if(isset($response['template']['table'])){
					$response	= $response['template']['table'];
				}else{
					$response	= $response[$this->plural];
				}

				set_transient($key, $response, MINUTE_IN_SECONDS);
			}else{
				$response	= false;
			}
		}

		if($response){
			if(isset($response['fields']) && isset($response['content'])){
				$fields	= array_column($response['fields'], 'index', 'title');
				$index	= $fields[$this->label];

				foreach($response['content'] as $item){
					if($item['i'.$index] == $file){
						$data	= [];

						foreach($fields as $name => $index){
							$data[$name]	= $item['i'.$index] ?? '';
						}

						return [
							$this->type		=> $file,
							'url'			=> $data['更新地址'],
							'package'		=> $data['下载地址'],
							'icons'			=> [],
							'banners'		=> [],
							'banners_rtl'	=> [],
							'new_version'	=> $data['版本'],
							'requires_php'	=> $data['PHP最低版本'],
							'requires'		=> $data['最低要求版本'],
							'tested'		=> $data['最新测试版本'],
						];
					}
				}
			}else{
				return $response[$file] ?? [];
			}
		}
	}

	public function filter_update($update, $data, $file, $locales){
		$new_data	= $this->get_data($file);

		if($new_data){
			return wp_parse_args($new_data, [
				'id'		=> $data['UpdateURI'], 
				'version'	=> $data['Version'],
			]);
		}

		return $update;
	}

	public function filter_pre_set_site_transient($updates){
		if(isset($updates->no_update) || isset($updates->response)){
			$file	= 'wpjam-basic/wpjam-basic.php';
			$update	= $this->get_data($file);

			if($update){
				$plugin	= get_plugin_data(WP_PLUGIN_DIR.'/'.$file);
				$key 	= version_compare($update['new_version'], $plugin['Version'], '>') ? 'response' : 'no_update';

				if(isset($updates->$key[$file])){
					$update	= array_merge((array)$updates->$key[$file], $update);
				}

				$updates->$key[$file]	= (object)$update;
			}
		}

		return $updates;
	}

	public static function create($type, $hostname, $update_url){
		if(in_array($type, ['plugin', 'theme'])){
			$plural	= $type.'s';
			$object	= new self([
				'type'			=> $type,
				'plural'		=> $plural,
				'label'			=> $type == 'plugin' ? '插件' : '主题',
				'hostname'		=> $hostname,
				'update_url'	=> $update_url
			]);

			add_filter('update_'.$plural.'_'.$hostname, [$object, 'filter_update'], 10, 4);

			if($plural == 'plugins' && $hostname == 'blog.wpjam.com'){
				add_filter('pre_set_site_transient_update_plugins', [$object, 'filter_pre_set_site_transient']);
			}
		}
	}
}

class WPJAM_Cache extends WPJAM_Args{
	use WPJAM_Instance_Trait;

	public function __call($method, $args){
		if(str_starts_with($method, 'cache_')){
			$method	= wpjam_remove_prefix($method, 'cache_');
		}

		$cb	= [];

		if($method == 'cas'){
			$cb[]	= array_shift($args);
		}

		$key	= array_shift($args);
		$gnd	= str_contains($method, 'get') || str_contains($method, 'delete');

		if(str_contains($method, '_multiple')){
			if($gnd){
				$cb[]	= $keys = array_map([$this, 'key'], $key);
			}else{
				$keys 	= array_map([$this, 'key'], array_keys($key));
				$cb[]	= array_combine($keys, array_values($key));
			}
		}else{
			$cb[]	= $this->key($key);

			if(!$gnd){
				$cb[]	= array_shift($args);
			}
		}

		$cb[]	= $this->group;

		if(!$gnd){
			$cb[]	= (int)(array_shift($args)) ?: $this->time;
		}

		$result	= call_user_func('wp_cache_'.$method, ...$cb);

		if($result && $method == 'get_multiple'){
			$map	= array_combine($keys, $key);
			$data	= [];

			foreach($result as $k => $v){
				if($v !== false){
					$key	= $map[$k];

					$data[$key]	= $v;
				}
			}

			return $data;
		}

		return $result;
	}

	protected function key($key){
		return implode(':', array_filter([$this->prefix, $key]));
	}

	public function get_with_cas($key, &$cas_token){
		return wp_cache_get_with_cas($this->key($key), $this->group, $cas_token);
	}

	public function cache_get_with_cas($key, &$cas_token){
		return $this->get_with_cas($key, $cas_token);
	}

	public static function get_instance($group, $args=[]){
		$name	= $group.(!empty($args['prefix']) ? ':'.$args['prefix'] : '');
		$object	= self::instance_exists($name);

		if(!$object && is_array($args)){
			if(array_pull($args, 'global')){
				wp_cache_add_global_groups($group);
			}

			$args['group']	= $group;
			$args['time']	= array_get($args, 'time') ?: DAY_IN_SECONDS;

			$object	= self::add_instance($name, new self($args));
		}

		return $object;
	}

	/* HTML 片段缓存
	Usage:

	if (!WPJAM_Cache::output('unique-key')) {
		functions_that_do_stuff_live();
		these_should_echo();
		WPJAM_Cache::store(3600);
	}
	*/
	public static function output($key) {
		$output	= get_transient($key);

		if(!empty($output)) {
			echo $output;
			return true;
		}else{
			ob_start();
			return false;
		}
	}

	public static function store($key, $cache_time='600') {
		$output = ob_get_flush();
		set_transient($key, $output, $cache_time);
		echo $output;
	}
}

class IP{
	private static $ip = null;
	private static $fp = null;
	private static $offset = null;
	private static $index = null;
	private static $cached = [];

	public static function find($ip){
		if (empty( $ip ) === true) {
			return 'N/A';
		}

		$nip	= gethostbyname($ip);
		$ipdot	= explode('.', $nip);

		if ($ipdot[0] < 0 || $ipdot[0] > 255 || count($ipdot) !== 4) {
			return 'N/A';
		}

		if (isset( self::$cached[$nip] ) === true) {
			return self::$cached[$nip];
		}

		if (self::$fp === null) {
			self::init();
		}

		$nip2 = pack('N', ip2long($nip));

		$tmp_offset	= (int) $ipdot[0] * 4;
		$start		= unpack('Vlen',
			self::$index[$tmp_offset].self::$index[$tmp_offset + 1].self::$index[$tmp_offset + 2].self::$index[$tmp_offset + 3]);

		$index_offset = $index_length = null;
		$max_comp_len = self::$offset['len'] - 1024 - 4;
		for ($start = $start['len'] * 8 + 1024; $start < $max_comp_len; $start += 8) {
			if (self::$index[$start].self::$index[$start+1].self::$index[$start+2].self::$index[$start+3] >= $nip2) {
				$index_offset = unpack('Vlen',
					self::$index[$start+4].self::$index[$start+5].self::$index[$start+6]."\x0");
				$index_length = unpack('Clen', self::$index[$start+7]);

				break;
			}
		}

		if ($index_offset === null) {
			return 'N/A';
		}

		fseek(self::$fp, self::$offset['len'] + $index_offset['len'] - 1024);

		self::$cached[$nip] = explode("\t", fread(self::$fp, $index_length['len']));

		return self::$cached[$nip];
	}

	private static function init(){
		if(self::$fp === null){
			self::$ip = new self();

			self::$fp = fopen(WP_CONTENT_DIR.'/uploads/17monipdb.dat', 'rb');
			if (self::$fp === false) {
				throw new Exception('Invalid 17monipdb.dat file!');
			}

			self::$offset = unpack('Nlen', fread(self::$fp, 4));
			if (self::$offset['len'] < 4) {
				throw new Exception('Invalid 17monipdb.dat file!');
			}

			self::$index = fread(self::$fp, self::$offset['len'] - 4);
		}
	}

	public function __destruct(){
		if(self::$fp !== null){
			fclose(self::$fp);
		}
	}
}