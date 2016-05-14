<?php

/// <summary>
/// This is the central control class for the CosmoMonger game.
/// </summary>
class GameManager
{
	/// <summary>
	/// Contains a reference to the current user.
	/// </summary>
	var $currentUser;

	/// <summary>
	/// Initializes a new instance of the <see cref="GameManager"/> class.
	/// </summary>
	/// <param name="username">The username of the currently logged in user.</param>
	public function __construct($username)
	{
		$this->currentUser = User::model()->findByAttributes(array('UserName'=>$username));
		if (!$this->currentUser)
		{
			throw new ArgumentException("Invalid username in GameManager", "username");
		}
	}
	

	/// <summary>
	/// Gets the current user.
	/// </summary>
	public function getCurrentUser()
	{
		return $this->currentUser;
	}

	/// <summary>
	/// Gets the current player
	/// </summary>
	/// <value>The current player.</value>
	public function getCurrentPlayer()
	{
		// with hint fetches the Ship info in the same query, since just about every player action will invole
		// their ship, fetching this now will reduce the number of needed queries
		$alivePlayers = $this->currentUser->Players(array('condition'=>'Alive = 1', 'with'=>'Ship'));
		assert (count($alivePlayers) < 2);
		return $alivePlayers[0];
	}
	
/*
	/// <summary>
	/// Returns the user with the passed in id. Returns null if the user does not exist.
	/// </summary>
	/// <param name="userId">The user id.</param>
	/// <returns>User object, null if the user does not exist.</returns>
	public virtual User GetUser(int userId)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		return (from u in db.Users where u.UserId == userId select u).SingleOrDefault();
	}

	/// <summary>
	/// Fetches the top records of players based on the recordType argument. 
	/// If the recordType is invalid an ArgumentException is thrown.
	/// The returned value pairs have the record value nicely formated with the Player as the key.
	/// </summary>
	/// <param name="recordType">Type of the record.</param>
	/// <param name="limit">The number of top players to return</param>
	/// <returns>Array of PlayerTopRecord objects</returns>
	public virtual PlayerTopRecord[] GetTopPlayers(Player.RecordType recordType, int limit)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		
		// Fetch the top player records
		switch (recordType)
		{
			case Player.RecordType.NetWorth:
				return (from p in db.Players 
						orderby p.NetWorth descending, 
						p.Name 
						select p)
						.Take(limit)
						.Select(p => new PlayerTopRecord(p, recordType, p.NetWorth))
						.ToArray();

			case Player.RecordType.ShipsDestroyed:
				return (from p in db.Players 
						orderby p.ShipsDestroyed descending, 
						p.Name 
						select p)
						.Take(limit)
						.Select(p => new PlayerTopRecord(p, recordType, p.ShipsDestroyed))
						.ToArray();

			case Player.RecordType.ForcedSurrenders:
				return (from p in db.Players 
						orderby p.ForcedSurrenders descending, 
						p.Name 
						select p)
						.Take(limit)
						.Select(p => new PlayerTopRecord(p, recordType, p.ForcedSurrenders))
						.ToArray();

			case Player.RecordType.ForcedFlees:
				return (from p in db.Players 
						orderby p.ForcedFlees descending, 
						p.Name 
						select p)
						.Take(limit)
						.Select(p => new PlayerTopRecord(p, recordType, p.ForcedFlees))
						.ToArray();

			case Player.RecordType.CargoLootedWorth:
				return (from p in db.Players 
						orderby p.CargoLootedWorth descending, 
						p.Name 
						select p)
						.Take(limit)
						.Select(p => new PlayerTopRecord(p, recordType, p.CargoLootedWorth))
						.ToArray();

			case Player.RecordType.ShipsLost:
				return (from p in db.Players 
						orderby p.ShipsLost descending, 
						p.Name 
						select p)
						.Take(limit)
						.Select(p => new PlayerTopRecord(p, recordType, p.ShipsLost))
						.ToArray();

			case Player.RecordType.SurrenderCount:
				return (from p in db.Players 
						orderby p.SurrenderCount descending, 
						p.Name 
						select p)
						.Take(limit)
						.Select(p => new PlayerTopRecord(p, recordType, p.SurrenderCount))
						.ToArray();

			case Player.RecordType.FleeCount:
				return (from p in db.Players 
						orderby p.FleeCount descending, 
						p.Name 
						select p)
						.Take(limit)
						.Select(p => new PlayerTopRecord(p, recordType, p.FleeCount))
						.ToArray();

			case Player.RecordType.CargoLostWorth:
				return (from p in db.Players 
						orderby p.CargoLostWorth descending, 
						p.Name 
						select p)
						.Take(limit)
						.Select(p => new PlayerTopRecord(p, recordType, p.CargoLostWorth))
						.ToArray();

			case Player.RecordType.DistanceTraveled:
				return (from p in db.Players
						orderby p.DistanceTraveled descending,
						p.Name
						select p)
						.Take(limit)
						.Select(p => new PlayerTopRecord(p, recordType, p.DistanceTraveled))
						.ToArray();

			case Player.RecordType.GoodsTraded:
				return (from p in db.Players
						orderby p.GoodsTraded descending,
						p.Name
						select p)
						.Take(limit)
						.Select(p => new PlayerTopRecord(p, recordType, p.GoodsTraded))
						.ToArray();

			default:
				throw new ArgumentException("Unhandled recordType in GetTopPlayers", "recordType");
		}
	}

	/// <summary>
	/// Fetches the Player object for the passed in player id. 
	/// If the player doesn't exist, null is returned.
	/// </summary>
	/// <param name="playerId">The player id.</param>
	/// <returns>Player object, null if the player does not exist.</returns>
	public virtual Player GetPlayer(int playerId)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		return (from p in db.Players where p.PlayerId == playerId select p).SingleOrDefault();
	}

	/// <summary>
	/// Return an array of all systems in the galaxy.
	/// </summary>
	/// <returns>Array of CosmoSystem objects</returns>
	public virtual CosmoSystem[] GetSystems()
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		return (from s in db.CosmoSystems select s).ToArray();
	}

	/// <summary>
	/// Gets all the races available.
	/// </summary>
	/// <returns>Array of Race objects</returns>
	public virtual Race[] GetRaces()
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		return (from r in db.Races select r).ToArray();
	}

	/// <summary>
	/// Gets the race by id number.
	/// </summary>
	/// <param name="raceId">The race id of the Race object to get.</param>
	/// <returns>Race oject, null if the race does not exist</returns>
	public virtual Race GetRace(int raceId)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		return (from r in db.Races where r.RaceId == raceId select r).SingleOrDefault();
	}

	/// <summary>
	/// Gets the system by id number.
	/// </summary>
	/// <param name="systemId">The system id of the System object to get.</param>
	/// <returns>CosmoSystem object, null if the system does not exist</returns>
	public virtual CosmoSystem GetSystem(int systemId)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		return (from s in db.CosmoSystems where s.SystemId == systemId select s).SingleOrDefault();
	}

	/// <summary>
	/// Gets the size of the galaxy.
	/// </summary>
	/// <returns>An int that gives the x/y size of the galaxy</returns>
	public virtual int GetGalaxySize()
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		return Math.Max(db.CosmoSystems.Max(x => x.PositionX), db.CosmoSystems.Max(x => x.PositionY));
	}

	/// <summary>
	/// Gets the price table for the whole galaxy
	/// </summary>
	/// <param name="orderByDistance">if set to <c>true</c> order systems by distance from current players ship.</param>
	/// <returns>A List of PriceTablEntry objects</returns>
	public virtual PriceTableEntry [] GetPriceTable(bool orderByDistance)
	{
		List<PriceTableEntry> priceTable = new List<PriceTableEntry>();

		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		CosmoSystem [] systems = db.CosmoSystems.ToArray();

		// Check if we need to order the systems by distance, closest first
		if (orderByDistance)
		{
			systems = systems.OrderBy(s => this.CurrentPlayer.Ship.GetSystemDistance(s)).ToArray();
		}

		foreach (CosmoSystem system in systems)
		{
			PriceTableEntry prices = new PriceTableEntry();
			prices.SystemName = system.Name;
			foreach (Good good in db.Goods)
			{
				prices.GoodPrices[good.Name] = (from g in system.SystemGoods
												where g.Good == good
												select g.Price).SingleOrDefault();
			}

			priceTable.Add(prices);
		}

		return priceTable.ToArray();
	}

	/// <summary>
	/// Gets an array of all the possible good types.
	/// </summary>
	/// <returns>Array of good types.</returns>
	public virtual Good[] GetGoods()
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		return (from g in db.Goods select g).ToArray();
	}

	/// <summary>
	/// Finds a player by their username or active player name.
	/// </summary>
	/// <param name="name">The name to search for.</param>
	/// <returns>The IEnumerable of Player objects for the matching user/players.</returns>
	public virtual IEnumerable<Player> FindPlayer(string name)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

		// We search active player names and their usernames
		return (from p in db.Players
				where p.Alive
				&& (p.Name.Contains(name) || p.User.UserName.Contains(name))
				select p).AsEnumerable();
	}

	/// <summary>
	/// Finds a user by username or past player name
	/// </summary>
	/// <param name="name">The name to search for.</param>
	/// <returns>The IEnumerable of User objects for the matching users.</returns>
	public virtual IEnumerable<User> FindUser(string name)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();

		// We search users and their player names
		return (from u in db.Users
				where u.UserName.Contains(name)
				|| u.Email.Contains(name)
				select u)
				.Union(
			   (from p in db.Players
				where p.Name.Contains(name)
				select p.User)).AsEnumerable();
	}

	/// <summary>
	/// Gets the ship by it's id.
	/// </summary>
	/// <param name="shipId">The id of the ship to find.</param>
	/// <returns>The matching Ship object or null if not found.</returns>
	public virtual Ship GetShip(int shipId)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		return (from s in db.Ships
				where s.ShipId == shipId
				select s).SingleOrDefault();
	}

	/// <summary>
	/// Gets a combat record by it's id.
	/// </summary>
	/// <param name="combatId">The id of the combat record to find.</param>
	/// <returns>The matching Combat record object or null if not found.</returns>
	public virtual Combat GetCombat(int combatId)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		return (from c in db.Combats
				where c.CombatId == combatId
				select c).SingleOrDefault();
	}

	/// <summary>
	/// Gets the records for a player
	/// </summary>
	/// <param name="playerId">The player id.</param>
	/// <returns>An array of PlayerRecords for the requested player</returns>
	public virtual PlayerRecord[] GetPlayerRecords(int playerId)
	{
		CosmoMongerDbDataContext db = CosmoManager.GetDbContext();
		return (from r in db.PlayerRecords
				where r.PlayerId == playerId
				orderby r.RecordTime
				select r).ToArray();
	}
*/
}

