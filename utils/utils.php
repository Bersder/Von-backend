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