<?php

function isFuhao($c) {
  return strpos(" ,.!　，。！", $c) !== false;
}
function splitText($str) {
  $r = [];
  $len = mb_strlen($str, "utf-8");
  for ($i=0; $i<$len; $i++) {
    $c = mb_substr($str, $i, 1, "utf-8");
    $s = $c;
    if (preg_match("/^[a-z]$/i", $c)) {
      for ($i++; $i<$len; $i++) {
        $c = mb_substr($str, $i, 1, "utf-8");
        if (preg_match("/^[a-z]$/i", $c)) {
          $s .= $c;
        } else {
          break;
        }
      }
      $i--;
    }
    for ($i++; $i<$len; $i++) {
      $c = mb_substr($str, $i, 1, "utf-8");
      if (isFuhao($c)) {
        $s .= $c;
      } else {
        break;
      }
    }
    $i--;
    $r[] = $s;
  }
  return $r;
}

function drawText($ih, $str, $x, $y, $fontColor, $fontSize, $maxWidth = 100, $maxLine = 4) {
  $sarr = splitText($str);
  $cnt = count($sarr);
  $teststr = "";
  $realstr = "";
  $currline = 1;
  $fontfile = "/home/xiaopeng/draw/simsun.ttf";
  $lineHeight = 0;
  for ($i=0; $i<$cnt; $i++) {
    $teststr = $realstr . $sarr[$i];
    $box = imagettfbbox($fontSize, 0, $fontfile, $teststr);
    if ($box[2] - $box[0] > $maxWidth) {
      if ($realstr == "") {// 一个字符组就超长了，单独处理
        $lineHeight = $box[3] - $box[5];
        $realstr = $sarr[$i];
        echo "output $realstr\n";
        imagettftext($ih, $fontSize, 0, $x, $y, $fontColor, $fontfile, $realstr);

        $realstr = "";
      } else {// 再加一个就超长了
        if ($currline == $maxLine) {
          $realstr .= "...";
        }
        echo "output $realstr\n";
        imagettftext($ih, $fontSize, 0, $x, $y, $fontColor, $fontfile, $realstr);

        $realstr = $sarr[$i];
      }
      if ($currline == $maxLine) {
        echo "over line\n";
        break;
      }
      $currline++;
      $y += $lineHeight + 10;
      if ($currline == $maxLine) {
        $maxWidth -= $fontSize * 2;// 留出省略号
      }
    } else {
      $realstr .= $sarr[$i];
      $lineHeight = $box[3] - $box[5];
    }
  }
  if ($currline < $maxLine && !empty($realstr)) {
    echo "output $realstr\n";
    imagettftext($ih, $fontSize, 0, $x, $y, $fontColor, $fontfile, $realstr);
  }

  //imagecolorallocate($ih, );
}

function copyImage($ih, $img, $x, $y, $w) {
  $sz = getimagesize($img);
  print_r($sz);
  $srcWidth = $sz[0];
  $srcHeight = $sz[1];

  $src = @imagecreatefromjpeg($img);
  imagecopyresized($ih, $src, 0, 0, 0, 0, $w, $w * $srcHeight / $srcWidth, $srcWidth, $srcHeight);
  imagedestroy($src);
}

function createImage($text) {
  $ih = imagecreatetruecolor(1200, 1700);
  $bgcolor = imagecolorallocate($ih, 255, 255, 255);
  imagefill($ih, 0, 0, $bgcolor);

  $fontColor = imagecolorallocate($ih, 0, 0, 0);
  copyImage($ih, "/home/xiaopeng/draw/origin.jpg", 0, 0, 1200);
  drawText($ih, $text, 200, 1000, $fontColor, 40, 600, 4);
  imagepng($ih, "/home/webroot/default/testpng.png");
  imagedestroy($ih);
}

splitText("abc def gif,fa fads fad fga fa");
splitText("下发放，放发嘎嘎发的！阿法狗-发给4-8我个。发给");
createImage("Plover2018新款休闲男秋冬季商务休闲时尚潮流修身夹克男士外套内衣女蕾丝镂空性感薄款聚拢法式无钢圈交叉细带美背少女文胸套装");

