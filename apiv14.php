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
        from Article.article_info as ai,Article.series_link as sl
        where ai.seriesID=sl.sid and seriesName=$serName
        order by time desc 
    ");
    while ($each=mysqli_fetch_assoc($res))$serArts[] = $each;


    $serList = [];
    if (isset($_GET['init'])){
        $res = maria($link,"
        select seriesName as serName,count
        from (select seriesID,count(*) as count from Article.article_info where seriesID is not null group by seriesID) as tmp left join Article.series_link as sl 
        on tmp.seriesID=sl.sid
        union
        select seriesName as serName,0 as count
        from Article.series_link
        where sid not in (select distinct seriesID from Article.article_info where seriesID is not null);
    ");
        while ($each=mysqli_fetch_assoc($res))$serList[] = $each;
        array_multisort(array_column($serList,'serName'),SORT_ASC,$serList);//按系列名升序排序
    }

    echo json_encode(['code'=>0,'data'=>['serInfo'=>$serInfo,'serArts'=>$serArts,'serList'=>$serList]],JSON_NUMERIC_CHECK);
}
else{
    echo json_encode(['code'=>1]);
}
