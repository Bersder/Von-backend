<?php //标签页初始化
require 'utils/init.php';
require 'links/public_link.php';
$tagCountList = [];
$res = maria($link,"
select tid as id,tagName,ifnull(count,0) as count
from Tag.tag_cloud as tc left join (select count(tid) as count,tid from Tag.tag_map group by tid) as tmp
using(tid)
order by tagName asc;
");
while ($each = mysqli_fetch_assoc($res))$tagCountList[] = $each;
echo json_encode(['code'=>0,'data'=>['tagCountList'=>$tagCountList]]);