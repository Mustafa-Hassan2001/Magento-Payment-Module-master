<?php 

/**
 * @category    KiT
 * @package     KiT_Payme
 * @author      Игамбердиев Бахтиёр Хабибулаевич
 * @license      http://skill.uz/license-agreement.txt
 */


class MySql extends PDO
{
	public $sql;
	public $SqlError;
	public $LibError;
	public function __construct($db_group) {
//		echo '<pre>';print_r($db_group);exit('</pre>');
	    $this->LibError = new XXI_Error();

		try {
			parent::__construct ( 'mysql:host=' . $db_group['DB_HOST'] . ';port=' . $db_group['DB_PORT'] . ';dbname=' . $db_group['DB_NAME'], $db_group['DB_USER'], $db_group['DB_PASS'] );
			$this->exec ( "set names " . $db_group['CHARSET'] );
			$this->setAttribute ( parent::ATTR_DEFAULT_FETCH_MODE, parent::FETCH_OBJ );
		} catch ( PDOException $e ) {
			
			die ( 'Подключение не удалось:');
			$this->LibError->TryCatch($e->getMessage());
		}
	}
	public function LogMysql($LogFile, $str){
		
		if (!$fp = fopen(BASEPATH.$LogFile, 'a')) {
			$this->FileError = "Не могу открыть файл";
			return false;
		}
		if (is_writable(BASEPATH.$LogFile)) {
			if (fwrite($fp, $str."\r") === FALSE) {
				return false;
			}
			fclose($fp);
			return true;
		}
	}
	public function query_($query = false) {
		if (! $query) {
			$this->sql = str_replace('{TABLE_PREFIX}', TABLE_PREFIX, $this->sql);
			$query = $this->sql;
		}
		else 
		{
			$query = str_replace('{TABLE_PREFIX}', TABLE_PREFIX, $query);
		}
		try
		{
			$this->setAttribute ( parent::ATTR_DEFAULT_FETCH_MODE, parent::FETCH_OBJ );
			$this->SqlInfo = $this->exec($query);
		}
		catch  (Exception $e) {
			$this->SqlError = parent::errorInfo();
			$this->LibError->TryCatch($e->getMessage());
		}
		
		return $this->SqlInfo;
	}
	
	public function query($query = false) {
		if (! $query) {
			$this->sql = str_replace('{TABLE_PREFIX}', TABLE_PREFIX, $this->sql);
			$query = $this->sql;
		}
		else
		{
			$query = str_replace('{TABLE_PREFIX}', TABLE_PREFIX, $query);
		}
		$args = func_get_args ();
		array_shift ( $args );
		
		$reponse = parent::prepare ( $query );
		if(!$reponse)
		{
		    $this->SqlError = parent::errorInfo();
		}
        try		
		{
		    $reponse->execute($args);
		}
		catch  (Exception $e) {
		    $this->SqlError = parent::errorInfo();
		    $this->LibError->TryCatch($e->getMessage());
		}
		return $reponse;
	}
	public function insecureQuery($query) { // you can use the old query at your risk ;) and should use secure quote() function with it
		return parent::query ( $query );
	}
	
	public function paramArray($value)
	{
	    if(is_array($value))
	    {
	        $i = 0;
	        foreach ($value As $key=>$val)
	        {
	            if ($i == 0) {
	                $id_m .= "'" . $val . "'";
	            } else {
	                $id_m .= ', \'' . $val . "'";
	            }
	            $i++;
	        }
	    }
	    else
	    {
	        for($i = 0; $i <= $value->_count - 1; $i ++) {
	            if ($i == 0) {
	                $id_m .= "'" . $value->$i . "'";
	            } else {
	                $id_m .= ', \'' . $value->$i . "'";
	            }
	        }
	    }
	    return $id_m;
	}
	public function param($param, $value, $param_Mysql = '') {
	    if ($param_Mysql == 'in' or is_array($value)) {
	        $id_m = $this->paramArray($value);
	        $this->sql = str_replace ( $param, $id_m, $this->sql );
	    }
	    elseif ($value == "NULL")
	    {
	        $this->sql = str_replace ( $param, 0, $this->sql );
	    }
	    elseif ($value == "'00'")
	    {
	        $this->sql = str_replace ( $param, "'00'", $this->sql );
	    }
	    elseif ($value == '0' And $value != Null)
	    {
	        $this->sql = str_replace ( $param, 0, $this->sql );
	    }
	    elseif ($value == 'CONF_PARAM') {
	        //$value = $$param_Mysql;
	        $value = explode('::', $param_Mysql);
	
	        if (gettype ( $value ) == 'integer') {
	            $this->sql = str_replace ( $param, $value, $this->sql );
	        } elseif ($value == 'NOW()') {
	            $this->sql = str_replace ( $param, 'NOW()', $this->sql );
	        } elseif ($value == NULL) {
	            $this->sql = str_replace ( $param, 'NULL', $this->sql );
	        } else {
	            $this->sql = str_replace ( $param, "'" . $value . "'", $this->sql );
	        }
	    } else {
	        if (gettype ( $value ) == 'integer') {
	            $this->sql = str_replace ( $param, $value, $this->sql );
	        } elseif ($value == 'NOW()') {
	            $this->sql = str_replace ( $param, 'NOW()', $this->sql );
	        } elseif ($value == NULL) {
	            $this->sql = str_replace ( $param, 'NULL', $this->sql );
	        } else {
	            $this->sql = str_replace ( $param, "'" . $value . "'", $this->sql );
	        }
	    }
	    return $this->sql;
	}

}
?>