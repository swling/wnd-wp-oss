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

require __DIR__ . '/autoload.php';
if (is_admin()) {
	require __DIR__ . '/options.php';
}

// 启用OSS
new WndOSS\WndOSS();

/**
 *@since 2019.07.29
 *新增CDN功能
 */
if (get_option('wndoss')['wndoss_enable_cdn'] ?? false) {
	new WndOSS\WndCDN();
}
