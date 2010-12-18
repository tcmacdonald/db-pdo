<?php
/**
 * DbPDO Singleton Class
 * @author tcmacdonald at gmail dot com
 **/
class DbPDO {
	private static $instance; 
	private static $id; 
	public static $dbh; 
	private function __clone() {}
	private function __construct() {
		$dbhost = DBHOST; 
		$dbname = DBNAME;
		$dbuser = DBUSER;
		$dbpass = DBPASS; 
		try {
			self::$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbuser,$dbpass);
			self::$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch (PDOException $e) {
    		die("Error!: ".$e->getMessage());
		}
	}
	/**
	 * Returns or creates single class instance
	 * @return object
	 **/
	public static function connect() {
		$c = __CLASS__; 
		return !isset(self::$instance) ? new $c : self::$instance; 
	}
	/**
	 * Returns multiple row result from query
	 * @param string $sql
	 * @return array
	 **/
	public static function fetchAll($sql,$fetch_type=PDO::FETCH_ASSOC) {
		$sth = self::$dbh->prepare($sql); 
		$sth->execute();
		return $sth->fetchAll($fetch_type);
	}
	/**
	 * Returns single row result from query
	 * @param string $sql
	 * @return array
	 **/
	public static function fetch($sql,$fetch_type=PDO::FETCH_ASSOC) {
		$sth = self::$dbh->prepare($sql); 
		$sth->execute();
		return $sth->fetch($fetch_type);
	}
	/**
	 * Returns single column result from query
	 * @param string $sql
	 * @return array
	 **/
	public static function fetchColumn($sql) {
		$sth = self::$dbh->prepare($sql); 
		$sth->execute();
		return $sth->fetchColumn($fetch_type);
	}
	/**
	 * Executes a single transactional query
	 * @param string $q
	 * @return boolean
	 **/
	public static function insert($q,$id=true) {
		try {
			self::$dbh->beginTransaction();
			$sth = self::$dbh->exec($q);
			if($id) self::$id = self::$dbh->lastInsertId(); 
			return self::$dbh->commit(); 
		} catch (PDOException $e) {
			self::$dbh->rollBack();
			return false; 
		}
	}
	/**
	 * Executes a multiple queries from prepared statement using ? placeholders
	 * @param string $stmt
	 * @param string $params array(array(...),array(...),array(...),...)
	 * @return boolean
	 **/
	public function exec($stmt=null,$params) {
		$q = self::$dbh->prepare($stmt);
		foreach($params as $arr) {
			if(!$q->execute(array_values($arr))) return false;  
		}
		return true; 
	}

	public function delete($sql) {
		return self::$dbh->exec($sql);
	}

	public static function update($q) {
		return self::insert($q,false); 
	}
	/**
	 * Prepares statement and binds specific values to placeholders contain in $arr
	 * @param string $sql - Query containing placeholders
	 * @param array $arr - Array containing placeholder values
	 * @return boolean
	 **/
	public static function bind($sql,$arrs) {
		$sth = self::$dbh->prepare($sql);
		foreach($arrs as $arr) {
			foreach($arr as $k=>$v) {
				$sth->bindValue($k,$v);
			}
			if(!$sth->execute()) return false; 
		}
		return true; 
	}
	/**
	 * Returns auto-ID from previous insert operation
	 * @return integer
	 **/
	public static function id() {
		return self::$id; 
	}
} // END class 