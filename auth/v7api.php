<?php //增添删除动态
require '../utils/init.php';
require '../utils/filters.php';
require '../links/secret_link.php';
if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    if (isset($_GET['delete']) && ($delId=positive_int_filter($_GET['delete']))){
        maria($link,"delete from Page.gossip where id=$delId limit 1");
        echo json_encode(['code'=>0]);
    }
    else{
        if(($type=in_array_filter($_POST['type'],['anime','code','game','trivial']))&&($content=maria_str_notnull_filter($_POST['content'],$link))){
            maria($link,"insert into Page.gossip (type, content, time) values ('$type',$content,now())");
            echo json_encode(['code'=>0]);
        }
        else
            echo json_encode(['code'=>1]);
    }
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}