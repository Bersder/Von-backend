<?php
function thumb_img($src,$dst=null,$maxsize=300,$quality=20){ //生成缩略图
    $type = strtolower(substr(strrchr($src,'.'),1));
    if ($type=='jpg'||$type=='jpeg')
        $srcImg = imagecreatefromjpeg($src);
    elseif ($type=='png')
        $srcImg = imagecreatefrompng($src);
    elseif ($type=='gif')
        $srcImg = imagecreatefromgif($src);
    elseif ($type=='bmp')
        $srcImg = imagecreatefrombmp($src);
//    elseif ($type=='webp')
//        $srcImg = imagecreatefromwebp($src);
    if (isset($srcImg)){//没有Waring
        $wh = getimagesize($src);
        $width = $wh[0];
        $height = $wh[1];

        if ($width>$maxsize||$height>$maxsize){
            if ($width>$height){
                $resize_width = $maxsize;
                $resize_height = $resize_width/$width*$height;
            }
            elseif ($width<$height){
                $resize_height = $maxsize;
                $resize_width = $resize_height/$height*$width;
            }
            else{
                $resize_width = $resize_height = $maxsize;
            }
        }
        else{
            $resize_width = $width;
            $resize_height = $height;
        }

        $dstImg = imagecreatetruecolor($resize_width, $resize_height);

        if ($type=='png')
            imagefill($dstImg,0,0,imagecolorallocatealpha($dstImg,0,0,0,127));
        elseif ($type=='gif')
            imagefill($dstImg,0,0,imagecolorallocate($dstImg,250,250,250));
        imagecopyresampled($dstImg,$srcImg,0,0,0,0,$resize_width,$resize_height,$width,$height);
        if ($type=='png'){
            if (!$dst)
                $dst = $src.'.thumb';
            imagesavealpha($dstImg,true);
            imagepng($dstImg,$dst);
        }
        else{
            if (!$dst)
                $dst = $src.'.thumb';
            imagejpeg($dstImg,$dst,$quality);
        }
        return $dst;
    }
    else
        return false;
}
function get_ip(){
    $ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;
    if ($ip&&preg_match('/^(\d{1,3}\.){3}\d{1,3}$/',$ip))
        return $ip;
    else
        return false;
}
function get_ip_loc($ip){
    $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
    $data = json_decode(file_get_contents($url),true);
    if ($data['code']!=0)
        return 'Unknown Area';
    else{
        $tmp = $data['data'];
        $country = ($tmp['country']=='中国'||$tmp['country']=='XX')?'':$tmp['country'];
        $region = $tmp['region']=='XX'?'':$tmp['region'];
        $city = $tmp['city']=='XX'?'':$tmp['city'];
        $isp = $tmp['isp']=='XX'?'':' '.$tmp['isp'];
        return $country.$region.$city.$isp;
    }
}