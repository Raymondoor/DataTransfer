<?php declare(strict_types=1);
namespace DataTransfer\Database;
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
	public static function connect():void{
		if(self::$driver === 'sqlite'){
			$dsn = "sqlite:".self::$host;
		}else{
			$dsn = self::$driver.':host='.self::$host.';dbname='.self::$name;
		}
		self::$connection = new \PDO($dsn,self::$user,self::$pass,self::$options);
		// if(self::$driver === 'sqlite'){
		// 	//
		// }
		// elseif(self::$driver === 'mysql'){
		// 	self::$connection->setAttribute(\Pdo\Mysql::ATTR_FOUND_ROWS,true);
		// }// ...
	}
	public static function getConnection():\PDO{
		if(!isset(self::$connection)){
			self::connect();
		}
		return self::$connection;
	}
	/**
	 * Prepare and execute the PDOStatement.
	 * @param array<int|string,mixed> $params Positional (`?`) or named (`:key`) bind values.
	 */
	public static function run(string $query, array $params = []):\PDOStatement{
		$statement = self::getConnection()->prepare($query);
		$statement->execute($params);
		return $statement;
	}
	/**
	 * Returns result of `PDO::exec()`.
	 */
	public static function exec(string $query):int{
		return self::getConnection()->exec($query);
	}
	/**
	 * Returns DB data with `fetchAll()`
	 * @param array<int|string,mixed> $params
	 * @return array<int, array<string, mixed>> A list of database rows.
	 */
	public static function select(string $query, array $params = []):array{
		$statement = self::run($query,$params);
		return $statement->fetchAll();
	}
	/**
	 * Returns DB data with `yield` using `fetch()`
	 * @param array<int|string,mixed> $params
	 * @return \Generator<int, array<string,mixed>>
	 */
	public static function selectUnbuffered(string $query, array $params = []):\Generator{
		$statement = self::run($query,$params);
		while($data = $statement->fetch(\PDO::FETCH_ASSOC)){
			yield $data;
		}
	}
	public static function rowCount(\PDOStatement $statement):int{
		return $statement->rowCount();
	}
	public static function lastInsertId(string|null $name = null):string|bool{
		return self::getConnection()->lastInsertId($name);
	}
	public static function beginTransaction():bool{
		return self::getConnection()->beginTransaction();
	}
	public static function rollback():bool{
		return self::getConnection()->rollBack();
	}
}
