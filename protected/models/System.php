<?php

class System extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'System':
	 * @var integer $SystemId
	 * @var string $Name
	 * @var integer $PositionX
	 * @var integer $PositionY
	 * @var integer $HasBank
	 * @var integer $RaceId
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
		return 'System';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Name','length','max'=>255),
			array('Name, PositionX, PositionY, HasBank, RaceId', 'required'),
			array('PositionX, PositionY, HasBank, RaceId', 'numerical', 'integerOnly'=>true),
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
			'npcs' => array(self::HAS_MANY, 'Npc', 'LastVisitedSystemId'),
			'races' => array(self::HAS_MANY, 'Race', 'HomeSystemId'),
			'ships' => array(self::HAS_MANY, 'Ship', 'SystemId'),
			'goods' => array(self::MANY_MANY, 'Good', 'SystemGood(SystemId, GoodId)'),
			'jumpDrives' => array(self::MANY_MANY, 'JumpDrive', 'SystemJumpDriveUpgrade(SystemId, JumpDriveId)'),
			'shields' => array(self::MANY_MANY, 'Shield', 'SystemShieldUpgrade(SystemId, ShieldId)'),
			'baseShips' => array(self::MANY_MANY, 'BaseShip', 'SystemShip(SystemId, BaseShipId)'),
			'weapons' => array(self::MANY_MANY, 'Weapon', 'SystemWeaponUpgrade(SystemId, WeaponId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'SystemId'=>'System',
			'Name'=>'Name',
			'PositionX'=>'Position X',
			'PositionY'=>'Position Y',
			'HasBank'=>'Has Bank',
			'RaceId'=>'Race',
		);
	}
}