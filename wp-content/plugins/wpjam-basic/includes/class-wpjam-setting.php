<?php
class WPJAM_Setting extends WPJAM_Args{
	use WPJAM_Instance_Trait;

	public function __call($method, $args){
		if(str_contains($method, '_setting')){
			$values	= $this->get_option();
			$name	= array_shift($args);

			if($method == 'get_setting'){
				if($name){
					if(is_array($name)){
						$name	= array_filter($name);

						return array_combine($name, array_map(fn($n) => $this->get_setting($n), $name));
					}else{
						if($values && is_array($values) && isset($values[$name])){
							$value	= $values[$name];

							if(is_wp_error($value)){
								return null;
							}elseif(is_string($value)){
								return str_replace("\r\n", "\n", trim($value));
							}

							return $value;
						}else{
							return null;
						}
					}
				}

				return $values;
			}

			if($method == 'update_setting'){
				if(is_array($name)){
					$values	= array_replace($values, $name);
				}else{
					$values[$name]	= array_shift($args);
				}
			}else{
				$values	= array_except($values, $name);
			}

			return $this->update_option($values);
		}

		$cb_args	= $this->type == 'blog_option' ? [$this->blog_id] : [];
		$cb_args[]	= $this->name;

		if($method == 'get_option'){
			if(wp_installing() && $this->option !== null){
				return $this->option;
			}

			$cb_args[]	= null;
		}elseif(in_array($method, ['add_option', 'update_option'])){
			$value		= array_shift($args);
			$cb_args[]	= $value ? $this->sanitize_option($value) : $value;
		}

		$callback	= str_replace('option', $this->type, $method);
		$result		= call_user_func($callback, ...$cb_args);

		if($method == 'get_option'){
			if(is_null($result)){
				$result	= $args ? $args[0] : [];
			}else{
				$result	= $this->sanitize_option($result);
			}

			if(wp_installing()){
				$this->option	= $result;
			}
		}else{
			if(wp_installing()){
				unset($this->option);
			}
		}

		return $result;
	}

	public static function get_instance($type='', $name='', $blog_id=0){
		if(!in_array($type, ['option', 'site_option']) || !$name){
			return null;
		}

		$key	= $type.':'.$name;

		if(is_multisite() && $type == 'option'){
			$blog_id	= (int)$blog_id ?: get_current_blog_id();
			$key		.= ':'.$blog_id;
			$type		= 'blog_option';
		}

		return self::instance_exists($key) ?: self::add_instance($key, new static([
			'type'		=> $type,
			'name'		=> $name,
			'blog_id'	=> $blog_id
		]));
	}

	public static function sanitize_option($value){
		if(is_wp_error($value)){
			return [];
		}

		return $value ?: [];
	}

	public static function parse_json_module($args){
		$option		= array_get($args, 'option_name');

		if(!$option){
			return null;
		}

		$setting	= array_get($args, 'setting_name');
		$setting	= $setting ?? array_get($args, 'setting');
		$output		= array_get($args, 'output') ?: ($setting ?: $option);
		$object 	= WPJAM_Option_Setting::get($option);
		$value		= $object ? $object->prepare() : wpjam_get_option($option);

		if($setting){
			$value	= $value[$setting] ?? null;
		}

		return [$output	=> $value];
	}
}

/**
* @config menu_page, admin_load, register_json, init, loaded, orderby
**/
#[config('menu_page', 'admin_load', 'register_json', 'init', 'loaded', 'orderby')]
class WPJAM_Option_Setting extends WPJAM_Register{
	public function __call($method, $args){
		if($method == 'get_defaults'){
			return $this->_defaults ??= $this->get_fields('object', true)->get_defaults();
		}elseif($method == 'get_site_setting'){
			$name	= array_shift($args);

			if($this->option_type == 'array'){
				return wpjam_get_site_setting($this->name, $name);
			}else{
				return $name ? wpjam_get_site_option($name, null) : null;
			}
		}elseif($method == 'get_setting'){
			$isset		= fn($value, $name) => $name ? isset($value) : $value !== [];
			$name		= $args[0] ?? '';
			$default	= $args[1] ?? ($name ? null : []);
			$blog_id	= $args[2] ?? 0;

			if($this->option_type == 'array'){
				$value	= wpjam_get_setting($this->name, $name, $blog_id);
			}else{
				$value	= wpjam_get_option($name, $blog_id, null);
			}

			if(!$isset($value, $name)){
				if($this->site_default && is_multisite()){
					$value	= $this->get_site_setting($name);
				}
			}

			if(!$isset($value, $name)){
				if($default !== $value){
					return $default;
				}

				if($this->field_default){
					$defaults	= $this->get_defaults();

					return $name ? array_get($defaults, $name) : $defaults;
				}
			}

			return $value;
		}elseif(in_array($method, ['update_setting', 'delete_setting'])){
			return call_user_func('wpjam_'.$method, $this->name, ...$args);			
		}
	}

