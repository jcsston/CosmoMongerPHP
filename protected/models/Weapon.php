<?php

class Weapon extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Weapon':
	 * @var integer $WeaponId
	 * @var string $Name
	 * @var integer $Power
	 * @var integer $TurnCost
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
		return 'Weapon';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Name','length','max'=>255),
			array('Name, Power, TurnCost, CargoCost, BasePrice', 'required'),
			array('Power, TurnCost, CargoCost, BasePrice', 'numerical', 'integerOnly'=>true),
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
			'baseShips' => array(self::HAS_MANY, 'BaseShip', 'InitialWeaponId'),
			'ships' => array(self::HAS_MANY, 'Ship', 'WeaponId'),
			'systems' => array(self::MANY_MANY, 'System', 'SystemWeaponUpgrade(SystemId, WeaponId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'WeaponId'=>'Weapon',
			'Name'=>'Name',
			'Power'=>'Power',
			'TurnCost'=>'Turn Cost',
			'CargoCost'=>'Cargo Cost',
			'BasePrice'=>'Base Price',
		);
	}
}