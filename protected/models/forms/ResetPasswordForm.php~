<?php

/**
 * ResetPasswordForm class.
 * ResetPasswordForm is the data structure for keeping
 * reset password form data. It is used by the 'ResetPassword' action of 'AccountController'.
 */
class ResetPasswordForm extends CFormModel
{
	public $username;
	public $resetPasswordCode;
	public $newPassword;
	
	public function rules()
	{
		return array(
			array('username, resetPasswordCode', 'required'),
		);
	}
}
