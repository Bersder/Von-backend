<?php //系列页面接口
require 'utils/init.php';
require 'utils/filters.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');
if (isset($_GET['serName'])&&($serName=maria_str_notnull_filter($_GET['serName'],$link))){
    $res = maria($link,"
        select seriesName as serName,description as serDes
        from Article.series_link as ser
        where ser.seriesName=$serName
        limit 1
    ");
    $serInfo = mysqli_fetch_assoc($res);

    $serArts = [];
    $res = maria($link,"
        select aid as id,type,title,preview,time,commentCount,tags
        from Article.article_info as ai,Article.series_link as ser
        where ai.series=ser.seriesName and seriesName=$serName
        order by time desc 
    ");
    while ($each=mysqli_fetch_assoc($res))$serArts[] = $each;


    $serList = [];
    if (isset($_GET['init'])){
        $res = maria($link,"
        select series as serName,count(*) as count
        from Article.article_info as ai
        where series is not null
        group by series
    ");
        while ($each=mysqli_fetch_assoc($res))$serList[] = $each;
    }

    echo json_encode(['code'=>0,'data'=>['serInfo'=>$serInfo,'serArts'=>$serArts,'serList'=>$serList]],JSON_NUMERIC_CHECK);
}
else{
    echo json_encode(['code'=>1]);
}
