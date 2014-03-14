<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	public $page_heading;
	public $page_heading_subtext;
	
	public function init()
	{
		if(Yii::app()->request->pathInfo != 'content-type/status') 
		{
			$data = ContentType::findAll();
			foreach($data as $ct) 
			{ 
				$ct->checkForErrors();
				if($ct->hasSchemaErrors()) 
				{
					Yii::app()->user->setFlash('danger',
						'<strong>Warning!</strong> Some content types database tables are not up to date. '.
						CHtml::link('Please update them here.', array('contentType/status'))
					);
				}
			}
		}
	}

	
	public function getModuleMenus($menuType)
	{
		$menu = array();
		$SnapCMSModule = Yii::app()->getModule('snapcms');
		foreach($SnapCMSModule->modules as $id=>$module)
		{
			$className = Yii::import($module['class']);
			$Module = new $className($id,$SnapCMSModule);
			if(method_exists($Module,'getMenu'))
				$menu = $Module->getMenu($menuType);
			//$menu += array('label'=>$Module->name, 'url'=>array('/snapcms/'.$id), 'items'=>$Module->menu, 'visible'=>!Yii::app()->user->isGuest);
		}
		return $menu;
	}
	
	public function createFrontendUrl($route, $params=array())
	{
		$baseUrl = Yii::app()->baseUrl;
		$backUrl = Yii::app()->urlManager->createUrl($route, $params, '&', true);
		//@todo: must be a better way to do this..
		$url = substr($baseUrl,0,-strlen('/admin')) . substr($backUrl,strlen($baseUrl)); //Removes admin/
		return $url;
	}
	
	public function createBackendUrl($route, $params=array())
	{
		//@todo: must be a better way to do this..
		$baseUrl = Yii::app()->baseUrl;		
		$frontUrl = Yii::app()->urlManager->createUrl($route, $params, '&', true);
		$frontUrl = substr($frontUrl,strlen($baseUrl));
		$url = $baseUrl.'/admin'.$frontUrl; //Adds admin/
		return $url;
	}
	
	public function isEditable()
	{
		return Yii::app()->user->checkAccess('Update Content') ? 'true' : 'false';
	}
}