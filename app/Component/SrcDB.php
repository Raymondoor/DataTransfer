<?php declare(strict_types=1);
namespace DataTransfer\Component;
/**
 * Static PDO wrapper to execute queries easily.
 */
class SrcDB{
	public static \PDO $connection;
	private static function connect():void{
		$driver = $_ENV['SRC_DB_DRVR'];
		$host = $_ENV['SRC_DB_HOST'];
		$name = $_ENV['SRC_DB_NAME'];
		$user = $_ENV['SRC_DB_USER'];
		$pass = $_ENV['SRC_DB_PASS'];
		if($driver === 'sqlite'){
			if(str_starts_with($host,'/')){
				$dsn = "sqlite:".$host;
			}else{
				$dsn = "sqlite:".(ROOT_PATH.'/'.trim($host,'./'));
			}
		}else{
			$dsn = $driver.':host='.$host.';dbname='.$name;
		}
		self::$connection = new \PDO($dsn,$user,$pass,
			[
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
				\PDO::ATTR_TIMEOUT => 10
			]
		);
		if($driver === 'sqlite'){
			//
		}
		elseif($driver === 'mysql'){
			self::$connection->setAttribute(\Pdo\Mysql::ATTR_FOUND_ROWS,true);
		}// ...
	}
	private static function getConnection():\PDO{
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
