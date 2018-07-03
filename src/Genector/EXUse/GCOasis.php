<?php

namespace app\connector;

use app\connector\genector\core\MySql;
use app\models\RECONOASIS;
use app\models\APPLICATION;

/**
 * 
 */
class GCOasis extends MySql
{
	public $config = [
		'host'=>'localhost',
		'username'=>'root',
		'password'=>'toor',
		'database'=>'sample',
	];

	private $tableUser = 'user';
	private $tableRole = 'role';

	private $data;
	private $role;

	public function __construct($data)
	{
		$this->data = $this->arrObj($data);
		$this->role = $this->role();
	}

	private function createUser()
	{
		$data = [
			'username'=>$this->data->USR_LOGIN,
			'password'=>'create'
		];

		$cu = $this->create($this->tableUser,$data);
		if ($cu->status=='success') {

			$this->createRole($cu->record->id);

			return [
				'status'=>'success',
			];
		}
	}

	private function updateUser()
	{
		$data = [
			'username'=>$this->data->USR_LOGIN,
			'password'=>'update'
		];

		$cu = $this->update($this->tableUser,$data,['username'=>$this->data->USR_LOGIN]);
		if ($cu) {

			$this->createRole($cu->record->id, 1);

			return [
				'status'=>'success'
			];
		}
	}

	private function deleteUser()
	{
		$cu = $this->delete($this->tableUser,['username'=>$this->data->USR_LOGIN]);
		if ($cu) {
			if (is_null($this->tableRole) or $this->tableRole == '' or $this->tableRole == $this->tableUser) {
				// 
			}else{

				$this->deleteRole($cu->record->id);

			}

			return [
				'status'=>'success'
			];
		}
	}

	public function runNow()
	{	
		$act = null;
		switch ($this->data->CONNECTOR_ACTION) {
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
			if ($act['status']=='success') {
				$this->log();
			}
		}

		return $act;
	}

	private function createRole($id, $deleteOld = false)
	{
		if (!is_null($this->role)) {
			if (is_null($this->tableRole) or $this->tableRole == '' or $this->tableRole == $this->tableUser) {
				$this->update($this->tableUser,['role'=>$this->role],['id'=>$id]);
			}else{
				if ($deleteOld) {
					$this->delete($this->tableRole,['user_id'=>$id]);
				}

				if (is_array($this->role)) {
					foreach ($this->role as $k => $v) {
						$cr = $this->create($this->tableRole,[
							'user_id'=>$id,
							'role'=>$v
						]);
					}
				}
			}
		}
	}

	private function deleteRole($id)
	{
		if (!is_null($this->role)) {
			if (!is_null($this->tableRole) or $this->tableRole !== '' or $this->tableRole !== $this->tableUser) {
				$this->delete($this->tableRole,['user_id'=>$id]);
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
		$app = APPLICATION::getApplicationdata($this->data->ID_APPLICATION);
		return $app[$data];
	}


	private function log()
	{
	    $sysdate = date('Y/m/d H:i:s');
        $model                         = new RECONOASIS();
        $model->DATA_PROVIDER          = '';
        $model->DRIVER                 = '';
        $model->PARAM_IP_ADDRESS       = $this->data->PARAM_IP_ADDRESS;
        $model->PARAM_USERNAME         = $this->data->PARAM_USERNAME;
        $model->PARAM_PASSWORD         = $this->data->PARAM_PASSWORD;
        $model->SCHEMA                 = $this->data->SCHEMA;
        $model->USR_LOGIN              = $this->data->USR_LOGIN;
        $model->OWNER                  = 'SEED';
        $model->USR_PASSWORD           = $this->data->USR_PASSWORD;
        $model->USR_UDF_NIK            = $this->data->USR_UDF_NIK;     
        $model->USR_EMAIL              = $this->data->USR_EMAIL;
        $model->ID_REQUEST_APPLICATION = $this->data->ID_REQUEST_APPLICATION;
        $model->SEX                    = '';     
        $model->TITLE                  = '';
        $model->USR_UDF_MOBILE_NO      = $this->data->USR_UDF_MOBILE_NO;
        $model->ROLE_KEY               = $this->data->ROLE_KEY;     
        $model->STANDARD               = '';
        $model->DESCRIPTION            = '';
        $model->CDEATED_DATE           = $sysdate;
        $model->save(false);
	}


}

