<?php

class Npc extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Npc':
	 * @var integer $NpcId
	 * @var integer $NType
	 * @var string $Name
	 * @var integer $RaceId
	 * @var integer $ShipId
	 * @var integer $Aggression
	 * @var string $NextActionTime
	 * @var integer $LastVisitedSystemId
	 * @var integer $LastAttackedShipId
	 * @var string $NextTravelTime
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
		return 'Npc';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Name','length','max'=>255),
			array('Name', 'required'),
			array('NType, Aggression', 'numerical', 'integerOnly'=>true),
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
			'race' => array(self::BELONGS_TO, 'Race', 'RaceId'),
			'ship' => array(self::BELONGS_TO, 'Ship', 'ShipId'),
			'lastAttackedShip' => array(self::BELONGS_TO, 'Ship', 'LastAttackedShipId'),
			'lastVisitedSystem' => array(self::BELONGS_TO, 'System', 'LastVisitedSystemId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'NpcId'=>'Npc',
			'NType'=>'Ntype',
			'Name'=>'Name',
			'RaceId'=>'Race',
			'ShipId'=>'Ship',
			'Aggression'=>'Aggression',
			'NextActionTime'=>'Next Action Time',
			'LastVisitedSystemId'=>'Last Visited System',
			'LastAttackedShipId'=>'Last Attacked Ship',
			'NextTravelTime'=>'Next Travel Time',
		);
	}
}