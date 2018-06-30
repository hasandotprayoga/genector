<?php 
namespace app\connector\genector\core;
/**
 * Bismillah,
 * * GENECTOR V1.
 * * * by HASANDOTPRAYOGA
 * * * 16 Syawal 1439
 *************************
 * * * 0813-1940-3309
 * * * hasandotprayoga@gmail.com
 ********************************
 */

class API
{

	public $config;
	/*
		$config = [
			'url'=>'http://www.nyontoh.com/',
			'username'=>'username',
			'password'=>'password'
		];
	*/
	
	private $init;

	private $data=[];
	
	public function __construct()
	{
	   	$this->init = curl_init();

	   	$this->option();
	   	$this->auth();
	}

	public function __destruct()
	{
		curl_close($this->init);
	}

	public function create($data)
	{
		$this->data = [
			'action'=>'create',
			'data'=>$data
		];
		return $this->call('POST', json_encode($this->data));
	}

	public function update($data, $key)
	{
		$this->data = [
			'action'=>'update',
			'data'=>$data,
			'key'=>$key
		];
		return $this->call('POST', json_encode($this->data));
	}

	public function delete($key)
	{
		$this->data = [
			'action'=>'delete',
			'data'=>null,
			'key'=>$key
		];
		return $this->call('POST', json_encode($this->data));
	}

	private function call($method, $data)
	{
	   	switch ($method){
	      	case "POST":
	        	curl_setopt($this->init, CURLOPT_POST, 1);
	        	if ($data)
	            	curl_setopt($this->init, CURLOPT_POSTFIELDS, $data);
	        	break;
	      	// default:
	       //  	if ($data)
	       //      	$this->config['url'] = sprintf("%s?%s", $this->getConfig('url'), http_build_query($data));
	   	}
	   	
	   	$result = curl_exec($this->init);
	   
	   	if(!$result){die("Connection Failure");}
	   	
	   	return $result;
	}

	private function option()
	{
	   	curl_setopt($this->init, CURLOPT_URL, $this->getConfig('url'));
	   	// OPTIONS:
	   	curl_setopt($this->init, CURLOPT_HTTPHEADER, array(
	    	'Content-Type: application/json',
	   	));
	   	curl_setopt($this->init, CURLOPT_RETURNTRANSFER, 1);
	}

	private function auth()
	{
	   	curl_setopt($this->init, CURLOPT_USERPWD, $this->getConfig('username').":".$this->getConfig('password'));
	}

	private function getConfig($data){ return $this->arrObj($this->config)->$data; }

	public function arrObj($arr){ return (object) $arr; }
}