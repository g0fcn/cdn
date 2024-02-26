<?php
// 导入设置
require_once 'setting.php';

// ChatGPT 3.5 配置
$api_key = get_option('chatgpt_api_key');
$api_url = get_option('chatgpt_api_url');
$delay_reply =get_option('delay_reply',0);


// 检测评论并回复
function chatgpt_comment_reply($comment_ID, $comment_approved) {
    global $api_key, $api_url;
      

    // 检查自动回复功能是否启用
    $auto_reply_enabled = get_option('chatgpt_auto_reply_enabled');
    if ($auto_reply_enabled != 'on') {
        return; // 自动回复功能未启用，直接返回
    }

    // 只在评论被批准后进行回复
    if ($comment_approved == 1) {
        $comment = get_comment($comment_ID);
        $comment_content = $comment->comment_content;

        // 检查是否已生成回复
        $has_reply = get_comment_meta($comment_ID, 'has_reply', true);
        if ($has_reply) {
            return; // 已生成回复，不再进行回复
        }

        // 使用延迟函数延迟x秒后执行生成回复
        wp_schedule_single_event(time() +$delay_reply, 'chatgpt_generate_reply', array($comment_content, $comment_ID, $api_key, $api_url));
    }
}

// 生成回复
function chatgpt_generate_reply($comment_content, $comment_ID, $api_key, $api_url) {
    // 生成回复内容
    $reply = chatgpt_generate_reply_content($comment_content, $api_key, $api_url);

    // 检查是否生成了有效的回复
    if (!empty($reply)) {
        $author_id = get_option('author_id'); // 机器人作者的用户 ID
        $reply_data = array(
            'comment_post_ID' => get_comment($comment_ID)->comment_post_ID,
            'comment_author' => get_option('author_name'),
            'comment_author_email' => get_userdata($author_id)->user_email,
            'comment_author_url' => get_userdata($author_id)->user_url,
            'comment_content' => $reply,
            'comment_type' => '',
            'comment_parent' => $comment_ID,
            'user_id' => $author_id,
            'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
            'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
            'comment_date' => current_time('mysql'),
            'comment_approved' => 1
        );

        // 移除回复评论时的动作钩子
        remove_action('wp_insert_comment', 'chatgpt_comment_reply', 10, 2);

        // 添加回复评论
        $reply_comment_ID = wp_insert_comment($reply_data);

        // 添加回复评论后，重新添加回复评论的动作钩子
        add_action('wp_insert_comment', 'chatgpt_comment_reply', 10, 2);

        // 标记已生成回复的评论
        update_comment_meta($comment_ID, 'has_reply', true);

        // 关联回复评论与原始评论的关系
        add_comment_meta($reply_comment_ID, 'original_comment', $comment_ID);
    }
}
// 注册生成回复的计划事件
add_action('chatgpt_generate_reply', 'chatgpt_generate_reply', 10, 4);

// 生成回复内容
function chatgpt_generate_reply_content($comment_content, $api_key, $api_url) {
    // ChatGPT 3.5 配置
    $text = array(
       array('role' => 'system', 'content' => '你是晓白BOT，模型是3.5，尽可能简短的回答'),
         array('role' => 'user', 'content' => mb_substr($comment_content,0,100)), //限制发送文本字数100字
    );

    // 调用 ChatGPT 3.5 API
    $reply = completions($api_key, $api_url, $text, false);

    // 提取回复内容
    if ($reply != "对不起，我不知道该怎么回答。") {
        return $reply;
    } else {
        return '';
    }
}

// 完整的 completions 函数
function completions($api_key, $api_url, $text) {
    $header = array(
        'Authorization: Bearer ' . $api_key,
        'Content-type: application/json',
    );
    
    $params = json_encode(array(
        'messages' => $text,
        'model' => 'gpt-3.5-turbo',
    ));

    // 调用 ChatGPT 3.5 API
    $curl = curl_init($api_url . '/v1/chat/completions');
    $options = array(
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_RETURNTRANSFER => true,
    );

    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);

    $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $text = "服务器连接错误，请稍后再试！";

    if (200 == $httpcode || 429 == $httpcode || 401 == $httpcode || 400 == $httpcode) {
        $json_array = json_decode($response, true);
        if (isset($json_array['choices'][0]['message']['content'])) {
            $text = str_replace("\\n", "\n", $json_array['choices'][0]['message']['content']);
            
        } elseif (isset($json_array['error']['message'])) {
            $text = $json_array['error']['message'];
        } else {
            $text = "对不起，我不知道该怎么回答。";
        }
    }
    return $text;
}

// 添加评论回调
add_action('wp_insert_comment', 'chatgpt_comment_reply', 10, 2);
