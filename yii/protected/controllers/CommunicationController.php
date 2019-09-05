<?php

class CommunicationController extends GameController
{
	public function actionInbox()
	{
		$this->render('Inbox');
	}

	public function actionSent()
	{
		$this->render('Sent');
	}

	public function actionCompose()
	{
		$this->render('Compose');
	}

	public function actionViewMessage()
	{
		if (isset($_GET['messageId']))
		{
			$message = $this->getGameManager()->getCurrentUser()->getMessage($_GET['messageId']);
			$viewData = array();
			$this->render('ViewMessage', $viewData);
		}
		else
		{
			$this->redirect(array('Inbox'));
		}
		
	}

	public function actionDeleteMessage()
	{
		$this->render('DeleteMessage');
	}

	public function actionUnreadMessages()
	{
		$unreadMessages = $this->getGameManager()->getCurrentUser()->getUnreadMessages();
		$this->render('UnreadMessages');
	}

	public function actionIndex()
	{
		$this->render('index');
	}
}