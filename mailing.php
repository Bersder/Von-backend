<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'utils/init.php';
require 'links/public_link.php';
require 'vendor/autoload.php';

function send_mail($send_configs,$send_me=false){
    $to_mail = $send_configs['to_mail'];
    $to_name = $send_configs['to_name'];
    $topic_name = $send_configs['topic_name'];
    $topic_url = $send_configs['topic_url'];
    $rawComment = preg_replace('/∫f\((.+?)\)/','[$1]',$send_configs['rawComment']);
    $rawReply = preg_replace('/∫f\((.+?)\)/','[$1]',$send_configs['rawReply']);
    $parent_id = $send_configs['parent_id'];
    $responder = $send_configs['responder'];

    $mail = new PHPMailer(true);
    $mdParser = new Parsedown();
    try {
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';//outlook: STARTTLS

        $mail->Host = 'smtp.qq.com';//outlook: smtp.office365.com
        $mail->Port = 587;
        $mail->Username = MY_MAIL;
        $mail->Password = MY_MAIL_PASSWORD;

        $mail->setFrom(MY_MAIL, MY_MAIL_NAME); //发件人
        $mail->addAddress($to_mail, $to_name); //收件人
        $mail->addReplyTo(MY_MAIL, MY_MAIL_NAME); //回复的时候回复给哪个邮箱 建议和发件人一致

        //Content
        $mail->isHTML(true);
        if ($send_me){
            $mail->Subject = '你的博客收到了一则评论';
            $mail->Body = '<table>' .
                '<tr><td style="font-size:25px;color:#5abebc;padding:0">你的博客收到了一则评论</td></tr>'.
                sprintf('<tr><td style="font-size:16px"><strong>%s</strong> 在<strong>《%s》</strong>上留下了一则评论：</td></tr>',$responder,$topic_name).
                sprintf('<tr><td style="font-size:14px;background:#eaeaea;padding:10px 20px;border-radius:5px">%s</td></tr>',$mdParser->text($rawReply)).
                sprintf('<tr><td style="font-size:16px;padding-top:20px"><a href="%s">点击此处前往现场(编号#%d)</a></td></tr>',$topic_url,$parent_id).
                '</table>';
            $mail->AltBody = sprintf("%s 评论了《%s》。前往 %s 查看（编号#%d）",$responder,$topic_name,$topic_url,$parent_id);
        }
        else{
            $mail->Subject = '你有一条来自忍野喵的回复';
            $mail->Body = '<table>' .
                '<tr><td style="font-size:25px;color:#5abebc;padding:0">你有一条来自忍野喵的回复</td></tr>'.
                sprintf('<tr><td style="font-size:16px">你在<strong>《%s》</strong>发表的评论：</td></tr>',$topic_name).
                sprintf('<tr><td style="font-size:14px;background:#eaeaea;padding:10px 20px;border-radius:5px">%s</td></tr>',$mdParser->text($rawComment)).
                sprintf('<tr><td style="font-size:16px;padding-top:20px">收到了来自 <strong>%s</strong> 的回复：</td></tr>',$responder).
                sprintf('<tr><td style="font-size:14px;background:#eaeaea;padding:10px 20px;border-radius:5px">%s</td></tr>',$mdParser->text($rawReply)).
                sprintf('<tr><td style="font-size:16px;padding-top:20px"><a href="%s">点击此处前往现场(编号#%d)</a></td></tr>',$topic_url,$parent_id).
                '</table>';
            $mail->AltBody = sprintf("忍野喵提醒你：你在《%s》留下的评论被 %s 回复了。前往 %s 查看（编号#%d）",$topic_name,$responder,$topic_url,$parent_id);
        }
        $mail->send();
        echo '发送成功';
    } catch (Exception $e) {
        echo '发送失败';
    }
}
if (php_sapi_name()!=='cli'){
    echo json_encode(['code'=>1,'message'=>'big brother is watching you']);
    die();
}
$args = getopt('f::d::',['pid:','sid:']);
$pid = $args['pid'];
$sid = $args['sid'];
$isDynamic = isset($args['d']);
if (!$isDynamic){
    $topic = mysqli_fetch_row(maria($link,"select topic_type,topic_id from Comment.comment where id=$sid limit 1"));
    list($topic_type,$topic_id) = $topic;
    switch ($topic_type){
        case 'note':
            $topic_name = mysqli_fetch_row(maria($link,"select title from Note.note_info where nid=$topic_id limit 1"))[0];
            $topic_url = sprintf("%s/note/%d#comment-%s",BLOG_DOMAIN,$topic_id,$pid);
            break;
        case 'link':
            $topic_name = '忍野喵的友链';
            $topic_url = sprintf("%s/links#comment-%s",BLOG_DOMAIN,$pid);
            break;
        default:
            $topic_name = mysqli_fetch_row(maria($link,"select title from Article.article_info where aid=$topic_id limit 1"))[0];
            $topic_url = sprintf("%s/archive/%s/%d#comment-%s",BLOG_DOMAIN,$topic_type,$topic_id,$pid);
    }
}
else{
    $topic = mysqli_fetch_row(maria($link,"select dyn_id from Dynamic.dyn_comment where id=$sid limit 1"));
    list($topic_id) = $topic;
    $topic_name = '忍野喵的动态';
    $topic_url = sprintf("%s/dynamic/%d",BLOG_DOMAIN,$topic_id);
}

if (!$isDynamic)
    $reply = mysqli_fetch_row(maria($link,"select uname,content from (select uid as id,content from Comment.comment where id=$sid limit 1) as tmp left join User.user using(id)"));
else
    $reply = mysqli_fetch_row(maria($link,"select uname,content from (select uid as id,content from Dynamic.dyn_comment where id=$sid limit 1) as tmp left join User.user using(id)"));
list($responder,$rawReply) = $reply;

if (isset($args['f'])){ //A评论主题,提醒我
    $send_configs = [
        'to_mail'=>RECEIVE_MAIL, 'to_name'=>MY_MAIL_NAME,
        'topic_name'=>$topic_name, 'topic_url'=>$topic_url,
        'rawComment'=>'', 'rawReply'=>$rawReply,
        'parent_id'=>$pid, 'responder'=>$responder
    ];
    //var_dump($send_configs);
    send_mail($send_configs,true);
}
else{ //A评论B,提醒B
    if (!$isDynamic)
        $notify_target = mysqli_fetch_row(maria($link,"select email,uname,content from (select uid as id,content from Comment.comment where id=$pid limit 1) as tmp left join User.user using(id)"));
    else
        $notify_target = mysqli_fetch_row(maria($link,"select email,uname,content from (select uid as id,content from Dynamic.dyn_comment where id=$pid limit 1) as tmp left join User.user using(id)"));
    list($to_mail,$to_name,$rawComment) = $notify_target;

    $send_configs = [
        'to_mail'=>$to_mail, 'to_name'=>$to_name,
        'topic_name'=>$topic_name, 'topic_url'=>$topic_url,
        'rawComment'=>$rawComment, 'rawReply'=>$rawReply,
        'parent_id'=>$pid, 'responder'=>$responder
    ];
    //var_dump($send_configs);
    send_mail($send_configs);
}
