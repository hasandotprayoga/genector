<?php 

namespace app\connector;
use app\models\APPLICATION;
use app\models\RECONPRVGT;

use app\connector\genector\core\API;

/**
 * 
 */
class GCPrvgt extends API
{
	public $config = [
		'url'=>'http://localhost/ngapi/curl/proses.php',
		'username'=>'admin',
		'password'=>'admin'
	];

	private $dataReq;
	private $role;

	public function __construct($data)
	{
		$this->dataReq = $this->arrObj($data);
		$this->role = $this->role();
	}

	public function createUser()
	{
		$this->data = [
			'profile'=>[
				'username'=>$this->dataReq->USR_LOGIN,
				'password'=>'createPass',
			],
			'role'=>$this->role
		];

		return $this->create();
	}

	public function updateUser()
	{
		$this->data = [
			'profile'=>[
				'username'=>$this->dataReq->USR_LOGIN,
				'password'=>'createPass',
			],
			'role'=>$this->role,
			'cond'=>[
				'username'=>$this->dataReq->USR_LOGIN
			]
		];

		return $this->update();
	}

	public function deleteUser()
	{
		$this->data = [
			'cond'=>[
				'username'=>$this->dataReq->USR_LOGIN
			]
		];

		return $this->delete();
	}

	public function runNow()
	{	
		$act = null;
		switch ($this->dataReq->CONNECTOR_ACTION) {
			case 'create':
				$act = $this->createUser();
				break;
			case 'update':
				$act = $this->updateUser();
				break;
			case 'delete':
				$act = $this->deleteUser();
				break;
			}


		if (!is_null($act)) {
			if ($act->status=='success') {
				$this->log();
				return ['status'=>'success'];
			}
		}

	}

	private function role()
	{

		$multi=strpos($this->data->ROLE_KEY,"~");

		if ($multi) {
			$role = explode("~", $this->data->ROLE_KEY);
			
			if ($this->app('MULTIPLE_ROLE') == 1) {
				return $this->role = $role;
			}else{
				return $this->role = $role[0];
			}
		}else{
			return $this->role = $this->data->ROLE_KEY;
		}


	}

	private function app($data)
	{
		$app = APPLICATION::getApplicationdata($this->dataReq->ID_APPLICATION);
		return $app[$data];
	}

	private function log()
	{
	   	$sysdate                 = date('Y/m/d H:i:s');
        $model                   = new RECONPRVGT();
        $model->DRIVER           = '';
        $model->PARAM_IP_ADDRESS = $this->dataReq->PARAM_IP_ADDRESS;
        $model->PARAM_USERNAME   = $this->dataReq->PARAM_USERNAME;
        $model->PARAM_PASSWORD   = $this->dataReq->PARAM_PASSWORD;
        $model->USR_UDF_NIK      = $this->dataReq->USR_UDF_NIK;     
        $model->USR_FIRST_NAME   = $this->dataReq->USR_FIRST_NAME;
        $model->LOCATIONS        = '';
        $model->USR_PASSWORD     = 'PRVGT123';     
        $model->ROLE_KEY         = $this->dataReq->ROLE_KEY;     
        $model->SCHEMA           = '';
        $model->CREATED_DATE     = $sysdate;
        $model->save(false);
	}
}