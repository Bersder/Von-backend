<?php //评论翻页接口
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsl') or die('数据库连接失败');

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

        $allCount = mysqli_fetch_row(maria($link,"select count(id) from Comment.comment where topic_id=$id and topic_type='$type'"))[0];
        $commentCount = mysqli_fetch_row(maria($link,"select count(id) from Comment.comment where topic_id=$id and topic_type='$type' and parent_id is null"))[0];
        $comments = [];
        $allReplies = [];
        $res = maria($link,"select * from Comment.comment where topic_id=$id and topic_type='$type' and parent_id is null order by datetime desc limit $offset,10");
        while ($each = mysqli_fetch_assoc($res))$comments[] = $each;
        $res = maria($link,"select * from Comment.comment where topic_id=$id and topic_type='$type' and parent_id is not null");
        while ($each = mysqli_fetch_assoc($res))$allReplies[] = $each;
        gen_replies($comments);
        echo json_encode(['code'=>0,'data'=>['comments'=>$comments,'allCount'=>$allCount,'commentCount'=>$commentCount]]);
    }
    else
        echo json_encode(['code'=>1]);

}


