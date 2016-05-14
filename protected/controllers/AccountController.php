<?php

class AccountController extends CController
{
	public function actionLogin()
	{
		$form=new LoginForm;
		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$form->attributes=$_POST['LoginForm'];
			// validate user input and redirect to previous page if valid
			if($form->validate())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('Login',array('form'=>$form));
	}

	/**
	 * Logout the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionRegister()
	{
		$this->render('Register');
	}

	public function actionSendVerificationCode()
	{
		$this->render('SendVerificationCode');
	}

	public function actionSendVerificationCodeSuccess()
	{
		$this->render('SendVerificationCodeSuccess');
	}

	public function actionVerifyEmail()
	{
		$this->render('VerifyEmail');
	}

	public function actionVerifyEmailSuccess()
	{
		$this->render('VerifyEmailSuccess');
	}

	public function actionChangePassword()
	{
		$this->render('ChangePassword');
	}

	public function actionChangePasswordSuccess()
	{
		$this->render('ChangePasswordSuccess');
	}

	public function actionChangeEmail()
	{
		$this->render('ChangeEmail');
	}

	public function actionChangeEmailSuccess()
	{
		$this->render('ChangeEmailSuccess');
	}

	public function actionUserProfile()
	{
		$this->render('UserProfile');
	}

	public function actionForgotPassword()
	{
		$this->render('ForgotPassword');
	}

	public function actionForgotPasswordSuccess()
	{
		$this->render('ForgotPasswordSuccess');
	}

	public function actionResetPassword()
	{
		$this->render('ResetPassword');
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	// -----------------------------------------------------------
	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}