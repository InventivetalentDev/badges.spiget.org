<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

function extractFormat(&$query, &$format)
{
    $formatSplit = preg_split("/\\./", $query);
    if (count($formatSplit) === 2) {
        $format = $formatSplit[1];
        $query = $formatSplit[0];
    }
    return $query;
}

function extractOptions(&$query, &$label, &$color)
{
    // <label>-<color>-<resource>
    $optionsSplit = preg_split("/-/", $query);
    if (count($optionsSplit) === 3) {
        $label = $optionsSplit[0];
        $color = $optionsSplit[1];
        $query = $optionsSplit[2];
    }
    return $query;
}


use PUGX\Poser\Render\SvgRender;
use PUGX\Poser\Poser;


function displayBadge($left, $right, $color, $format = "svg", $style = "plastic")
{
    if ($format === "svg") {
        header("Content-Type: image/svg+xml;charset=utf-8");
    } else {
        header("Content-Type: image/$format");
    }
    header("X-Badge-Left: $left");
    header("X-Badge-Right: $right");
    header("X-Badge-Color: $color");
    header("X-Badge-Style: $style");
    header("X-Badge-Format: $format");

    $poser = new Poser(array(
        new SvgRender()
    ));
    $image = $poser->generate($left, $right, $color, $style);

    if ($format === "svg") {
        echo $image;
    } else {
        // http://stackoverflow.com/questions/13125352/readimageblob-fatal-error-when-converting-svg-into-png
        $image = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>' . $image;

        $im = new Imagick();
        $im->readImageBlob($image);
        $im->setImageFormat($format);
        echo $im;
        $im->clear();
        $im->destroy();
    }

    exit();
}