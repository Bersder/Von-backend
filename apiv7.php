<?php  //评论发布接口
require 'utils/init.php';
require 'utils/filters.php';
require 'links/public_link.php';
require 'vendor/autoload.php';
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];
function get_qq_avatar($qq, $path){
    $url = 'https://q1.qlogo.cn/g?b=qq&s=100&nk=' . $qq;
    $state = @file_get_contents($url, 0, null, 0, 1);//获取网络资源的字符内容
    if ($state) {
        $filename = substr(md5($qq), 0, 16) . substr(time(), 0, 16);
        ob_start();//开启缓冲区
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();//清空关闭缓冲区
        $size = strlen($img);
        $fp2 = @fopen($path . $filename, "a");
        fwrite($fp2, $img);
        fclose($fp2);
        return $filename;
    } else {
        return 'passerby.png';
    }
}


if (isset($_POST['puzzle'])&&($ass=base64_decode($_POST['puzzle']))){
    $ass = explode(',',$ass);
    if (sizeof($ass)==3&&($ass[0]+$ass[1]==$ass[2])){
        if(($topic_id = positive_int_filter($_POST['id']))&&($topic_type = in_array_filter($_POST['type'],['anime','code','game','trivial','note','link','bgm']))){
            if(($uname=maria_str_notnull_filter($_POST['nickname'],$link))&&($email=maria_str_notnull_filter($_POST['email'],$link))){
                if (($parent_id=maria_pintORnull_filter($_POST['to_id']))&&($to_uid=maria_pintORnull_filter($_POST['to_uid']))){
                    $qq = maria_pintORnull_filter($_POST['qq']);
                    $ulink = maria_strORnull_filter($_POST['website'],$link);
                    $content = mysqli_real_escape_string($link,$_POST['content']);
                    $notify = $_POST['notifyMe']=='true'?1:0;
                    $new = 1;

                    $user_info = mysqli_fetch_assoc(maria($link,"select id,avatar from User.user where uname=$uname and email=$email limit 1"));//评论人id
                    if (!$user_info){ //新用户
                        if ($qq!='null'){ //新用户填写了qq，拉取头像
                            $avatar_file = get_qq_avatar($qq,$DISK_ROOT . '/uploads/avatar/');
                            $avatar_src = '/uploads/avatar/' . $avatar_file;
                            maria($link,"insert into User.user (uname, email, ulink, avatar, datetime) values ($uname,$email,$ulink,'$avatar_src',now())");
                            $uid = mysqli_insert_id($link);
                        }
                        else{// 新用户没填qq，avatar设null
                            maria($link,"insert into User.user (uname, email, ulink,datetime) values ($uname,$email,$ulink,now())");
                            $uid = mysqli_insert_id($link);
                        }
                    }
                    else{ // 存在用户记录
                        $uid = $user_info['id'];
                        if ($qq!='null'&&!$user_info['avatar']){//已存在的用户，如果之前没有avatar而这次带上了qq，更新其avatar
                            $avatar_file = get_qq_avatar($qq,$DISK_ROOT . '/uploads/avatar/');
                            $avatar_src = '/uploads/avatar/' . $avatar_file;
                            maria($link,"update User.user set avatar='$avatar_src',points=points+1 where id=$uid limit 1");
                        }
                        else
                            maria($link,"update User.user set points=points+1 where id=$uid limit 1");
                        $new = 0;
                    }
                    maria($link,"insert into Comment.comment (parent_id, topic_id, topic_type, content, uid, to_uid, notify, datetime) values ($parent_id,$topic_id,'$topic_type','$content',$uid,$to_uid,$notify,now())");
                    $son_id = mysqli_insert_id($link);
                    if ($topic_type=='note')//评论计数
                        maria($link,"update Note.note_info set commentCount=commentCount+1 where nid=$topic_id limit 1");
                    else if ($topic_type!='link')
                        maria($link,"update Article.article_info set commentCount=commentCount+1 where aid=$topic_id limit 1");
                    if($parent_id!='null'){//回复提醒
                        $notify_target = mysqli_fetch_assoc(maria($link,"select notify from Comment.comment as c,User.user as u where c.id=$parent_id and c.uid=u.id limit 1"));
                        if ($notify_target['notify']){
                            passthru("/usr/local/php/bin/php mailing.php --pid=$parent_id --sid=$son_id > /dev/null 2>&1 &");
                            //发邮件提醒！！！
                            //使用phpmailer，未实现
                        }
                    }
                    else{
                        passthru("/usr/local/php/bin/php mailing.php --pid=$son_id --sid=$son_id -f > /dev/null 2>&1 &");
                        //直接评论主题，提醒我
                    }
                    echo json_encode(['code'=>0,'new'=>$new]);
                }
                else json_encode(['code'=>1]); //下面的都是非法的
            }
            else json_encode(['code'=>2]);
        }
        else json_encode(['code'=>3]);
    }
    else json_encode(['code'=>4]);
}

