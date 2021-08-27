<?php

class Mysql{

		static private $link = null;
		static private $info = array(
			'last_query' => null,
			'num_rows' => null,
			'insert_id' => null
		);
		static private $connection_info = array();

		static private $where;
		static private $limit;
		static private $order;

		function __construct($host, $user, $pass, $db){
			self::$connection_info = array('host' => $host, 'user' => $user, 'pass' => $pass, 'db' => $db);
		}

		function __destruct(){
			if(is_resource(self::$link)) mysql_close(self::$link);
		}

		/**
		 * Setter method
		 */

		static private function set($field, $value){
			self::$info[$field] = $value;
		}

		/**
		 * Getter methods
		 */

		public function last_query(){
			return self::$info['last_query'];
		}

		public function num_rows(){
			return self::$info['num_rows'];
		}

		public function insert_id(){
			return self::$info['insert_id'];
		}

		/**
		 * Create or return a connection to the MySQL server.
		 */

		static private function connection(){
			if(!is_resource(self::$link) || empty(self::$link)){
				if(($link = mysql_connect(self::$connection_info['host'], self::$connection_info['user'], self::$connection_info['pass'])) && mysql_select_db(self::$connection_info['db'], $link)){
					self::$link = $link;
					mysql_set_charset('utf8');
				}else{
					throw new Exception('Could not connect to MySQL database.');
				}
			}
			return self::$link;
		}

		/**
		 * MySQL Where methods
		 */

		static private function __where($info, $type = 'AND'){
			$link =& self::connection();
			$where = self::$where;
			foreach($info as $row => $value){
				if(empty($where)){
					$where = sprintf("WHERE `%s`='%s'", $row, mysql_real_escape_string($value));
				}else{
					$where .= sprintf(" %s `%s`='%s'", $type, $row, mysql_real_escape_string($value));
				}
			}
			self::$where = $where;
		}

		public function where($field, $equal = null){
			if(is_array($field)){
				self::__where($field);
			}else{
				self::__where(array($field => $equal));
			}
			return $this;
		}


		public function get($table, $select = '*'){
			$link =& self::connection();
			if(is_array($select)){
				$cols = '';
				foreach($select as $col){
					$cols .= "`{$col}`,";
				}
				$select = substr($cols, 0, -1);
			}
			$sql = sprintf("SELECT %s FROM %s%s", $select, $table, self::extra());
			self::set('last_query', $sql);
			if(!($result = mysql_query($sql))){
				throw new Exception('Error executing MySQL query: '.$sql.'. MySQL error '.mysql_errno().': '.mysql_error());
				$data = false;
			}elseif(is_resource($result)){
				$num_rows = mysql_num_rows($result);
				self::set('num_rows', $num_rows);
				if($num_rows === 0){
					$data = false;
				}elseif(preg_match('/LIMIT 1/', $sql)){
					$data = mysql_fetch_assoc($result);
				}else{
					$data = array();
					while($row = mysql_fetch_assoc($result)){
						$data[] = $row;
					}
				}
			}else{
				$data = false;
			}
			mysql_free_result($result);
			return $data;
		}

		public function insert($table, $data){
			$link =& self::connection();
			$fields = '';
			$values = '';
			foreach($data as $col => $value){
				$fields .= sprintf("`%s`,", $col);
				$values .= sprintf("'%s',", mysql_real_escape_string($value));
			}
			$fields = substr($fields, 0, -1);
			$values = substr($values, 0, -1);
			$sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, $fields, $values);
			self::set('last_query', $sql);
			if(!mysql_query($sql)){
				throw new Exception('Error executing MySQL query: '.$sql.'. MySQL error '.mysql_errno().': '.mysql_error());
			}else{
				self::set('insert_id', mysql_insert_id());
				return true;
			}
		}

	}