	protected function filter_args(){
		return $this->args;
	}

	public function get_arg($key, $default=null, $should_callback=true){
		$value	= parent::get_arg($key, $default, $should_callback);

		if($value && $key == 'menu_page'){
			if(is_network_admin() && !$this->site_default){
				return;
			}

			if(wp_is_numeric_array($value)){
				foreach($value as &$m){
					if(!empty($m['tab_slug'])){
						if(empty($m['plugin_page'])){
							$m	= null;
						}
					}elseif(!empty($m['menu_slug']) && $m['menu_slug'] == $this->name){
						$m	= wp_parse_args($m, ['menu_title'=>$this->title]);
					}
				}

				return array_filter($value);
			}else{
				if(!empty($value['tab_slug'])){
					if(empty($value['plugin_page'])){
						return;
					}

					$defaults	= ['title'=>$this->title];
				}else{
					$defaults	= ['menu_slug'=>$this->name, 'menu_title'=>$this->title];
				}

				return wp_parse_args($value, $defaults);
			}
		}

		return $value;
	}

	public function get_current(){
		return self::get_sub(self::generate_sub_name()) ?: $this;
	}

	protected function get_sections($get_subs=false, $filter=true){
		$sections	= $this->get_arg('sections');

		if(!is_null($sections)){
			$sections	= is_array($sections) ? $sections : [];
		}else{
			$fields		= $this->get_arg('fields', null, false);

			if(!is_null($fields)){
				$id			= $this->sub_name ?: $this->name;
				$sections	= [$id => [
					'title'		=> $this->title, 	
					'fields'	=> $fields
				]];
			}else{
				$sections	= [];
			}
		}

		foreach($sections as $id => &$section){
			if(is_array($section)){
				$section['fields']	= $section['fields'] ?? [];

				if(is_callable($section['fields'])){
					$section['fields']	= call_user_func($section['fields'], $id, $this->name);
				}
			}else{
				unset($sections[$id]);
			}
		}

		if($get_subs){
			foreach($this->get_subs() as $sub){
				$sections	= array_merge($sections, $sub->get_sections(false, false));
			}
		}

		return $filter ? WPJAM_Option_Section::filter($sections, $this->name) : $sections;
	}

	public function get_fields($output='', $get_subs=true){
		$fields	= array_merge(...array_values(wp_list_pluck($this->get_sections($get_subs), 'fields')));

		return $output == '' ? $fields : wpjam_fields($fields);
	}

	public function prepare(){
		return $this->get_fields('object', true)->prepare(['value_callback'=>[$this, 'value_callback']]);
	}

	public function validate($value){
		return $this->get_fields('object', false)->validate($value);
	}

	public function value_callback($name=''){
		return is_network_admin() ? $this->get_site_setting($name) : $this->get_setting($name);
	}

	public function register_settings(){
		if($this->capability && $this->capability != 'manage_options'){
			add_filter('option_page_capability_'.$this->option_page, [$this, 'filter_capability']);
		}

		$args		= ['sanitize_callback'	=> [$this, 'sanitize_callback']];
		$settings	= [];

		if($this->option_type == 'single'){
			foreach($this->get_sections() as $section){
				foreach(WPJAM_Fields::flatten($section['fields']) as $key => $field){
					$settings[$key]	= array_merge($args, ['field'=>$field]);

					register_setting($this->option_group, $key, $settings[$key]);
				}
			}
		}else{
			$settings[$this->name]	= array_merge($args, ['type'=>'object']);

			register_setting($this->option_group, $this->name, $settings[$this->name]);
		}

		return $settings;
	}

