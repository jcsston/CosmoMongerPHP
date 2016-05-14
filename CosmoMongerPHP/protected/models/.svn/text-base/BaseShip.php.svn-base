<?php

class BaseShip extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'BaseShip':
	 * @var integer $BaseShipId
	 * @var string $Name
	 * @var integer $BasePrice
	 * @var integer $CargoSpace
	 * @var integer $InitialJumpDriveId
	 * @var integer $InitialWeaponId
	 * @var integer $InitialShieldId
	 * @var integer $HitFactor
	 * @var integer $Level
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
		return 'BaseShip';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Name','length','max'=>255),
			array('BasePrice, CargoSpace, HitFactor, Level', 'required'),
			array('BasePrice, CargoSpace, HitFactor, Level', 'numerical', 'integerOnly'=>true),
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
			'initialJumpDrive' => array(self::BELONGS_TO, 'JumpDrive', 'InitialJumpDriveId'),
			'initialShield' => array(self::BELONGS_TO, 'Shield', 'InitialShieldId'),
			'initialWeapon' => array(self::BELONGS_TO, 'Weapon', 'InitialWeaponId'),
			'ships' => array(self::HAS_MANY, 'Ship', 'BaseShipId'),
			'systems' => array(self::MANY_MANY, 'System', 'SystemShip(SystemId, BaseShipId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'BaseShipId'=>'Base Ship',
			'Name'=>'Name',
			'BasePrice'=>'Base Price',
			'CargoSpace'=>'Cargo Space',
			'InitialJumpDriveId'=>'Initial Jump Drive',
			'InitialWeaponId'=>'Initial Weapon',
			'InitialShieldId'=>'Initial Shield',
			'HitFactor'=>'Hit Factor',
			'Level'=>'Level',
		);
	}
}