<?php
require 'utils/init.php';
require 'links/public_link.php';
require 'utils/utils.php';
function visit_log($ip,$xid,$xType){
    global $link;
    if ($ip){
        $loc = mysqli_real_escape_string($link,get_ip_loc($ip));
        maria($link,"insert into Tmp.visit_log values('$ip','$loc','$xType',$xid,1) on duplicate key update pv=pv+1");
        $pv = mysqli_fetch_row(maria($link,"select pv from Tmp.visit_log where ip='$ip' and xtype='$xType' and xid=$xid"))[0];
        if ($pv<=3){
            if ($xType=='note')
                maria($link,"update Note.note_info set readCount=readCount+1 where nid=$xid limit 1");
            else
                maria($link, "update Article.article_info set readCount=readCount+1 where aid=$xid limit 1");
        }
    }
}
if (php_sapi_name()!=='cli'){
    echo json_encode(['code'=>1,'message'=>'big brother is watching you']);
    die();
}
$args = getopt('',['ip:','xid:','xType:']);
$ip = $args['ip'];
$xid = $args['xid'];
$xType = $args['xType'];
@visit_log($ip,$xid,$xType);