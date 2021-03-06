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
			'Race' => array(self::BELONGS_TO, 'Race', 'RaceId'),
			'Ship' => array(self::BELONGS_TO, 'Ship', 'ShipId'),
			'User' => array(self::BELONGS_TO, 'User', 'UserId'),
			'PlayerRecords' => array(self::HAS_MANY, 'PlayerRecord', 'PlayerId'),
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
	
	/// <summary>
	/// Name of the starting player ship
	/// </summary>
	public static $StartingShip = "Glorified Trash Can";

	/*
        public enum RecordType
        {
            NetWorth, 
            ShipsDestroyed, ForcedSurrenders, ForcedFlees,
            CargoLootedWorth, ShipsLost, SurrenderCount, 
            FleeCount, CargoLostWorth, 
            DistanceTraveled, GoodsTraded
        }

        /// We will kill this method in PHP, never implemented this feature and it is problematic to boot
        /// <summary>
        /// Updates the player profile with the new player name. 
        /// Throws an ArgumentException if an existing player with the same name already exists.
        /// </summary>
        /// <param name="name">The new name of the player.</param>
        public virtual void UpdateProfile(string name)
        {
            CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

            // Check for another living player with same name
            bool matchingName = (from p in db.Players where p.Name == name && p.Alive && p != this select p).Any();
            if (matchingName)
            {
                throw new ArgumentException("Another player has the same name", "name");
            }

            // Update this player
            this.Name = name;

            // Save database changes
            db.SaveChanges();
        }
        
	/// <summary>
	/// A property changed event, called when BankCredits is changed.
	/// </summary>
	partial void OnBankCreditsChanged()
	{
		// Because BankCredits has changed we need to update NetWorth
		this.UpdateNetWorth();
	}
        */
        
	/// <summary>
	/// Updates the net worth for this player.
	/// </summary>
	public function updateNetWorth()
	{
		$netWorth = $this->BankCredits + $this->Ship->Credits; 
		$netWorth += $this->Ship->TradeInValue + $this->Ship->CargoWorth;

		$props = array(
			"PlayerId", $this->PlayerId,
			"NewNetWorth", $netWorth,
			"OldNetWorth", $this->NetWorth
		);
		Yii::log("Updating player net worth|". json_encode($props), "verbose", "CosmoMongerPHP.models.Player");

		$this->NetWorth = $netWorth;

		// Do not call save here as this method is called during modification of the player data and would be reduntant
	}


	/// <summary>
	/// Withdraw credits from the Bank.
	/// </summary>
	/// <param name="credits">The amount of credits to withdraw.</param>
	/// <exception cref="InvalidOperationException">Thrown in the system the player currently is in doesn't have a bank</exception>
	/// <exception cref="ArgumentOutOfRangeException">Thrown if more credits than available are withdrawn</exception>
	public function bankWithdraw($credits)
	{
		// Check that there is a bank in the current system
		if (!$this->Ship->CosmoSystem->HasBank)
		{
			throw new InvalidOperationException("No bank available for withdraw from");
		}

		// Check that the credits is postive
		if (0 >= $credits)
		{
		    throw new ArgumentException("Cannot withdraw a negative number of credits", "credits");
		}

		// Check that the player has enough credits to withdraw
		if ($this->BankCredits < $credits)
		{
		    throw new ArgumentException("Cannot withdraw more credits than available in the bank", "credits");
		}

		$props = array(
			"PlayerId" => $this->PlayerId,
			"Credits" => $credits,
			"BankCredits" => $this->BankCredits,
			"ShipCredits" => $this->Ship->Credits
		);
		Yii::log("Withdrawing credits from bank|". json_encode($props), "verbose", "CosmoMongerPHP.models.Player");

		$this->BankCredits -= $credits;
		$this->Ship->Credits += $credits;

		// Save database changes
		$this->Ship->save();
		$this->save();
	}

	/// <summary>
	/// Deposit credits in the Bank.
	/// </summary>
	/// <param name="credits">The amount of credits to deposit.</param>
	/// <exception cref="InvalidOperationException">Thrown in the system the player currently is in doesn't have a bank</exception>
	/// /// <exception cref="ArgumentOutOfRangeException">Thrown if more credits than available are deposited</exception>
	public function bankDeposit($credits)
	{
		// Check that there is a bank in the current system
		if (!$this->Ship->CosmoSystem->HasBank)
		{
			throw new InvalidOperationException("No bank available to deposit in");
		}

		// Check that the credits is postive
		if (0 >= $credits)
		{
		    throw new ArgumentException("Cannot deposit a negative number of credits", "credits");
		}

		// Check that the player has enough credits to deposit
		if ($this->Ship->Credits < $credits)
		{
		    throw new ArgumentException("Cannot deposit more credits than available in cash", "credits");
		}

		$props = array(
			"PlayerId" => $this->PlayerId,
			"Credits" => $credits,
			"BankCredits" => $this->BankCredits,
			"ShipCredits" => $this->Ship->Credits
		);
		Yii::log("Depositing credits into bank|". json_encode($props), "verbose", "CosmoMongerPHP.models.Player");
		
		$this->Ship->Credits -= $credits;
		$this->BankCredits += $credits;

		// Save database changes
		$this->Ship->save();
		$this->save();
	}
        
	/// <summary>
	/// Updates the player record snapshot, creating a new one if needed.
	/// </summary>
	public function updateRecordSnapshot()
	{
		$currentSnapshotAge = (int)($this->TimePlayed - $this->LastRecordSnapshotAge);
		
		// If the last snap is older than 1min, we need to create a new one
		if ($currentSnapshotAge > 60)
		{
			// Create new PlayerRecord row
			$record = new PlayerRecord();
			$record->PlayerId = $this->PlayerId;
			$currentDate = new DateTime();
			$record->RecordTime = $currentDate->getTimestamp();
			$record->TimePlayed = $this->TimePlayed;

			// Copy record values
			$record->CargoLootedWorth = $this->CargoLootedWorth;
			$record->CargoLostWorth = $this->CargoLostWorth;
			$record->FleeCount = $this->FleeCount;
			$record->ForcedFlees = $this->ForcedFlees;
			$record->ForcedSurrenders = $this->ForcedSurrenders;
			$record->NetWorth = $this->NetWorth;
			$record->ShipsDestroyed = $this->ShipsDestroyed;
			$record->ShipsLost = $this->ShipsLost;
			$record->SurrenderCount = $this->SurrenderCount;
			$record->GoodsTraded = $this->GoodsTraded;
			$record->DistanceTraveled = $this->DistanceTraveled;

			// Update snapshot age
			$this->LastRecordSnapshotAge = (int)$this->TimePlayed;

			// Insert record
			$record->save();
			$this->save();
		}
	}
        
	/// <summary>
	/// Kills this player.
	/// </summary>
	public function kill()
	{
		$props = array(
			"PlayerId" => $this->PlayerId,
			"Alive" => $this->Alive
		);
		Yii::log("Killing player|". json_encode($props), "verbose", "CosmoMongerPHP.models.Player");

		// Kill this player
		$this->Alive = false;

		// Save database changes
		return $this->save();
	}
	
	/// <summary>
	/// Creates the starting ship.
	/// Note, changes are not submitted to database
	/// </summary>
	/// <param name="startingSystem">The starting system.</param>
	public function createStartingShip($startingSystem)
	{
		if ($this->Ship != null)
		{
			throw new InvalidOperationException("Player already has a ship");
		}
		
		// Create new player ship
		$this->ShipId = $startingSystem->createShip(Player::$StartingShip);
		return $this->save();
	}

	/// <summary>
	/// Updates the play time for this player.
	/// </summary>
	public function updatePlayTime()
	{
		if ($this->Alive)
		{
			$currentDate = new DateTime();
			$lastPlayTime = new DateTime($this->LastPlayed);
			
			// Calcuate time since last play in seconds
			$playTimeLength = $currentDate->getTimestamp() - $lastPlayTime->getTimestamp();

			// Login timeout is 5 minutes, so we ignore times greater than 5 minutes
			if ($playTimeLength < 5 * 60)
			{
				// Update the time played
				$this->TimePlayed += $playTimeLength;

				// Check if the time player is past 7 days
				if ($this->TimePlayed > 60 * 60 * 24 * 7)
				{
					// Player has reached the time limit
					$this->Alive = false; // Die, die, die!!!
				}
			}

			// Update last play datetime
			$this->LastPlayed = $currentDate->getTimestamp();

			// Save database changes
			$this->save();

			// Update player records
			$this->updateRecordSnapshot();
		}
	}
}