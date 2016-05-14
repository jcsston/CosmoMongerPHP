<?php

/**
 * ForgotPasswordForm class.
 * ForgotPasswordForm is the data structure for keeping
 * forgotten password e-mail form data. It is used by the 'ForgotPassword' action of 'AccountController'.
 */
class ForgotPasswordForm extends CFormModel
{
	public $email;
	
	public function rules()
	{
		return array(
			// all fields are required
			array('email', 'required'),
			array('email','email'),
		);
	}

}
