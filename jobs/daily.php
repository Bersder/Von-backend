<?php//定期任务
require 'links/secret_link.php';
require 'utils/init.php';
if (php_sapi_name()!=='cli'){
    echo json_encode(['code'=>1,'message'=>'big brother is watching you']);
    die();
}
maria($link,"truncate table Tmp.visit_log");
maria($link,"update Tmp.auth_log set remain=3");