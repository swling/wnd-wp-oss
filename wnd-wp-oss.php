<?php

/**
 *Plugin Name: Wnd-WP-OSS
 *Plugin URI: https://wndwp.com
 *Description: 基于阿里云官方PHP SDK，将WordPress文件上传平滑移动到阿里云对象存储
 *Version: 0.11
 *Author: swling
 *Author URI: https://wndwp.com
 *
 *
 *@since 2019.07.26
 */
$option = get_option('wndoss');

require __DIR__ . '/autoload.php';
require __DIR__ . '/options.php';
require __DIR__ . '/class-wnd-oss.php';
require __DIR__ . '/class-wnd-cdn.php';

new WND\OSS\Wnd_OSS();

/**
 *@since 2019.07.29
 *新增CDN功能
 */
if ($option['wndoss_enable_cdn'] ?? false) {
	new WND\CDN\Wnd_CDN();
}
