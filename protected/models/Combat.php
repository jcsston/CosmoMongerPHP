<?php

class Combat extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Combat':
	 * @var integer $CombatId
	 * @var integer $AttackerShipId
	 * @var integer $DefenderShipId
	 * @var integer $Turn
	 * @var integer $TurnPointsLeft
	 * @var integer $Surrendered
	 * @var integer $CargoJettisoned
	 * @var integer $Status
	 * @var string $LastActionTime
	 * @var integer $CreditsLooted
	 * @var integer $Search
	 * @var integer $AttackerHits
	 * @var integer $AttackerMisses
	 * @var integer $DefenderHits
	 * @var integer $DefenderMisses
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
		return 'Combat';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Turn, TurnPointsLeft, Status, LastActionTime, Search, AttackerHits, AttackerMisses, DefenderHits, DefenderMisses', 'required'),
			array('Turn, TurnPointsLeft, Surrendered, CargoJettisoned, Status, CreditsLooted, Search, AttackerHits, AttackerMisses, DefenderHits, DefenderMisses', 'numerical', 'integerOnly'=>true),
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
			'attackerShip' => array(self::BELONGS_TO, 'Ship', 'AttackerShipId'),
			'defenderShip' => array(self::BELONGS_TO, 'Ship', 'DefenderShipId'),
			'goods' => array(self::MANY_MANY, 'Good', 'CombatGood(CombatId, GoodId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'CombatId'=>'Combat',
			'AttackerShipId'=>'Attacker Ship',
			'DefenderShipId'=>'Defender Ship',
			'Turn'=>'Turn',
			'TurnPointsLeft'=>'Turn Points Left',
			'Surrendered'=>'Surrendered',
			'CargoJettisoned'=>'Cargo Jettisoned',
			'Status'=>'Status',
			'LastActionTime'=>'Last Action Time',
			'CreditsLooted'=>'Credits Looted',
			'Search'=>'Search',
			'AttackerHits'=>'Attacker Hits',
			'AttackerMisses'=>'Attacker Misses',
			'DefenderHits'=>'Defender Hits',
			'DefenderMisses'=>'Defender Misses',
		);
	}
}