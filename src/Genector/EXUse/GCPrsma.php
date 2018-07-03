<?php

namespace app\connector;

use app\connector\genector\core\Oracle;
use app\models\APPLICATION;

/**
 * 
 */
class GCPrsma extends Oracle
{

	public $config =  [
		'host'=>'localhost',
		'username'=>'IDM_HASAN',
		'password'=>'12345'
	];

	private $tableUser = 'SAMPLEE';
	private $tableRole = 'ROLEE';

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
			'USERNAME'=>$this->data->USR_LOGIN,
			'PASSWORD'=>'create',
		];

		$cu = $this->create($this->tableUser,$data);
		if ($cu->status=='success') {

			// return $cu->record;

			$this->createRole($cu->record->ID);

			return [
				'status'=>'success',
			];
		}
	}

	private function updateUser()
	{
		$data = [
			'USERNAME'=>$this->data->USR_LOGIN,
			'PASSWORD'=>'update'
		];

		$cu = $this->update($this->tableUser,$data,['USERNAME'=>$this->data->USR_LOGIN]);
		if ($cu) {

			$this->createRole($cu->record->ID, 1);

			return [
				'status'=>'success'
			];
		}
	}

	private function deleteUser()
	{
		$cu = $this->delete($this->tableUser,['USERNAME'=>$this->data->USR_LOGIN]);
		if ($cu) {

			if (is_null($this->tableRole) or $this->tableRole == '' or $this->tableRole == $this->tableUser) {
				// 
			}else{
					$this->deleteRole($cu->record->ID);
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


		// if (!is_null($act)) {
		// 	if ($act['status']=='success') {
		// 		$this->log();
		// 	}
		// }

		return $act;
	}

	private function createRole($id, $deleteOld = false)
	{
		if (!is_null($this->role)) {
			if (is_null($this->tableRole) or $this->tableRole == '' or $this->tableRole == $this->tableUser) {
				$this->update($this->tableUser,['ROLE'=>$this->role],['ID'=>$id]);
			}else{
				if ($deleteOld) {
					$this->delete($this->tableRole,['USER_ID'=>$id]);
				}

				if (is_array($this->role)) {
					foreach ($this->role as $k => $v) {
						$cr = $this->create($this->tableRole,[
							'ID'=>rand(),
							'USER_ID'=>$id,
							'NAME'=>$v
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
				$this->delete($this->tableRole,['USER_ID'=>$id]);
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
}