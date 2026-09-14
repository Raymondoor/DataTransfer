<?php declare(strict_types=1);
/**
 * Example of what belongs in test/Agent: a plain, disposable check an agent
 * runs directly (`php test/Agent/i18n-check.php`) to confirm something
 * actually works - hitting a real endpoint, poking at fake data, whatever
 * the moment calls for. Not a PHPUnit test, not PSR-4 mapped, no class
 * required. Delete or replace freely; nothing else depends on this file.
 */
require_once __DIR__.'/../../vendor/autoload.php';

use Frrame\Component\I18n;

I18n::load('public', 'en');
$title = I18n::t('public.welcome.title', ['name' => 'Frrame']);

if($title !== 'Welcome to Frrame!'){
    fwrite(STDERR, "FAIL: expected 'Welcome to Frrame!', got '{$title}'".PHP_EOL);
    exit(1);
}

echo "OK: {$title}".PHP_EOL;
