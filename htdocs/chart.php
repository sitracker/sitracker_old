<?php
// chart.php - Outputs a chart in png format using the GD library
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// TODO Stub

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

if (!extension_loaded('gd')) trigger_error("{$CONFIG['application_name']} requires the gd module", E_USER_ERROR);

// External variables
$type = $_REQUEST['type'];
$data = explode('|',cleanvar($_REQUEST['data']));
$legends = explode('|',cleanvar($_REQUEST['legends']));
$title = urldecode(cleanvar($_REQUEST['title']));

$countdata = count($data);
$sumdata = array_sum($data);

// Graph settings
$width = 500;
$height = 150;
if ($countdata > 8) $height += (($countdata - 8) * 14);

$img = imagecreatetruecolor($width, $height);

$white = imagecolorallocate($img, 255, 255, 255);
$blue = imagecolorallocate($img, 240, 240, 255);
$black = imagecolorallocate($img, 0, 0, 0);
$red = imagecolorallocate($img, 255, 0, 0);

imagefill($img, 0, 0, $white);

$rgb[] = "190,190,255";

$rgb[] = "205,255,255";
$rgb[] = "255,255,156";
$rgb[] = "156,255,156";

$rgb[] = "255,205,195";
$rgb[] = "255,140,255";
$rgb[] = "100,100,155";
$rgb[] = "98,153,90";
$rgb[] = "205,210,230";
$rgb[] = "192,100,100";
$rgb[] = "204,204,0";
$rgb[] = "255,102,102";
$rgb[] = "0,204,204";
$rgb[] = "0,255,0";
$rgb[] = "255,168,88";
$rgb[] = "128,0,128";
$rgb[] = "0,153,153";
$rgb[] = "255,230,204";
$rgb[] = "128,170,213";
$rgb[] = "75,75,75";
// repeats...
$rgb[] = "190,190,255";
$rgb[] = "156,255,156";
$rgb[] = "255,255,156";
$rgb[] = "205,255,255";
$rgb[] = "255,205,195";
$rgb[] = "255,140,255";
$rgb[] = "100,100,155";
$rgb[] = "98,153,90";
$rgb[] = "205,210,230";
$rgb[] = "192,100,100";
$rgb[] = "204,204,0";
$rgb[] = "255,102,102";
$rgb[] = "0,204,204";
$rgb[] = "0,255,0";
$rgb[] = "255,168,88";
$rgb[] = "128,0,128";
$rgb[] = "0,153,153";
$rgb[] = "255,230,204";
$rgb[] = "128,170,213";
$rgb[] = "75,75,75";



switch ($type)
{
    case 'pie':
        // ImageString($img,3, 10, 10, "Pie Chart $countdata / $sumdata", $black);
        // for($i=0;$i<=$Randomized;$i++){$data[$i]=rand(2,20);};//full array with garbage.
        $cx = '120';$cy ='60'; //Set Pie Postition. CenterX,CenterY
        $sx = '200';$sy='100';$sz ='15';// Set Size-dimensions. SizeX,SizeY,SizeZ

        if (!empty($title))
        {
            $cy += 10;
            imagestring($img,2, 2, ($legendY-1), "{$title}", $black);
        }


        //convert to angles.
        for($i=0;$i<=$countdata;$i++)
        {
            $angle[$i] = (($data[$i] / $sumdata) * 360);
            $angle_sum[$i] = array_sum($angle);
        };

        $background = imagecolorallocate($img, 255, 255, 255);
        //Random colors.

        for($i=0;$i<=$countdata;$i++)
        {
            $rgbcolors = explode(',',$rgb[$i]);
            $colors[$i] = imagecolorallocate($img,$rgbcolors[0],$rgbcolors[1],$rgbcolors[2]);
            $colord[$i] = imagecolorallocate($img,($rgbcolors[0]/1.5),($rgbcolors[1]/1.5),($rgbcolors[2]/1.5));
        }


        //3D effect.
        $legendY = 80 - ($countdata * 10);
        if ($legendY < 10) $legendY = 10;
        for($z=1;$z<=$sz;$z++)
        {
            for($i=0;$i<$countdata;$i++)
            {
               imagefilledarc($img,$cx,($cy+$sz)-$z,$sx,$sy,$angle_sum[$i-1],$angle_sum[$i],$colord[$i],IMG_ARC_PIE);
            };

        };
        imagerectangle($img, 250, $legendY-5, 470, $legendY+($countdata*15), $black);
        //Top pie.
        for($i=0;$i<$countdata;$i++)
        {
            imagefilledarc($img,$cx,$cy,$sx,$sy,$angle_sum[$i-1] ,$angle_sum[$i], $colors[$i], IMG_ARC_PIE);
            imagefilledrectangle($img, 255, ($legendY+1), 264, ($legendY+9), $colors[$i]);
            imagestring($img,2, 270, ($legendY-1), substr(urldecode($legends[$i]),0,27)." ({$data[$i]})", $black);
            // imagearc($img,$cx,$cy,$sx,$sy,$angle_sum[$i1] ,$angle_sum[$i], $blue);
            $legendY+=15;
        };
    break;

    default:
        imagestring($img,3, 10, 10, "Invalid chart type", $red);
}



// output to browser
// flush image
header('Content-type: image/png');
header("Content-disposition-type: attachment\r\n");
header("Content-disposition: filename=sit_chart_".date('Y-m-d').".png");
imagepng($img);
imagedestroy($img);

?>