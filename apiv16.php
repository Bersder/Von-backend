<?php //动态页面接口
require 'utils/init.php';
require 'utils/utils.php';
require 'links/public_link.php';

function get_like_ids(){
    global $link;
    $likedIDs = [];
    if ($ip = get_ip()){
        $res = maria($link,"select dyn_id from Dynamic.dyn_like_log where ip='$ip'");
        while ($each=mysqli_fetch_row($res))$likedIDs[] = $each[0];
    }
    return $likedIDs;
}

if (isset($_GET['offset'])){//获取更多动态
    $offset = intval($_GET['offset']);
    $dynamics = [];
    $likedIDs = get_like_ids();
    list($author,$avatar) = mysqli_fetch_row(maria($link,"select uname,avatar from User.user where id=1"));
    if (isset($_GET['did'])&&$did=intval($_GET['did']))
        $res = maria($link,"select * from Dynamic.dyn_record where id=$did limit 1");
    else
        $res = maria($link,"select * from Dynamic.dyn_record order by time desc limit $offset,10");
    while ($each = mysqli_fetch_assoc($res)){
        $each['likeStatus'] = in_array($each['id'],$likedIDs);
        $each['author'] = $author;
        $each['avatar'] = $avatar;
        $each['imgs'] = $each['imgs']?explode(',',$each['imgs']):[];
        $dynamics[] = $each;
    }
    echo json_encode(['code'=>0,'data'=>['dynamics'=>$dynamics]]);
}
else{//初始化
    $info =  mysqli_fetch_assoc(maria($link,"select sign,status from User.me limit 1"));
    $aCount = mysqli_fetch_row(maria($link,"select count(aid) from Article.article_info"))[0];
    $nCount = mysqli_fetch_row(maria($link,"select count(nid) from Note.note_info"))[0];
    $dCount = mysqli_fetch_row(maria($link,"select count(id) from Dynamic.dyn_record"))[0];
    $statistics = ['aCount'=>$aCount,'nCount'=>$nCount,'dCount'=>$dCount];
    $recommend = [];
    $res = maria($link,"select id,title,url from Dynamic.recommend order by id desc limit 20");
    while ($each = mysqli_fetch_assoc($res))$recommend[] = $each;

    $dynamics = [];
    $likedIDs = get_like_ids();
    list($author,$avatar) = mysqli_fetch_row(maria($link,"select uname,avatar from User.user where id=1"));
    if (isset($_GET['did'])&&$did=intval($_GET['did']))
        $res = maria($link,"select * from Dynamic.dyn_record where id=$did limit 1");
    else
        $res = maria($link,"select * from Dynamic.dyn_record order by time desc limit 10");
    while ($each = mysqli_fetch_assoc($res)){
        $each['likeStatus'] = in_array($each['id'],$likedIDs);
        $each['author'] = $author;
        $each['avatar'] = $avatar;
        $each['imgs'] = $each['imgs']?explode(',',$each['imgs']):[];
        $dynamics[] = $each;
    }
    //danmaku
    echo json_encode(['code'=>0,'data'=>[
        'author'=>$author,'avatar'=>$avatar,
        'info'=>$info,'statistics'=>$statistics,
        'recommendations'=>$recommend,
        'dynamics'=>$dynamics
    ]]);
}



