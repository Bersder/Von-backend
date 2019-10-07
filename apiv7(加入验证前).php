<?php  //评论发布接口
require 'utils/init.php';
require 'utils/filters.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');


if (isset($_POST['puzzle'])&&($ass=base64_decode($_POST['puzzle']))){
    $ass = explode(',',$ass);
    if (sizeof($ass)==3&&($ass[0]+$ass[1]==$ass[2])){
        
    }
    else json_encode(['code'=>4]);
}


if(($topic_id = positive_int_filter($_POST['id']))&&($topic_type = in_array_filter($_POST['type'],['anime','code','game','trivial','note','link']))){
    if(($uname=maria_str_notnull_filter($_POST['nickname'],$link))&&($email=maria_str_notnull_filter($_POST['email'],$link))){
        //    $uname = mysqli_real_escape_string($link,$_POST['nickname']);
        //    $email = $_POST['email'];
        if (($parent_id=maria_pintORnull_filter($_POST['to_id']))&&($to_uid=maria_pintORnull_filter($_POST['to_uid']))){
            //        $parent_id = $_POST['to_id']?$_POST['to_id']:'null';
            //        $to_uid = $_POST['to_uid']?$_POST['to_uid']:'null';
            $to_uname = maria_strORnull_filter($_POST['to_uname'],$link);
            //$to_uname = $_POST['to_uname']?"'".$_POST['to_uname']."'":'null';
            $ulink = maria_strORnull_filter($_POST['website'],$link);
            $content = mysqli_real_escape_string($link,$_POST['content']);
            $notify = $_POST['notifyMe']=='true'?1:0;
            $new = 1;

            $user_info = mysqli_fetch_assoc(maria($link,"select id,ulink,avatar from User.user where uname=$uname and email=$email limit 1"));
            if (!$user_info){ //新用户
                maria($link,"insert into User.user (uname, email, ulink,datetime) values ($uname,$email,$ulink,now())");
                $uid = mysqli_insert_id($link);
                $avatar = 'null';
            }
            else{ // 存在用户记录
                $uid = $user_info['id'];
                maria($link,"update User.user set points=points+1 where id=$uid limit 1");
                $avatar = $user_info['avatar']?"'".$user_info['avatar']."'":'null';
                $ulink = $user_info['ulink']?"'".$user_info['ulink']."'":'null';
                $new = 0;
            }
            maria($link,"insert into Comment.comment (parent_id, topic_id, topic_type, content, uid, uname, ulink, avatar, to_uid, to_uname, notify, datetime) values ($parent_id,$topic_id,'$topic_type','$content',$uid,$uname,$ulink,$avatar,$to_uid,$to_uname,$notify,now())");
            if ($topic_type=='note')//评论计数
                maria($link,"update Note.note_info set commentCount=commentCount+1 where nid=$topic_id limit 1");
            else
                maria($link,"update Article.article_info set commentCount=commentCount+1 where aid=$topic_id limit 1");
            if($parent_id!='null'){//回复提醒
                $notify_target = mysqli_fetch_assoc(maria($link,"select uname,avatar,notify from Comment.comment where id=$parent_id limit 1"));
                if ($notify_target['notify']){
                    //发邮件提醒！！！
                    //使用phpmailer，未实现
                }
            }
            else{
                //直接评论主题，提醒我
            }
            echo json_encode(['code'=>0,'new'=>$new]);
        }
        else json_encode(['code'=>1]); //下面的都是非法的

    }
    else json_encode(['code'=>2]);

}
else json_encode(['code'=>3]);





