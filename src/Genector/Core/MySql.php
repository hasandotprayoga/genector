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

		if ($exec) {
			return ['status'=>'success','id'=>$exec->insert_id];
		}else{
			return false;
		}
	}

	public function update($table, $data, $cond){
		$data = $this->getData($data);
		$condition = $this->getCond($cond);
		$sql = "UPDATE $table SET $data WHERE $condition";

		return $this->conn()->query($sql);
	}

	public function delete($table, $cond){

		$condition = $this->getCond($cond);
		$sql = "DELETE FROM $table WHERE $condition";

		return $this->conn()->query($sql);
	}

	/**
	*
	*/

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
	        $d[] = "$k='$v'";
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