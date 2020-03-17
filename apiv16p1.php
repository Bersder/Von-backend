<?php //动态区评论获取
require 'utils/init.php';
require 'utils/filters.php';
require 'utils/utils.php';
require 'links/public_link.php';
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
if (isset($_GET['did'])&&($did=positive_int_filter($_GET['did']))){
    $offset = isset($_GET['offset'])?intval($_GET['offset']):0;
    $allCount = mysqli_fetch_row(maria($link,"select count(id) from Dynamic.dyn_comment where dyn_id=$did"))[0];
    $commentCount = mysqli_fetch_row(maria($link,"select count(id) from Dynamic.dyn_comment where dyn_id=$did and parent_id is null"))[0];
    $comments = [];
    $allReplies = [];

    $res = maria($link,"
        select * from
            (select c1.id as id,uid,avatar,ulink,uname,to_uid,parent_id,c1.datetime as datetime,content
             from Dynamic.dyn_comment as c1,User.user as u1
             where dyn_id=$did and parent_id is null and c1.uid=u1.id) as c2
            left join
            (select id as to_uid,uname as to_uname from User.user) as u2
            using(to_uid)
            order by datetime desc limit $offset,10
        ");
    while ($each = mysqli_fetch_assoc($res))$comments[] = $each;

    $res = maria($link,"
        select * from
            (select c1.id as id,uid,avatar,ulink,uname,to_uid,parent_id,c1.datetime as datetime,content
             from Dynamic.dyn_comment as c1,User.user as u1
             where dyn_id=$did and parent_id is not null and c1.uid=u1.id) as c2
            left join
            (select id as to_uid,uname as to_uname from User.user) as u2
            using(to_uid)
        ");
    while ($each = mysqli_fetch_assoc($res))$allReplies[] = $each;
    gen_replies($comments);
    echo json_encode(['code'=>0,'data'=>['comments'=>$comments,'allCount'=>$allCount,'commentCount'=>$commentCount]],JSON_NUMERIC_CHECK);
}
elseif (isset($_GET['like'])&&($did=positive_int_filter($_GET['like']))){
    //动态喜欢，利用ip进行唯一性确定
    if ($ip = get_ip()){
        $loc = maria_escape(get_ip_loc($ip),$link);
        if(!mysqli_fetch_row(maria($link,"select 1 from Dynamic.dyn_like_log where ip='$ip' and dyn_id=$did"))){
            maria($link,"update Dynamic.dyn_record set liked=liked+1 where id=$did limit 1");
            maria($link,"insert into Dynamic.dyn_like_log values ('$ip',$did,$loc,now())");
        }
        echo json_encode(['code'=>0],JSON_NUMERIC_CHECK);
    }
}
else
    echo json_encode(['code'=>1],JSON_NUMERIC_CHECK);