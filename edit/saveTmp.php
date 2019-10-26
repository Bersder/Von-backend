<?php
require '../utils/init.php';
require '../utils/filters.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');
if (isset($_POST['token'])&&($auth = token_authorize($_POST['token']))){
    if(isset($_GET['aid'])&&($type=in_array_filter($_POST['type'],['anime','code','game','trivial']))&&($aid=positive_int_filter($_GET['aid']))){
        $author = maria_escape($_POST['author'],$link) ;
        $preview = maria_escape($_POST['preview'],$link) ;
        $rawContent = maria_escape($_POST['rawContent'],$link) ;
        $seriesID = maria_strORnull_filter($_POST['seriesID'],$link);
        $tags = maria_escape($_POST['tags'],$link) ;
        $inputTags = maria_escape($_POST['inputTags'],$link) ;
        $title = maria_escape($_POST['title'],$link) ;
        maria($link,"update Article.article_info_tmp set author=$author,preview=$preview,seriesID=$seriesID,tags=$tags,inputTags=$inputTags,title=$title,type='$type',lut=now() where aid=$aid limit 1");
        maria($link,"update Article.article_content_tmp set rawContent=$rawContent where aid=$aid limit 1");
        echo json_encode(['code'=>0]);
    }
    elseif(isset($_GET['nid'])&&$_POST['type']==='note'&&($nid=positive_int_filter($_GET['nid']))&&($catID=positive_int_filter($_POST['categoryID']))){
        $author = maria_escape($_POST['author'],$link) ;
        $preview = maria_escape($_POST['preview'],$link) ;
        $rawContent = maria_escape($_POST['rawContent'],$link) ;
        $tags = maria_escape($_POST['tags'],$link) ;
        $inputTags = maria_escape($_POST['inputTags'],$link) ;
        $title = maria_escape($_POST['title'],$link) ;
        maria($link,"update Note.note_info_tmp set author=$author,preview=$preview,catID=$catID,tags=$tags,inputTags=$inputTags,title=$title,type='note',lut=now() where nid=$nid limit 1");
        maria($link,"update Note.note_content_tmp set rawContent=$rawContent where nid=$nid limit 1");
        echo json_encode(['code'=>0]);
    }
    else{//没有aid/aid非法/不对应
        echo json_encode(['code'=>1]);
    }
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}
