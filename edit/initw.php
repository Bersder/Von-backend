<?php
require '../utils/init.php';
require '../utils/filters.php';
$link = mysqli_connect('127.0.0.1','root','awsl') or die('数据库连接失败');
if (isset($_POST['token'])&&($auth = token_authorize($_POST['token']))){
    if(isset($_GET['aid'])){//存在aid，检验是否在tmp中，不存在就指导前端重指向
        if($aid=positive_int_filter($_GET['aid'])){
            if ($aid_exist = mysqli_fetch_row(maria($link,"select 1 from Article.article_info_tmp where aid=$aid and asdraft=1 limit 1"))[0]){
                $info = mysqli_fetch_assoc(maria($link,"select * from Article.article_info_tmp where aid=$aid limit 1"));
                $content = mysqli_fetch_assoc(maria($link,"select rawContent from Article.article_content_tmp where aid=$aid limit 1"));
                $info['tags'] = $info['tags']==''?[]:explode(',',$info['tags']);
                $res = maria($link,"select tagName from Tag.tag_cloud limit 500");$tagOptions = [];
                while ($each = mysqli_fetch_assoc($res))$tagOptions[] = $each['tagName'];
                $res = maria($link,"select seriesName from Series.series_link limit 50");$seriesOptions = [];
                while ($each = mysqli_fetch_assoc($res))$seriesOptions[] = $each['seriesName'];
                echo json_encode(['info'=>$info,'rawContent'=>$content['rawContent'],'tagOptions'=>$tagOptions,'seriesOptions'=>$seriesOptions,'exist'=>$aid_exist]);
            }
            else
                echo json_encode(['exist'=>$aid_exist]);
        }

    }
    else{ //新写文章，返回aid，并创建草稿记录
        maria($link,"insert into Article.article_info_tmp (aid,lut) values (null,now())");
        $aid = mysqli_insert_id($link);
        maria($link,"insert into Article.article_content_tmp (aid) values ($aid)");
//    maria($link,"insert into article_info (aid) values ($aid)");
//    maria($link,"insert into article_content (aid) values ($aid)");
        $res = maria($link,"select tagName from Tag.tag_cloud limit 500");$tagOptions = [];
        while ($each = mysqli_fetch_assoc($res))$tagOptions[] = $each['tagName'];
        $res = maria($link,"select seriesName from Series.series_link limit 50");$seriesOptions = [];
        while ($each = mysqli_fetch_assoc($res))$seriesOptions[] = $each['seriesName'];
        echo json_encode(['code'=>0,'tagOptions'=>$tagOptions,'seriesOptions'=>$seriesOptions,'aid'=>$aid]);
    }
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}


