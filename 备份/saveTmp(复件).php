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
if(isset($_GET['aid'])&&in_array($_POST['type'],['anime','code','game','trivial'])){
    maria($link,'use Article');
    $aid = $_GET['aid'];
    if(preg_match('/\\d+/',$aid)){
        $author = $_POST['author'];
        $preview = $_POST['preview'];
        $rawContent = $_POST['rawContent'];
        $series = $_POST['series']?"'".$_POST['series']."'":'null';
        $tags = $_POST['tags'];
        $inputTags = $_POST['inputTags'];
        $title = $_POST['title'];
        $type = $_POST['type'];
        maria($link,"update article_info_tmp set author='$author',preview='$preview',series=$series,tags='$tags',inputTags='$inputTags',title='$title',type='$type',lut=now() where aid=$aid limit 1");
        maria($link,"update article_content_tmp set rawContent='$rawContent' where aid=$aid limit 1");
    }
}
elseif(isset($_GET['nid'])&&$_POST['type']=='note'){
    maria($link,'use Note');
    $nid = $_GET['nid'];
    if(preg_match('/\\d+/',$nid)){
        $author = $_POST['author'];
        $preview = $_POST['preview'];
        $rawContent = $_POST['rawContent'];
        $category = $_POST['category'];
        $tags = $_POST['tags'];
        $inputTags = $_POST['inputTags'];
        $title = $_POST['title'];
        $type = 'note';
        maria($link,"update note_info_tmp set author='$author',preview='$preview',category='$category',tags='$tags',inputTags='$inputTags',title='$title',type='$type',lut=now() where nid=$nid limit 1");
        maria($link,"update note_content_tmp set rawContent='$rawContent' where nid=$nid limit 1");
    }

}
else{//没有aid
    echo json_encode(['error'=>1]);
}