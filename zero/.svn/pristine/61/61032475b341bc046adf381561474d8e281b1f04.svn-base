<?php
spl_autoload_register(function ($className) {
	$className = str_replace('Zero\\', '', $className);
	$path = '/'.str_replace('\\', '/', $className).'.php';
	if (file_exists(__DIR__.$path)) {
		require_once __DIR__.$path;
	}
});