<?php

class IgnoreList extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'IgnoreList':
	 * @var integer $UserId
	 * @var integer $AntiFriendId
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
		return 'IgnoreList';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
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
			'user' => array(self::BELONGS_TO, 'User', 'UserId'),
			'antiFriend' => array(self::BELONGS_TO, 'User', 'AntiFriendId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'UserId'=>'User',
			'AntiFriendId'=>'Anti Friend',
		);
	}
}