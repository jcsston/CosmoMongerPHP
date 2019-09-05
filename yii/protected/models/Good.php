<?php

class Good extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Good':
	 * @var integer $GoodId
	 * @var string $Name
	 * @var integer $BasePrice
	 * @var integer $Contraband
	 * @var integer $TargetCount
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
		return 'Good';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Name','length','max'=>255),
			array('Name, BasePrice, Contraband', 'required'),
			array('BasePrice, Contraband, TargetCount', 'numerical', 'integerOnly'=>true),
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
			'combats' => array(self::MANY_MANY, 'Combat', 'CombatGood(CombatId, GoodId)'),
			'ships' => array(self::MANY_MANY, 'Ship', 'ShipGood(ShipId, GoodId)'),
			'systems' => array(self::MANY_MANY, 'System', 'SystemGood(SystemId, GoodId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'GoodId'=>'Good',
			'Name'=>'Name',
			'BasePrice'=>'Base Price',
			'Contraband'=>'Contraband',
			'TargetCount'=>'Target Count',
		);
	}
}