	public function render_sections($tab_page=false){
		$sections	= $this->get_sections();

		$count	= count($sections);
		$nav	= ($count > 1 && !$tab_page) ? wpjam_tag('ul') : null;
		$nonce	= wp_create_nonce($this->option_group);
		$form	= wpjam_tag('form', ['action'=>'options.php', 'method'=>'POST', 'id'=>'wpjam_option', 'data'=>['nonce'=>$nonce]]);

		foreach($sections as $id => $section){
			$tab	= wpjam_tag();

			if($count > 1){
				if(!$tab_page){
					$tab	= wpjam_tag('div', ['id'=>'tab_'.$id]);
					$attr	= !empty($section['show_if']) ? ['data-show_if'=>$section['show_if']] : [];

					$nav->append([$section['title'], 'a', ['class'=>'nav-tab', 'href'=>'#tab_'.$id]], 'li', $attr);
				}

				if(!empty($section['title'])){
					$tab->append($section['title'], ($tab_page ? 'h3' : 'h2'));
				}
			}

			if(!empty($section['callback'])) {
				$tab->append(wpjam_ob_get_contents($section['callback'], $section));
			}

			if(!empty($section['summary'])) {
				$tab->append(wpautop($section['summary']));
			}

			$tab->append(wpjam_fields($section['fields'])->render(['value_callback'=>[$this, 'value_callback']]));

			$form->append($tab);
		}

		$button	= wpjam_tag('p', ['submit'], get_submit_button('', 'primary', 'option_submit', false, ['data-action'=>'save']));

		if($this->reset){
			$button->append(get_submit_button('重置选项', 'secondary', 'option_reset', false, ['data-action'=>'reset']));
		}

		$form->append($button);

		return $nav ? $form->before($nav, 'h2', ['nav-tab-wrapper', 'wp-clearfix'])->wrap('div', ['tabs']) : $form;
	}

	public function sanitize_callback($value){
		try{
			$action	= wpjam_get_post_parameter('option_action');

			if($this->option_type == 'array'){
				$option		= $this->name;
				$current	= $this->value_callback();
				$value		= $this->validate($value) ?: [];

				if($action == 'reset'){
					$value	= diff_deep($current, $value);
				}else{
					$value	= array_merge($current, $value);
					$value	= filter_null($value, true);
					$result	= $this->call_method('sanitize_callback', $value, $option);
					$result	= wpjam_throw_if_error($result);

					if(!is_null($result)){
						$value	= $result;
					}
				}
			}else{
				$option		= str_replace('sanitize_option_', '', current_filter());
				$registered	= get_registered_settings();

				if(!isset($registered[$option])){
					return $value;
				}

				$fields	= [$option=>$registered[$option]['field']];
				$value	= wpjam_fields($fields)->validate([$option=>$value]);
				$value	= $value[$option] ?? null;
			}

			return $value;
		}catch(WPJAM_Exception $e){
			add_settings_error($option, $e->get_error_code(), $e->get_error_message());

			return $this->option_type == 'array' ? $current : get_option($option);
		}
	}

	public function ajax_response(){
		if(!check_ajax_referer($this->option_group, false, false)){
			wp_die('invalid_nonce');
		}

		if(!current_user_can($this->capability)){
			wp_die('access_denied');
		}

		$action	= wpjam_get_post_parameter('option_action');

		foreach($this->register_settings() as $option => $args){
			$option = trim($option);

			if($this->option_type == 'array'){
				$value	= wpjam_get_data_parameter();
			}else{
				if($action == 'reset'){
					delete_option($option); continue;
				}

				$value	= wpjam_get_data_parameter($option);
			}

			if($this->update_callback){
				if(!is_callable($this->update_callback)){
					wp_die('无效的回调函数');
				}

				call_user_func($this->update_callback, $option, $value, is_network_admin());
			}else{
				$callback	= is_network_admin() ? 'update_site_option' : 'update_option';

				if($this->option_type == 'array'){
					$callback	= 'wpjam_'.$callback;
				}else{
					$value		= is_wp_error($value) ? null : $value;
				}

				call_user_func($callback, $option, $value);
			}
		}

		$errmsg = '';

		foreach(get_settings_errors() as $key => $details){
			if(in_array($details['type'], ['updated', 'success', 'info'])){
				continue;
			}

			$errmsg	.= $details['message'].'&emsp;';
		}

		if($errmsg){
			wp_die($errmsg);
		}

		$response	= $this->response ?? ($this->ajax ? $action : 'redirect');
		$errmsg		= $action == 'reset' ? '设置已重置。' : '设置已保存。';

		return ['type'=>$response, 'errmsg'=>$errmsg];
	}

