<?php
/*
Plugin Name: wp-ai-comment
Description: 自动对用户的评论用GPT进行回复
Version: 1.2
Author: 晓白
Author URI: https://www.xbnb.cn
Description：晓白版权所有，晓白QQ：3523826768，保留版权是一种美德，转载请保留版权，谢谢
*/

// 设置页面
require_once plugin_dir_path(__FILE__) . 'core.php';

// 注册设置页面
function chatgpt_add_settings_menu() {
    add_options_page(
    'wp-ai-comment',
    'wp-ai-comment',
    'manage_options',
    'chatgpt-settings',
    'chatgpt_render_settings_page'
);

}
add_action('admin_menu', 'chatgpt_add_settings_menu');

// 渲染设置页面
function chatgpt_render_settings_page() {
    ?><div class="wrap">
    <h1>wp-ai-comment设置</h1>
    <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/main.css', __FILE__); ?>">
    <form method="post" action="options.php">
        <?php
        settings_fields('chatgpt-settings-group');
        do_settings_sections('chatgpt-settings');
        ?>
        <h2>自动回复设置</h2>
        <label>
            <input type="checkbox" name="chatgpt_auto_reply_enabled" <?php checked(get_option('chatgpt_auto_reply_enabled'), 'on'); ?>>
            启用GPT自动回复
        </label>
        <?php submit_button(); ?>
    </form>
</div>
<?php
}

// 注册设置字段
function chatgpt_register_settings() {
register_setting('chatgpt-settings-group', 'chatgpt_auto_reply_enabled');
register_setting('chatgpt-settings-group', 'chatgpt_api_key');
register_setting('chatgpt-settings-group', 'chatgpt_api_url');
register_setting('chatgpt-settings-group', 'author_id');
register_setting('chatgpt-settings-group', 'author_name');
register_setting('chatgpt-settings-group', 'delay_reply');
}
add_action('admin_init', 'chatgpt_register_settings');

// 添加设置字段
function chatgpt_add_settings_fields() {
add_settings_section(
'chatgpt-settings-section',
'wp自动回复设置',
'chatgpt_settings_section_callback',
'chatgpt-settings'
);
add_settings_field(
'chatgpt-api-key',
'API密钥',
'chatgpt_api_key_callback',
'chatgpt-settings',
'chatgpt-settings-section'
);
add_settings_field(
'delay_reply',
'延迟回复',
'chatgpt_delay_reply_callback',
'chatgpt-settings',
'chatgpt-settings-section'
);
add_settings_field(
'author_name',
'gpt名称',
'chatgpt_author_name_callback',
'chatgpt-settings',
'chatgpt-settings-section'
);
add_settings_field(
'author_id',
'用户id',
'chatgpt_author_id_callback',
'chatgpt-settings',
'chatgpt-settings-section'
);

add_settings_field(
'chatgpt-api-url',
'chatgpt反代地址',
'chatgpt_api_url_callback',
'chatgpt-settings',
'chatgpt-settings-section'
);
}
add_action('admin_init', 'chatgpt_add_settings_fields');

// 设置字段回调函数
function chatgpt_settings_section_callback() {
echo '请在下面的字段中输入您的ChatGPT设置';
}

function chatgpt_api_key_callback() {
    $api_key = get_option('chatgpt_api_key');
    echo '<input type="text" name="chatgpt_api_key" value="' . esc_attr($api_key) . '" pattern="^sk.*" title="密钥必须以 \'sk\'开头." required />';
    echo '<p><a href="https://faka.xbnb.cn">gpt密钥购买</a></p>';
}
function chatgpt_author_name_callback() {
    $author_name = get_option('author_name');
    echo '<input type="text" name="author_name" value="' . esc_attr($author_name) . '" />';
    echo '<p>chatgpt的名字只有在id没有设置或者设置的id用户不存在时才会生效</p>';
}
function chatgpt_delay_reply_callback() {
    $delay_reply = get_option('delay_reply');
    echo '<input type="text" name="delay_reply" value="' . esc_attr($delay_reply) . '" />';
    echo '<p>延迟多久回复,单位s</p>';
}



function chatgpt_api_url_callback() {
    $api_url = get_option('chatgpt_api_url');
    if (empty($api_url)) {
        $api_url = 'https://api.openai.com'; // 设置默认值
    } else {
        $api_url = rtrim($api_url, '/'); // 移除地址末尾的斜杠
    }
    echo '<input type="text" name="chatgpt_api_url" value="' . esc_attr($api_url) . '" />';
    echo '<p><a href="https://www.xbnb.cn/3329">反代地址分享</a></p>';
}

function chatgpt_author_id_callback() {
    $author_id = get_option('author_id');
    if (empty($author_id) || !is_numeric($author_id)) {
        $author_id = 1; // 设置默认值
    }
    echo '<input type="text" name="author_id" pattern="[0-9]*" value="' . esc_attr($author_id) . '" />';
}

