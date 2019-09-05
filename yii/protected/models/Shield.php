<?php

class Shield extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Shield':
	 * @var integer $ShieldId
	 * @var string $Name
	 * @var integer $Strength
	 * @var integer $BasePrice
	 * @var integer $CargoCost
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
		return 'Shield';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Name','length','max'=>255),
			array('Name, Strength, BasePrice, CargoCost', 'required'),
			array('Strength, BasePrice, CargoCost', 'numerical', 'integerOnly'=>true),
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
			'baseShips' => array(self::HAS_MANY, 'BaseShip', 'InitialShieldId'),
			'ships' => array(self::HAS_MANY, 'Ship', 'ShieldId'),
			'systems' => array(self::MANY_MANY, 'System', 'SystemShieldUpgrade(SystemId, ShieldId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ShieldId'=>'Shield',
			'Name'=>'Name',
			'Strength'=>'Strength',
			'BasePrice'=>'Base Price',
			'CargoCost'=>'Cargo Cost',
		);
	}
}