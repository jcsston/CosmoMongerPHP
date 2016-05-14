<?php

class JumpDrive extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'JumpDrive':
	 * @var integer $JumpDriveId
	 * @var string $Name
	 * @var integer $ChargeTime
	 * @var integer $Range
	 * @var integer $CargoCost
	 * @var integer $BasePrice
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
		return 'JumpDrive';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Name','length','max'=>255),
			array('Name, Range, CargoCost, BasePrice', 'required'),
			array('ChargeTime, Range, CargoCost, BasePrice', 'numerical', 'integerOnly'=>true),
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
			'baseShips' => array(self::HAS_MANY, 'BaseShip', 'InitialJumpDriveId'),
			'ships' => array(self::HAS_MANY, 'Ship', 'JumpDriveId'),
			'systems' => array(self::MANY_MANY, 'System', 'SystemJumpDriveUpgrade(SystemId, JumpDriveId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'JumpDriveId'=>'Jump Drive',
			'Name'=>'Name',
			'ChargeTime'=>'Charge Time',
			'Range'=>'Range',
			'CargoCost'=>'Cargo Cost',
			'BasePrice'=>'Base Price',
		);
	}
}