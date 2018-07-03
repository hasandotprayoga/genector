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

		$key = $this->primaryKey($table)->COLUMN_NAME;

		$sql = oci_parse($this->conn(), "INSERT INTO $table ($fields) VALUES($values) returning $key into :ID");

		OCIBindByName($sql,":ID",$id);

		$exec = oci_execute($sql);

		if ($exec) {

			$record = $this->record($table, [$key=>$id]);

			return $this->arrObj(['status'=>'success','record'=>$record]);
		}else{
			return false;
		}

	}

	public function update($table, $data, $cond){
		$data = $this->getData($data);
		$condition = $this->getCond($cond);
		
		$sql = oci_parse($this->conn(), "UPDATE $table SET $data WHERE $condition");

		$exec = oci_execute($sql);

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

		$sql = oci_parse($this->conn(), "DELETE FROM $table WHERE $condition");

		$exec = oci_execute($sql);

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

		$sql = oci_parse($this->conn(), "select * from $table where $condition");

		$exec = oci_execute($sql);

		return $this->arrObj(oci_fetch_array($sql,OCI_ASSOC+OCI_RETURN_NULLS));
	}

	private function primaryKey($table)
	{
		$sql = oci_parse($this->conn(), "SELECT COLS.COLUMN_NAME FROM ALL_CONSTRAINTS CONST, ALL_CONS_COLUMNS COLS WHERE COLS.TABLE_NAME = 'SAMPLEE' AND CONST.CONSTRAINT_TYPE = 'P'  AND CONST.CONSTRAINT_NAME = COLS.CONSTRAINT_NAME AND CONST.OWNER= COLS.OWNER ORDER BY COLS.TABLE_NAME, COLS.POSITION");

		$exec = oci_execute($sql);

		return $this->arrObj(oci_fetch_array($sql));
	}

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