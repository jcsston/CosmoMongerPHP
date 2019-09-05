<?php

class Race extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Race':
	 * @var integer $RaceId
	 * @var string $Name
	 * @var integer $Weapons
	 * @var integer $Shields
	 * @var integer $Engine
	 * @var integer $Accuracy
	 * @var integer $RacialEnemyId
	 * @var integer $RacialPreferenceId
	 * @var string $Description
	 * @var integer $HomeSystemId
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
		return 'Race';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Name','length','max'=>255),
			array('Description','length','max'=>1000),
			array('Name, Weapons, Shields, Engine', 'required'),
			array('Weapons, Shields, Engine, Accuracy', 'numerical', 'integerOnly'=>true),
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
			'Npcs' => array(self::HAS_MANY, 'Npc', 'RaceId'),
			'Players' => array(self::HAS_MANY, 'Player', 'RaceId'),
			//'Race' => array(self::HAS_ONE, 'Race', 'RaceId'),
			'RacialEnemy' => array(self::BELONGS_TO, 'Race', 'RacialEnemyId'),
			//'races' => array(self::HAS_MANY, 'Race', 'RacialPreferenceId'),
			'RacialPreference' => array(self::BELONGS_TO, 'Race', 'RacialPreferenceId'),
			'HomeSystem' => array(self::BELONGS_TO, 'System', 'HomeSystemId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'RaceId'=>'Race',
			'Name'=>'Name',
			'Weapons'=>'Weapons',
			'Shields'=>'Shields',
			'Engine'=>'Engine',
			'Accuracy'=>'Accuracy',
			'RacialEnemyId'=>'Racial Enemy',
			'RacialPreferenceId'=>'Racial Preference',
			'Description'=>'Description',
			'HomeSystemId'=>'Home System',
		);
	}
}