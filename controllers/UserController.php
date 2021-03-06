<?php

class UserController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('index','view'),
				'roles'=>array('View User'),
			),
			array('allow', 
				'actions'=>array('create'),
				'roles'=>array('Create User'),
			),
			array('allow',
				'actions'=>array('update','admin','changePassword'),
				'roles'=>array('Update User'),
			),
			array('allow',
				'actions'=>array('delete'),
				'roles'=>array('Delete User'),
			),
			array('allow',
				'actions'=>array('updateGroup','groups','createGroup','deleteGroup'),
				'roles'=>array('Manage User Groups'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new User;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$model->scenario='admin_change_password';
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->layout='//layouts/column1';
		$this->render('create',array(
			'model'=>$model,
			'groups'=>Yii::app()->authManager->getRoles(),
			'userGroups'=>Yii::app()->authManager->getRoles($model->id),
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		$authManager=Yii::app()->authManager;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$selUserGroups = array_combine($_POST['UserGroups'],$_POST['UserGroups']);

			foreach($authManager->getRoles() as $ug) 
			{	
				if(isset($selUserGroups[$ug->name]) && !$authManager->isAssigned($ug->name,$id)) {
					$authManager->assign($ug->name,$id);
				} else if(!isset($selUserGroups[$ug->name])) {
					$authManager->revoke($ug->name,$id);
				}
			}
			
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
		
		$userGroups = Yii::app()->authManager->getRoles($model->id);
		$groups = $authManager->getRoles();
		unset($groups['Anonymous']);

		$this->layout='//layouts/column1';
		$this->render('update',array(
			'model'=>$model,
			'groups'=>$groups,
			'userGroups'=>$userGroups,
		));
	}
	
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdateGroup($name)
	{
		$authManager=Yii::app()->authManager;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['GroupPermissions']))
		{
			$selGroupPermissions = array_combine($_POST['GroupPermissions'],$_POST['GroupPermissions']);

			foreach($authManager->getAuthItems(CAuthItem::TYPE_OPERATION) as $perm) 
			{	
				//var_dump(isset($selGroupPermissions[$perm->name]));
				//var_dump(!$authManager->hasItemChild($name,$perm->name));
				//var_dump('----');
				if(isset($selGroupPermissions[$perm->name]) && !$authManager->hasItemChild($name,$perm->name)) {
					$authManager->addItemChild($name,$perm->name);
					//echo 'adding permission'.$perm->name;
				} else if(!isset($selGroupPermissions[$perm->name])) {
					$authManager->removeItemChild($name,$perm->name);
					//echo 'removing permission'.$perm->name;
				}
			}
			$this->redirect(array('/user/groups'));
		}

		$this->layout='//layouts/column1';
		$this->render('update_group',array(
			'name'=>$name,
			'permissions'=>$authManager->getAuthItems(CAuthItem::TYPE_OPERATION),
			'groupPermissions'=>$authManager->getItemChildren($name),
			'tasks'=>$authManager->getAuthItems(CAuthItem::TYPE_TASK),
		));
	}
	
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionCreateGroup()
	{
		$authManager=Yii::app()->authManager;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['name']) && !empty($_POST['name']))
		{
			$authManager->createAuthItem($_POST['name'], CAuthItem::TYPE_ROLE, $_POST['description']);
			$this->redirect(array('/user/groups'));
		}

		$this->layout='//layouts/column1';
		$this->render('create_group',array(
			'authManager'=>$authManager,
		));
	}
	
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionDeleteGroup($name)
	{
		$authManager=Yii::app()->authManager;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$authManager->removeAuthItem($name);
		$this->redirect(array('/user/groups'));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('User');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	/**
	 * Manages all models.
	 */
	public function actionGroups()
	{
		$authManager = Yii::app()->authManager;
		$roleNames = array();
		foreach($authManager->getRoles() as $role) {
			$roleNames[]=$role->name;
		}
		$criteria = new CDbCriteria();
		$criteria->addInCondition('name', $roleNames);
		
		$dataProvider = new CActiveDataProvider('AuthItem',array(
			'criteria'=>$criteria
		));
		
		//$dataProvider->setData($authManager->getRoles());
		//$authManager->getRoles()
		$this->render('groups',array(
			'dataProvider'=>$dataProvider,
		));
	}
	
	/**
	 * Change a players password
	 */
	public function actionChangePassword($id)
	{
		$model=$this->loadModel($id);
		
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$model->password = $_POST['User']['password'];
			$model->scenario = 'admin_change_password';
			
			if($model->save()) {
				Yii::app()->user->setFlash('success','Password updated');
				$this->redirect(array('view','id'=>$model->id));
			}
		}
		
		$model->password = '';
		
		$this->layout = '//layouts/column1';
		$this->render('change_password',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return User the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param User $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