	public function filter_capability(){
		return $this->capability;
	}

	public static function generate_sub_name($args=null){
		$args	??= $GLOBALS;
		$name	= $args['plugin_page'] ?? '';

		if($name && !empty($args['current_tab'])){
			$name	.= ':'.$args['current_tab'];
		}

		return $name;
	}

	public static function create($name, $args){
		$args	= is_callable($args) ? call_user_func($args, $name) : $args;
		$args	= apply_filters('wpjam_register_option_args', $args, $name);
		$args	= wp_parse_args($args, [
			'option_group'	=> $name, 
			'option_page'	=> $name, 
			'option_type'	=> 'array',
			'capability'	=> 'manage_options',
			'ajax'			=> true,
		]);

		$except	= ['title', 'model', 'menu_page', 'admin_load', 'plugin_page', 'current_tab'];
		$sub	= self::generate_sub_name($args);
		$object	= self::get($name);

		if($object){
			if($sub){
				$object->update_args(array_except($args, $except));

				return $object->register_sub($sub, $args);
			}else{
				if(is_null($object->primary)){
					return self::re_register($name, array_merge($object->to_array(), $args, ['primary'=>true]));
				}else{
					trigger_error('option_setting'.'「'.$name.'」已经注册。'.var_export($args, true));

					return $object;
				}
			}
		}else{
			if($args['option_type'] == 'array' && !doing_filter('sanitize_option_'.$name)){
				if(is_null(get_option($name, null))){
					add_option($name, []);
				}
			}

			if($sub){
				$object	= self::register($name, array_except($args, $except));

				return $object->register_sub($sub, $args);
			}else{
				return self::register($name, array_merge($args, ['primary'=>true]));
			}
		}
	}
}


/**
* @config menu_page, admin_load, init, loaded, orderby
**/
#[config('menu_page', 'admin_load', 'init', 'loaded', 'orderby')]
class WPJAM_Option_Section extends WPJAM_Register{
	public static function filter($sections, $option_name){
		foreach(self::get_by('option_name', $option_name) as $object){
			$object_sections	= $object->get_arg('sections');
			$object_sections	= is_array($object_sections) ? $object_sections : [];

			foreach($object_sections as $id => $section){
				if(!empty($section['fields']) && is_callable($section['fields'])){
					$section['fields']	= call_user_func($section['fields'], $id, $option_name);
				}

				if(isset($sections[$id])){
					$sections[$id]	= merge_deep($sections[$id], $section);
				}else{
					if(isset($section['title']) && isset($section['fields'])){
						$sections[$id]	= $section;
					}
				}
			}
		}

		return apply_filters('wpjam_option_setting_sections', $sections, $option_name);
	}

	public static function add($option_name, ...$args){
		if(is_array($args[0])){
			$args	= $args[0];
		}else{
			$section	= isset($args[1]['fields']) ? $args[1] : ['fields'=>$args[1]];
			$args		= [$args[0] => $section];
		}

		if(!isset($args['model']) && !isset($args['sections'])){
			$args	= ['sections'=>$args];
		}

		return self::register(array_merge($args, ['option_name'=>$option_name]));
	}
}

class WPJAM_Option_Model{
	protected static function call_method($method, ...$args){
		$object	= self::get_object();	

		return $object ? call_user_func_array([$object, $method], $args) : null;
	}

	protected static function get_object(){
		return WPJAM_Option_Setting::get_by_model(get_called_class(), 'WPJAM_Option_Model');
	}

	public static function get_setting($name='', $default=null){
		return self::call_method('get_setting', $name) ?? $default;
	}

	public static function update_setting(...$args){
		return self::call_method('update_setting', ...$args);
	}

	public static function delete_setting($name){
		return self::call_method('delete_setting', $name);
	}
}

