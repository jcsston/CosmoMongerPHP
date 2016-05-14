<?php

class PlayerRecord extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'PlayerRecord':
	 * @var integer $PlayerRecordId
	 * @var integer $PlayerId
	 * @var string $RecordTime
	 * @var double $TimePlayed
	 * @var integer $NetWorth
	 * @var integer $ShipsDestroyed
	 * @var integer $ForcedSurrenders
	 * @var integer $ForcedFlees
	 * @var integer $CargoLootedWorth
	 * @var integer $ShipsLost
	 * @var integer $SurrenderCount
	 * @var integer $FleeCount
	 * @var integer $CargoLostWorth
	 * @var double $DistanceTraveled
	 * @var integer $GoodsTraded
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
		return 'PlayerRecord';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('RecordTime, TimePlayed, NetWorth, ShipsDestroyed, ForcedSurrenders, ForcedFlees, CargoLootedWorth, ShipsLost, SurrenderCount, FleeCount, CargoLostWorth, DistanceTraveled, GoodsTraded', 'required'),
			array('NetWorth, ShipsDestroyed, ForcedSurrenders, ForcedFlees, CargoLootedWorth, ShipsLost, SurrenderCount, FleeCount, CargoLostWorth, GoodsTraded', 'numerical', 'integerOnly'=>true),
			array('TimePlayed, DistanceTraveled', 'numerical'),
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
			'player' => array(self::BELONGS_TO, 'Player', 'PlayerId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'PlayerRecordId'=>'Player Record',
			'PlayerId'=>'Player',
			'RecordTime'=>'Record Time',
			'TimePlayed'=>'Time Played',
			'NetWorth'=>'Net Worth',
			'ShipsDestroyed'=>'Ships Destroyed',
			'ForcedSurrenders'=>'Forced Surrenders',
			'ForcedFlees'=>'Forced Flees',
			'CargoLootedWorth'=>'Cargo Looted Worth',
			'ShipsLost'=>'Ships Lost',
			'SurrenderCount'=>'Surrender Count',
			'FleeCount'=>'Flee Count',
			'CargoLostWorth'=>'Cargo Lost Worth',
			'DistanceTraveled'=>'Distance Traveled',
			'GoodsTraded'=>'Goods Traded',
		);
	}
}