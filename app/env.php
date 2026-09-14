<?php declare(strict_types=1);
# Load `.env` to global
try{
	$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'/../');
	$dotenv->load();
	$dotenv->required(['APP_PROD', 'APP_DEBUG'])->allowedValues(['0', '1']);
}catch(\Throwable $t){
	http_response_code(500);
	echo 'Cannot load environment variables.';
	exit(1);
}