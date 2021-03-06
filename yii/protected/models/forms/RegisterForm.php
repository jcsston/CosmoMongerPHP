<?php

/**
 * RegisterForm class.
 * RegisterForm is the data structure for keeping
 * user registation form data. It is used by the 'Register' action of 'AccountController'.
 */
class RegisterForm extends CFormModel
{
	public $username;
	public $email;
	public $password;
	public $confirmPassword;
	public $verifyCode;
	
	/**
	 * Declares the validation rules.
	 * The rules state that all the fields are required,
	 * 
	 */
	public function rules()
	{
		return array(
			// all fields are required
			array('username, email, password, confirmPassword', 'required'),
			array('email','email'),
			array('email','length','max'=>255),
			array('username','length','max'=>255),
			array('password','length','min'=>8),
			array('confirmPassword', 'compare', 'compareAttribute'=>'password'),
			// verifyCode needs to be entered correctly
			array('verifyCode', 'application.extensions.recaptcha.EReCaptchaValidator', 'privateKey'=>Yii::app()->params['RecaptchaPrivateKey']),
		);
	}

}
