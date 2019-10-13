<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function players()
    {
        return $this->hasMany('App\Player', 'player_id', 'player_id');
    }

    /**
     * This creates a player in the database and returns a reference to the new player.
     * If the player already exists an InvalidArgumentException will be thrown.
     * @param name The player name.
     * @param race The race of the new Player.
     * @return \App\Player The newly created Player
     */
    public function CreatePlayer(string $name, \App\Race $race): \App\Player
    {
        $otherPlayerName = \App\Player::where([
            ['name', $name],
            ['user_id', '!=', $this->user_id]
        ])->get()->isNotEmpty();

        if ($otherPlayerName)
        {
            throw new \InvalidArgumentException("Player by another user with the same name already exists");
        }

        $props = [
            "Name" => $name,
            "Race" => $race->name
        ];
        Log::debug("Creating player in User.CreatePlayer", $props);

        $player = new \App\Player();
        $player->user_id = $this->getKey();
        $player->name = $name;
        $player->race_id = $race->getKey();
        $player->alive = true;
        $player->last_played = new \DateTime;

        // Assign the default starting location based on the race
        $startingSystem = $race->homeSystem;
        if ($startingSystem == null)
        {
            Log::critical("Unable to load player starting system from database");
            return null;
        }

        // Create a new ship for this player
        $ship = $player->CreateStartingShip($startingSystem);

        // Starting credits is 2000
        $ship->credits = 2000;
        $ship->save();

        $player->UpdateNetWorth();

        $player->save();

        return $player;
    }

    /*
    /// <summary>
    /// Returns an list of BuddyList objects for this User
    /// </summary>
    /// <returns>Array of BuddyList objects</returns>
    public virtual BuddyList[] GetBuddyList()
    {
        return (from b in BuddyLists
                orderby b.Friend.UserName
                select b).ToArray();
    }

    /// <summary>
    /// Adds the passed in user to the users buddy list.
    /// If the user is already in the list an ArgumentException is thrown.
    /// </summary>
    /// <param name="buddy">The buddy to add.</param>
    /// <exception cref="ArgumentException">Thrown when buddy is already in the buddy list</exception>
    public virtual void AddBuddy(User buddy)
    {
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
        if (buddy == this)
        {
            throw new ArgumentException("Cannot add self to buddy list", "buddy");
        }

        bool matchingBuddy = (from bl in this.BuddyLists where bl.FriendId == buddy.UserId select bl).Any();
        if (matchingBuddy)
        {
            throw new ArgumentException("User is already in the buddy list", "buddy");
        }

        BuddyList buddyEntry = new BuddyList();
        buddyEntry.User = this;
        buddyEntry.FriendId = buddy.UserId;
        db.BuddyLists.InsertOnSubmit(buddyEntry);

        db.SaveChanges();
    }

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

    /// <summary>
    /// This returns any unread messages for the User.
    /// If no unread messages exist an empty array is returned.
    /// </summary>
    /// <remarks>This function always fetches fresh values from database and is not cached.</remarks>
    /// <returns>Array of Message objects</returns>
    public virtual IEnumerable<Message> GetUnreadMessages()
    {
        // Ensure that the messages are freshly loaded from the database by querying database directly
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
        return (from m in db.Messages
                where m.RecipientUserId == this.UserId
                && !m.Received
                && m.VisibleToRecipient
                select m).AsEnumerable();
    }

    /// <summary>
    /// Send a message to the toUser message queue
    /// </summary>
    /// <param name="toUser">The user to send the message to.</param>
    /// <param name="subject">The message subject.</param>
    /// <param name="message">The message to send.</param>
    public virtual void SendMessage(User toUser, string subject, string message)
    {
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        // Check if this user is on the to user's ignore list
        bool presentOnIgnoreList = (from ilo in this.IgnoreListsOn
                                    where ilo.User == toUser
                                    select ilo).Any();
        if (presentOnIgnoreList)
        {
            // Don't send the message
            return;
        }

        // Build the message
        Message msg = new Message();
        msg.RecipientUser = toUser;
        msg.SenderUser = this;
        msg.Subject = subject;
        msg.Content = message;
        msg.Time = DateTime.UtcNow;
        msg.VisibleToRecipient = true;
        msg.VisibleToSender = true;

        // Add the message to the database
        db.Messages.InsertOnSubmit(msg);

        // Save changes to database
        db.SaveChanges();
    }

    /// <summary>
    /// Bans the user by setting the Active field to false.
    /// </summary>
    public virtual void Ban()
    {
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        this.Active = false;

        // Save changes to database
        db.SaveChanges();
    }

    /// <summary>
    /// Unbans the user by setting the Active field to true.
    /// </summary>
    public virtual void Unban()
    {
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        this.Active = true;

        // Save changes to database
        db.SaveChanges();
    }

    /// <summary>
    /// Gets a message recieved or sent by this user.
    /// </summary>
    /// <param name="messageId">The message id.</param>
    /// <returns>A Message object if found. Null if no message was found.</returns>
    public virtual Message GetMessage(int messageId)
    {
        Message message = (from m in this.Messages
                            where m.MessageId == messageId
                            && m.VisibleToRecipient
                            select m).SingleOrDefault();

        // If the message was not found, look in sent messages
        if (message == null)
        {
            message = (from m in this.MessagesSent
                        where m.MessageId == messageId
                        && m.VisibleToSender
                        select m).SingleOrDefault();
        }

        return message;
    }

    /// <summary>
    /// Deletes this message from the database.
    /// </summary>
    /// <param name="messageId">The message id to delete.</param>
    /// <exception cref="ArgumentException">Thrown if the message id is not found.</exception>
    public virtual void DeleteMessage(int messageId)
    {
        CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

        Message message = (from m in this.Messages
                            where m.MessageId == messageId
                            select m).SingleOrDefault();
        if (message != null)
        {
            message.VisibleToRecipient = false;
        }
        else
        {
            // If the message was not found, look in sent messages
            message = (from m in this.MessagesSent
                        where m.MessageId == messageId
                        select m).SingleOrDefault();
            if (message == null)
            {
                throw new ArgumentException("Invalid Message Id", "messageId");
            }

            message.VisibleToSender = false;
        }

        db.SaveChanges();
    }

    /// <summary>
    /// Gets the visible messages for this user.
    /// </summary>
    /// <returns>IEnumerable of Messages</returns>
    public virtual IEnumerable<Message> GetMessages()
    {
        return (from m in this.Messages
                where m.VisibleToRecipient
                orderby m.Time descending
                select m).AsEnumerable();
    }

    /// <summary>
    /// Gets the visible messages sent by this user.
    /// </summary>
    /// <returns>IEnumerable of Messages</returns>
    public virtual IEnumerable<Message> GetMessagesSent()
    {
        return (from m in this.MessagesSent
                where m.VisibleToSender
                orderby m.Time descending
                select m).AsEnumerable();
    }
    */
}
