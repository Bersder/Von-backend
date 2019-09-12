<?php //相册初始化/加载更多
require '../utils/init.php';
require '../utils/filters.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');
if (isset($_POST['token'])&&($auth = token_authorize($_POST['token']))){
    if (isset($_GET['pn'])&&isset($_GET['type'])){
        if ($pn=positive_int_filter($_GET['pn'])){
            $offset = ($pn-1)*20;
            if ($type=in_array_filter($_GET['type'],['anime','game','trivial'])){
                $pictures = [];
                $res = maria($link,"select id,imgSrc,description,time from Page.album where type='$type' order by time desc limit $offset,20");
                while ($each = mysqli_fetch_assoc($res))$pictures[] = $each;
                echo json_encode(['code'=>0,'data'=>['pictures'=>$pictures]]);
            }
            else{
                $pictures = [];
                $res = maria($link,"select id,imgSrc,description,time from Page.album order by time desc limit $offset,20");
                while ($each = mysqli_fetch_assoc($res))$pictures[] = $each;
                echo json_encode(['code'=>0,'data'=>['pictures'=>$pictures]]);
            }

        }
        else
            echo json_encode(['code'=>1]);
    }
    else{
        $counts = [];
        $res = maria($link,"select type,count(id) as count from Page.album group by type");
        while ($each=mysqli_fetch_row($res))$counts[$each[0]] = $each[1];
        $pictures = [];
        $res =  maria($link,"select id,imgSrc,description,time from Page.album order by time desc limit 20");
        while ($each=mysqli_fetch_assoc($res))$pictures[] = $each;
        echo json_encode(['code'=>0,'data'=>['pictures'=>$pictures,'counts'=>$counts]],JSON_NUMERIC_CHECK);
    }
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}