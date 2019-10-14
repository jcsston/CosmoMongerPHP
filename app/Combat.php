<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Combat extends Model
{
    protected $table = 'combat';
    protected $primaryKey = 'combat_id';

    /**
     * Default values
     */
    protected $attributes = [
        "turn" => 0,
        "turn_points_left" => Combat::PointsPerTurn,
        "surrendered" => false,
        "cargo_jettisoned" => 0,
        "status" => Combat::CombatStatus_Incomplete,
        "credit_looted" => 0,
        "search" => false,
        "attacker_hits" => 0,
        "attacker_misses" => 0,
        "defender_hits" => 0,
        "defender_misses" => 0,
    ];

    /**
     * The default number of points given per turn
    */
    public const PointsPerTurn = 20;

    /**
     * The default amount of seconds given per turn
     */
    public const SecondsPerTurn = 30;

    /**
     * The minumin amount (in %) of credits/cargo destroyed when a ship is destroyed.
     */
    public const CreditsCargoDestroyedPercentMin = 10;

    /**
     * The maxinum amount (in %) of credits/cargo destroyed when a ship is destroyed.
     */
    public const CreditsCargoDestroyedPercentMax = 25;

    /**
     * Combat is still in progress
    */
    public const CombatStatus_Incomplete = 0;

    /**
     * Combat is over. Current Turn Ship has destroyed the other ship.
    */
    public const CombatStatus_ShipDestroyed = 1;

    /**
     * Combat is over. Current Turn Ship chose to pickup cargo jettisoned by the other ship.
     * This has allowed the other ship to escape.
    */
    public const CombatStatus_CargoPickup = 2;

    /**
     * Combat is over. Current Turn Ship has escaped combat by fully charging JumpDrive.
    */
    public const CombatStatus_ShipFled = 3;

    /**
     * Combat is over. Current Turn Ship has accepted the other ships surrender.
    */
    public const CombatStatus_ShipSurrendered = 4;

    /**
     * Combat is over. Current Turn Ship has constented to the search for contraband cargo.
    */
    public const CombatStatus_ShipSearched = 5;

    public function attackerShip()
    {
        return $this->hasOne('App\Ship', 'ship_id', 'attacker_ship_id');
    }

    public function defenderShip()
    {
        return $this->hasOne('App\Ship', 'ship_id', 'defender_ship_id');
    }

    public function combatGoods()
    {
        return $this->hasMany('App\CombatGood', 'combat_id', 'combat_id');
    }

    /// <summary>
    /// Gets a reference to the current Ship ('Player') turn.
    /// We reference Ships rather than Players because NPCs are not considered Players,
    /// but do own Ships and can fight.
    /// </summary>
    /// <value>The ship whose turn is currently is.</value>
    /// <exception cref="UnexpectedValueException">Thrown when the Turn field is an invalid value</exception>
    public function ShipTurn(): \App\Ship
    {
        if ($this->turn == 0)
        {
            return $this->attackerShip()->get()->first();
        }
        else if ($this->turn == 1)
        {
            return $this->defenderShip()->get()->first();
        }

        throw new \UnexpectedValueException("Turn");
    }

    /// <summary>
    /// Gets a reference to the other Ship.
    /// We reference Ships rather than Players because NPCs are not considered Players,
    /// but do own Ships and can fight.
    /// </summary>
    /// <value>The ship whose turn it is not.</value>
    /// <exception cref="UnexpectedValueException">Thrown when the Turn field is an invalid value</exception>
    public function ShipOther(): \App\Ship
    {
        if ($this->turn == 0)
        {
            return $this->defenderShip()->get()->first();
        }
        else if ($this->turn == 1)
        {
            return $this->attackerShip()->get()->first();
        }

        throw new \UnexpectedValueException("Turn");
    }

    /**
     * This function expects an array with weights
     * as its array indices. You should not use a
     * simple array — e.g. array('a', 'b', 'c'). Since
     * the first item in an array like this has index 0,
     * it will never be selected.
     *
     * It will return a randomly selected value.
     */
    private function wrand($data) {
        $totalw = array_sum(array_keys($data));
        $rand   = rand(1, $totalw);

        $curw   = 0;
        foreach ($data as $i => $val) {
            $curw += $i;
            if ($curw >= $rand) return $val;
        }

        return end($data);
    }

    /**
     * Fires the primary weapon at the opposing ship.
     * If the opposing ship is destoryed then the non-destoryed cargo and credits are
     * picked up and victory is declared. If the opposing ship is player driven then
     * the player is cloned on a nearby planet with a bank and given a small ship to get started again.
     * @return true if weapon hit, false otherwise
     * @throws \UnexpectedValueException Thrown if combat is over
     * @throws \OverflowException Thrown if not enough turn points are left to fire weapon
     */
    public function FireWeapon(): boolean
    {
        // Check that the combat is still in-progress
        if ($this->status != self::CombatStatus_Incomplete)
        {
            throw new \UnexpectedValueException("Combat is over");
        }

        $firingWeapon = $this->ShipTurn()->weapon;

        // Check there are enough turn points to fire weapon
        if ($this->turn_points_feft < $firingWeapon->turn_cost)
        {
            throw new \OverflowException("Not enough turn points left to fire weapon");
        }

        // Power up weapon
        $weaponDamage = $this->ShipTurn()->weapon->power;

        $turnRace = $this->ShipTurn()->race;
        if ($turnRace != null)
        {
            // Apply racial factor to weapons -/+ 10%
            $weaponDamage *= 1.0 + ($turnRace->weapons / 10.0);
        }

        // Get the weapon accuracy
        $weaponAccuracy = $this->weapon->base_accuracy;

        if ($turnRace != null)
        {
            // Apply racial factor to accuracy -/+ 10%
            $weaponAccuracy *= 1.0 + ($turnRace->accuracy / 10.0);
        }

        // Apply target ship's HitFactor modifier to accuracy -/+20%
        $weaponAccuracy *= 1.0 + ($this->ShipOther()->base_ship->hit_factor / 10.0);

        $weaponMissPerc = round(100.0 * $weaponAccuracy);
        $weaponHitPerc = round(100.0 * (1.0 - $weaponAccuracy));
        // Determine if the weapon will miss based on accuracy rating
        $weaponMiss = $this->wrand([
            $weaponMissPerc => false,
            $weaponHitPerc => true
        ]);

        if ($weaponMiss)
        {
            // Clear the weapon damage amount
            $weaponDamage = 0;

            // Count miss
            if ($this->turn == 0)
            {
                $this->attacker_misses++;
            }
            else
            {
                $this->defender_misses++;
            }
        }
        else
        {
            // Apply damage to the other ship
            $this->ShipOther()->ApplyDamage($weaponDamage);

            // Count hit
            if ($this->turn == 0)
            {
                $this->attacker_hits++;
            }
            else
            {
                $this->defender_hits++;
            }
        }

        // Deduct turn points
        $this->turn_points_left -= $firingWeapon->turn_cost;

        $props = [
            "CombatId" => $this->combat_id,
            "TurnShipId" => $this->ShipTurn()->ship_id,
            "OtherShipId" => $this->ShipOther()->ship_id,
            "WeaponDamage" => $weaponDamage,
            "WeaponAccuracy" => $weaponAccuracy,
            "TurnPointsLeft" => $this->turn_points_left
        ];
        Log::debug("Attacking ship fired weapon", $props);

        // Update turn action time
        $this->last_action_time = new \DateTime;

        // Save database changes
        $this->save();

        // Did we destory the other ship?
        if ($this->shipOther->destroyed)
        {
            // Victory
            $this->OtherShipDestroyed();
        }
        else if ($this->turn_points_left <= 0)
        {
            // No more turn points left, end turn
            $this->EndTurn();
        }

        return !$weaponMiss;
    }

    /**
     * Uses the rest of the current turn points to charge the jump drive.
     * If the jump drive becomes completely charged, the ship escapes and combat is ended.
     * @throws \UnexpectedValueException Thrown if combat is over
     */
    public function ChargeJumpDrive()
    {
        // Check that the combat is still in-progress
        if ($this->status != Combat::CombatStatus_Incomplete)
        {
            throw new \UnexpectedValueException("Combat is over");
        }

        $shipTurn = $this->ShipTurn();
        // Alloc turn points to the charge of the JumpDrive
        // Calculate how much the JumpDrive will charge
        // Formula: x = 100 / (ChargeTime / 4)
        // This means that if it takes 12 seconds to jump, it will take 3 turns to escape
        // or if it takes 4 seconds to jump, it will take 1 turn to escape
        $jumpDriveChargePerTurn = 100.0 / ($shipTurn->jumpDrive->charge_time / 4.0);

        $turnRace = $shipTurn->race;
        if ($turnRace != null)
        {
            // Apply racial jumpdrive boost/decrease
            $jumpDriveChargePerTurn *= 1.0 + ($turnRace->engine / 10.0);
        }

        // Based on many turn points left is how much the normal jumpdrive will charge
        // if you only have half your turn points left, you will only get half the normal charge amount
        $jumpDriveChargeCurrentTurn = (int)ceil($jumpDriveChargePerTurn * (1.0 * $this->turn_points_left / Combat::PointsPerTurn));

        $props = [
            "CombatId" => $this->combat_id,
            "TurnShipId" => $shipTurn->ship_id,
            "ChargePerTurn" => $jumpDriveChargePerTurn,
            "ChargeCurrentTurn" => $jumpDriveChargeCurrentTurn,
            "CurrentJumpDriveCharge" => $shipTurn->current_jump_drive_charge
        ];
        Log::debug("Charging JumpDrive", $props);

        $shipTurn->current_jump_drive_charge += $jumpDriveChargeCurrentTurn;
        $shipTurn->save();

        $this->turn_points_left = 0;

        // Did the jumpdrive fully charge?
        if ($shipTurn->current_jump_drive_charge >= 100)
        {
            // This ship escapes combat
            $this->status = Combat::CombatStatus_ShipFled;

            // Update player records
            $turnPlayer = $shipTurn->player;
            if ($turnPlayer != null)
            {
                $turnPlayer->flee_count++;
                $turnPlayer->save();
            }

            $otherPlayer = $this->ShipOther()->player;
            if ($otherPlayer != null)
            {
                $otherPlayer->forced_flees++;
                $otherPlayer->save();
            }

            // Cleanup combat
            $this->CleanupCombat();
        }

        // Update turn action time
        $this->last_action_time = new \DateTime;

        // Save database changes
        //$this->save();

        // End combat turn
        $this->EndTurn();
    }

    /// <summary>
    /// Cleanups after the combat. Ensures that both ships have completed traveling,
    /// are repaired, and have had their JumpDrive charge discharged.
    /// </summary>
    private function CleanupCombat()
    {
        $shipTurn = $this->ShipTurn();
        $shipOther = $this->ShipOther();

        // Check how much the longer the ship needed prep for jumping
        if (Carbon::now()->greaterThan($shipTurn->target_system_arrival_time))
        {
            // The ship still needs time to prep,
            // Combat is non real-time so we will cheat here and make the ship instantly jump
            $shipTurn->target_system_arrival_time = (new \DateTime)->modify('+1 second');
        }

        // Check how much the longer the other ship needed prep for jumping
        if (Carbon::now()->greaterThan($shipOther->target_system_arrival_time))
        {
            // The other ship still needs time to prep,
            // Combat is non real-time so we will cheat here and make the ship instantly jump
            $shipOther->target_system_arrival_time = (new \DateTime)->modify('+1 second');
        }

        // Ensure both ships are no longer traveling
        $shipTurn->CheckIfTraveling();
        $shipOther->CheckIfTraveling();

        // Reset jump drive charges
        $shipOther->current_jump_drive_charge = 0;
        $shipTurn->current_jump_drive_charge = 0;

        // Repair both ships
        $shipTurn->Repair();
        $shipOther->Repair();
    }

    /**
     * Ends the current ships turn. Giving control to the other ship.
     * This does check the surrender and cargo flags, so do not call when offering surrender
     * or jettisoning cargo.
    */
    public function EndTurn()
    {
        // Only swap turns if combat is still in-progress
        if ($this->status == Combat::CombatStatus_Incomplete)
        {
            $this->SwapTurn();
        }

        // Check if surrender was not accepted
        if ($this->surrendered)
        {
            // Reset flag
            $this->surrendered = false;
        }

        // Check if cargo was not picked up
        if ($this->cargo_jettisoned)
        {
            // Delete ignored space goods
            $this->combatGoods->delete();

            // Reset flag
            $this->cargo_jettisoned = false;
        }

        // Check for search flag
        if ($this->search)
        {
            // Reset flag
            $this->search = false;
        }

        // Save database changes
        $this->save();
    }

    /**
     * Swaps the turn to the other ship, giving up any turn points left.
     * This does not check the surrender or cargo flags.
     * Changes have to be saved to database afterwords
     * @throws \UnexpectedValueException Thrown if combat is over
     */
    private function SwapTurn()
    {
        // Check that the combat is still in-progress
        if ($this->status != Combat::CombatStatus_Incomplete)
        {
            throw new \UnexpectedValueException("Combat is over");
        }

        // Swap the turn
        if ($this->turn == 0)
        {
            $this->turn = 1;
        }
        else if ($this->turn == 1)
        {
            $this->turn = 0;
        }
        else
        {
            throw new \UnexpectedValueException("Unexpected Turn Value: " . $this->turn);
        }

        // Rest the turn point counter
        $this->turn_points_left = Combat::PointsPerTurn;
    }

    /*
    /// <summary>
    /// Gets the number of cargo items jettisoned and avaiable to be picked up.
    /// </summary>
    /// <value>The total quantity of cargo jettisoned.</value>
    public int CargoJettisonedCount
    {
        get
        {
            if (this.CargoJettisoned)
            {
                return this.CombatGoods.Sum(g => g.Quantity);
            }
            else
            {
                return 0;
            }
        }
    }

    /// <summary>
    /// Gets the turn time left.
    /// </summary>
    /// <value>The turn time left.</value>
    public TimeSpan TurnTimeLeft
    {
        get
        {
            return this.LastActionTime.AddSeconds(Combat.SecondsPerTurn) - DateTime.UtcNow;
        }
    }

    /// <summary>
    /// Checks the turn time left. Auto-charging JumpDrive if no time is left in the turn.
    /// </summary>
    public void CheckTurnTimeLeft()
    {
        // Check if any time is left
        if (this.TurnTimeLeft.TotalSeconds < 0 && this.Status == CombatStatus.Incomplete)
        {
            // No time left in turn, auto-charge JumpDrive
            this.ChargeJumpDrive();
        }
    }

    /// <summary>
    /// Gives up the rest of the turn and signals that the current ship is surrendering to the opposing ship.
    /// </summary>
    /// <exception cref="InvalidOperationException">Thrown when combat is over or other ship has surrendered</exception>
    public virtual void OfferSurrender()
    {
        // Check that the combat is still in-progress
        if (this.Status != CombatStatus.Incomplete)
        {
            throw new InvalidOperationException("Combat is over");
        }

        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        // Surrender flag must be not set
        if (this.Surrendered)
        {
            throw new InvalidOperationException("Other ship already offered surrender");
        }

        this.Surrendered = true;

        Dictionary<string, object> props = new Dictionary<string, object>
        {
            { "CombatId", this.CombatId },
            { "TurnShipId", this.ShipTurn.ShipId },
            { "OtherShipId", this.ShipOther.ShipId }
        };
        Logger.Write("Offered surrender", "Model", 150, 0, TraceEventType.Verbose, "Combat.OfferSurrender", props);

        // Update turn action time
        this.LastActionTime = DateTime.UtcNow;

        // Turn is ended
        this.SwapTurn();

        // Save database changes
        db.SaveChanges();
    }

    /// <summary>
    /// Accept the surrender of the opposing ship.
    /// This gives all the goods and credits aboard the opposing ship to the current ship and ends combat.
    /// </summary>
    /// <exception cref="InvalidOperationException">Thrown when combat is over or no surrender has been offered</exception>
    public virtual void AcceptSurrender()
    {
        // Check that the combat is still in-progress
        if (this.Status != CombatStatus.Incomplete)
        {
            throw new InvalidOperationException("Combat is over");
        }

        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        // Surrender flag must be set
        if (!this.Surrendered)
        {
            throw new InvalidOperationException("No surrender offered");
        }

        Dictionary<string, object> props = new Dictionary<string, object>
        {
            { "CombatId", this.CombatId },
            { "TurnShipId", this.ShipTurn.ShipId },
            { "OtherShipId", this.ShipOther.ShipId }
        };
        Logger.Write("Accepted surrender", "Model", 150, 0, TraceEventType.Verbose, "Combat.AcceptSurrender", props);

        // In space the cargo will go
        this.SendCargoIntoSpace(this.ShipOther);

        // Move enemy cargo from space into our cargo bays
        this.LoadCargo();

        // Take the other players credits
        this.StealCredits();

        // Update player records
        Player turnPlayer = this.ShipTurn.Players.SingleOrDefault();
        if (turnPlayer != null)
        {
            turnPlayer.ForcedSurrenders++;
        }

        Player otherPlayer = this.ShipOther.Players.SingleOrDefault();
        if (otherPlayer != null)
        {
            otherPlayer.SurrenderCount++;
        }

        // Combat has ended
        this.Status = CombatStatus.ShipSurrendered;

        // Update turn action time
        this.LastActionTime = DateTime.UtcNow;

        // Cleanup combat
        this.CleanupCombat();

        // Save database changes
        db.SaveChanges();
    }

    /// <summary>
    /// Jettison all of the ships current cargo.
    /// This will allow the ship to escape if the opposing ship picks up the cargo.
    /// </summary>
    /// <exception cref="InvalidOperationException">Thrown when combat is over or there is no cargo to jettison or cargo has already need jettisoned</exception>
    public virtual void JettisonCargo()
    {
        // Check that the combat is still in-progress
        if (this.Status != CombatStatus.Incomplete)
        {
            throw new InvalidOperationException("Combat is over");
        }

        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        // Jettison Cargo flag must be not set
        if (this.CargoJettisoned)
        {
            throw new InvalidOperationException("There is already cargo jettisoned");
        }

        // Check that the ship has cargo to jettison
        if (this.ShipTurn.ShipGoods.Sum(g => g.Quantity) == 0)
        {
            throw new InvalidOperationException("No ship cargo to jettison");
        }

        // Sending cargo into space
        this.SendCargoIntoSpace(this.ShipTurn);

        this.CargoJettisoned = true;

        Dictionary<string, object> props = new Dictionary<string, object>
        {
            { "CombatId", this.CombatId },
            { "TurnShipId", this.ShipTurn.ShipId },
            { "OtherShipId", this.ShipOther.ShipId }
        };
        Logger.Write("Jettisoned cargo", "Model", 150, 0, TraceEventType.Verbose, "Combat.JettisonShipCargo", props);

        // Update turn action time
        this.LastActionTime = DateTime.UtcNow;

        // Swap Turn to other player
        this.SwapTurn();

        // Save database changes
        db.SaveChanges();
    }

    /// <summary>
    /// Pickup cargo jettisoned by opposing ship, this will end combat as the other ship will escape.
    /// If the cargo is not picked up, it is deleted on the next turn.
    /// </summary>
    /// <exception cref="InvalidOperationException">Thrown when combat is over or there is no cargo to jettison</exception>
    public virtual void PickupCargo()
    {
        // Check that the combat is still in-progress
        if (this.Status != CombatStatus.Incomplete)
        {
            throw new InvalidOperationException("Combat is over");
        }

        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        // Jettison Cargo flag must be set
        if (!this.CargoJettisoned)
        {
            throw new InvalidOperationException("No cargo jettisoned");
        }

        // Find cargo to pickup
        this.LoadCargo();

        // Combat has ended
        this.Status = CombatStatus.CargoPickup;

        // Update turn action time
        this.LastActionTime = DateTime.UtcNow;

        // Save database changes
        db.SaveChanges();

        // Cleanup combat
        this.CleanupCombat();
    }

    /// <summary>
    /// Starts the search of the other ship
    /// </summary>
    /// <exception cref="InvalidOperationException">Thrown when combat is over or there is already a search in-progress</exception>
    public virtual void StartSearch()
    {
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        // Check that the combat is still in-progress
        if (this.Status != CombatStatus.Incomplete)
        {
            throw new InvalidOperationException("Combat is over");
        }

        // Check that we are not already searching one of the ships
        if (this.Search)
        {
            throw new InvalidOperationException("Already searching other ship");
        }

        // Start search
        this.Search = true;

        // Other ship turn
        this.SwapTurn();

        db.SaveChanges();
    }

    /// <summary>
    /// Accepts the search of the current turn ship by the other ship
    /// </summary>
    /// <exception cref="InvalidOperationException">Thrown when combat is over or there is no search in-progress</exception>
    /// <exception cref="ArgumentException">Thrown when there is not enough credits to pay the fine. Combat continues.</exception>
    public virtual void AcceptSearch()
    {
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        // Check that the combat is still in-progress
        if (this.Status != CombatStatus.Incomplete)
        {
            throw new InvalidOperationException("Combat is over");
        }

        if (!this.Search)
        {
            throw new InvalidOperationException("No search in progress");
        }

        // Look for contraband cargo
        ShipGood[] goods = (from g in this.ShipTurn.GetGoods()
                            where g.Good.Contraband
                            select g).ToArray();
        int contrabandCargoWorth = goods.Sum(g => g.Quantity * g.Good.BasePrice);
        if (contrabandCargoWorth > 0)
        {
            // Ship has some contraband cargo

            // The fine is twice the worth of the cargo
            int contrabandFine = contrabandCargoWorth * 2;

            // Check if the ship has enough to pay the fine
            if (this.ShipTurn.Credits < contrabandFine)
            {
                // Not enough credits to pay the fine, search is rejected
                throw new ArgumentException("Not enough credits to pay the fine!");
            }
            else
            {
                // Charge the fine
                this.ShipTurn.Credits -= contrabandFine;
                this.ShipOther.Credits += contrabandFine;
                this.CreditsLooted += contrabandFine;
            }

            // Take the cargo away
            foreach (ShipGood good in goods)
            {
                // Add a CombatGood record
                CombatGood combatGood = new CombatGood();
                combatGood.Combat = this;
                combatGood.Good = good.Good;
                combatGood.Quantity = good.Quantity;
                db.CombatGoods.InsertOnSubmit(combatGood);

                // Load it up on the police ship
                this.ShipOther.AddGood(good.GoodId, good.Quantity);

                // Take the cargo away
                good.Quantity = 0;
            }
        }

        // Combat is now over
        this.Status = CombatStatus.ShipSearched;

        db.SaveChanges();
    }

    /// <summary>
    /// Called when the other ship has been destroyed. Current turn ship has won
    /// </summary>
    private void OtherShipDestroyed()
    {
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        // No longer traveling, combat has ended with one ship being destroyed
        this.ShipOther.TargetSystemId = null;
        this.ShipOther.TargetSystemArrivalTime = null;
        this.ShipTurn.TargetSystemId = null;
        this.ShipTurn.TargetSystemArrivalTime = null;

        // Destroy some of the other ship's credits/cargo
        double creditsCargoDestroyedPerc = this.rnd.Next(Combat.CreditsCargoDestroyedPercentMin, Combat.CreditsCargoDestroyedPercentMax) / 100.0;
        Dictionary<string, object> props;

        foreach (ShipGood good in this.ShipOther.ShipGoods)
        {
            // Calculate how many goods to destroy
            int goodsDestroyed = (int)Math.Round(good.Quantity * creditsCargoDestroyedPerc);

            props = new Dictionary<string, object>
            {
                { "CombatId", this.CombatId },
                { "OtherShipId", good.ShipId },
                { "GoodId", good.GoodId },
                { "Quantity", good.Quantity },
                { "QuantityDestroyed", goodsDestroyed }
            };
            Logger.Write("Destroying some of losing ship cargo", "Model", 150, 0, TraceEventType.Verbose, "InProgressCombat.OtherShipDestroyed", props);

            // Destroy the goods
            good.Quantity -= goodsDestroyed;
        }

        // Calculate how many credits to destroy
        int cashCreditsDestroyed = (int)Math.Round(this.ShipOther.Credits * creditsCargoDestroyedPerc);

        props = new Dictionary<string, object>
        {
            { "CombatId", this.CombatId },
            { "OtherShipCredits", this.ShipOther.Credits },
            { "CashCreditsDestroyed", cashCreditsDestroyed }
        };
        Logger.Write("Destroying some of losing shipcredits", "Model", 150, 0, TraceEventType.Verbose, "InProgressCombat.OtherShipDestroyed", props);

        // Destroy credits
        this.ShipOther.Credits -= cashCreditsDestroyed;

        // Sending cargo into space
        this.SendCargoIntoSpace(this.ShipOther);

        // Move cargo into our cargo bays
        this.LoadCargo();

        props = new Dictionary<string, object>
        {
            { "CombatId", this.CombatId },
            { "OtherShipCredits", this.ShipOther.Credits },
            { "TurnShipCredits", this.ShipTurn.Credits }
        };
        Logger.Write("Transfering losing player credits to winner", "Model", 150, 0, TraceEventType.Verbose, "InProgressCombat.OtherShipDestroyed", props);

        // Take the other ship credits
        this.StealCredits();

        Player otherPlayer = this.ShipOther.Players.SingleOrDefault();
        if (otherPlayer != null)
        {
            // Relocate other player to nearest system with a bank
            CosmoSystem bankSystem = this.ShipOther.GetNearestBankSystem();

            // Save a reference to the players old ship
            Ship otherPlayerOldShip = otherPlayer.Ship;

            otherPlayer.Ship = null;

            props = new Dictionary<string, object>
            {
                { "CombatId", this.CombatId },
                { "OtherPlayerId", otherPlayer.PlayerId },
                { "BankSystemId", bankSystem.SystemId }
            };
            Logger.Write("Relocating losing player to nearest system with bank", "Model", 150, 0, TraceEventType.Verbose, "InProgressCombat.OtherShipDestroyed", props);

            // Give the player a new ship in the bank system
            otherPlayer.CreateStartingShip(bankSystem);

            // Restore ship credits
            otherPlayer.Ship.Credits = otherPlayerOldShip.Credits;

            // Update other player stats
            otherPlayer.ShipsLost++;
        }

        // Update turn player stats
        Player turnPlayer = this.ShipTurn.Players.SingleOrDefault();
        if (turnPlayer != null)
        {
            turnPlayer.ShipsDestroyed++;
        }

        // Combat has ended
        this.Status = CombatStatus.ShipDestroyed;

        // Save database changes
        db.SaveChanges();

        // Cleanup combat
        this.CleanupCombat();

        // End combat turn
        this.EndTurn();
    }

    /// <summary>
    /// Steals the other players credits.
    /// </summary>
    private void StealCredits()
    {
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        Dictionary<string, object> props = new Dictionary<string, object>
        {
            { "CombatId", this.CombatId },
            { "TurnShipId", this.ShipTurn.ShipId },
            { "OtherShipId", this.ShipOther.ShipId },
            { "TurnShipCredits", this.ShipTurn.Credits },
            { "OtherShipCredits", this.ShipOther.Credits }
        };
        Logger.Write("Transfering losing ship credits to winner", "Model", 150, 0, TraceEventType.Verbose, "InProgressCombat.OtherShipDestroyed", props);

        // Take the other ship credits
        this.CreditsLooted = this.ShipOther.Credits;
        this.ShipOther.Credits = 0;

        // Give to the winner ship
        this.ShipTurn.Credits += this.CreditsLooted;

        // If loser is player, give him starting credits
        Player otherPlayer = this.ShipOther.Players.SingleOrDefault();
        if (otherPlayer != null)
        {
            // Give the player some starting credits
            double cloneCredits = 2000 - ((otherPlayer.BankCredits + 1) / 5000.0 * 2000);

            // Ignore negative values
            cloneCredits = Math.Max(cloneCredits, 0);
            this.ShipOther.Credits = (int)cloneCredits;

            props = new Dictionary<string, object>
            {
                { "CombatId", this.CombatId },
                { "TurnShipId", this.ShipTurn.ShipId },
                { "OtherPlayerId", otherPlayer.PlayerId },
                { "Credits", this.ShipOther.Credits }
            };
            Logger.Write("Giving losing player starting credits", "Model", 150, 0, TraceEventType.Verbose, "InProgressCombat.OtherShipDestroyed", props);
        }

        // Save database changes
        db.SaveChanges();
    }

    /// <summary>
    /// Sends all the cargo on the ship into space.
    /// </summary>
    /// <param name="sourceShip">The source ship to throw the cargo out of.</param>
    private void SendCargoIntoSpace(Ship sourceShip)
    {
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        // We will unload all the cargo off of the source ship and move it into the combat 'space'
        foreach (ShipGood shipGood in sourceShip.ShipGoods.Where(g => g.Quantity > 0))
        {
            CombatGood good = (from g in this.CombatGoods
                                where g.Good == shipGood.Good
                                select g).SingleOrDefault();
            if (good == null)
            {
                good = new CombatGood();
                good.Combat = this;
                good.Good = shipGood.Good;
                db.CombatGoods.InsertOnSubmit(good);
            }

            // Into space the good goes...
            good.Quantity += shipGood.Quantity;

            // The good is no longer on the ship
            shipGood.Quantity = 0;
        }

        // Update the player stats on lost cargo
        Player shipPlayer = sourceShip.Players.SingleOrDefault();
        if (shipPlayer != null)
        {
            int cargoWorth = this.CombatGoods.Sum(g => (g.Quantity * g.Good.BasePrice));
            shipPlayer.CargoLostWorth += cargoWorth;
        }

        // Save database changes
        db.SaveChanges();
    }

    /// <summary>
    /// Loads as much of the cargo in space as possible into the current turn ship.
    /// </summary>
    private void LoadCargo()
    {
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        // We will unload all the cargo off of the source ship and move it into the combat 'space'
        // Load the higher priced goods first
        foreach (CombatGood good in this.CombatGoods.OrderByDescending(g => g.Good.BasePrice))
        {
            // Load up the cargo
            int quantityLoaded = this.ShipTurn.AddGood(good.GoodId, good.Quantity);
            good.QuantityPickedUp = quantityLoaded;
        }

        Dictionary<string, object> props = new Dictionary<string, object>
        {
            { "CombatId", this.CombatId },
            { "TurnShipId", this.ShipTurn.ShipId },
            { "OtherShipId", this.ShipOther.ShipId },
            { "TotalCargoCount", this.CombatGoods.Sum(g => g.Quantity) },
            { "TotalPickupCount", this.CombatGoods.Sum(g => g.QuantityPickedUp) }
        };
        Logger.Write("Picked up cargo", "Model", 150, 0, TraceEventType.Verbose, "Combat.LoadCargo", props);

        // Update the player stats on looted cargo
        Player shipPlayer = this.ShipTurn.Players.SingleOrDefault();
        if (shipPlayer != null)
        {
            int cargoWorth = this.CombatGoods.Sum(g => (g.Quantity * g.Good.BasePrice));
            shipPlayer.CargoLootedWorth += cargoWorth;
        }

        // Save database changes
        db.SaveChanges();
    }
    */
}
