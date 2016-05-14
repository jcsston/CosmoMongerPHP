<?php

class Player extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Player':
	 * @var integer $PlayerId
	 * @var integer $UserId
	 * @var string $Name
	 * @var integer $RaceId
	 * @var integer $ShipId
	 * @var integer $BankCredits
	 * @var double $TimePlayed
	 * @var string $LastPlayed
	 * @var integer $NetWorth
	 * @var integer $ShipsDestroyed
	 * @var integer $ForcedSurrenders
	 * @var integer $ForcedFlees
	 * @var integer $CargoLootedWorth
	 * @var integer $ShipsLost
	 * @var integer $SurrenderCount
	 * @var integer $FleeCount
	 * @var integer $CargoLostWorth
	 * @var integer $Alive
	 * @var integer $LastRecordSnapshotAge
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
		return 'Player';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Name','length','max'=>255),
			array('Name, BankCredits, TimePlayed, LastPlayed, LastRecordSnapshotAge, DistanceTraveled, GoodsTraded', 'required'),
			array('BankCredits, NetWorth, ShipsDestroyed, ForcedSurrenders, ForcedFlees, CargoLootedWorth, ShipsLost, SurrenderCount, FleeCount, CargoLostWorth, Alive, LastRecordSnapshotAge, GoodsTraded', 'numerical', 'integerOnly'=>true),
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
			'race' => array(self::BELONGS_TO, 'Race', 'RaceId'),
			'ship' => array(self::BELONGS_TO, 'Ship', 'ShipId'),
			'user' => array(self::BELONGS_TO, 'User', 'UserId'),
			'playerRecords' => array(self::HAS_MANY, 'PlayerRecord', 'PlayerId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'PlayerId'=>'Player',
			'UserId'=>'User',
			'Name'=>'Name',
			'RaceId'=>'Race',
			'ShipId'=>'Ship',
			'BankCredits'=>'Bank Credits',
			'TimePlayed'=>'Time Played',
			'LastPlayed'=>'Last Played',
			'NetWorth'=>'Net Worth',
			'ShipsDestroyed'=>'Ships Destroyed',
			'ForcedSurrenders'=>'Forced Surrenders',
			'ForcedFlees'=>'Forced Flees',
			'CargoLootedWorth'=>'Cargo Looted Worth',
			'ShipsLost'=>'Ships Lost',
			'SurrenderCount'=>'Surrender Count',
			'FleeCount'=>'Flee Count',
			'CargoLostWorth'=>'Cargo Lost Worth',
			'Alive'=>'Alive',
			'LastRecordSnapshotAge'=>'Last Record Snapshot Age',
			'DistanceTraveled'=>'Distance Traveled',
			'GoodsTraded'=>'Goods Traded',
		);
	}
}