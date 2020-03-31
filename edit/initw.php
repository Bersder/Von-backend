<?php
require '../utils/init.php';
require '../utils/filters.php';
require '../links/limit_link.php';
if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    if(isset($_GET['aid'])){//存在aid，检验是否在tmp中，不存在就指导前端重指向
        if($aid=positive_int_filter($_GET['aid'])){
            if ($aid_exist = mysqli_fetch_row(maria($link,"select 1 from Article.article_info_tmp where aid=$aid and asdraft=1 limit 1"))[0]){
                $info = mysqli_fetch_assoc(maria($link,"
                select title,type,preview,seriesName as series,sid as seriesID,inputTags,imgSrc
                from Article.article_info_tmp left join Article.series_link on seriesID=sid
                where aid=$aid
                limit 1;
                "));
                $content = mysqli_fetch_assoc(maria($link,"select rawContent from Article.article_content_tmp where aid=$aid limit 1"));

                $tagsID = mysqli_fetch_row(maria($link,"select group_concat(tid) as tagsID from Tag.tm_tc_tmp where xid=$aid and type<>'note'"))[0];
                $tagsID = $tagsID?explode(',',$tagsID):[];
                $info['tagsID'] = $tagsID;

                $tagMap = [];
                $res = maria($link,"select tid,tagName from Tag.tag_cloud");
                while ($each = mysqli_fetch_assoc($res))$tagMap[$each['tid']] = $each['tagName'];

                $res = maria($link,"select sid,seriesName from Article.series_link limit 50");$seriesOptions = [];
                while ($each = mysqli_fetch_assoc($res))$seriesOptions[] = $each;
                echo json_encode(['code'=>0,'info'=>$info,'rawContent'=>$content['rawContent'],'tagMap'=>$tagMap,'seriesOptions'=>$seriesOptions,'exist'=>$aid_exist]);
            }
            else
                echo json_encode(['code'=>0,'exist'=>$aid_exist]);
        }

    }
    else{ //新写文章，返回aid，并创建草稿记录
        maria($link,"insert into Article.article_info_tmp (aid,type,lut) values (null,'code',now())");
        $aid = mysqli_insert_id($link);
        maria($link,"insert into Article.article_content_tmp (aid) values ($aid)");

        $tagMap = [];
        $res = maria($link,"select tid,tagName from Tag.tag_cloud");
        while ($each = mysqli_fetch_assoc($res))$tagMap[$each['tid']] = $each['tagName'];

        $res = maria($link,"select sid,seriesName from Article.series_link limit 50");$seriesOptions = [];
        while ($each = mysqli_fetch_assoc($res))$seriesOptions[] = $each;
        echo json_encode(['code'=>0,'tagMap'=>$tagMap,'seriesOptions'=>$seriesOptions,'aid'=>$aid]);
    }
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}


