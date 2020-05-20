<?php
require 'utils/init.php';
require 'utils/filters.php';
require 'links/public_link.php';
require 'utils/utils.php';
function visit_log($ip,$xid,$xType,$bro,$os){
    global $link;
    if ($ip){
        $loc = get_ip_loc($ip);
        if (preg_match('/google|ovh|Advanced|Microsoft/i',$loc)){
            maria($link,"update Tmp.visitor_log set visitTime=now() where id=1 limit 1");
        }
        else{
            $ip = maria_escape($ip,$link);
            $loc = maria_escape($loc,$link);
            $bro = maria_escape($bro,$link);
            $os = maria_escape($os,$link);
            $visitedVid = maria_scalar($link,"
            select id from Tmp.visitor_log
            where ip=$ip and xtype='$xType' and xid=$xid and visitTime>curdate()
            ");
            if ($visitedVid){
                maria($link,"update Tmp.visitor_log set browser=$bro,os=$os,visitTime=now() where id=$visitedVid limit 1");
            }
            else{
                maria($link,"insert into Tmp.visitor_log values (null,$ip,$loc,$bro,$os,'$xType',$xid,now())");
                if ($xType=='note')
                    maria($link,"update Note.note_info set readCount=readCount+1 where nid=$xid limit 1");
                else
                    maria($link, "update Article.article_info set readCount=readCount+1 where aid=$xid limit 1");
            }
        }
    }
}
if (php_sapi_name()!=='cli'){
    echo json_encode(['code'=>1,'message'=>'big brother is watching you']);
    die();
}
$args = getopt('',['ip:','xid:','xType:','browser:','os:']);
$ip = $args['ip'];
$xid = $args['xid'];
$xType = $args['xType'];
$browser = isset($args['browser'])?$args['browser']:'Unknown';
$os = isset($args['os'])?$args['os']:'Unknown';
@visit_log($ip,$xid,$xType,$browser,$os);