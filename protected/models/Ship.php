<?php

class Ship extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Ship':
	 * @var integer $ShipId
	 * @var integer $BaseShipId
	 * @var integer $SystemId
	 * @var integer $WeaponId
	 * @var integer $JumpDriveId
	 * @var integer $ShieldId
	 * @var integer $DamageEngine
	 * @var integer $DamageWeapon
	 * @var integer $DamageShield
	 * @var integer $DamageHull
	 * @var integer $TargetSystemId
	 * @var integer $CurrentJumpDriveCharge
	 * @var string $TargetSystemArrivalTime
	 * @var integer $Credits
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
		return 'Ship';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('DamageEngine, DamageWeapon, DamageShield, DamageHull, CurrentJumpDriveCharge, Credits', 'required'),
			array('DamageEngine, DamageWeapon, DamageShield, DamageHull, TargetSystemId, CurrentJumpDriveCharge, Credits', 'numerical', 'integerOnly'=>true),
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
			'combats' => array(self::HAS_MANY, 'Combat', 'DefenderShipId'),
			'npcs' => array(self::HAS_MANY, 'Npc', 'LastAttackedShipId'),
			'players' => array(self::HAS_MANY, 'Player', 'ShipId'),
			'baseShip' => array(self::BELONGS_TO, 'BaseShip', 'BaseShipId'),
			'jumpDrive' => array(self::BELONGS_TO, 'JumpDrive', 'JumpDriveId'),
			'shield' => array(self::BELONGS_TO, 'Shield', 'ShieldId'),
			'system' => array(self::BELONGS_TO, 'System', 'SystemId'),
			'weapon' => array(self::BELONGS_TO, 'Weapon', 'WeaponId'),
			'goods' => array(self::MANY_MANY, 'Good', 'ShipGood(ShipId, GoodId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ShipId'=>'Ship',
			'BaseShipId'=>'Base Ship',
			'SystemId'=>'System',
			'WeaponId'=>'Weapon',
			'JumpDriveId'=>'Jump Drive',
			'ShieldId'=>'Shield',
			'DamageEngine'=>'Damage Engine',
			'DamageWeapon'=>'Damage Weapon',
			'DamageShield'=>'Damage Shield',
			'DamageHull'=>'Damage Hull',
			'TargetSystemId'=>'Target System',
			'CurrentJumpDriveCharge'=>'Current Jump Drive Charge',
			'TargetSystemArrivalTime'=>'Target System Arrival Time',
			'Credits'=>'Credits',
		);
	}
}