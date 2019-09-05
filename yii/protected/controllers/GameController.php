<?php

/// <summary>
/// This is the base controller for all game related controllers.
/// Any user has to be authorized to access this controller.
/// </summary>
class GameController extends CController
{
	/// <summary>
	/// Holds the GameManger object for this controller
	/// </summary>
	var $gameManager = null;
	
	public function filters()
	{
		return array(
			'accessControl',
			array(
				'CInlineFilter',
				'name'=>'TrackPlayTime',
			),
		);
	}
	
	public function accessRules()
	{
		return array(
			array('deny', // deny unauthenticated users access to GameController based actions
				'users'=>array('?'),
			),
		);
	}
	
	/// <summary>
	/// Gets the GameManager for the current player in the context of this controller.
	/// </summary>
	/// <value>The GameManager object for this controller.</value>
	protected function getGameManager()
	{
		if ($this->gameManager === null)
		{
			$this->gameManager = new GameManager(Yii::app()->user->name);
			
			// TODO: Check if we need to do NPC AI processing
		}
		
		return $this->gameManager;	
	}
	
	public function filterTrackPlayTime($filterChain)
	{
		// Update the player playtime
		$currentPlayer = $this->getGameManager()->getCurrentPlayer();
		if ($currentPlayer != null)
		{
			$currentPlayer->updatePlayTime();
	
			// Redirect to the dead screen if the player has died
			if (!$currentPlayer->Alive)
			{
				$this->redirect(array("Player/Dead"));
			}
		}
		$filterChain->run();
	}
	
	/*
	/// <summary>
	/// Called before an action has executed.
	/// This override is used to redirect users without an active player to the PlayerController.CreatePlayer action.
	/// </summary>
	/// <param name="filterContext">The context of the executing action.</param>
	protected override void OnActionExecuting(ActionExecutingContext filterContext)
	{
		// Check if the user has a current player and is not trying to create a player
		Type controllerType = filterContext.Controller.GetType();
		if (this.ControllerGame.CurrentPlayer == null)
		{
			if (controllerType != typeof(PlayerController))
			{
				// Redirect to the CreatePlayer action
				filterContext.HttpContext.Response.Redirect(this.Url.Action("CreatePlayer", "Player"));
			}
		}
		else
		{
			if (this.ControllerGame.CurrentPlayer.Ship.InProgressCombat != null && controllerType != typeof(CommunicationController) && controllerType != typeof(CombatController) && controllerType != typeof(AdminController))
			{
				// The player is currently in combat, redirect to combat start page
				filterContext.HttpContext.Response.Redirect(this.Url.Action("CombatStart", "Combat"));
			}
			else
			{
				// Check that the session for the user matches
				if (this.Session.SessionID != this.ControllerGame.CurrentUser.SessionID)
				{
					// Redirect to the Logout page
					filterContext.HttpContext.Response.Redirect(this.Url.Action("Logout", "Account"));
				}

				// Update the player playtime
				Player currentPlayer = this.ControllerGame.CurrentPlayer;
				if (currentPlayer != null)
				{
					currentPlayer.UpdatePlayTime();

					// Redirect to the dead screen if the player has died
					if (!currentPlayer.Alive)
					{
						filterContext.HttpContext.Response.Redirect(this.Url.Action("Dead", "Player"));
					}
				}

				base.OnActionExecuting(filterContext);
			}
		}
	}
	*/
}

