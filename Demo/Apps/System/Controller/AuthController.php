<?php
class AuthController extends Syx_Controller
{
	protected $_messages = array(
		Syx_Auth_Result::FAILURE_IDENTITY_NOT_FOUND => '用户密码不匹配。',
		Syx_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS => '用户冲突。',
		Syx_Auth_Result::FAILURE_CREDENTIAL_INVALID => '用户密码不匹配。',
		Syx_Auth_Result::FAILURE_IDENTITY_IS_NULL => '帐号或密码为空。',
		Syx_Auth_Result::FAILURE_CREDENTIAL_IS_NULL => '帐号或密码为空。',
		Syx_Auth_Result::FAILURE_UNCATEGORIZED => '未识别的登录错误。',
		Syx_Auth_Result::FAILURE => '登录失败。',
		Syx_Auth_Result::SUCCESS => '登录成功。'
	);

	public function login()
	{
		if ($this->_request->isPost()) {
			$storage = new Syx_Auth_Storage_Session('New_Admin', 'login');

			if ($storage->read()) {
				// 已经登录
				// die('已经登录');
				// 跳转
			}

			if (!($db = Syx_Db::getDefaultAdapter())) {
				throw new Xms_Exception('A db connection was not provided.');
			}


			$auth = Syx_Auth::getInstance();

			$auth->setStorage($storage);

			$result = $auth->authenticate(new Xms_Auth_DbTable($db, $this->_request));

			$message = $this->_messages[$result->getCode()];

			echo $message;
		} else {
			$this->assign('basePath', $this->_request->getBasePath());
			$this->assign('baseUrl', $this->_request->getBaseUrl());
			$this->assign('photo', $this->imageDataURI(ROOT_PATH . '/Public/Assets/photo.png'));
		}
	}

	private function imageDataURI($photo)
	{
		$info = getimagesize($photo);
		$data = base64_encode(file_get_contents($photo));
		$mime = image_type_to_mime_type($info[2]);
		return 'data:' . $mime . ';base64,' . $data;
	}

	public function logout()
	{

	}

	public function forgot()
	{

	}
}