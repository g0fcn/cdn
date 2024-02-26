<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
session_start();
header("Content-type:text/html;character=utf-8");
require_once('../../../wp-load.php');
date_default_timezone_set('Asia/Shanghai');

if(isset($_GET['ajax'])){
	$error = 0;$msg='';$link='';$jump=0;
	if(!is_user_logged_in()){
		$error = 1;$msg=__('请先登录','erphpdown');
	}
	$postid=isset($_GET['postid']) && is_numeric($_GET['postid']) ?intval($_GET['postid']) :false;
	$postid = $wpdb->escape($postid);
	$index=isset($_GET['index']) && is_numeric($_GET['index']) ? intval($_GET['index']) : '';
	$index = esc_sql($index);
	$index_name = '';
	$index_vip = '';
	$price = '';

	if($postid){

		$ice_data = '';
	    $erphp_down=get_post_meta($postid, 'erphp_down',TRUE);
	    if($erphp_down == 6){
	        if(function_exists('getErphpActLeft')){
	            $ErphpActLeft = getErphpActLeft($postid);
	            if($ErphpActLeft < 1){
	               $error = 1;$msg=__('抱歉，库存不足!','erphpdown');
	            }
	        }else{
	            $error = 1;$msg=__('抱歉，网站未启用【激活码发放】扩展（Erphpdown-基础设置 里的免费扩展）!','erphpdown');
	        }
	        $price=get_post_meta($postid, 'down_price', true);
	        $num = (isset($_GET['num']) && is_numeric($_GET['num']) && floor($_GET['num'])==$_GET['num']) ?$_GET['num'] : 1;
	        $email = isset($_GET['email']) && is_email($_GET['email']) ?$_GET['email'] : '';
	        if(!$email){
	            $error = 1;$msg=__('请填写一个接收卡密的邮箱!','erphpdown');
	        }
	        $ice_data = $email.'|'.$num;
	        $price = $price*$num;

	        if(!$price){
				$error = 1;$msg=__('价格错误','erphpdown');
			}

			if(isset($_SESSION['erphp_promo_code']) && $_SESSION['erphp_promo_code']){
		        $promo = str_replace("\\","", $_SESSION['erphp_promo_code']);
		        $promo_arr = json_decode($promo,true);
		        if($promo_arr['type'] == 1){
		            $promo_money = get_option('erphp_promo_money1');
		            if($promo_money){
		                $price = $price - $promo_money;
		            }
		        }elseif($promo_arr['type'] == 2){
		            $promo_money = get_option('erphp_promo_money2');
		            if($promo_money){
		                $price = $price * 0.1 * $promo_money;
		            }
		        }
		    }

			if(!$error){
				$user_info=wp_get_current_user();
				$okMoney=erphpGetUserOkMoney();
				if(sprintf("%.2f",$okMoney) >= $price && $okMoney > 0 && $price > 0)
				{
					if(erphpSetUserMoneyXiaoFei($price))
					{
						addUserMoneyLog($user_info->ID, '-'.$price, __('购买资源','erphpdown'));
						$ppost = get_post($postid);
						$order_num = 'FK'.date("ymdhis").mt_rand(100,999).mt_rand(100,999).mt_rand(100,999);
				        erphpAddDownloadByWppay($ppost->post_title,$postid,$user_info->ID,$order_num,$price,1,'',$ppost->post_author,'',erphpGetIP());

		    			if(function_exists('doErphpActKa')){
		    				if($num > 1){
		    					$activation_num = '';
		    					for($i=0; $i<$num; $i++){
		    						$anum = doErphpActKa($user_info->ID, $postid, $order_num);
		    						if($anum){
			    						$activation_num .= $anum.'<br>';
			    					}
		    					}
		    				}else{
								$activation_num = doErphpActKa($user_info->ID, $postid, $order_num);
							}

							if($activation_num){
								$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_num='".$order_num."'");
								if($email){
									$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
									wp_mail($email, '【'.$ppost->post_title.'】卡密', '您购买的【'.$ppost->post_title.'】卡密：<br>'.$activation_num.'<br>订单号：<br>'.$order_num, $headers);
								}
							}
						}


						$EPD = new EPD();
						$EPD->doAuthorAff($price, $ppost->post_author);
						$EPD->doAff($price, $user_info->ID);

						if(get_option('erphp_remind')){
							$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
							wp_mail(get_option('admin_email'), '【'.get_bloginfo('name').'】订单提醒 - '.$ppost->post_title, '用户'.$user_info->user_login.'消费'.$price.get_option('ice_name_alipay').'购买了'.$ppost->post_title.' '.$num.'个'.get_permalink($postid).'<br>订单号：<br>'.$order_num, $headers);
						}

						$jump = 2;

					}else{
						$error = 1;$msg=__('系统错误','erphpdown');
					}
				}else{
					$error = 1;$msg=__('余额不足','erphpdown');
				}
		    }
	    }else{

			$days=get_post_meta($postid, 'down_days', true);
			$down_repeat = get_post_meta($postid, 'down_repeat', true);
			$down_only_pay = get_post_meta($postid, 'down_only_pay', true);

			if($down_only_pay){
				$error = 1;$msg=__('不支持余额购买，请直接在线支付购买','erphpdown');
			}

			$user_info=wp_get_current_user();
			if($index){
				$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
			}else{
				$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
			}
			if($days > 0){
				$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($hasdown_info->ice_time)));
				$nowDate = date('Y-m-d H:i:s');
				if(strtotime($nowDate) > strtotime($lastDownDate)){
					$hasdown_info = null;
				}
			}

			if($hasdown_info && !$down_repeat){
				$error = 1;$msg=__('请勿重复购买','erphpdown');
			}

			if($index){
				$urls = get_post_meta($postid, 'down_urls', true);
				if($urls){
					$cnt = count($urls['index']);
					if($cnt){
						for($i=0; $i<$cnt;$i++){
							if($urls['index'][$i] == $index){
		    					$index_name = $urls['name'][$i];
		    					$price = $urls['price'][$i];
		    					$index_vip = $urls['vip'][$i];
		    					break;
		    				}
						}
					}
				}
			}else{
				$price=get_post_meta($postid, 'down_price', true);
				$start_down2 = get_post_meta($postid, 'start_down2',TRUE);
				if($start_down2){
					$price = $price*get_option('ice_proportion_alipay');
				}
			}

			$memberDown=get_post_meta($postid, 'member_down',TRUE);
			if($index_vip){
				$memberDown = $index_vip;
			}
			$hidden=get_post_meta($postid, 'hidden_content', true);
			$start_down=get_post_meta($postid, 'start_down', true);
			$start_down2 = get_post_meta($postid, 'start_down2',TRUE);
			$start_see=get_post_meta($postid, 'start_see', true);
			$start_see2=get_post_meta($postid, 'start_see2', true);
			$down_activation = get_post_meta($postid, 'down_activation', true);
			if(!$price)
			{
				$error = 1;$msg=__('价格错误','erphpdown');
			}

			if($down_activation && function_exists('doErphpAct')){
				$ErphpActLeft = getErphpActLeft($postid);
	            if($ErphpActLeft < 1){
	               $error = 1;$msg=__('抱歉，库存不足!','erphpdown');
	            }
			}
			
			$okMoney=erphpGetUserOkMoney();
			$userType=getUsreMemberType();
			if($memberDown==4 || $memberDown==15 || $memberDown==8 || $memberDown==9 || (($memberDown == 10 || $memberDown == 11 || $memberDown == 12) && !$userType) || ($memberDown == 17 && $userType < 8) || ($memberDown == 18 && $userType < 9) || ($memberDown == 19 && $userType != 10))
			{
				$error = 1;$msg=__('您无权购买此资源','erphpdown');
			}

			if($userType && ($memberDown==2 || $memberDown==13))
			{
				$price=sprintf("%.2f",$price*0.5);
			}
			if($userType && ($memberDown==5 || $memberDown==14))
			{
				$price=sprintf("%.2f",$price*0.8);
			}
			if($userType>=9 && $memberDown==11)
			{
				$price=sprintf("%.2f",$price*0.5);
			}
			if($userType>=9 && $memberDown==12)
			{
				$price=sprintf("%.2f",$price*0.8);
			}

			if(isset($_SESSION['erphp_promo_code']) && $_SESSION['erphp_promo_code']){
		        $promo = str_replace("\\","", $_SESSION['erphp_promo_code']);
		        $promo_arr = json_decode($promo,true);
		        if($promo_arr['type'] == 1){
		            $promo_money = get_option('erphp_promo_money1');
		            if($promo_money){
		                $price = $price - $promo_money;
		            }
		        }elseif($promo_arr['type'] == 2){
		            $promo_money = get_option('erphp_promo_money2');
		            if($promo_money){
		                $price = $price * 0.1 * $promo_money;
		            }
		        }
		    }

			if(!$error){
				if(sprintf("%.2f",$okMoney) >= $price && $okMoney > 0 && $price > 0)
				{
					if(erphpSetUserMoneyXiaoFei($price))
					{
						addUserMoneyLog($user_info->ID, '-'.$price, __('购买资源','erphpdown'));
						$subject   = get_post($postid)->post_title;
						if($index_name){
							$subject .= ' - '.$index_name;
						}
						$postUserId=get_post($postid)->post_author;
						
						if($start_down || $start_down2 || $start_see || $start_see2){
							$result=erphpAddDownload($subject, $postid, $price, 1, '', $postUserId, $index, erphpGetIP());
							if($result)
							{
								if($down_activation && function_exists('doErphpAct')){
									$activation_num = doErphpAct($user_info->ID,$postid);
									if($activation_num){
										$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_url='".$result."'");
										if($user_info->user_email){
											$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
											wp_mail($user_info->user_email, '【'.$subject.'】激活码', '您购买的资源【'.$subject.'】激活码：'.$activation_num, $headers);
										}
									}
								}

								$EPD = new EPD();
								$EPD->doAuthorAff($price, $postUserId);
								$EPD->doAff($price, $user_info->ID);

								if(get_option('erphp_remind')){
									$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
									wp_mail(get_option('admin_email'), '【'.get_bloginfo('name').'】订单提醒 - '.$subject, '用户'.$user_info->user_login.'消费'.$price.get_option('ice_name_alipay').'购买了'.$subject.get_permalink($postid), $headers);
								}

								if($start_down || $start_down2)
								{
									$jump = 1;
			                        $link = constant("erphpdown") . 'download.php?postid=' . $postid.'&index='.$index.'&timestamp='.time();
								}
								elseif($start_see || $start_see2)
								{
									$jump = 2;
								}

								do_action( 'erphpdown_post_checkout', $user_info->ID, $postid, $index, $price, $result );
							}
							else
							{
								$wpdb->query("update $wpdb->iceinfo set ice_get_money=ice_get_money-".$price ." where ice_user_id=".$user_info->ID);
								$error = 1;$msg=__('系统错误','erphpdown');
							}
						}
					}
					else 
					{
						$error = 1;$msg=__('系统错误','erphpdown');
					}
				}
				else 
				{
					$error = 1;$msg=__('余额不足','erphpdown');
				}
			}
		}
	}

	$arr=array(
	    "error"=>$error, 
	    "msg"=>$msg,
	    "jump"=>$jump,
	    "link"=>$link
	); 
	$jarr=json_encode($arr); 
	echo $jarr;
	exit;
}else{

	if(!is_user_logged_in()){
		wp_die(__('请先登录','erphpdown'),__('友情提示','erphpdown'));
	}
	$postid=isset($_GET['postid']) && is_numeric($_GET['postid']) ?intval($_GET['postid']) :false;
	$postid = $wpdb->escape($postid);
	$index=isset($_GET['index']) && is_numeric($_GET['index']) ? intval($_GET['index']) : '';
	$index = esc_sql($index);
	$index_name = '';
	$index_vip = '';
	$price = '';

	if($postid){
		$days=get_post_meta($postid, 'down_days', true);
		$down_repeat = get_post_meta($postid, 'down_repeat', true);
		$down_only_pay = get_post_meta($postid, 'down_only_pay', true);
		if($down_only_pay){
			wp_die(__('不支持余额购买，请直接在线支付购买','erphpdown'),__('友情提示','erphpdown'));
		}
		$user_info=wp_get_current_user();
		if($index){
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
		}else{
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
		}
		if($days > 0){
			$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($hasdown_info->ice_time)));
			$nowDate = date('Y-m-d H:i:s');
			if(strtotime($nowDate) > strtotime($lastDownDate)){
				$hasdown_info = null;
			}
		}
		if($hasdown_info && !$down_repeat){
			wp_die(__('请勿重复购买，请返回原页面刷新页面！','erphpdown'),__('友情提示','erphpdown'));
		}

		if($index){
			$urls = get_post_meta($postid, 'down_urls', true);
			if($urls){
				$cnt = count($urls['index']);
				if($cnt){
					for($i=0; $i<$cnt;$i++){
						if($urls['index'][$i] == $index){
	    					$index_name = $urls['name'][$i];
	    					$price = $urls['price'][$i];
	    					$index_vip = $urls['vip'][$i];
	    					break;
	    				}
					}
				}
			}
		}else{
			$price=get_post_meta($postid, 'down_price', true);
			$start_down2 = get_post_meta($postid, 'start_down2',TRUE);
			if($start_down2){
				$price = $price*get_option('ice_proportion_alipay');
			}
		}

		$memberDown=get_post_meta($postid, 'member_down',TRUE);
		if($index_vip){
			$memberDown = $index_vip;
		}
		$hidden=get_post_meta($postid, 'hidden_content', true);
		$start_down=get_post_meta($postid, 'start_down', true);
		$start_down2 = get_post_meta($postid, 'start_down2',TRUE);
		$start_see=get_post_meta($postid, 'start_see', true);
		$start_see2=get_post_meta($postid, 'start_see2', true);
		$down_activation = get_post_meta($postid, 'down_activation', true);
		if(!$price)
		{
			wp_die(__('价格错误','erphpdown'),__('友情提示','erphpdown'));
		}

		if($down_activation && function_exists('doErphpAct')){
			$ErphpActLeft = getErphpActLeft($postid);
            if($ErphpActLeft < 1){
               wp_die(__('抱歉，库存不足!','erphpdown'),__('友情提示','erphpdown'));
            }
		}
		
		$okMoney=erphpGetUserOkMoney();
		$userType=getUsreMemberType();
		if($memberDown==4 || $memberDown==15 || $memberDown==8 || $memberDown==9 || (($memberDown == 10 || $memberDown == 11 || $memberDown == 12) && !$userType) || ($memberDown == 17 && $userType < 8) || ($memberDown == 18 && $userType < 9) || ($memberDown == 19 && $userType != 10))
		{
			wp_die(__('您无权购买此资源','erphpdown'),__('友情提示','erphpdown'));
		}

		if($userType && ($memberDown==2 || $memberDown==13))
		{
			$price=sprintf("%.2f",$price*0.5);
		}
		if($userType && ($memberDown==5 || $memberDown==14))
		{
			$price=sprintf("%.2f",$price*0.8);
		}
		if($userType>=9 && $memberDown==11)
		{
			$price=sprintf("%.2f",$price*0.5);
		}
		if($userType>=9 && $memberDown==12)
		{
			$price=sprintf("%.2f",$price*0.8);
		}

		if(isset($_SESSION['erphp_promo_code']) && $_SESSION['erphp_promo_code']){
	        $promo = str_replace("\\","", $_SESSION['erphp_promo_code']);
	        $promo_arr = json_decode($promo,true);
	        if($promo_arr['type'] == 1){
	            $promo_money = get_option('erphp_promo_money1');
	            if($promo_money){
	                $price = $price - $promo_money;
	            }
	        }elseif($promo_arr['type'] == 2){
	            $promo_money = get_option('erphp_promo_money2');
	            if($promo_money){
	                $price = $price * 0.1 * $promo_money;
	            }
	        }
	    }

		if(sprintf("%.2f",$okMoney) >= $price && $okMoney > 0 && $price > 0)
		{
			if(erphpSetUserMoneyXiaoFei($price))
			{
				addUserMoneyLog($user_info->ID, '-'.$price, __('购买资源','erphpdown'));
				$subject   = get_post($postid)->post_title;
				if($index_name){
					$subject .= ' - '.$index_name;
				}
				$postUserId=get_post($postid)->post_author;
				
				if($start_down || $start_down2 || $start_see || $start_see2)
				{
					$result=erphpAddDownload($subject, $postid, $price, 1, '', $postUserId, $index, erphpGetIP());
					if($result)
					{
						if($down_activation && function_exists('doErphpAct')){
							$activation_num = doErphpAct($user_info->ID,$postid);
							if($activation_num){
								$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_url='".$result."'");
								if($user_info->user_email){
									$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
									wp_mail($user_info->user_email, '【'.$subject.'】激活码', '您购买的资源【'.$subject.'】激活码：'.$activation_num, $headers);
								}
							}
						}

						$EPD = new EPD();
						$EPD->doAuthorAff($price, $postUserId);
						$EPD->doAff($price, $user_info->ID);

						if(get_option('erphp_remind')){
							$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
							wp_mail(get_option('admin_email'), '【'.get_bloginfo('name').'】订单提醒 - '.$subject, '用户'.$user_info->user_login.'消费'.$price.get_option('ice_name_alipay').'购买了'.$subject.get_permalink($postid), $headers);
						}

						do_action( 'erphpdown_post_checkout', $user_info->ID, $postid, $index, $price, $result );

						if($start_down || $start_down2)
						{
							header("Location:".constant("erphpdown").'download.php?postid=' . $postid.'&index='.$index.'&timestamp='.time());
						}
						elseif($start_see || $start_see2)
						{
							header("Location:".get_permalink($postid));
						}
						exit;
					}
					else
					{
						$wpdb->query("update $wpdb->iceinfo set ice_get_money=ice_get_money-".$price ." where ice_user_id=".$user_info->ID);
						wp_die(__('系统错误','erphpdown'),'友情提示');
						exit;
					}
				}
			}
			else 
			{
				wp_die(__('系统错误','erphpdown'),__('友情提示','erphpdown'));
			}
		}
		else 
		{
			wp_die(__('余额不足','erphpdown'),__('友情提示','erphpdown'));
		}
	}
}