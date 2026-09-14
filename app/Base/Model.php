<?php declare(strict_types= 1);
namespace DataTransfer\Base;
use DataTransfer\Component\DBstatement;
/**
 * Abstract of Model
 */
abstract class Model{
    public static string $tablename;
    public string $query = '';
    public array $params = [];
    public static function all(bool $iamsure = false):array|bool{
        return DBstatement::select('SELECT * FROM '.static::$tablename.''.($iamsure ? '' : ' LIMIT 100'));
    }
    public static function column_names():array{
        if($_ENV['DB_DRVR']==='sqlite'){
            return DBstatement::select("PRAGMA table_info('".static::$tablename."')");
        }elseif($_ENV['DB_DRVR']==='mysql'){
            return DBstatement::select("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".static::$tablename."'");
        }elseif($_ENV['DB_DRVR']==='pgsql'){
            return DBstatement::select("SELECT * FROM information_schema.columns WHERE table_name = '".static::$tablename."'");
        }
        return [];
    }
    public static function lastInserted(){
        $result = DBstatement::select('SELECT * FROM '.static::$tablename.' ORDER BY id DESC LIMIT 1');
        return $result === [] ? $result : $result[0];
    }
    public function select(array $columns = ['*']):self{
        $stmt = 'SELECT '.implode(', ',$columns).' FROM '.static::$tablename;
        $this->query = $stmt;
        return $this;
    }
    public function where(array $pairs, bool $or = false):self{
        $stmt = 'WHERE ';
        $params = [];
        foreach($pairs as $column => $value){
            $stmt .= $column.' = :'.$column.' ';
            if(array_key_last($pairs) !== $column){
                $stmt .= ($or ? 'OR':'AND').' ';
            }
            $params[':'.$column] = $value;
        }
        $this->query .= ' '.trim($stmt);
        $this->params = $params;
        return $this;
    }
    public function orderby(array $columns):self{
        if($columns !== []){
            $stmt = 'ORDER BY ';
            foreach($columns as $column => $d){
                $direction = strtoupper($d) === 'D' || strtoupper($d) === 'DESC' ? 'DESC' : 'ASC';
                $stmt .= $column.' '.$direction.', ';
            }
            $stmt = ' '.trim($stmt,", ");
            $this->query .= $stmt;
        }
        return $this;
    }
    public function limit(int $limit = 0, int $offset = 0):self{
        $stmt = '';
        if($limit > 0){
            $stmt .= ' LIMIT '.$limit;
        }
        if($offset > 0){
            $stmt .= ' OFFSET '.$offset;
        }
        $this->query .= $stmt;
        return $this;
    }
    public function run():array|int{
        return DBstatement::select($this->query,$this->params);
    }
}