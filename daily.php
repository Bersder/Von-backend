<?php//每日任务
require 'links/secret_link.php';
require 'utils/init.php';

maria($link,"truncate table Tmp.visit_log");
maria($link,"update Tmp.auth_log set remain=3");