<?php

/**
 * SendVerificationCodeForm class.
 * SendVerificationCodeForm is the data structure for keeping
 * username form data. It is used by the 'SendVerificationCode' action of 'AccountController'.
 */
class SendVerificationCodeForm extends CFormModel
{
	public $username;
	
	/**
	 * Declares the validation rules.
	 * The rules state that all the fields are required,
	 * 
	 */
	public function rules()
	{
		return array(
			// username is required
			array('username', 'required'),
		);
	}

}
