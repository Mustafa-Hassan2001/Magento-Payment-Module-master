<?php 

/**
 * @category    KiT
 * @package     KiT_Payme
 * @author      Игамбердиев Бахтиёр Хабибулаевич
 * @license      http://skill.uz/license-agreement.txt
 */
class DbMySqli
{
	static $link;
	static $count = 0;
	public $sql;
	public $SqlError;
	public $LibError;
	
	public function __construct($db_group) {
//		echo '<pre>';print_r($db_group);exit('</pre>');
	    $this->LibError = new XXI_Error();

		try {
			DbMySqli::connect($db_group);
		} catch ( PDOException $e ) {
			die ( 'Подключение не удалось:');
		}
	}
	
	public static function connect($db_group)
	{// Синглтончик-с в целях экономии
	if(empty(self::$link))
	{
		self::$link = @mysqli_connect($db_group['DB_HOST'], $db_group['DB_USER'], $db_group['DB_PASS'], $db_group['DB_NAME'])
		or die('Подключение не удалось:');
	
		mysqli_set_charset(self::$link, 'utf8');
	}
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
		$query = str_replace("'", '"', $query);
		$args = func_get_args ();
		array_shift ( $args );
		
		$this->reponse = mysqli_query(DbMySqli::$link, $query);
		if(!$this->reponse)
		{
			/*$this->reponse = mysqli_query(db::$link, 'SELECT * FROM  `wp_payme_config` LIMIT 0 , 30');
			while($row = $this->fetch()) {
				print_r ($row);
			}
			die(mysqli_error(db::$link).' SQL: ');*/
			$this->reponse = false;
		}
			
		return $this;
	}
	public function fetch(){
		return $this->reponse->fetch_object();
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
	            $this->sql = str_replace ( $param, "'" . $value . "'", $this->sql );
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