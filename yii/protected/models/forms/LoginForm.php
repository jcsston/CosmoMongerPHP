<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password', 'required'),
			// password needs to be authenticated
			array('password', 'authenticate'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute, $params)
	{
		if (!$this->hasErrors())  // we only want to authenticate when no input errors
		{
			$identity = new UserIdentity($this->username, $this->password);
			$identity->authenticate();
			switch ($identity->errorCode)
			{
				case UserIdentity::ERROR_NONE:
					$duration = 60 * 5; // 5 minutes
					Yii::app()->user->login($identity, $duration);
					break;
				case UserIdentity::ERROR_USERNAME_INVALID:
					$this->addError('username', 'Username is incorrect.');
					break;
				default: // UserIdentity::ERROR_PASSWORD_INVALID
					$this->addError('password', 'Password is incorrect.');
					break;
			}
		}
	}
}
