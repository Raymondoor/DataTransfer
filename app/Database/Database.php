<?php declare(strict_types=1);
namespace DataTransfer\Database;

use DataTransfer\Exception\DataTransferException;
/**
 * Static PDO wrapper to execute queries easily.
 */
abstract class Database{
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
	abstract public static function connect():void;
	public static function getConnection():\PDO{
		if(!isset(static::$connection)){
			static::connect();
		}
		return static::$connection;
	}
	/**
	 * Prepare and execute the PDOStatement.
	 * @param array<int|string,mixed> $params Positional (`?`) or named (`:key`) bind values.
	 */
	public static function run(string $query, array $params = []):\PDOStatement{
		$statement = static::getConnection()->prepare($query);
		$statement->execute($params);
		return $statement;
	}
	/**
	 * Returns result of `PDO::exec()`.
	 */
	public static function exec(string $query):int{
		return static::getConnection()->exec($query);
	}
	/**
	 * Returns DB data with `fetchAll()`
	 * @param array<int|string,mixed> $params
	 * @return array<int, array<string, mixed>> A list of database rows.
	 */
	public static function select(string $query, array $params = []):array{
		$statement = static::run($query,$params);
		return $statement->fetchAll();
	}
	/**
	 * Returns DB data with `yield` using `fetch()`
	 * @param array<int|string,mixed> $params
	 * @return \Generator<int, array<string,mixed>>
	 */
	public static function selectUnbuffered(string $query, array $params = []):\Generator{
		$statement = static::run($query,$params);
		while($data = $statement->fetch(\PDO::FETCH_ASSOC)){
			yield $data;
		}
	}
	public static function rowCount(\PDOStatement $statement):int{
		return $statement->rowCount();
	}
	public static function lastInsertId(string|null $name = null):string|bool{
		return static::getConnection()->lastInsertId($name);
	}
	public static function beginTransaction():bool{
		return static::getConnection()->beginTransaction();
	}
	public static function rollback():bool{
		return static::getConnection()->rollBack();
	}
	public static function selectColumns(string $table):array{
		$cols = match(static::$driver){
			'sqlite' => static::sanitizeSelectColumnsSqlite($table),
			'pgsql' => static::sanitizeSelectColumnsPgsql($table),
			// 'mysql' => static::select("select * from pragma_table_info('".$table."') as columns"),
			default => throw new DataTransferException('wrong driver?')
		};
		return $cols;
	}
	public static function sanitizeSelectColumnsSqlite(string $table):array{
		$raw = static::select("select name from pragma_table_info('".$table."')");
		$sanitized = [];
		foreach($raw as $column){
			$sanitized[] = $column['name'];
		}
		return $sanitized;
	}
	public static function sanitizeSelectColumnsPgsql(string $table):array{
		// @todo not implemented yet
		$raw = static::select("select column_name as columns from information_schema.columns where table_name = '".$table."'");
		$sanitized = [];
		foreach($raw as $column){
			$sanitized[] = $column['name'];
		}
		return $sanitized;
	}
}
