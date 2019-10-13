<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * This is the base test class for player related tests.
 *  This class will create a test player to work with and also cleanup the player when done.
 */
class BasePlayerTest extends TestCase
{
    private $baseTestUsername = "testUser";
    private $baseTestEmail = "testUser@cosmomonger.com";
    private $baseTestPlayerName = "testPlayer";
    private $playerCount = 0;

    protected function CreateTestPlayerWithDefaultName(?\App\Race $playerRace=null): \App\Player
    {
        $this->playerCount++;
        $baseTestUsername = $this->baseTestUsername . $this->playerCount;
        $baseTestEmail = $this->playerCount . $this->baseTestEmail;
        $baseTestPlayerName = $this->baseTestPlayerName . $this->playerCount;

        return $this->CreateTestPlayer($baseTestUsername, $baseTestEmail, $baseTestPlayerName, $playerRace);
    }

    protected function CreateTestPlayer(string $baseTestUsername, string $baseTestEmail, string $baseTestPlayerName, ?\App\Race $playerRace): \App\Player
    {
        if ($playerRace == null) {
            // Default to Human race
            $humanRace = \App\Race::where('name', "Human")->first();

            $this->assertNotNull($humanRace, "Human Race needs to be present in the database");
            $playerRace = $humanRace;
        }

        $testUser = new \App\User;
        $testUser->name = $baseTestUsername;
        $testUser->email = $baseTestEmail;
        $testUser->password = 'testPassword15';
        $testUser->save();
        $testUserId = $testUser->getKey();
        $this->assertNotNull($testUserId, "Test User was created.");

        return $testUser->CreatePlayer($baseTestUsername, $playerRace);
    }

    /**
     * Was previously Cleanup
     */
    protected function removeTestPlayers()
    {
        // Cleanup any possible test players
        $users = \App\User::where([
             ['name', 'like', $this->baseTestUsername . "%"],
             ['email', 'like', "%" . $this->baseTestEmail]
        ]);

        // Remove linked players
        foreach ($users->get() as $user) {
            foreach ($user->players as $player) {
                $ship = $player->ship;
                if ($ship) {
                    foreach ($ship->goods as $good) {
                        Log::debug("Deleting ship_good " . $good->getKey());
                        $good->delete();
                    }
                    Log::debug("Deleting ship " . $ship->getKey());
                    $ship->delete();
                }
                Log::debug("Deleting player " . $player->getKey());
                $player->delete();
            }
        }
        Log::debug("Deleting users " . $users->count());
        $users->delete();

        // Remove orphaned players
        DB::table('player')->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('users')
                  ->whereRaw('users.id = player.user_id');
        })
        ->delete();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->removeTestPlayers();
    }

    protected function tearDown(): void
    {
        $this->removeTestPlayers();
        parent::tearDown();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreatePlayer()
    {
        $player = $this->CreateTestPlayerWithDefaultName();
        $this->assertNotNull($player, "Player should be created");
        $this->assertNotNull($player->getKey(), "Player should have id");
    }
}
