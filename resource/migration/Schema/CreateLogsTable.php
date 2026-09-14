<?php declare(strict_types=1);
namespace Frrame\Migration;
use Raymondoor\Migrr\App\Schema\SqliteSchema;
final class CreateLogsTable{
    public static string $table = 'logs';
    public static function up():string{
        return new SqliteSchema()->create_table(self::$table)
            ->columns()
                ->id_template()
                ->nextColumnName('channel')->text()->notnull()
                ->nextColumnName('level')->int()->notnull()
                ->nextColumnName('level_name')->varchar(12)->notnull()
                ->nextColumnName('message')->text()
                ->nextColumnName('context')->json()
                ->nextColumnName('extra')->json()
                ->nextColumn()->created_at_template()
            ->endColumns()
        ->end()->query;
    }
}