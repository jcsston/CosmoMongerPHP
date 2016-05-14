<?php

class User extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'User':
	 * @var integer $UserId
	 * @var string $Email
	 * @var string $UserName
	 * @var integer $Validated
	 * @var integer $Active
	 * @var integer $Admin
	 * @var string $Password
	 * @var string $LastLogin
	 * @var integer $LoginAttemptCount
	 * @var string $LastActivity
	 * @var string $LastVerificationSent
	 * @var string $Joined
	 * @var string $SessionID
	 * @var string $PasswordResetCode
	 * @var string $PasswordResetExpiration
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'User';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Email','length','max'=>255),
			array('UserName','length','max'=>255),
			array('Password','length','max'=>160),
			array('SessionID','length','max'=>255),
			array('PasswordResetCode','length','max'=>128),
			array('Email, UserName, Password, Joined', 'required'),
			array('Validated, Active, Admin, LoginAttemptCount', 'numerical', 'integerOnly'=>true),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'buddyLists' => array(self::HAS_MANY, 'BuddyList', 'FriendId'),
			'ignoreLists' => array(self::HAS_MANY, 'IgnoreList', 'AntiFriendId'),
			'messages' => array(self::HAS_MANY, 'Message', 'RecipientUserId'),
			'players' => array(self::HAS_MANY, 'Player', 'UserId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'UserId'=>'User',
			'Email'=>'Email',
			'UserName'=>'User Name',
			'Validated'=>'Validated',
			'Active'=>'Active',
			'Admin'=>'Admin',
			'Password'=>'Password',
			'LastLogin'=>'Last Login',
			'LoginAttemptCount'=>'Login Attempt Count',
			'LastActivity'=>'Last Activity',
			'LastVerificationSent'=>'Last Verification Sent',
			'Joined'=>'Joined',
			'SessionID'=>'Session',
			'PasswordResetCode'=>'Password Reset Code',
			'PasswordResetExpiration'=>'Password Reset Expiration',
		);
	}
}