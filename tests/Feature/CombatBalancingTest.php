<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class CombatBalancingTest extends BasePlayerTest
{
    /**
     * Number of mock combats to conduct
     */
    public $TrialCount = 1;

    private $combat;
    private $player1;
    private $player2;
    private $combatStats = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->player1 = $this->CreateTestPlayerWithDefaultName();
        $this->player2 = $this->CreateTestPlayerWithDefaultName();
    }

    private function ResetCombatStats()
    {
        $this->combatStats["Winner"] = [];
        $this->combatStats["Turns"] = [];
        $this->combatStats["AttackerHits"] = [];
        $this->combatStats["AttackerMisses"] = [];
        $this->combatStats["DefenderHits"] = [];
        $this->combatStats["DefenderMisses"] = [];
    }

    private function StartCombat()
    {
        $this->player1->ship->Attack($this->player2->ship);

        $this->combat = $this->player1->ship->InProgressCombat();
    }

    private function DoCombat()
    {
        // Track number of hits/misses
        $attackerHitCount = 0;
        $attackerMissCount = 0;
        $defenderHitCount = 0;
        $defenderMissCount = 0;

        // Track number of turns taken
        $combat = $this->combat;
        $lastTurn = $combat->turn;
        $turnsTaken = 0;

        // Keep going until the combat is over
        while ($combat->status == \App\Combat::CombatStatus_Incomplete)
        {
            try
            {
                if ($combat->turn == 0)
                {
                    if ($combat->FireWeapon())
                    {
                        $attackerHitCount++;
                    }
                    else
                    {
                        $attackerMissCount++;
                    }
                }
                else
                {
                    if ($combat->FireWeapon())
                    {
                        $defenderHitCount++;
                    }
                    else
                    {
                        $defenderMissCount++;
                    }
                }
            }
            catch (\OverflowException $e)
            {
                // Not enough turn points to fire weapon, charge jumpdrive instead
                $combat->ChargeJumpDrive();
            }

            if ($combat->turn != $lastTurn)
            {
                // New Turn
                $lastTurn = $combat->turn;
                $turnsTaken++;
            }
        }


        $this->assertEquals($turnsTaken, $combat->turns_taken);
        $this->assertEquals($attackerHitCount, $combat->attacker_hit_count);
        $this->assertEquals($attackerMissCount, $combat->attacker_miss_count);
        $this->assertEquals($defenderHitCount, $combat->defender_hit_count);
        $this->assertEquals($defenderMissCount, $combat->defender_miss_count);

        $this->combatStats["Winner"][] = $combat->turn;
        $this->combatStats["Turns"][] = $combat->turns_taken;
        $this->combatStats["AttackerHits"][] = $combat->attacker_hit_count;
        $this->combatStats["AttackerMisses"][] = $combat->attacker_miss_count;
        $this->combatStats["DefenderHits"][] = $combat->defender_hit_count;
        $this->combatStats["DefenderMisses"][] = $combat->defender_miss_count;

        Log::debug(sprintf("Winner: ? Turns: %i Attacker:  %i/ %i Defender:  %i/ %i", $combat->turn, $turnsTaken, $attackerHitCount, $attackerMissCount, $defenderHitCount, $defenderMissCount));
    }

    private function SetPlayerShip(\App\Player $player, string $shipName)
    {
        // TODO: Convert to PHP
        // Assign the default base ship type
        // BaseShip baseShip = (from bs in db.BaseShips
        //                         where bs.Name == shipName
        //                         select bs).SingleOrDefault();

        // player.Ship.BaseShip = baseShip;

        // Setup default upgrades
        // player.Ship.JumpDrive = player.Ship.BaseShip.InitialJumpDrive;
        // player.Ship.Shield = player.Ship.BaseShip.InitialShield;
        // player.Ship.Weapon = player.Ship.BaseShip.InitialWeapon;
    }

    private function SetPlayerRace(\App\Player $player, string $raceName)
    {
        // Assign the correct race
        // TODO: Convert to PHP
        // $player->Race = (from r in db.Races
        //                 where r.Name == raceName
        //                 select r).SingleOrDefault();
    }

    public function testHumanTrashCanVsHumanTrashCan()
    {
        // Reset combat stats
        $this->ResetCombatStats();

        // Set both players to be human
        $this->SetPlayerRace($this->player1, "Human");
        $this->SetPlayerRace($this->player2, "Human");

        for ($i = 0; $i < $this->TrialCount; $i++)
        {
            // Make sure each player has the right ship
            $this->SetPlayerShip($this->player1, "Glorified Trash Can");
            $this->SetPlayerShip($this->player2, "Glorified Trash Can");

            $this->StartCombat();
            $this->DoCombat();
        }

        //Debug.WriteLine(string.Format("Average Winner: {0} Turns: {1} Attacker: {2}/{3} Defender: {4}/{5}", combatStats["Winner"].Average(), combatStats["Turns"].Average(), combatStats["AttackerHits"].Average(), combatStats["AttackerMisses"].Average(), combatStats["DefenderHits"].Average(), combatStats["DefenderMisses"].Average()));
    }

    public function testHumanTrashCanVsHumanRover()
    {
        // Reset combat stats
        $this->ResetCombatStats();

        // Set both players to be human
        $this->SetPlayerRace($this->player1, "Human");
        $this->SetPlayerRace($this->player2, "Human");

        for ($i = 0; $i < $this->TrialCount; $i++)
        {
            // Make sure each player has the right ship
            $this->SetPlayerShip($this->player1, "Glorified Trash Can");
            $this->SetPlayerShip($this->player2, "Rover");

            $this->StartCombat();
            $this->DoCombat();
        }

        //Debug.WriteLine(string.Format("Average Winner: {0} Turns: {1} Attacker: {2}/{3} Defender: {4}/{5}", combatStats["Winner"].Average(), combatStats["Turns"].Average(), combatStats["AttackerHits"].Average(), combatStats["AttackerMisses"].Average(), combatStats["DefenderHits"].Average(), combatStats["DefenderMisses"].Average()));
    }

    public function testHumanRoverVsHumanRover()
    {
        // Reset combat stats
        $this->ResetCombatStats();

        // Set both players to be human
        $this->SetPlayerRace($this->player1, "Human");
        $this->SetPlayerRace($this->player2, "Human");

        for ($i = 0; $i < $this->TrialCount; $i++)
        {
            // Make sure each player has the right ship
            $this->SetPlayerShip($this->player1, "Rover");
            $this->SetPlayerShip($this->player2, "Rover");

            $this->StartCombat();
            $this->DoCombat();
        }

        //Log::debug("Average Winner: " . $this->combatStats["Winner"] . " Turns: {1} Attacker: {2}/{3} Defender: {4}/{5}", combatStats["Winner"].Average(), combatStats["Turns"].Average(), combatStats["AttackerHits"].Average(), combatStats["AttackerMisses"].Average(), combatStats["DefenderHits"].Average(), combatStats["DefenderMisses"].Average()));
    }
}
