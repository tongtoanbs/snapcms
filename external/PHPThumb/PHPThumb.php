<?php
// SETTINGS
// 
// To squeak a few more KB of memory performance out of this script replace the dynamic settings
// here with static paths.

// The path to PHPThumb


$path = Yii::getPathOfAlias('vendor.weotch.phpthumb.src');
require_once $path . '/ThumbLib.inc.php';

// The path to your web root
//$document_path = '/'.trim($_SERVER['DOCUMENT_ROOT'], '/').($_SERVER['DOCUMENT_ROOT']!=''?'/':'');
$document_path = '';

// The path to your cache folder
$cache_path = __DIR__.'/../../../../../public_html/cache/';

// The URI to your cache folder
$cache_uri = Yii::app()->controller->createFrontendUrl('/cache') .'/';

// How long caches should live. Remember, hard refreshes will also clear out your cache so you'll be
// safe setting this pretty high
$cache_life = '-1 month';

// End configurable settings

$params = array('src'=>false, 'w'=>false, 'h'=>false);
$options = array('resizeUp'=>true,'jpegQuality'=>100,'cache_life'=>$cache_life);
extract(array_merge($params, $options, $_GET));

$last = strrpos($src,'/');
$shortSrcPos = strrpos($src, '/', $last - strlen($src) - 1);
$shortSrc = substr($src, $shortSrcPos);
$cache = md5($shortSrc).'_'.$w.'_'.$h;
$cache_path.= $cache;

if (
	file_exists($cache_path) &&
	($cache_life == false || filemtime($cache_path) > strtotime($cache_life)) &&
	@$_SERVER['HTTP_CACHE_CONTROL'] != 'no-cache'
)
{
	header('Content-type: image/jpeg');
	header('Location: '.$cache_uri.$cache);
	exit();
}
else
{
	if (!file_exists($src))
	{
		$src = $document_path.$src;
	}
	
	$thumb = PhpThumbFactory::create($src);
	$thumb->setOptions($options);
	$thumb->adaptiveResize($w, $h);
	
	$thumb->save($cache_path);
}

$thumb->show();
flush();