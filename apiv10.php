<?php  //全站搜索，最多支持两个关键词
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');
if(isset($_GET['s'])&&$_GET['s']){
    $keys =  preg_replace('/\\s+/',' ',trim($_GET['s']));
    $keys = array_slice(explode(' ',$keys),0,2);

    if (sizeof($keys)>1)
        $condition = "instr(concat(title,',',preview,',',rawContent),'$keys[0]') or instr(concat(title,',',preview,',',rawContent),'$keys[1]')";
    else
        $condition = "instr(concat(title,',',preview,',',rawContent),'$keys[0]')";
    $result = [];
    $res = maria($link,"select nid as id,title,preview,imgSrc,commentCount,time,type from Note.note_info inner join Note.note_content using(nid) where $condition");
    while ($each = mysqli_fetch_assoc($res))$result[] = $each;
    $res = maria($link,"select aid as id,title,preview,imgSrc,commentCount,time,type from Article.article_info inner join Article.article_content using(aid) where $condition");
    while ($each = mysqli_fetch_assoc($res))$result[] = $each;
    if (sizeof($result)){
        $found = 1;
        array_multisort(array_column($result,'time'),SORT_DESC,$result);
    }
    else{
        $found = 0;
        $res = maria($link,"select aid as id,type,title from Article.article_info order by time desc limit 10");
        while ($each = mysqli_fetch_assoc($res))$result[] = $each;
        $res = maria($link,"select nid as id,type,title from Note.note_info order by time desc limit 10");
        while ($each = mysqli_fetch_assoc($res))$result[] = $each;
        shuffle($result);
    }

    echo json_encode(['code'=>0,'data'=>['result'=>$result,'keys'=>$keys,'found'=>$found]]);
}