class WPJAM_Extend extends WPJAM_Args{
	public function load(){
		if($this->name && is_admin()){
			if($this->sitewide && is_network_admin()){
				$this->summary	.= $this->summary ? '，' : '';
				$this->summary	.= '在管理网络激活将整个站点都会激活！';
			}

			wpjam_register_option($this->name, array_merge($this->to_array(), ['model'=>$this, 'ajax'=>false]));
		}

		foreach(array_keys($this->get_data()) as $extend){
			$file	= $this->parse_file($extend);

			if($file){
				include $file;
			}
		}
	}

	private function parse_file($extend){
		if(!$extend || in_array($extend, ['.', '..'])){
			return;
		}

		$file	= '';

		if($this->hierarchical){
			if(is_dir($this->dir.'/'.$extend)){
				$file	= $extend.'/'.$extend.'.php';
			}
		}else{
			if($this->name){
				$file	= $extend.'.php';
			}else{
				if(pathinfo($extend, PATHINFO_EXTENSION) == 'php'){
					$file	= $extend;
				}
			}
		}

		return ($file && is_file($this->dir.'/'.$file)) ? $this->dir.'/'.$file : '';
	}

	private function get_data($type=''){
		if($this->name){
			if(!$type){
				$data	= $this->get_data('option');

				if($this->sitewide && is_multisite()){
					$data	= array_merge($data, $this->get_data('site_option'));
				}
			}else{
				$data	= call_user_func('wpjam_get_'.$type, $this->name);
				$data	= $data ? array_filter($data) : [];
				$data	= $this->sanitize_callback($data);
			}
		}else{
			$data	= [];

			if($handle = opendir($this->dir)){
				while(false !== ($extend = readdir($handle))){
					if(!in_array($extend, ['.', '..'])){
						$data[$extend]	= true;
					}
				}

				closedir($handle);
			}
		}

		return $data;
	}

	public function get_fields(){
		$fields	= [];
		$values	= $this->get_data('option');

		if(is_multisite() && $this->sitewide){
			$sitewide	= $this->get_data('site_option');

			if(is_network_admin()){
				$values	= $sitewide;
			}
		}

		if($handle = opendir($this->dir)){
			while(false  !== ($extend = readdir($handle))){
				if(!$this->hierarchical){
					$extend	= wpjam_remove_postfix($extend, '.php');
				}

				$file	= $this->parse_file($extend);
				$data	= $this->get_file_data($file);

				if($data && ($data['Name'] || $data['PluginName'])){
					if(is_multisite() && $this->sitewide && !is_network_admin()){
						if(!empty($sitewide[$extend])){
							continue;
						}
					}

					$title	= $data['Name'] ?: $data['PluginName'];
					$title	= $data['URI'] ? '<a href="'.$data['URI'].'" target="_blank">'.$title.'</a>' : $title;
					$value	= !empty($values[$extend]);

					$fields[$extend] = ['title'=>$title, 'type'=>'checkbox', 'value'=>$value, 'description'=>$data['Description']];
				}
			}

			closedir($handle);
		}

		return wp_list_sort($fields, 'value', 'DESC', true);
	}

	public function sanitize_callback($data){
		if($data && !$this->hierarchical){
			$update	= false;
			$data	= array_filter($data);
			$keys	= array_keys($data);

			foreach($keys as &$key){
				if(str_ends_with($key, '.php')){
					$key	= wpjam_remove_postfix($key, '.php');
					$update	= true;
				}
			}

			if($update){
				$keys	= array_unique($keys);
				$data	= array_fill_keys($keys, true);
			}
		}

		return $data;
	}

	public static function get_file_data($file){
		return $file ? get_file_data($file, [
			'Name'			=> 'Name',
			'URI'			=> 'URI',
			'PluginName'	=> 'Plugin Name',
			'PluginURI'		=> 'Plugin URI',
			'Version'		=> 'Version',
			'Description'	=> 'Description'
		]) : [];
	}

	public static function get_file_summay($file){
		$data	= self::get_file_data($file);

		foreach(['URI', 'Name'] as $key){
			if(empty($data[$key])){
				$data[$key]	= $data['Plugin'.$key] ?? '';
			}
		}

		$summary	= str_replace('。', '，', $data['Description']);
		$summary	.= '详细介绍请点击：<a href="'.$data['URI'].'" target="_blank">'.$data['Name'].'</a>。';

		return $summary;
	}

