<?php

class PlayerController extends GameController
{
	/// <summary>
	/// Creates a player using the inputed player name and race by calling the User.CreatePlayer method. 
	/// Raises an error if another player with the same name already exists.
	/// Redirects to PlayerProfile view if player is successfully created, CreatePlayer view otherwise.
	/// </summary>
	/// <returns>The PlayerProfile view if player is created, CreatePlayer view otherwise.</returns>
	public function actionCreatePlayer()
	{
		$player = $this->getGameManager()->getCurrentPlayer();
		if ($player != NULL) {
			// User already has a player, redirecting to player profile
			$this->redirect($this->createUrl('PlayerProfile'));
			return;
		}

		$this->render('CreatePlayer');
	}

	/// <summary>
	/// Kills the player.
	/// </summary>
	/// <returns>A redirect to the CreatePlayer action</returns>
	public function actionKillPlayer()
	{
		$this->render('KillPlayer');
	}

	/// <summary>
	/// Dead from old age.
	/// </summary>
	/// <returns>The Dead view</returns>
	public function actionDead()
	{
		$this->render('Dead');
	}

	/// <summary>
	/// Looks up the profile data for the current player and returns the PlayerProfile view.
	/// </summary>
	/// <returns>The PlayerProfile view with the current Player model data.</returns>
	public function actionPlayerProfile()
	{
		$player = $this->getGameManager()->getCurrentPlayer();
		if ($player == NULL) {
			$this->redirect($this->createUrl('CreatePlayer'));
			return;
		}
		$viewData = array(
			'playerId' => $player->PlayerId,
			'name' => $player->Name,
			'raceName' => $player->Race->Name,
			'netWorth' => '$' . number_format($player->NetWorth),
			'shipCredits' => '$' . number_format($player->Ship->Credits),
			'bankCredits' => '$' . number_format($player->BankCredits),
			'shipTradeInValue' =>'$' . number_format(0),
			'cargoWorth' => '$' . number_format(0),
			'playerAge' => sprintf("%01.2f", ($player->TimePlayed / 3600)),
		);
		if ($player->Race->RacialPreference != null) 
		{
			$viewData['racialPreference'] = $player->Race->RacialPreference->Name;
		}
		else
		{
			$viewData['racialPreference'] = "None";
		}
		if ($player->Race->RacialEnemy != null)
		{
			$viewData['racialEnemy'] = $player->Race->RacialEnemy->Name;
		}
		else
		{
			$viewData['racialEnemy'] = "None";	
		}
		
		$this->render('PlayerProfile', $viewData);
	}

	/// <summary>
	/// Redirects to the PlayerProfile action
	/// </summary>
	/// <returns>
	/// The PlayerProfile action
	/// </returns>
	public function actionIndex()
	{
		$this->redirect($this->createUrl('PlayerProfile'));
	}

}