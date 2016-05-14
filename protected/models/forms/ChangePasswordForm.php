<?php

/**
 * ChangePasswordForm class.
 * ChangePasswordForm is the data structure for keeping
 * changing the users password form data. It is used by the 'ChangePassword' action of 'AccountController'.
 */
class ChangePasswordForm extends CFormModel
{
	public $currentPassword;
	public $newPassword;
	public $confirmPassword;
	
	public function rules()
	{
		return array(
			// all fields are required
			array('currentPassword, newPassword, confirmPassword', 'required'),
			array('newPassword','length','min'=>8),
			array('confirmPassword', 'compare', 'compareAttribute'=>'newPassword'),
		);
	}

}