	public static function create($dir, $args=[], $name=''){
		if($dir && is_dir($dir)){
			$hook	= array_pull($args, 'hook');
			$object	= new self(array_merge($args, ['dir'=>$dir, 'name'=>$name]));

			if($hook){
				add_action($hook, [$object, 'load'], ($object->priority ?? 10));
			}else{
				$object->load();
			}
		}
	}
}

class WPJAM_Notice extends WPJAM_Args{
	use WPJAM_Instance_Trait;

	public function __call($method, $args){
		if(str_ends_with($method, '_option')){
			return wpjam_call_for_blog($this->id, $method, 'wpjam_notices', ...$args);
		}elseif(str_ends_with($method, '_user_meta')){
			return wpjam_call($method, $this->id, 'wpjam_notices', ...$args);
		}

		$data	= $this->get_items();

		if($method == 'insert'){
			$item	= is_array($args[0]) ? $args[0] : ['notice'=>$args[0]];
			$key	= array_get($item, 'key') ?: md5(maybe_serialize($item));
			$item	= wp_parse_args($item, ['notice'=>'', 'type'=>'error', 'time'=>time()]);
			$data	= array_merge([$key=>$item], $data);
		}else{
			if(!isset($data[$args[0]])){
				return true;
			}

			$data	= array_except($data, $args[0]);
		}

		return $this->update_items($data);
	}

	protected function get_items(){
		if($this->type == 'user'){
			$data	= $this->get_user_meta(true) ?: [];
		}else{
			$data	= $this->get_option() ?: [];
		}

		return array_filter($data, fn($item) => $item['time'] > time() - MONTH_IN_SECONDS * 3 && trim($item['notice']));
	}

	protected function update_items($data){
		if($this->type == 'user'){
			return $data ? $this->update_user_meta($data) : $this->delete_user_meta();
		}else{
			return $data ? $this->update_option($data) : $this->delete_option();
		}
	}

	public function render_items(){
		foreach($this->get_items() as $key => $item){
			$item	= wp_parse_args($item, [
				'type'		=> 'info',
				'class'		=> 'is-dismissible',
				'admin_url'	=> '',
				'notice'	=> '',
				'title'		=> '',
				'modal'		=> 0,
			]);

			$notice	= trim($item['notice']);

			if($item['admin_url']){
				$notice	.= $item['modal'] ? "\n\n" : ' ';
				$notice	.= '<a style="text-decoration:none;" href="'.add_query_arg(['notice_key'=>$key, 'notice_type'=>$this->type], home_url($item['admin_url'])).'">点击查看<span class="dashicons dashicons-arrow-right-alt"></span></a>';
			}

			$notice	= wpautop($notice).wpjam_get_page_button('delete_notice', ['data'=>['notice_key'=>$key, 'notice_type'=>$this->type]]);

			if($item['modal']){
				if(empty($modal)){	// 弹窗每次只显示一条
					$modal	= $notice;
					$title	= $item['title'] ?: '消息';

					echo '<div id="notice_modal" class="hidden" data-title="'.esc_attr($title).'">'.$modal.'</div>';
				}
			}else{
				echo '<div class="notice notice-'.$item['type'].' '.$item['class'].'">'.$notice.'</div>';
			}
		}
	}

	public static function render(){
		self::ajax_delete();

		(self::get_instance('user'))->render_items();

		if(current_user_can('manage_options')){
			(self::get_instance('admin'))->render_items();
		}
	}

	public static function ajax_delete(){
		$type	= wpjam_get_data_parameter('notice_type');
		$key	= wpjam_get_data_parameter('notice_key');

		if($key){
			if($type == 'admin' && !current_user_can('manage_options')){
				wpjam_send_error_json('bad_authentication');
			}

			(self::get_instance($type))->delete($key);

			wpjam_send_json();
		}
	}

	public static function get_instance($type='', $id=0){
		$type	= $type == 'user' ? 'user' : 'admin';
		$id		= (int)$id ?: ($type == 'user' ? get_current_user_id() : get_current_blog_id());

		return self::instance_exists($type.':'.$id) ?: self::add_instance($type.':'.$id, new static(['type'=>$type, 'id'=>$id]));
	}

	public static function add($item){	// 兼容函数
		return wpjam_add_admin_notice($item);
	}
}