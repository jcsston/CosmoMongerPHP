<?php

class User extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'User':
	 * @var integer $UserId
	 * @var string $Email
	 * @var string $UserName
	 * @var integer $Validated
	 * @var integer $Active
	 * @var integer $Admin
	 * @var string $Password
	 * @var string $LastLogin
	 * @var integer $LoginAttemptCount
	 * @var string $LastActivity
	 * @var string $LastVerificationSent
	 * @var string $Joined
	 * @var string $SessionID
	 * @var string $PasswordResetCode
	 * @var string $PasswordResetExpiration
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
		return 'User';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('Email','length','max'=>255),
			array('UserName','length','max'=>255),
			array('Password','length','max'=>160),
			array('SessionID','length','max'=>255),
			array('PasswordResetCode','length','max'=>128),
			array('Email, UserName, Password, Joined', 'required'),
			// TODO: Find out why bit fields don't register as integers
			//array('Validated, Active, Admin', 'numerical', 'integerOnly'=>true),
			array('LoginAttemptCount', 'numerical', 'integerOnly'=>true),
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
			'BuddyLists' => array(self::HAS_MANY, 'BuddyList', 'UserId'),
			'BuddyListsOn' => array(self::HAS_MANY, 'BuddyList', 'FriendId'),
			'IgnoreLists' => array(self::HAS_MANY, 'IgnoreList', 'UserId'),
			'IgnoreListsOn' => array(self::HAS_MANY, 'IgnoreList', 'AntiFriendId'),
			'Messages' => array(self::HAS_MANY, 'Message', 'RecipientUserId'),
			'MessagesSent' => array(self::HAS_MANY, 'Message', 'SenderUserId'),
			'Players' => array(self::HAS_MANY, 'Player', 'UserId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'UserId'=>'User',
			'Email'=>'Email',
			'UserName'=>'User Name',
			'Validated'=>'Validated',
			'Active'=>'Active',
			'Admin'=>'Admin',
			'Password'=>'Password',
			'LastLogin'=>'Last Login',
			'LoginAttemptCount'=>'Login Attempt Count',
			'LastActivity'=>'Last Activity',
			'LastVerificationSent'=>'Last Verification Sent',
			'Joined'=>'Joined',
			'SessionID'=>'Session',
			'PasswordResetCode'=>'Password Reset Code',
			'PasswordResetExpiration'=>'Password Reset Expiration',
		);
	}
	
	// Salt is 16 bytes long
	public static $SALT_LENGTH = 16;
	
	private function checkPassword($password) 
	{
		$currentPassword = base64_decode($this->Password);
		
		// Extract the salt
		$salt = substr($currentPassword, 0, User::$SALT_LENGTH);
		
		$enteredPassword = $this->hashPassword($password, $salt);
		
		Yii::log("Entered Pass Hash: " . $enteredPassword, "trace", "CosmoMongerPHP.models.User");
		Yii::log("Stored Pass Hash:  " . $this->Password, "trace", "CosmoMongerPHP.models.User");
		
		return ($this->Password === $enteredPassword);
	}
	
	private function hashPassword($password, $salt=NULL) 
	{
		if (!isset($salt))
		{
			// Generate a random 16 byte salt
			$salt = '';
			while (strlen($salt) < User::$SALT_LENGTH)
			{
				$salt .= dechex(mt_rand());
			}
			$salt = substr($salt, (0 - User::$SALT_LENGTH));
			Yii::log("Generated Salt: $salt", "trace", "CosmoMongerPHP.models.User");
		}
		
		// Convert the entered password string to UTF-16 / UCS-2LE encoding
		$utf16Password = mb_convert_encoding($password, "UCS-2LE");		
		$passwordString = $salt . $utf16Password;
		
		// Hash the salt + UTF-16 password
		$hashedPassword = hash("sha512", $passwordString, true);
		$hashedPasswordWithSalt = base64_encode($salt . $hashedPassword);

		return $hashedPasswordWithSalt;
	}
	
	/// <summary>
	/// Verifies that the specified password matches this users password.
	/// </summary>
	/// <param name="password">The password to check.</param>
	/// <returns>
	/// true if the specified password are valid; otherwise, false.
	/// </returns>
	public function validatePassword($password)
	{
		$validPassword = $this->checkPassword($password);
		if ($validPassword && $this->Validated)
		{
			$this->LoginAttemptCount = 0;
			$currentDate = new Date();
			$this->LastLogin = $currentDate->getDate();

			// Save database changes
			$this->save();
			
			return true;
		}
		else if ($this->Active)
		{
			$this->LoginAttemptCount += 1;
			//Yii::log("Invalid password for user {$this->UserName} try {$this->LoginAttemptCount}", "warning", "auth");
			
			// If login attempts reaches 3, we start adding a delay to the login process
			// This is to prevent brute forcing login passwords
			if ($this->LoginAttemptCount >= 3)
			{
				// Make the user disabled in the database right now, to prevent attacks 
				// from simply ending the connection if the login takes too long
				$this->Active = false;
				$this->save();

				// The delay increases for every login attempt
				// 3rd failed login 4 sec delay
				// 4th failed login 8 sec delay
				// 5th failed login 16 sec delay
				// ...
				// 10th failed login 512 sec delay
				sleep((int)pow(2, $this->LoginAttemptCount - 1));

				// Re-enable the user
				$this->Active = true;
			}

			// Save database changes
			if (!$this->save()) 
			{
				Yii::log("Failed to update record for user {$this->UserName}|". json_encode($this->getErrors()), "error", "CosmoMongerPHP.models.User");
			}
		}
		
		return false;
	}
	
	/// <summary>
	/// Gets the verification code sent to the users e-mail address to verify their e-mail.
	/// </summary>
	/// <value>The verification code.</value>
	public function getVerificationCode()
	{
		return "{$this->UserId}";
	}
		
	/// <summary>
	/// Sends the verification code to the users e-mail.
	/// </summary>
	/// <param name="baseVerificationCodeUrl">The base verification code URL. Example: http://localhost:54084/Account/VerifyEmail?username=jcsston&amp;verificationCode=</param>
	/// <exception cref="CosmoMongerException">Thrown if not enough time has passed since the last verification e-mail for this user.</exception>
	public function sendVerificationCode($baseVerificationCodeUrl)
	{
		$fiveMinutesAgo = new Date();
		$fiveMinutesAgo->subtractSeconds(5 * 60);

		// Check that it has been at least 5 minutes since the last verification e-mail
		$lastVerificationSent = new Date($this->LastVerificationSent);
		if ($lastVerificationSent->after($fiveMinutesAgo)) {
			throw new CosmoMongerException("Verification e-mails can only be sent every 5 minutes.");
		}

		// Build e-mail message
		$email = Yii::app()->email;
		$email->from = Yii::app()->params['adminEmail'];
		$email->to = $this->Email;
		$email->subject = "Email Verification for CosmoMonger";
		$email->message =
			"Welcome to CosmoMonger. To activate your account and verify your e-mail\n" .
			"address, please click on the following link:\n\n" .
			$baseVerificationCodeUrl . $this->getVerificationCode() . "\n\n" .
			"If you have received this mail in error, you do not need to take any\n" .
			"action to cancel the account. The account will not be activated, and\n" .
			"you will not receive any further emails.\n\n" .
			"If clicking the link above does not work, copy and paste the URL in a\n" .
			"new browser window instead.\n" .
			"\nThank you for playing CosmoMonger.";
			
		// Send e-mail
		$sent = $email->send();
		if (!$sent) 
		{
			$props = array(
				"UserId" => $this->UserId,
				"Email" => $this->Email,
				"Message" => $email->message,
			);
			Yii::log("Failed to send e-mail with verification code|". json_encode($props), "error", "CosmoMongerPHP.models.User");
		}
		else
		{
			// Update datetime of last verification e-mail sent
			$currentDate = new Date();
			$this->LastVerificationSent = $currentDate->getDate();
	
			// Save database changes
			$this->save();
		}
		return $sent;
	}
	
	/// <summary>
	/// Sends the forgotten password link which allows the user to click it and have their password reset
	/// to a new random password.
	/// </summary>
	/// <param name="baseResetPasswordUrl">The base reset password URL. Example: http://localhost:54084/Account/ResetPassword?username=jcsston&amp;resetPasswordCode=</param>
	public function sendForgotPasswordLink($baseResetPasswordUrl)
	{
		// Generate new password reset code
		$resetCode = '';
		while (strlen($resetCode) < 32)
		{
			$resetCode .= dechex(mt_rand());
		}
		
		// Build e-mail message
		$email = Yii::app()->email;
		$email->from = Yii::app()->params['adminEmail'];
		$email->to = $this->Email;
		$email->subject = "Reset Password Link for CosmoMonger";
		$email->message =
			"This email is a response to your request for a new password for your\n" .
			"CosmoMonger account. To confirm that you really want to change your\n" .
			"password, please click on the following link:\n\n" .
			$baseResetPasswordUrl . $resetCode . "\n\n" .
			"Clicking on this link will take you to a web page that will let you\n" .
			"reset your password. Once you've rest your password, you'll\n" .
			"be able to log in to your CosmoMonger account.\n" .
			"If you did not request a new password you can safely ignore this e-mail.\n" .
			"\nThank you for playing CosmoMonger.";
		
		// Send e-mail
		$sent = $email->send();
		if (!$sent) 
		{
			$props = array(
				"UserId" => $this->UserId,
				"Email" => $this->Email,
				"Message" => $email->message,
			);
			Yii::log("Failed to send password reset e-mail|". json_encode($props), "error", "CosmoMongerPHP.models.User");
		}
		else
		{
			$this->PasswordResetCode = $resetCode;
			$currentDate = new Date();
			// Expires in 5 hours
			$currentDate->addSeconds(5 * 60 * 60);
			$this->PasswordResetExpiration = $currentDate->getDate();

			// Save database changes
			$this->save();
		}
		
		return $sent;
	}
        
	/// <summary>
	/// Verifies that the verification code matches this users verification code. 
	/// Used to verify the users e-mail and approve the user account.
	/// </summary>
	/// <param name="verificationCode">The verification code to check.</param>
	/// <returns>
	/// true if the specified verification code is valid and the user has been approved; otherwise, false.
	/// </returns>
	public function verifyEmail($verificationCode)
	{
		if ($this->getVerificationCode() === $verificationCode)
		{
			// Verify the user
			$this->Validated = true;
			
			// Save database changes
			$this->save();
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/// <summary>
	/// Change the password for a user
	/// </summary>
	/// <param name="currentPassword">The current password for the specified user.</param>
	/// <param name="newPassword">The new password for the specified user.</param>
	/// <returns>
	/// true if the password was updated successfully; otherwise, false.
	/// </returns>
	public function changePassword($currentPassword, $newPassword)
	{
		if ($this->checkPassword($currentPassword))
		{
			// Update the users password
			$this->Password = $this->hashPassword($newPassword);
			
			// Save database changes
			return $this->save();
		}
		else
		{
			throw new ArgumentException("Current Password is incorrect", "currentPassword");
		}
	}
        
	/// <summary>
	/// Update the user's e-mail with the new e-mail address.
	/// </summary>
	/// <param name="email">The e-mail to set.</param>
	/// <exception cref="ArgumentException">Thrown if the e-mail is already taken by another user</exception>
	public function updateEmail($email)
	{
		// Check that the e-mail doesn't already exist on other user
		$matchingEmail = User::model()->findByAttributes(array('Email'=>$email));
		if ($matchingEmail && $matchingEmail->UserId !== $this->UserId)
		{
			throw new ArgumentException("Another user has the same e-mail", "email");
		}
		
		$props = array(
			"UserId" => $this->UserId,
			"OldEmail" => $this->Email,
			"NewEmail" => $email,
		);
		Yii::log("Changing user email|". json_encode($props), "trace", "CosmoMongerPHP.models.User");
		
		$this->Email = $email;
		
		// Save changes
		return $this->save();
	}
	
	/// <summary>
	/// Resets the password and returns the new password
	/// </summary>
	/// <param name="resetPasswordCode">The reset password code.</param>
	/// <returns>
	/// The new password
	/// </returns>
	public function resetPassword($resetPasswordCode)
	{
		// Check if the passed in code matches the one in the database and code has not expired
		if ($resetPasswordCode === $this->PasswordResetCode) 
		{
			$currentDate = new Date();
			if ($currentDate->before(new Date($this->PasswordResetExpiration)))
			{
				// Code is still good, Generate new password
				$newPassword = '';
				while (strlen($newPassword) < 8)
				{
					$newPassword .= dechex(mt_rand());
				}
				$this->Password = $this->hashPassword($newPassword);
	
				// Clear out code (it's been used)
				$this->PasswordResetCode = null;
				$this->PasswordResetExpiration = null;
		
				// Save database changes
				$this->save();
				
				return $newPassword;
			}
			else
			{
				throw new ArgumentException("Expired Password Reset Code", "resetPasswordCode");
			}
		}
		else
		{
			throw new ArgumentException("Invalid Password Reset Code", "resetPasswordCode");
		}
	}
		

	/// <summary>
	/// This creates a player in the database and returns a reference to the new player. 
	/// If the player already exists an ArgumentException will be thrown, referencing the name argument.
	/// </summary>
	/// <param name="name">The player name.</param>
	/// <param name="race">The race of the new Player.</param>
	/// <returns>The newly created Player</returns>
	public function createPlayer($name, $race)
	{
		$otherPlayerName = Player::model()->find('Name = ? AND UserId != ?', array($name, $this->UserId)); 
		if ($otherPlayerName)
		{
			throw new ArgumentException("Player by another user with the same name already exists", "name");
		}

		$props = array(
			"Name" => $name,
			"Race" => $race->Name
		);
		Yii::log("Creating player|". json_encode($props), "info", "CosmoMongerPHP.models.User");

		$player = new Player();
		$player->UserId = $this->UserId;
		$player->Name = $name;
		$player->RaceId = $race->RaceId;
		$player->Alive = true;
		$currentDate = new Date();
		$player->LastPlayed = $currentDate->getDate();

		// Assign the default starting location based on the race
		$startingSystem = $race->HomeSystem;
		if ($startingSystem == null)
		{
			$props = array(
				"RaceId" => $race->RaceId
			);
			Yii::log("Unable to load player starting system from database|". json_encode($props), "error", "CosmoMongerPHP.models.User");
			return null;
		}

		// Create a new ship for this player
		$player->createStartingShip($startingSystem);

		// Starting credits is 2000
		$player->Ship->Credits = 2000;

		$player->updateNetWorth();

		$player->save();
		$this->save();

		return $player;
	}

	/// <summary>
	/// Returns an list of BuddyList objects for this User
	/// </summary>
	/// <returns>Array of BuddyList objects</returns>
	public function getBuddyList()
	{
		return $this->BuddyLists(array('orderby' => 'b.Friend.UserName'));
	}
	
	/// <summary>
	/// Adds the passed in user to the users buddy list. 
	/// If the user is already in the list an ArgumentException is thrown.
	/// </summary>
	/// <param name="buddy">The buddy to add.</param>
	/// <exception cref="ArgumentException">Thrown when buddy is already in the buddy list</exception>
	public function addBuddy($buddy)
	{
		if ($buddy->UserId === $this->UserId)
		{
			throw new ArgumentException("Cannot add self to buddy list", "buddy");
		}

		$matchingBuddy = $this->BuddyLists(array("FriendId" => $buddy->UserId));
		if ($matchingBuddy)
		{
			throw new ArgumentException("User is already in the buddy list", "buddy");
		}

		$buddyEntry = new BuddyList();
		$buddyEntry->UserId = $this->UserId;
		$buddyEntry->FriendId = $buddy->UserId;

		$buddyEntry->save();
	}
		
	/*
	/// <summary>
	/// Removes the passed in user from the users buddy list. 
	/// If the user is not in the buddy list, an ArgumentException is thrown.
	/// </summary>
	/// <param name="buddy">The buddy to remove.</param>
	/// <exception cref="ArgumentException">Thrown when buddy not in the buddy list</exception>
	public virtual void RemoveBuddy(User buddy)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		BuddyList buddyToRemove = (from bl in this.BuddyLists where bl.FriendId == buddy.UserId select bl).SingleOrDefault();
		if (buddyToRemove == null)
		{
			throw new ArgumentException("User is not in the buddy list", "buddy");
		}

		db.BuddyLists.DeleteOnSubmit(buddyToRemove);
		db.SaveChanges();
	}

	/// <summary>
	/// Returns an list of IgnoreList objects for this User
	/// </summary>
	/// <returns>Array of IgnoreList objects</returns>
	public virtual IgnoreList[] GetIgnoreList()
	{
		return this.IgnoreLists.ToArray();
	}

	/// <summary>
	/// Adds the passed in user to the users ignore list. If the user is already in the list an ArgumentException is thrown.
	/// </summary>
	/// <param name="ignoreUser">The user to add to the ignore list.</param>
	/// <exception cref="ArgumentException">Thrown when the ignore user is already in the ignore list</exception>
	public virtual void AddIgnore(User ignoreUser)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		if (ignoreUser == this)
		{
			throw new ArgumentException("Cannot add self to ignore list", "ignoreUser");
		}

		bool matchingAntiFriend = (from il in this.IgnoreLists where il.AntiFriendId == ignoreUser.UserId select il).Any();
		if (matchingAntiFriend)
		{
			throw new ArgumentException("User is already in the ignore list", "ignoreUser");
		}

		IgnoreList ignoreEntry = new IgnoreList();
		ignoreEntry.User = this;
		ignoreEntry.AntiFriendId = ignoreUser.UserId;
		db.IgnoreLists.InsertOnSubmit(ignoreEntry);

		db.SaveChanges();
	}

	/// <summary>
	/// Removes the passed in user from the users ignore list. If the user is not in the ignore list, an ArgumentException is thrown.
	/// </summary>
	/// <param name="ignoreUser">The ignore user.</param>
	/// <exception cref="ArgumentException">Thrown when the ignore user is not in the ignore list</exception>
	public virtual void RemoveIgnore(User ignoreUser)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		IgnoreList antiFriendToRemove = (from il in this.IgnoreLists where il.AntiFriendId == ignoreUser.UserId select il).SingleOrDefault();
		if (antiFriendToRemove == null)
		{
			throw new ArgumentException("User is not in the ignore list", "ignoreUser");
		}

		db.IgnoreLists.DeleteOnSubmit(antiFriendToRemove);
		db.SaveChanges();
	}
*/

	/// <summary>
	/// This returns any unread messages for the User.
	/// If no unread messages exist an empty array is returned.
	/// </summary>
	/// <remarks>This function always fetches fresh values from database and is not cached.</remarks>
	/// <returns>Array of Message objects</returns>
	public function getUnreadMessages()
	{
		// Ensure that the messages are freshly loaded from the database by querying database directly
		return Message::model()->findAllByAttributes(array('RecipientUserId' => $this->UserId, 'Received' => false, 'VisibleToRecipient' => true));
	}
	
	/// <summary>
	/// Send a message to the toUser message queue
	/// </summary>
	/// <param name="toUser">The user to send the message to.</param>
	/// <param name="subject">The message subject.</param>
	/// <param name="message">The message to send.</param>
	public function sendMessage($toUser, $subject, $message)
	{
		// Check if this user is on the to user's ignore list
		$presentOnIgnoreList = $this->IgnoreListsOn(array('condition' => 'UserId = :userId', 'params' => array(':userId' => $toUser->UserId)));
		if ($presentOnIgnoreList)
		{
			// Don't send the message
			return false;
		}

		// Build the message
		$msg = new Message();
		$msg->RecipientUserId = $toUser->UserId;
		$msg->SenderUserId = $this->UserId;
		$msg->Subject = $subject;
		$msg->Content = $message;
		
		$currentDate = new Date();
		$msg->Time = $currentDate->getDate();
		
		$msg->VisibleToRecipient = true;
		$msg->VisibleToSender = true;

		// Add the message to the database
		return $msg->save();
	}

	/// <summary>
	/// Bans the user by setting the Active field to false.
	/// </summary>
	public function ban()
	{
		$this->Active = false;

		// Save changes to database
		return $this->save();
	}

	/// <summary>
	/// Unbans the user by setting the Active field to true.
	/// </summary>
	public function unban()
	{
		$this->Active = true;

		// Save changes to database
		return $this->save();
	}


	/// <summary>
	/// Gets a message recieved or sent by this user.
	/// </summary>
	/// <param name="messageId">The message id.</param>
	/// <returns>A Message object if found. Null if no message was found.</returns>
	public function getMessage($messageId)
	{
		$message = Message::model()->findAllByPk($messageId, 'RecipientUserId = :userId1 OR SenderUserId = :userId2', array(':userId1' => $this->UserId, ':userId2' => $this->UserId));
		return $message;
	}

	/// <summary>
	/// Deletes this message from the database.
	/// </summary>
	/// <param name="messageId">The message id to delete.</param>
	/// <exception cref="ArgumentException">Thrown if the message id is not found.</exception>
	public function deleteMessage($messageId)
	{
		$message = Message::model()->findAllByPk($messageId);
		if ($message != null)
		{
			if ($message->RecipientUserId === $this->UserId)
			{
				$message->VisibleToRecipient = false;
			}
			else if ($message->SenderUserId === $this->UserId)
			{
				$message->VisibleToSender = false;
			}
			else
			{
				throw new ArgumentException("Message not visible to user", "messageId");
			}
			
			return $message->save();
		}
		else
		{
			throw new ArgumentException("Invalid Message Id", "messageId");
		}
	}

	/// <summary>
	/// Gets the visible messages for this user.
	/// </summary>
	/// <returns>IEnumerable of Messages</returns>
	public function getMessages()
	{
		return $this->Messages(array('condition' => 'VisibleToRecipient = true', 'order' => 'Time DESC'));
	}
	
	/// <summary>
	/// Gets the visible messages sent by this user.
	/// </summary>
	/// <returns>IEnumerable of Messages</returns>
	public function getMessagesSent()
	{
		return $this->MessagesSent(array('condition' => 'VisibleToSender = true', 'order' => 'Time DESC'));
	}

        
	/// <summary>
	/// Creates a new user.
	/// </summary>
	/// <param name="username">The username of the new user.</param>
	/// <param name="password">The password for the new user.</param>
	/// <param name="email">The email address for the new user.</param>
	/// <exception cref="ArgumentException">
	/// Throws an ArgumentException for the username param if there is another user 
	/// already in the system with the same username.
	/// Throws an ArgumentException for the email param if there is another user
	/// already in the system with the same e-mail.
	/// </exception>
	/// <returns>A new CosmoMongerMembershipUser object for the newly created user.</returns>
	public static function createUser($username, $password, $email)
	{
		// Check for an existing user
		$matchingUsername = User::model()->findByAttributes(array('UserName'=>$username));
		if ($matchingUsername)
		{
			throw new ArgumentException("Duplicate username", "username");
		}

		$matchingEmail = User::model()->findByAttributes(array('Email'=>$email));
		if ($matchingEmail)
		{
			throw new ArgumentException("Duplicate email", "email");
		}

		// Create the new user
		$user = new User;
		$user->UserName = $username;
		$user->Email = $email;
		$user->Password = $user->hashPassword($password);
		$user->Active = true;
		$user->Validated = false;
		$currentDate = new Date();
		$user->Joined = $currentDate->getDate();

		// Save database changes
		return $user->save();
	}
}
