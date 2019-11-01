<?php //link页面获取链接
require 'utils/init.php';
require 'links/public_link.php';
$headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='link' limit 1"));
$friendList = [];
$res = maria($link,"select uname,avatar,message,ulink from User.user where isFriend=1");
while ($each = mysqli_fetch_assoc($res))$friendList[] = $each;
$outerLinks = [];
$res = maria($link,"select * from Page.link order by name asc ");
while ($each = mysqli_fetch_assoc($res)){
    if (isset($outerLinks[$each['type']])){
        $type = $each['type'];
        unset($each['type']);
        $outerLinks[$type][] = $each;

    }
    else{
        $outerLinks[$each['type']] = [];
        $type = $each['type'];
        unset($each['type']);
        $outerLinks[$type][] = $each;
    }
}
echo json_encode(['code'=>0,'data'=>['FList'=>$friendList,'outerLinks'=>$outerLinks,'headerInfo'=>$headerInfo]]);