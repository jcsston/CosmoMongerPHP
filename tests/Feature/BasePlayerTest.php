<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    private function CreateTestPlayer(string $baseTestUsername, string $baseTestEmail, string $baseTestPlayerName, ?\App\Race $playerRace): \App\Player
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

        /*
        // TODO: Implement CreatePlayer function on User model
        return $testUser->CreatePlayer($baseTestUsername, $playerRace);
        */
        return new \App\Player;
    }

    private function removeTestPlayers()
    {
        // Cleanup any possible test players
        $users = \App\User::where([
             ['name', 'like', $this->baseTestUsername . "%"],
             ['email', 'like', "%" . $this->baseTestEmail]
        ]);
        $users->delete();
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
