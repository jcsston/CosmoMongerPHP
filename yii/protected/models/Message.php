<?php

class Message extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Message':
	 * @var integer $MessageId
	 * @var integer $RecipientUserId
	 * @var integer $SenderUserId
	 * @var string $Content
	 * @var string $Time
	 * @var integer $Received
	 * @var string $Subject
	 * @var integer $VisibleToRecipient
	 * @var integer $VisibleToSender
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
		return 'Message';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Subject','length','max'=>255),
			array('Content, Time, Subject, VisibleToRecipient, VisibleToSender', 'required'),
			array('Received, VisibleToRecipient, VisibleToSender', 'numerical', 'integerOnly'=>true),
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
			'senderUser' => array(self::BELONGS_TO, 'User', 'SenderUserId'),
			'recipientUser' => array(self::BELONGS_TO, 'User', 'RecipientUserId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'MessageId'=>'Message',
			'RecipientUserId'=>'Recipient User',
			'SenderUserId'=>'Sender User',
			'Content'=>'Content',
			'Time'=>'Time',
			'Received'=>'Received',
			'Subject'=>'Subject',
			'VisibleToRecipient'=>'Visible To Recipient',
			'VisibleToSender'=>'Visible To Sender',
		);
	}
}