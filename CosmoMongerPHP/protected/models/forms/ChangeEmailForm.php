<?php

/**
 * ChangeEmailForm class.
 * ChangeEmailForm is the data structure for keeping
 * email form data. It is used by the 'ChangeEmail' action of 'AccountController'.
 */
class ChangeEmailForm extends CFormModel
{
	public $email;

	/**
	 * Declares the validation rules.
	 * The rules state that all the fields are required,
	 * 
	 */
	public function rules()
	{
		return array(
			// email is required
			array('email', 'required'),
			array('email', 'email'),
		);
	}

}
