<?php //评论翻页接口
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');

function gen_replies(&$items){
    global $allReplies;
    foreach ($items as &$item){
        $replies=[];
        foreach ($allReplies as $reply){
            if ($reply['parent_id'] == $item['id'])
                $replies[] = $reply;
        }
        if (sizeof($replies))gen_replies($replies);
        $item['replies'] = $replies;
    }
}
if(isset($_POST['type'])&&in_array($_POST['type'],['anime','code','game','trivial','note','link'])){
    if (isset($_POST['id'])&&preg_match('/^[1-9]\\d*$/',$_POST['id'])){
        $type = $_POST['type'];
        $id = $_POST['id'];
        $offset = isset($_POST['offset'])?intval($_POST['offset']):0;

        $allCount = mysqli_fetch_row(maria($link,"select count(id) from Comment.comment where topic_id=$id and topic_type='$type'"))[0];//评论（含回复）
        $commentCount = mysqli_fetch_row(maria($link,"select count(id) from Comment.comment where topic_id=$id and topic_type='$type' and parent_id is null"))[0];//主评论
        $comments = [];
        $allReplies = [];
        //select * from Comment.comment where topic_id=$id and topic_type='$type' and parent_id is null order by datetime desc limit $offset,10
        $res = maria($link,"
        select * from
            (select c1.id as id,uid,avatar,ulink,uname,to_uid,parent_id,c1.datetime as datetime,content
             from Comment.comment as c1,User.user as u1
             where topic_id=$id and topic_type='$type' and parent_id is null and c1.uid=u1.id) as c2
            left join
            (select id as to_uid,uname as to_uname from User.user) as u2
            using(to_uid)
            order by datetime desc limit $offset,10
        ");
        while ($each = mysqli_fetch_assoc($res))$comments[] = $each;
        //select * from Comment.comment where topic_id=$id and topic_type='$type' and parent_id is not null
        $res = maria($link,"
        select * from
            (select c1.id as id,uid,avatar,ulink,uname,to_uid,parent_id,c1.datetime as datetime,content
             from Comment.comment as c1,User.user as u1
             where topic_id=$id and topic_type='$type' and parent_id is not null and c1.uid=u1.id) as c2
            left join
            (select id as to_uid,uname as to_uname from User.user) as u2
            using(to_uid)
        ");
        while ($each = mysqli_fetch_assoc($res))$allReplies[] = $each;
        gen_replies($comments);
        echo json_encode(['code'=>0,'data'=>['comments'=>$comments,'allCount'=>$allCount,'commentCount'=>$commentCount]]);
    }
    else
        echo json_encode(['code'=>1]);

}


