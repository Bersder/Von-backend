<?php //置顶/取消置顶接口
require '../utils/init.php';
require '../utils/filters.php';
require '../links/secret_link.php';
if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    if($id = positive_int_filter($_POST['id'])){
        $flag = isset($_GET['topped'])?1:0;
        maria($link,"update Article.article_info set topped=$flag where aid=$id limit 1");
        echo json_encode(['code'=>0]);
    }
    else
        echo json_encode(['code'=>1]);
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}