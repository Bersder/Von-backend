<?php //状态改变或签名改变
require '../utils/init.php';
require '../utils/filters.php';
require '../links/secret_link.php';
if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    if (isset($_POST['sign'])){
        $sign = maria_escape($_POST['sign'],$link);
        maria($link,"update User.me set sign=$sign limit 1");
        echo json_encode(['code'=>0]);
    }
    elseif (isset($_POST['status'])&&preg_match('/^\\d$/',$_POST['status'])){
        $status = $_POST['status'];
        maria($link,"update User.me set status=$status limit 1");
        echo json_encode(['code'=>0]);
    }
    elseif (isset($_POST['memo'])){
        $memo = maria_escape($_POST['memo'],$link);
        maria($link,"update User.me set memo=$memo limit 1");
        echo json_encode(['code'=>0]);
    }
    else{
        $info = mysqli_fetch_assoc(maria($link,"select sign,status,memo from User.me limit 1"));
        echo json_encode(['code'=>0,'data'=>['info'=>$info]]);
    }
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}