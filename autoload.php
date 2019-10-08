<?php
/**
 *自动加载器
 */
function classLoader($class) {
	if (stripos($class, 'OSS\\') !== 0 and stripos($class, 'WndOSS\\') !== 0) {
		return;
	}

	$path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
	$file = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $path . '.php';
	if (file_exists($file)) {
		require $file;
	}
}
spl_autoload_register('classLoader');
