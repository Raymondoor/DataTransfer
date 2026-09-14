<?php declare(strict_types=1);
require_once __DIR__.'/../../../vendor/autoload.php';

use Faker\Factory as FakerFactory;
use Frrame\Component\DBstatement;
use Monolog\Level;

// Seeds the `logs` table (resource/migration/CreateLogsTable.php) with fake rows.
// Mirrors script/migration/up.php's plain, array-free-of-magic style.
$faker = FakerFactory::create();

for($i = 0; $i < 20; $i++){
    DBstatement::run(
        "INSERT INTO logs (channel, message, level, level_name, context, extra) VALUES (:channel, :message, :level, :level_name, :context, :extra)",
        [
            ':channel' => $faker->randomElement(['app', 'auth', 'db']),
            ':message' => $faker->sentence(),
            ':level' => Level::Info->value,
            ':level_name' => Level::Info->getName(),
            ':context' => null,
            ':extra' => null,
        ]
    );
}

echo "Seeded 20 fake rows into `logs`.".PHP_EOL;
