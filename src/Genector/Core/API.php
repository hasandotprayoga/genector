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

	public $data;

	private function sendData($data)
	{
		return json_encode($data);
	}

	public function create()
	{
		
		return $this->send($this->sendData([
			'action'=>'create',
			'data'=>$this->data
		]));
	}

	public function update()
	{
		
		return $this->send($this->sendData([
			'action'=>'update',
			'data'=>$this->data
		]));
	}

	public function delete()
	{
		
		return $this->send($this->sendData([
			'action'=>'delete',
			'data'=>$this->data
		]));
	}

	private function send($data)
	{
		$ch = curl_init( $this->getConfig('url') );
		# Setup request to send json via POST.
		$payload = json_encode($data);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		# Return response instead of printing.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		# Auth
		curl_setopt(
	   		$ch, 
	   		CURLOPT_USERPWD, 
	   		$this->getConfig('username').":".$this->getConfig('password')
	   	);
		# Send request.
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
		# Print response.
		return $result;
	}

	private function auth()
	{
	}

	private function getConfig($data){ return $this->arrObj($this->config)->$data; }

	public function arrObj($arr){ return (object) $arr; }
}