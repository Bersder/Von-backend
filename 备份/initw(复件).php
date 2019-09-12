<?php
header('Access-Control-Allow-Origin:*');
header('content-type: application/json;charset=UTF-8');
$link = mysqli_connect('127.0.0.1','root','awsl') or die('数据库连接失败');
function maria($link,$sql){
    $res = mysqli_query($link,$sql);
    if(!$res){
        echo '错误编号：'.mysqli_errno($link).'<br>';
        echo '错误信息：'.mysqli_error($link).'<br>';
        exit;
    }
    return $res;
}
maria($link,'use Article');


if(isset($_GET['aid'])){//存在aid，检验是否在tmp中，不存在就指导前端重指向
    $aid = $_GET['aid'];
    if(preg_match('/\\d+/',$aid)){
        if ($aid_exist = mysqli_fetch_row(maria($link,"select 1 from article_info_tmp where aid=$aid and asdraft=1 limit 1"))[0]){
            $info = mysqli_fetch_assoc(maria($link,"select * from article_info_tmp where aid=$aid limit 1"));
            $content = mysqli_fetch_assoc(maria($link,"select rawContent from article_content_tmp where aid=$aid limit 1"));
            $info['tags'] = $info['tags']==''?[]:explode(',',$info['tags']);
            $res = maria($link,"select tagName from Tag.tag_cloud limit 500");$tagOptions = [];
            while ($each = mysqli_fetch_assoc($res))$tagOptions[] = $each['tagName'];
            $seriesOptions = [];
            $res = maria($link,"select seriesName from Series.series_link limit 50");
            while ($each = mysqli_fetch_assoc($res))$seriesOptions[] = $each['seriesName'];
            echo json_encode(['info'=>$info,'rawContent'=>$content['rawContent'],'tagOptions'=>$tagOptions,'seriesOptions'=>$seriesOptions,'exist'=>$aid_exist]);
        }
        else
            echo json_encode(['exist'=>$aid_exist]);
    }

}
else{ //新写文章，返回aid，并创建草稿记录
    maria($link,"insert into article_info_tmp (aid,lut) values (null,now())");
    $aid = mysqli_insert_id($link);
    maria($link,"insert into article_content_tmp (aid) values ($aid)");
//    maria($link,"insert into article_info (aid) values ($aid)");
//    maria($link,"insert into article_content (aid) values ($aid)");

//    maria($link,'insert into article_info (aid) values (null)');
//    $aid = mysqli_insert_id($link);
//    maria($link,"insert into article_content (aid) VALUES ($aid)");
//    maria($link,"insert into article_info_tmp (aid,lut) values ($aid,now())");
//    maria($link,"insert into article_content_tmp (aid) VALUES ($aid)");
    $res = maria($link,"select tagName from Tag.tag_cloud limit 500");$tagOptions = [];
    while ($each = mysqli_fetch_assoc($res))$tagOptions[] = $each['tagName'];
    $seriesOptions = [];
    $res = maria($link,"select seriesName from Series.series_link limit 50");
    while ($each = mysqli_fetch_assoc($res))$seriesOptions[] = $each['seriesName'];
    echo json_encode(['tagOptions'=>$tagOptions,'seriesOptions'=>$seriesOptions,'aid'=>$aid]);
}

