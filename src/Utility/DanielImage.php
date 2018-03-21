<?php

namespace App\Utility;

class DanielImage
{
	public static function resizeGreaterSideAndKeepingRatio($image, $size)
	{
		$w = $image->getWidth();
        $h = $image->getHeight();

        $maiorLado = ($w >= $h) ? $w : $h;
        if ($maiorLado > $size) {
            $image = $image->resize($size, $size, 'inside');
        }

        return $image;
	}
}