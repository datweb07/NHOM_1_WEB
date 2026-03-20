<?php

function clientRoute(string $uri): void
{
	$path = trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

	if ($path === '' || $path === 'index.php') {
		require_once dirname(__DIR__, 2) . '/views/client/home/index.php';
		return;
	}

	if ($path === 'san-pham' || $path === 'san-pham/list') {
		require_once dirname(__DIR__, 2) . '/views/client/san_pham/list.php';
		return;
	}

	if ($path === 'san-pham/chi-tiet' || $path === 'san-pham/detail') {
		require_once dirname(__DIR__, 2) . '/views/client/san_pham/detail.php';
		return;
	}

	require_once dirname(__DIR__, 2) . '/views/client/home/index.php';
}

