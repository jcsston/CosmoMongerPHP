<?php

/**
 * VerifyEmailForm class.
 * VerifyEmailForm is the data structure for keeping
 * username and verification form data. It is used by the 'SendVerificationCode' action of 'AccountController'.
 */
class VerifyEmailForm extends CFormModel
{
	public $username;
	public $verificationCode;
	
	/**
	 * Declares the validation rules.
	 * The rules state that all the fields are required,
	 * 
	 */
	public function rules()
	{
		return array(
			// username and verificationCode are required
			array('username, verificationCode', 'required'),
		);
	}

}
