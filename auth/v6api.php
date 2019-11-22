<?php //获取动态历史
require '../utils/init.php';
require '../utils/filters.php';
require '../links/secret_link.php';
if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    if (isset($_GET['more'])&&($more=positive_int_filter($_GET['more']))) {
        $offset = $_GET['more']*10;
        $dynamics = [];
        $res = maria($link,"select * from Page.gossip order by time desc limit $offset,10");
        while ($each = mysqli_fetch_assoc($res))$dynamics[] = $each;
        echo json_encode(['code'=>0,'data'=>['dynamics'=>$dynamics]]);
    }
    else{
        $dNum = mysqli_fetch_row(maria($link,"select count(id) from Page.gossip"))[0];
        $dynamics = [];
        $res = maria($link,"select * from Page.gossip order by time desc limit 10");
        while ($each = mysqli_fetch_assoc($res))$dynamics[] = $each;
        echo json_encode(['code'=>0,'data'=>['dynamics'=>$dynamics,'dNum'=>$dNum]]);
    }
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}