<?php

namespace app\connector\genector\core;
/**
 * Bismillah,
 * * GENECTOR V1.
 * * * by HASANDOTPRAYOGA
 * * * 11 Syawal 1439
 *************************
 * * * 0813-1940-3309
 * * * hasandotprayoga@gmail.com
 ********************************
 */

class Oracle
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

		$sql = oci_parse($this->conn(), "INSERT INTO $table ($fields) VALUES($values)");

		$exec = oci_execute($sql);

		if ($exec) {
			return ['status'=>'success'];
		}else{
			return false;
		}

	}

	public function update($table, $data, $cond){
		$data = $this->getData($data);
		$condition = $this->getCond($cond);
		
		$sql = oci_parse($this->conn(), "UPDATE $table SET $data WHERE $condition");

		return oci_execute($sql);
	}

	public function delete($table, $cond){

		$condition = $this->getCond($cond);

		$sql = oci_parse($this->conn(), "DELETE FROM $table WHERE $condition");

		return oci_execute($sql);
	}

	/**
	*
	*/

	private function conn(){
		$conn = oci_connect($this->getConfig('username'),$this->getConfig('password'),$this->getConfig('host'));

		if (!$conn) {
			return false;
		}else{
		    return $conn;
		}

	}

	private function disconn(){ return  oci_close($this->conn()); }

	private function getVal($arr){
		$val = [];

	    foreach ($arr as $k => $v) {
	        $val[$k] = $v;
	    }

	    return "'".join("', '",$val)."'";
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