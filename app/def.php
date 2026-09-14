<?php declare(strict_types=1);
/**
 * Absolute path to root of this application. This is where `composer.json` or `vendor/` may exist.
 */
define('ROOT_PATH',realpath(__DIR__.'/../'));
define('APP_PATH',realpath(ROOT_PATH.'/app'));
define('PUBLIC_PATH',realpath(ROOT_PATH.'/public'));
define('RESOURCE_PATH',realpath(ROOT_PATH.'/resource'));
define('HOME_URL',$_ENV['APP_PROTOCOL'].'://'.$_ENV['APP_DOMAIN'].(!empty($_ENV['APP_PORT']) ? ':'.$_ENV['APP_PORT'] : '').$_ENV['APP_URI']);
define('IMAGE_URL',HOME_URL.'/asset/image');
