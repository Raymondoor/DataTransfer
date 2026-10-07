<?php declare(strict_types=1);
namespace R3T\Database;
use R3T\Exception\R3TException;
/**
 * Static PDO wrapper to execute queries easily.
 */
class OprDB extends Database{
	public static \PDO $connection;
	public static string $driver;
	public static string $host;
	public static string $name;
	public static string $user;
	public static string $pass;
	public static array $options = [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
		\PDO::ATTR_TIMEOUT => 10
	];
	public static function dropAllTables():bool{
		$tables = self::selectAllTables();
		foreach($tables as $table){
			self::exec("DROP TABLE IF EXISTS ".$table);
		}
		return true;
	}
}
