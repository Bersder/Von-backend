<?php //acg页面content-aside初始化
require 'utils/init.php';
require 'links/public_link.php';
if(isset($_GET['_'])&&in_array($_GET['_'],['anime','code','game',])){
    $type = $_GET['_'];
    $typeID = array_search($type,['anime','code','game']) + 1;
    $album = [];
    $dynamic = mysqli_fetch_assoc(maria($link,"select id,content,time from Dynamic.dyn_record where type=$typeID order by id desc limit 1"));
    $headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='$type' limit 1"));
    if ($type!='code'){
        $res = maria($link,"select imgSrc,description,time from Page.album where type='$type' order by time desc limit 15");
        while ($each = mysqli_fetch_assoc($res))$album[] = $each;
    }
    if ($type=='code'){
        $seriesList = [];
        $res = maria($link,"
        select seriesName as name,count
        from (select seriesID,count(*) as count from Article.article_info where seriesID is not null group by seriesID) as tmp left join Article.series_link as sl 
        on tmp.seriesID=sl.sid;
        ");
        while ($each = mysqli_fetch_assoc($res))$seriesList[] = $each;
        echo json_encode(['code'=>0,'data'=>['album'=>$album,'dynamic'=>$dynamic,'seriesList'=>$seriesList,'headerInfo'=>$headerInfo]]);

    }
    elseif($type=='anime'){
        // 还有追番列表
        $bgms = [];
        $res = maria($link,"select nameCN,link,curNum,epsNum from Page.bangumi where fin=0");
        while ($each = mysqli_fetch_assoc($res))$bgms[] = $each;
        echo json_encode(['code'=>0,'data'=>['album'=>$album,'bgms'=>$bgms,'dynamic'=>$dynamic,'headerInfo'=>$headerInfo]]);
    }
    else
        echo json_encode(['code'=>0,'data'=>['album'=>$album,'dynamic'=>$dynamic,'headerInfo'=>$headerInfo]]);
}