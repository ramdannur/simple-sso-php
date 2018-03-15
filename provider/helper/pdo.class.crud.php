<?php
class crud extends PDO{
	private $enggine;
	private $host;
	private $database;
	private $user;
	private $pass;
	private $result;
	public function __construct(){
		$this->enggine = 'mysql';
		$this->host = 'localhost';
		$this->database = 'thq_sso_db';
		$this->user = 'root';
		$this->pass = '';
		$dns = $this->enggine.':dbname='.$this->database.';host='.$this->host;
		parent::__construct($dns, $this->user, $this->pass);
	}
	public function insert($tabel, $rows=null){
		$command = 'INSERT INTO '.$tabel;
		$row = null;
		$value = null;
		foreach ($rows as $key => $nilainya) {
			$row .=",".$key;
			$value .=", :".$key;
		}
		$command .="(".substr($row,1).")";
		$command .="VALUES(".substr($value, 1).")";
		$stmt = parent::prepare($command);
		$stmt->execute($rows);
		$rowcount = $stmt->rowcount();
		return $rowcount;
	}
	public function delete($tabel, $where=null){
		$command = 'DELETE FROM '.$tabel;
		$list = Array();
		$parameter = null;
		foreach ($where as $key => $value) {
			$list[] = "$key = :$key";
			$parameter .=', ":'.$key.'":"'.$value.'"';
		}
		$command .=' WHERE '.implode(' AND ', $list);
		$json = "{".substr($parameter, 1)."}";
		$param = json_decode($json, true);
		$query = parent::prepare($command);
		$query->execute($param);
		$rowcount = $query->rowcount();
		return $rowcount;
	}
	public function update($tabel, $field, $where = null){
		$update = 'UPDATE '.$tabel.' SET ';
		$set = null;
		$value = null;
		foreach ($field as $key => $values) {
			$set .=', '.$key.' = :'.$key;
			$value .=', ":'.$key.'":"'.$values.'"';
		}
		$update .= substr(trim($set), 1);
		$json = '{'.substr($value,1).'}';
		$param = json_decode($json,true);
		if ($where != null) {
			$update .=' WHERE '.$where;
		}
		$query = parent::prepare($update);
		$query->execute($param);
		$rowcount = $query->rowcount();
		return $rowcount;
	}
	public function select($tabel, $rows, $where = null, $order = null, $limit = null, $group = null){
		$command = 'SELECT '.$rows.' FROM '.$tabel;
		if ($where != null) {
			$command .=' WHERE '.$where;
		}
		if ($order != null) {
			$command .=' ORDER BY '.$order;
		}
		if ($limit != null) {
			$command .=' LIMIT '.$limit;
		}
		if ($group != null) {
			$command .=' GROUP BY '.$group;
		}
   #   echo $command;
		$query = parent::prepare($command);
		$query->execute();
		$posts = array();
		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$posts[] = $row;
		}
		return $this->result =  json_encode(array('stand'=>$posts));
	}
	public function getReult(){
		return $this->result;
	}
	public function halo(){
		return "halo";
	}
}
#config stat
$db_konek = "Connected!";
?>