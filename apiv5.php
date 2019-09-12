<?php //标签页面初始化& 相关筛选
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');

if(isset($_GET['tag'])){ //带实际标签的访问

    $tag = mysqli_real_escape_string($link,$_GET['tag']);
    $res = maria($link,"select relateArt,relateNote from Tag.tag_cloud where tagName='$tag' limit 1");
    $tagRelate = mysqli_fetch_assoc($res);
    $exist=!$tagRelate?0:1;
    if (isset($_GET['init'])){
        $artInfos = [];
        $res = maria($link,"select aid as id,type,title,time,tags from Article.article_info where type is not null order by time desc limit 500");
        while ($each = mysqli_fetch_assoc($res))array_push($artInfos,$each);
        $noteInfos = [];
        $res = maria($link,"select nid as id,type,title,time,tags from Note.note_info where type is not null order by time desc limit 500");
        while ($each = mysqli_fetch_assoc($res))array_push($noteInfos,$each);
        $tags = [];
        $res = maria($link,"select tagName,relateArt,relateNote from Tag.tag_cloud order by tagName asc limit 500");
        while ($each = mysqli_fetch_assoc($res))$tags[] = $each;
        echo json_encode(['code'=>0,'data'=>['artInfos'=>$artInfos,'noteInfos'=>$noteInfos,'tagRelate'=>$tagRelate,'tags'=>$tags],'exist'=>$exist]);
    }
    else{
        echo json_encode(['code'=>0,'data'=>['tagRelate'=>$tagRelate],'exist'=>$exist]);
    }

}
else{ //直接访问/tags
    $artInfos = [];
    $res = maria($link,"select aid as id,type,title,time,tags from Article.article_info where type is not null order by time desc limit 500");
    while ($each=mysqli_fetch_assoc($res))array_push($artInfos,$each);
    $noteInfos = [];
    $res = maria($link,"select nid as id,type,title,time,tags from Note.note_info where type is not null order by time desc limit 500");
    while ($each=mysqli_fetch_assoc($res))array_push($noteInfos,$each);
    $tags = [];
    $res = maria($link,"select tagName,relateArt,relateNote from Tag.tag_cloud order by tagName asc limit 500");
    while ($each = mysqli_fetch_assoc($res))$tags[] = $each;
    $rand = [];
    $res = maria($link,"select aid as id,type,title,time,tags from Article.article_info where aid >= floor(((select max(aid) from Article.article_info)-(select min(aid) from Article.article_info)) * rand() + (select min(aid) from Article.article_info)) limit 5");
    while ($each = mysqli_fetch_assoc($res))$rand[] = $each;
    $res = maria($link,"select nid as id,type,title,time,tags from Note.note_info where nid >= floor(((select max(nid) from Note.note_info)-(select min(nid) from Note.note_info)) * rand() + (select min(nid) from Note.note_info)) limit 5");
    while ($each = mysqli_fetch_assoc($res))$rand[] = $each;
    shuffle($rand);
    echo json_encode(['code'=>0,'data'=>['artInfos'=>$artInfos,'noteInfos'=>$noteInfos,'tags'=>$tags,'rand'=>$rand]]);
}
