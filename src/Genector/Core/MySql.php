<?php

namespace app\connector\genector\core;
/**
 * Bismillah,
 * * GENECTOR V1.
 * * * by HASANDOTPRAYOGA
 * * * 23 Romadhon 1439
 *************************
 * * * 0813-1940-3309
 * * * hasandotprayoga@gmail.com
 ********************************
 */

class MySql
{

	/*
	*
	$config = [
		'host'=>'Your Host',
		'username'=>'Your Username',
		'password'=>'your Password'
	]; 
	*
	*/
	public $config;
	
	public function __construct(){ 
		// $this->config = $arr;
		return $this->conn();
	}

	public function __destruct(){ return $this->disconn(); }

	/**
	*
	*/

	public function create($table, $data){
		$fields = $this->getField($data);
		$values = $this->getVal($data);

		$sql = "INSERT INTO $table ($fields) VALUES ($values)";

		$exec = $this->conn();
		$exec->query($sql);

		$key = $this->primaryKey($table)->Column_name;

		if ($exec) {
			$record =$this->record($table, [$key => $exec->insert_id]);
			
			return $this->arrObj(['status'=>'success','record'=>$record]);
		}else{
			return false;
		}
	}

	public function read($table, $select, $cond=false){
		if ($select !== '*') {
			$columns = $this->getVal($select);
		}else{
			$columns = '*';
		}

		if ($cond) {
			$sql = "SELECT $columns FROM $table where $cond";
		}else{
			$sql = "SELECT $columns FROM $table";
		}

		$result = $this->conn()->query($sql);
		if ($result->num_rows > 0) {
		    while( $row = $result->fetch_assoc()){
			    $arr[] = $row;
			}

			return $arr;
		} else {
		    return false;
		}
	}

	public function update($table, $data, $cond){
		$data = $this->getData($data);
		$condition = $this->getCond($cond);
		$sql = "UPDATE $table SET $data WHERE $condition";

		$exec = $this->conn();
		$exec->query($sql);

		if ($exec) {
			$record =$this->record($table, $cond);
			return $this->arrObj(['status'=>'success','record'=>$record]);
		}else{
			return false;
		}
	}

	public function delete($table, $cond){

		$condition = $this->getCond($cond);
		$record =$this->record($table, $cond);
		
		$sql = "DELETE FROM $table WHERE $condition";

		$exec = $this->conn()->query($sql);

		if ($exec) {
			return $this->arrObj(['status'=>'success','record'=>$record]);
		}else{
			return false;
		}
	}

	/**
	*
	*/

	private function record($table, $cond)
	{	
		$condition = $this->getCond($cond);
		$q = "select * from $table where $condition";
		$exec = $this->conn()->query($q);

		return $this->arrObj($exec->fetch_assoc());
	}

	private function primaryKey($table)
	{
		$q = "show keys from $table where key_name = 'PRIMARY'";

		$exec = $this->conn()->query($q);

		return $this->arrObj($exec->fetch_assoc());
	}

	private function conn(){
		$conn = new \mysqli(
			$this->getConfig('host'),
			$this->getConfig('username'),
			$this->getConfig('password'),
			$this->getConfig('database')
		);

		if ($conn->connect_error) {
		    return false;
		}else{
			return $conn;
		}
	}

	private function disconn(){ return $this->conn()->close(); }

	private function getVal($arr){
		$val = [];

	    foreach ($arr as $k => $v) {
	        $val[$k] = $v;
	    }

	    return '"'.join('", "',$val).'"';
	}

	private function getField($arr){ return implode(", ", array_keys($arr)); }

	private function getData($arr){
		$d = [];

	    foreach ($arr as $k => $v) {
	        if (is_array($v)) {
	    		$val = implode(", ", $v);
	    	}else{
	    		$val = $v;
	    	}

	    	$d[] = "$k='$val'";
	    }
	    
	    return join(', ',$d);
	}

	private function getCond($arr){
		$d = [];

	    foreach ($arr as $k => $v) {
	    	$d[] = "$k='$v'";
	    }

	    return join(", ",$d);
	    
	}

	private function getConfig($data){ return $this->arrObj($this->config)->$data; }

	public function arrObj($arr){ return (object) $arr; }

}

?>