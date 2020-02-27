<?php //ACGT content-aside 初始化
require 'utils/init.php';
require 'links/public_link.php';
if(isset($_GET['_'])&&in_array($_GET['_'],['anime','code','game','trivial'])){
    $type = $_GET['_'];
    $typeID = array_search($type,['anime','code','game','trivial']) + 1;

    $album = [];
    $res = maria($link,"select imgSrc,description,time from Page.album where type='$type' order by time desc limit 10");
    while ($each = mysqli_fetch_assoc($res))$album[] = $each;
    $dynamic = mysqli_fetch_assoc(maria($link,"select id,content,time from Dynamic.dyn_record where type=$typeID order by id desc limit 1"));

    switch ($type){
        case 'anime':
            $bgms = [];
            $res = maria($link,"select nameCN,link,curNum,epsNum from Page.bangumi where fin=0");
            while ($each = mysqli_fetch_assoc($res))$bgms[] = $each;
            echo json_encode(['code'=>0,'data'=>['album'=>$album,'bgms'=>$bgms,'dynamic'=>$dynamic]]);
            break;
        case 'code':
            $seriesList = [];
            $res = maria($link,"
            select seriesName as name,count
            from (select seriesID,count(*) as count from Article.article_info where seriesID is not null group by seriesID) as tmp left join Article.series_link as sl 
            on tmp.seriesID=sl.sid;
            ");
            while ($each = mysqli_fetch_assoc($res))$seriesList[] = $each;
            echo json_encode(['code'=>0,'data'=>['seriesList'=>$seriesList,'dynamic'=>$dynamic]]);
            break;
        case 'game':
            echo json_encode(['code'=>0,'data'=>['album'=>$album,'dynamic'=>$dynamic]]);
            break;
        default:
            echo json_encode(['code'=>0,'data'=>['album'=>$album,'dynamic'=>$dynamic]]);
    }
}