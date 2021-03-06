<?php

class Ship extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Ship':
	 * @var integer $ShipId
	 * @var integer $BaseShipId
	 * @var integer $SystemId
	 * @var integer $WeaponId
	 * @var integer $JumpDriveId
	 * @var integer $ShieldId
	 * @var integer $DamageEngine
	 * @var integer $DamageWeapon
	 * @var integer $DamageShield
	 * @var integer $DamageHull
	 * @var integer $TargetSystemId
	 * @var integer $CurrentJumpDriveCharge
	 * @var string $TargetSystemArrivalTime
	 * @var integer $Credits
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
		return 'Ship';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('DamageEngine, DamageWeapon, DamageShield, DamageHull, CurrentJumpDriveCharge, Credits', 'required'),
			array('DamageEngine, DamageWeapon, DamageShield, DamageHull, TargetSystemId, CurrentJumpDriveCharge, Credits', 'numerical', 'integerOnly'=>true),
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
			'Combats' => array(self::HAS_MANY, 'Combat', 'DefenderShipId'),
			'Npcs' => array(self::HAS_MANY, 'Npc', 'LastAttackedShipId'),
			'Players' => array(self::HAS_MANY, 'Player', 'ShipId'),
			'BaseShip' => array(self::BELONGS_TO, 'BaseShip', 'BaseShipId'),
			'JumpDrive' => array(self::BELONGS_TO, 'JumpDrive', 'JumpDriveId'),
			'Shield' => array(self::BELONGS_TO, 'Shield', 'ShieldId'),
			'System' => array(self::BELONGS_TO, 'System', 'SystemId'),
			'Weapon' => array(self::BELONGS_TO, 'Weapon', 'WeaponId'),
			'ShipGoods' => array(self::MANY_MANY, 'Good', 'ShipGood(ShipId, GoodId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ShipId'=>'Ship',
			'BaseShipId'=>'Base Ship',
			'SystemId'=>'System',
			'WeaponId'=>'Weapon',
			'JumpDriveId'=>'Jump Drive',
			'ShieldId'=>'Shield',
			'DamageEngine'=>'Damage Engine',
			'DamageWeapon'=>'Damage Weapon',
			'DamageShield'=>'Damage Shield',
			'DamageHull'=>'Damage Hull',
			'TargetSystemId'=>'Target System',
			'CurrentJumpDriveCharge'=>'Current Jump Drive Charge',
			'TargetSystemArrivalTime'=>'Target System Arrival Time',
			'Credits'=>'Credits',
		);
	}
	

	/// <summary>
	/// Gets the current free cargo space for this ship.
	/// Calculated by taking the total cargo space and subtracting all upgrades, cargo, etc.
	/// </summary>
	public function getCargoSpaceFree()
	{
		$cargoSpace = $this->getCargoSpaceTotal();
		$cargoSpace -= $this->JumpDrive->CargoCost;
		$cargoSpace -= $this->Shield->CargoCost;
		$cargoSpace -= $this->Weapon->CargoCost;
		
		// Calculate the amount of space taken up by cargo goods
		$shipGoodSumCommand =  Yii::app()->db->createCommand("SELECT SUM(Quantity) FROM ShipGood WHERE ShipId = :shipId");
		$shipGoodSumCommand->bindValue($this->ShipId);
		$cargoSpace -= $$shipGoodSumCommand->queryScalar();
		
		return $cargoSpace;
	}
	
	/// <summary>
	/// Gets to total amount of cargo space 
	/// Currently this is just the BaseShip model CargoSpace value. 
	/// But we have this to account for possible future ship upgrades that actually upgrade your cargo space.
	/// </summary>
	public function getCargoSpaceTotal()
	{
		return $this->BaseShip->CargoSpace;
	}

	/// <summary>
	/// Gets the current trade-in value for this ship.
	/// Calculated by looking in the current system and seeing what a matching ship is selling for, if that is not found then
	/// the BaseShip.BasePrice is taken.
	/// </summary>
	public function getTradeInValue()
	{
		// Starting value is the base price
		$shipValue = $this->BaseShip->BasePrice;

		// If the same ship is for sale in the current system, that price replaces shipValue
		$matchingShip = SystemShip::model()->findByAttributes(array('SystemId' => $this->SystemId, 'BaseShipId' => $this->BaseShipId));
		if ($matchingShip != null)
		{
			$shipValue = $matchingShip->Price;
		}

		// Add the trade-in-value of the upgrades
		$shipValue += $this->JumpDrive->GetTradeInValue($this) + $this->Weapon->GetTradeInValue($this) + $this->Shield->GetTradeInValue($this);

		// Take 20% off the face value of the ship to account for wear and tear
		return (int)($shipValue * 0.80);
	}

	/// <summary>
	/// Gets the worth of the cargo aboard the ship.
	/// </summary>
	public function getCargoWorth()
	{
		$shipGoodWorthCommand =  Yii::app()->db->createCommand("SELECT SUM(sg.Quantity * g.BasePrice) FROM ShipGood sg INNER JOIN Good g ON (g.GoodId = sg.GoodId) WHERE ShipId = :shipId");
		$shipGoodWorthCommand->bindValue($this->ShipId);
		return $shipGoodWorthCommand->executeScalar();
	}	

	/// <summary>
	/// Gets the current in progress combat if any
	/// </summary>
	/// <value>The in progress combat object, null if no combat is taking place.</value>
	public function getInProgressCombat()
	{
		// This is a more optimized query, results in a single query to the database with Complete in the where clause
		return Combat::model()->findByAttribute('Complete = false AND (AttackerShipId = :shipId OR DefenderShipId = :shipId)', array(':shipId' => $this->ShipId));
	}
	
	/// <summary>
	/// Gets a value indicating whether this <see cref="Ship"/> is destroyed.
	/// </summary>
	/// <value><c>true</c> if destroyed; otherwise, <c>false</c>.</value>
	public function isDestroyed()
	{
		return $this->DamageHull >= 100;
	}
	
	/// <summary>
	/// Gets the race of this ship, player or NPC.
	/// </summary>
	/// <value>The race of the ship.</value>
	public function getRace()
	{
		return Race::model()->findBySql(
			"SELECT r.* FROM Race r 
			LEFT OUTER JOIN Player p ON (p.RaceId = r.RaceId)
			LEFT OUTER JOIN Npc n ON (n.RaceId = r.RaceId)
			WHERE p.ShipId = :shipId OR n.ShipId = :shipId",
			array(':shipId' => $this->ShipId));
	}
	
	/// <summary>
	/// Gets the 'name' of this ship, player name or NPC name.
	/// </summary>
	/// <value>The name of the ship.</value>
	public function getName()
	{
		if (len($this->Players) > 0)
		{
			return $this->Players[0]->Name;
		}
		else if (len($this->Npcs) > 0)
		{
			return $this->Npcs[0]->Name;
		}
		else
		{
			throw new InvalidOperationException("Ship {$this->ShipId} has no name?");
		}
	}
	

	/// <summary>
	/// Starts the ship traveling to the target system.
	/// </summary>
	/// <param name="targetSystem">The target system to travel to.</param>
	/// <returns>The number of seconds before the ship arrives at the target system</returns>
	/// <exception cref="ArgumentException">Thrown if the ship is already in the target system</exception>
	/// <exception cref="ArgumentOutOfRangeException">Thrown if the target system is out of range of the ship</exception>
	/// <exception cref="InvalidOperationException">Thrown if the ship is already traveling</exception>
	public function travel($targetSystem)
	{
		// Check if ship is in the target system
		if ($this->SystemId === $targetSystem->SystemId)
		{
			throw new ArgumentException("Ship is already in the target system", "targetSystem");
		}

		// Check that the system is within range
		$inRangeSystems = $this->getInRangeSystems();
		if (!in_array($targetSystem, $inRangeSystems))
		{
		    throw new ArgumentException("Target system is out of JumpDrive range", "targetSystem");
		}

		// Check that the ship is not already traveling
		if ($this->TargetSystemId != null || $this->TargetSystemArrivalTime != null)
		{
			throw new InvalidOperationException("Ship is already traveling");
		}

		// Get the travel time
		$travelTime = $this->JumpDrive->ChargeTime;

		// Update the player stats
		$shipPlayer = $this->Players[0];
		if ($shipPlayer != null)
		{
			$shipPlayer->DistanceTraveled += $this->getSystemDistance($targetSystem);
			$shipPlayer->save();
		}

		// Update the database
		$this->TargetSystemId = $targetSystem->SystemId;
		$targetSystemTime = new DateTime();
		$targetSystemTime->add(DateInterval::createFromDateString($travelTime . ' seconds'));
		$this->TargetSystemArrivalTime = $targetSystemTime->getTimestamp();
		
		$this->save();

		return $travelTime;
	}
	
	/// <summary>
	/// Checks if ship is currently traveling.
	/// </summary>
	/// <returns>true if traveling, false if no longer traveling</returns>
	public function checkIfTraveling()
	{
		// Do we have an arrival time?
		if ($this->TargetSystemArrivalTime != null)
		{
			// Assert that there also is a target system id (should never happen)
			assert($this->TargetSystemId != null, "There also should be a target system");

			// Has the arrival time passed?
			$currentTime = new DateTime();
			$arrivalTime = new DateTime($this->TargetSystemArrivalTime);
			if ($currentTime->getTimestamp() > $arrivalTime->getTimestamp())
			{
				// The ship has arrived, change the location of the ship and clear out the travel fields
				$this->SystemId = $this->TargetSystemId;
				$this->TargetSystemId = null;
				$this->TargetSystemArrivalTime = null;

				$this->save();
			}
			else
			{
				// Ship is still traveling
				return true;
			}
		}

		return false;
	}
	
	/// <summary>
	/// Gets a list of Systems within traveling range of the Ship. Excluding the current system.
	/// </summary>
	/// <returns>Array of CosmoSystems within JumpDrive distance</returns>
	public function getInRangeSystems()
	{
		// Find all systems within range of the JumpDrive
		// We use the distance formula, sqrt((x2 - x1)^2 + (y2 - y1)^2)
		return System::model()->findAllBySql("SELECT s2.* FROM System s1, System s2
		WHERE s1.SystemId != s2.SystemId
		AND s1.SystemId = :systemId
		AND SQRT(POW(s2.PositionX - s1.PositionX, 2) + POW(s2.PositionY - s1.PositionY, 2)) < :range",
		array(':systemId' => $this->SystemId, ':range' => $this->JumpDrive->Range));
	}
      
	/// <summary>
	/// Gets the goods on this ship
	/// </summary>
	/// <returns>Array of ShipGoods</returns>
	public function getGoods()
	{
		return $this->ShipGoods;
	}

	/// <summary>
	/// Fetches the ShipGood object for the passed in goodId id. 
	/// </summary>
	/// <param name="goodId">The good id of the ShipGood object to get.</param>
	/// <returns>
	/// The ShipGood object with the matching goodId. 
	/// If there is no ShipGood for the passed in good id, null is returned.
	/// </returns>
	public function getGood($goodId)
	{
		return $this->ShipGoods(array('GoodId' => $goodId));
	}
	/*


        /// <summary>
        /// Attacks the target ship.
        /// </summary>
        /// <param name="target">The target ship to attack.</param>
        /// <exception cref="InvalidOperationException">Thrown when this ship is already in combat</exception>
        /// <exception cref="ArgumentException">Thrown when target ship is already in combat</exception>
        /// <exception cref="ArgumentException">Thrown when trying to attack self</exception>
        public virtual void Attack(Ship target)
        {
            CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

            // Check if this ship is already in combat
            if (this.InProgressCombat != null)
            {
                throw new InvalidOperationException("Current ship is already in combat");
            }

            // Check that the target ship is not in combat
            if (target.InProgressCombat != null)
            {
                throw new ArgumentException("Target ship is already in combat", "target");
            }

            // Check that we are not trying to attack ourself...
            if (target == this)
            {
                throw new ArgumentException("Cannot attack self", "traget");
            }

            Combat combat = new Combat();
            combat.AttackerShip = this;
            combat.DefenderShip = target;
            combat.TurnPointsLeft = Combat.PointsPerTurn;
            combat.Status = Combat.CombatStatus.Incomplete;
            combat.Surrendered = false;
            combat.Turn = 0;
            combat.CargoJettisoned = false;
            combat.Search = false;
            combat.LastActionTime = DateTime.UtcNow;

            // Save changes to the database
            db.Combats.InsertOnSubmit(combat);

            try
            {
                db.SaveChanges();
            }
            catch (SqlException ex)
            {
                ExceptionPolicy.HandleException(ex, "SQL Policy");

                // A combat must already be in-progress
                // Remove this row
                db.Combats.DeleteOnSubmit(combat);

                throw new InvalidOperationException("Ship is already in combat");
            }
            catch (DuplicateKeyException ex)
            {
                ExceptionPolicy.HandleException(ex, "SQL Policy");

                // A combat must already be in-progress
                throw new InvalidOperationException("Ship is already in combat");
            }
        }

        /// <summary>
        /// Gets ships that are leaving the system and we can attack
        /// </summary>
        /// <returns>An array of Ship objects that are leaving the system and open to attack</returns>
        public virtual IEnumerable<Ship> GetShipsToAttack()
        {
            return (from s in this.CosmoSystem.GetLeavingShips()
                    where s != this
                    select s).AsEnumerable();
        }

        /// <summary>
        /// Gets the other ships in system.
        /// </summary>
        /// <returns>An array of Ship objects that are currently in the system, (includes ones we can attack)</returns>
        public virtual IEnumerable<Ship> GetOtherShipsInSystem()
        {
            return (from s in this.CosmoSystem.GetShipsInSystem()
                    where s != this
                    select s).AsEnumerable();
        }

        /// <summary>
        /// Add the good to this ship.
        /// </summary>
        /// <param name="goodId">The good type to add to this ship.</param>
        /// <param name="quantity">The quantity of the good to add.</param>
        /// <returns>The number of goods actually added to the ship. 0 is returned when ship cargo is full.</returns>
        public virtual int AddGood(int goodId, int quantity)
        {
            CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

            // Limit the quantity to the amount of free cargo space
            int actualQuantity = Math.Min(quantity, this.CargoSpaceFree);

            Dictionary<string, object> props = new Dictionary<string, object>
            {
                { "GoodId", goodId },
                { "Quantity", quantity },
                { "ActualQuantity", actualQuantity },
                { "ShipId", this.ShipId }
            };
            Logger.Write("Adding Good to Ship in Ship.AddGood", "Model", 150, 0, TraceEventType.Verbose, "Adding Good to Ship", props);

            ShipGood shipGood = this.GetGood(goodId);
            if (shipGood == null)
            {
                // Ship is not already carrying this good, so we have to create a new ShipGood
                shipGood = new ShipGood();
                shipGood.Ship = this;
                shipGood.GoodId = goodId;
                shipGood.Quantity = actualQuantity;

                db.ShipGoods.InsertOnSubmit(shipGood);
            }
            else
            {
                // Add the correct number of goods to the ship
                shipGood.Quantity += actualQuantity;
            }

            db.SaveChanges();

            // Return the number of goods added to the ship
            return actualQuantity;
        }

        /// <summary>
        /// Gets the distance from the ship to the target system
        /// </summary>
        /// <param name="targetSystem">The target system to measure to.</param>
        /// <returns>A floating-point distance</returns>
        public double GetSystemDistance(CosmoSystem targetSystem)
        {
            return Math.Sqrt(Math.Pow(targetSystem.PositionX - this.CosmoSystem.PositionX, 2) + Math.Pow(targetSystem.PositionY - this.CosmoSystem.PositionY, 2));
        }

        /// <summary>
        /// Gets the nearest system with a bank. May return the system the ship currently is in.
        /// </summary>
        /// <returns>The a reference to the nearest system with a bank</returns>
        public CosmoSystem GetNearestBankSystem()
        {
            CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

            var systemsWithBank = (from s in db.CosmoSystems
                                   where s.HasBank
                                   orderby Math.Sqrt(Math.Pow(this.CosmoSystem.PositionX - s.PositionX, 2) 
                                            + Math.Pow(this.CosmoSystem.PositionY - s.PositionY, 2))
                                   select s);
            return systemsWithBank.First();
        }

        /// <summary>
        /// Completely repairs this ship.
        /// </summary>
        public void Repair()
        {
            CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

            this.DamageEngine = 0;
            this.DamageHull = 0;
            this.DamageShield = 0;
            this.DamageWeapon = 0;

            db.SaveChanges();
        }

        /// <summary>
        /// Applies the damage of a weapon hit to the ship.
        /// </summary>
        /// <remarks>Does not apply changes to database, must call SubmitChanges to save.</remarks>
        /// <param name="weaponDamage">The full weapon damage amount.</param>
        public void ApplyDamage(double weaponDamage)
        {
            // Grab ship base shield strength
            double shieldPower = this.Shield.Strength;

            Race shipRace = this.Race;
            if (shipRace != null)
            {
                // Apply racial factor to shields -/+ 10%
                shieldPower *= 1.0 + (shipRace.Shields / 10.0);
            }

            // Formula is: ShieldStrength * (1 - (ShieldDamage / 100))
            // Which means 0% damage gives full power, 50% damage gives half power, 
            // and 100% damage gives no shield power
            shieldPower *= 1.0 - (this.DamageShield / 100.0);

            // Reduce the weapon damage, depending on shield power
            weaponDamage = Math.Max(weaponDamage - shieldPower, 1.0);

            // 50% of the damage goes to shields and rest goes to the hull
            double newDamageShield = this.DamageShield + (weaponDamage / 0.5);
            newDamageShield = Math.Ceiling(newDamageShield);

            // Hull strength is dependant on the ship level
            // Level 1 is base, level 2 +25% strength, level 3 +50%, level 4 +75%, level 5 +100%
            double hullStrength = 1.0 + (this.BaseShip.Level - 1) / 4.0;

            double damageToHull = ((weaponDamage / 1.5) * (this.DamageShield / 100.0)) / hullStrength;
            double newDamageHull = Math.Ceiling(this.DamageHull + damageToHull);

            Dictionary<string, object> props = new Dictionary<string, object>
            {
                { "ShipId", this.ShipId },
                { "WeaponDamage", weaponDamage },
                { "DamageShield", this.DamageShield },
                { "DamageHull", this.DamageHull },
                { "HullStrength", hullStrength },
                { "NewDamageShield", newDamageShield },
                { "NewDamageHull", newDamageHull }
            };
            Logger.Write("Applyed damage to ship", "Model", 150, 0, TraceEventType.Verbose, "Ship.ApplyDamage", props);

            // Max damage is 100%
            this.DamageShield = (int)Math.Min(newDamageShield, 100);
            this.DamageHull = (int)Math.Min(newDamageHull, 100);
        }

        /// <summary>
        /// Called when the BaseShip/BaseShipId is changed.
        /// </summary>
        partial void OnBaseShipIdChanged()
        {
            Player player = this.Players.SingleOrDefault();
            if (player != null)
            {
                player.UpdateNetWorth();
            }
        }

        /// <summary>
        /// Called when the JumpDrive/JumpDriveId is changed.
        /// </summary>
        partial void OnJumpDriveIdChanged()
        {
            Player player = this.Players.SingleOrDefault();
            if (player != null)
            {
                player.UpdateNetWorth();
            }
        }

        /// <summary>
        /// Called when the Shield/ShieldId is changed.
        /// </summary>
        partial void OnShieldIdChanged()
        {
            Player player = this.Players.SingleOrDefault();
            if (player != null)
            {
                player.UpdateNetWorth();
            }
        }

        /// <summary>
        /// Called when the Weapon/WeaponId is changed.
        /// </summary>
        partial void OnWeaponIdChanged()
        {
            Player player = this.Players.SingleOrDefault();
            if (player != null)
            {
                player.UpdateNetWorth();
            }
        }

        /// <summary>
        /// A property changed event, called when Credits is changed.
        /// </summary>
        partial void OnCreditsChanged()
        {
            // Because Credits has changed we need to update NetWorth for players
            Player player = this.Players.SingleOrDefault();
            if (player != null)
            {
                player.UpdateNetWorth();
            }
        }
	*/
}