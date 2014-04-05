<?php
class SnapWebUser extends CWebUser
{
	public $allowAutoLogin = true;
    public $identityCookie = array(
        'path' => '/',
        'domain' => 'snapcms.local',
    );

	protected function afterLogin($fromCookie)
	{			
		if($this->checkAccess('Update Content'))
		{
			$_SESSION['KCFINDER'] = array();
			$_SESSION['KCFINDER']['disabled'] = false;
			$_SESSION['KCFINDER']['uploadURL'] = "/uploads";
			$_SESSION['KCFINDER']['uploadDir'] = "../../uploads/";
		}
	}
}