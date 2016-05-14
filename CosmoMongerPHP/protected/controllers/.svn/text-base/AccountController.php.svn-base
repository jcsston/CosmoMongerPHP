<?php

class AccountController extends CController
{	
	public function filters()
	{
		return array(
			'accessControl',
		);
	}
	
	public function accessRules()
	{
		return array(
			array('deny', // deny unauthenticated users access to UserProfile
				'actions'=>array('UserProfile'),
				'users'=>array('?'),
			),
		);
	}
	
	public function actionLogin()
	{
		$form = new LoginForm;
		
		// Allow auto-filling from the url
		if (isset($_REQUESTS['LoginForm']))
		{
			$form->attributes = $_REQUEST['LoginForm']; 
		}
		
		// collect user input data
		if (isset($_POST['LoginForm']))
		{
			$form->attributes = $_POST['LoginForm'];
			// validate user input and redirect to previous page if valid
			if ($form->validate())
			{
				$this->redirect(Yii::app()->user->returnUrl);
			}
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
		$form = new RegisterForm;
		// collect user input data
		if (isset($_POST['RegisterForm']))
		{
			$form->attributes = $_POST['RegisterForm'];
			// validate user input and create the user account
			if($form->validate()) 
			{
				try 
				{
					if (User::createUser($form->username, $form->password, $form->email)) 
					{
						$this->redirect($this->createUrl('SendVerificationCode', array('SendVerificationCodeForm[username]' => $form->username)));
					}
				} 
				catch (ArgumentException $ex)
				{
					$form->addError($ex->getParamName(), $ex->getMessage());
				}
			}
		}
		// display the Register form
		$this->render('Register',array('form'=>$form));
	}

	public function actionSendVerificationCode()
	{
		$form = new SendVerificationCodeForm;
		
		// collect user input data
		if (isset($_REQUEST['SendVerificationCodeForm']))
		{
			$form->attributes = $_REQUEST['SendVerificationCodeForm'];
			// validate user input and send the verification code
			if($form->validate()) 
			{
				try 
				{
					$user = User::model()->findByAttributes(array('UserName'=>$form->username));
					if ($user) 
					{
						// Build the verification url
						$url = 'http://' . $_SERVER['SERVER_NAME'] 
							// Add the port if needed
							. ($_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '') 
							// Build the path part of the url
							. $this->createUrl('VerifyEmail', array('VerifyEmailForm[username]' => $form->username)) 
							. '&' . urlencode("VerifyEmailForm[verificationCode]") . '=';
						
						if ($user->sendVerificationCode($url)) 
						{
							$this->redirect($this->createUrl('SendVerificationCodeSuccess', array('username' => $form->username)));
						}
						else
						{
							$form->addError("", "Failed to send verification code e-mail. Please try again, if the problem persists, please contact " . Yii::app()->params['adminEmail']);
						}
					}
					else
					{
						$form->addError("username", "Invalid username");
					}
				} 
				catch (CosmoMongerException $ex)
				{
					$form->addError("username", $ex->getMessage());
				}
			}
		}
		
		$this->render('SendVerificationCode', array('form'=>$form));
	}

	public function actionSendVerificationCodeSuccess()
	{
		$this->render('SendVerificationCodeSuccess', array('username'=>$_GET['username']));
	}

	public function actionVerifyEmail()
	{
		$form = new VerifyEmailForm;

		// collect user input data
		if (isset($_REQUEST['VerifyEmailForm']))
		{
			$form->attributes = $_REQUEST['VerifyEmailForm'];
			// validate user input and the verification code
			if($form->validate()) 
			{
				$user = User::model()->findByAttributes(array('UserName'=>$form->username));
				if ($user) 
				{
					// Verify the code
					if ($user->verifyEmail($form->verificationCode)) 
					{
						$this->redirect($this->createUrl('VerifyEmailSuccess', array('email'=>$user->Email, 'username' => $form->username)));
					}
					else
					{
						$form->addError("verificationCode", "Invalid verification code");
					}
				}
				else
				{
					$form->addError("username", "Invalid username");
				}
			}
		}
		
		$this->render('VerifyEmail', array('form'=>$form));
	}

	public function actionVerifyEmailSuccess()
	{
		$this->render('VerifyEmailSuccess', array('email'=>$_GET['email'], 'username'=>$_GET['username']));
	}

	public function actionChangePassword()
	{
		$user = User::model()->findByAttributes(array('UserName'=>Yii::app()->user->name));
		
		$form = new ChangePasswordForm;		
		// collect user input data
		if (isset($_POST['ChangePasswordForm']))
		{
			$form->attributes = $_POST['ChangePasswordForm'];
			// validate user input and the verification code
			if($form->validate()) 
			{
                // Attempt to change the password
                try
                {
					$user->changePassword($form->currentPassword, $form->newPassword);
					
					// Success!
					$this->redirect($this->createUrl('ChangePasswordSuccess'));
                }
                catch (ArgumentException $ex)
                {
                    // Display the error
                    $form->addError($ex->getParamName(), $ex->getMessage());
                }
			}
		}
		
		$this->render('ChangePassword', array('form'=>$form));
	}

	public function actionChangePasswordSuccess()
	{
		$this->render('ChangePasswordSuccess');
	}

	public function actionChangeEmail()
	{
		$user = User::model()->findByAttributes(array('UserName'=>Yii::app()->user->name));
		
		$form = new ChangeEmailForm;
		// Fill in the form with the current users email
		$form->email = $user->Email;
		
		// collect user input data
		if (isset($_POST['ChangeEmailForm']))
		{
			$form->attributes = $_POST['ChangeEmailForm'];
			// validate user input and the verification code
			if($form->validate()) 
			{
                // Attempt to change the email
                try
                {
					
					$user->updateEmail($form->email);
					
					// Success!
					$this->redirect($this->createUrl('ChangeEmailSuccess'));
                }
                catch (ArgumentException $ex)
                {
                    // Display the error
                    $form->addError($ex->getParamName(), $ex->getMessage());
                }
			}
		}
		
		$this->render('ChangeEmail', array('form'=>$form));
	}

	public function actionChangeEmailSuccess()
	{
		$this->render('ChangeEmailSuccess');
	}

	public function actionUserProfile()
	{
		$user = User::model()->findByAttributes(array('UserName'=>Yii::app()->user->name));
		$this->render('UserProfile', array('email'=>$user->Email, 'name'=>$user->UserName, 'joinDate'=>$user->Joined));
	}

	public function actionForgotPassword()
	{
		$form = new ForgotPasswordForm;		
		// collect user input data
		if (isset($_POST['ForgotPasswordForm']))
		{
			$form->attributes = $_POST['ForgotPasswordForm'];
			// validate user input
			if($form->validate()) 
			{
                // Attempt to send the reset password e-mail
				$user = User::model()->findByAttributes(array('Email'=>$form->email));
				if ($user)
				{
					$basePasswordResetUrl = 'http://' . $_SERVER['SERVER_NAME'] 
						// Add the port if needed
						. ($_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '') 
						// Build the path part of the url
						. $this->createUrl('ResetPassword', array('ResetPasswordForm[username]' => $user->UserName)) 
						. '&' . urlencode("ResetPasswordForm[resetPasswordCode]") . '=';
					
					if ($user->sendForgotPasswordLink($basePasswordResetUrl))
					{
						// Success!
						$this->redirect($this->createUrl('ForgotPasswordSuccess'));
					}
					else
					{
						$form->addError("", "Failed to send password reset e-mail. Please try again, if the problem persists, please contact " . Yii::app()->params['adminEmail']);
					}
				}
				else
				{
					$form->addError("email", "Unable to lookup user with matching email address.");
				}
			}
		}
		
		$this->render('ForgotPassword', array('form'=>$form));
	}

	public function actionForgotPasswordSuccess()
	{
		$this->render('ForgotPasswordSuccess');
	}

	public function actionResetPassword()
	{
		$form = new ResetPasswordForm;		
		// collect user input data
		if (isset($_GET['ResetPasswordForm']))
		{
			$form->attributes = $_GET['ResetPasswordForm'];
		}
		
		$this->render('ResetPassword', array('form'=>$form));
	}
	
	public function actionResetPasswordConfirm()
	{
		$form = new ResetPasswordForm;		
		// collect user input data
		if (isset($_POST['ResetPasswordForm']))
		{
			$form->attributes = $_POST['ResetPasswordForm'];
			// validate that the form is correct
			if($form->validate()) 
			{
                // Attempt to reset the users password using the supplied reset password code
				$user = User::model()->findByAttributes(array('UserName'=>$form->username));
				if ($user)
				{
					try
					{
						$form->newPassword = $user->resetPassword($form->resetPasswordCode);
						$this->render('ResetPasswordSuccess', array('form'=>$form));
						return;
					}
					catch (ArgumentException $ex)
					{
						// Display the error
						$form->addError($ex->getParamName(), $ex->getMessage());
					}
				}
				else
				{
					$form->addError("username", "Invalid username");
				}
			}
		}
		
		$this->render('ResetPassword', array('form'=>$form));
	}
	
	public function actionIndex()
	{
		$this->redirect($this->createUrl('UserProfile'));
	}
}