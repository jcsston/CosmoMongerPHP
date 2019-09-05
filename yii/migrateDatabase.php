<?php
// Note: You may have to manually drop all the FKs in the database

$sourceDb = mssql_connect('win2k3.stone.local', 'CosmoUser', 'CosmoMonger', 'CosmoMonger');
mssql_select_db('CosmoMonger', $sourceDb);
$targetDb = new PDO('mysql:host=warpcore;dbname=CosmoMonger', 'CosmoMonger', '6phc6FA4yc6VHnVm');

$tableList = array(
  'BaseShip'
, 'BuddyList'
, 'Combat'
, 'CombatGood'
, 'Good'
, 'IgnoreList'
, 'JumpDrive'
, 'Message'
, 'Npc'
, 'NpcName'
, 'Player'
, 'PlayerRecord'
, 'Race'
, 'Shield'
, 'Ship'
, 'ShipGood'
, 'System'
, 'SystemGood'
, 'SystemJumpDriveUpgrade'
, 'SystemShieldUpgrade'
, 'SystemShip'
, 'SystemWeaponUpgrade'
, 'User'
, 'Weapon'
);

$dropFkSql = "
ALTER TABLE `BaseShip` DROP FOREIGN KEY `FK_JumpDrive_BaseShip`;
ALTER TABLE `BaseShip` DROP FOREIGN KEY `FK_Shield_BaseShip`;
ALTER TABLE `BaseShip` DROP FOREIGN KEY `FK_Weapon_BaseShip`;
ALTER TABLE `BuddyList` DROP FOREIGN KEY `FK_User_BuddyList_1`;
ALTER TABLE `BuddyList` DROP FOREIGN KEY `FK_User_BuddyList_2`;
ALTER TABLE `Combat` DROP FOREIGN KEY `FK_Ship_Combat_AttackerShipId`;
ALTER TABLE `Combat` DROP FOREIGN KEY `FK_Ship_Combat_DefenderShipId`;
ALTER TABLE `CombatGood` DROP FOREIGN KEY `FK_Combat_CombatGood_CombatId`;
ALTER TABLE `CombatGood` DROP FOREIGN KEY `FK_Good_CombatGood_GoodId`;
ALTER TABLE `IgnoreList` DROP FOREIGN KEY `FK_User_IgnoreList_1`;
ALTER TABLE `IgnoreList` DROP FOREIGN KEY `FK_User_IgnoreList_2`;
ALTER TABLE `Message` DROP FOREIGN KEY `FK_User_Message_1`;
ALTER TABLE `Message` DROP FOREIGN KEY `FK_User_Message_2`;
ALTER TABLE `Npc` DROP FOREIGN KEY `FK_Ship_Npc_LastShipAttackedId`;
ALTER TABLE `Npc` DROP FOREIGN KEY `FK_System_Npc_LastVisitedSystemId`;
ALTER TABLE `Npc` DROP FOREIGN KEY `FK_Race_Npc`;
ALTER TABLE `Npc` DROP FOREIGN KEY `FK_Ship_Npc`;
ALTER TABLE `Player` DROP FOREIGN KEY `FK_Race_Player`;
ALTER TABLE `Player` DROP FOREIGN KEY `FK_Ship_Player`;
ALTER TABLE `Player` DROP FOREIGN KEY `FK_User_Player`;
ALTER TABLE `PlayerRecord` DROP FOREIGN KEY `FK_Player_PlayerRecord_PlayerId`;
ALTER TABLE `Race` DROP FOREIGN KEY `FK_System_Race_HomeSystemId`;
ALTER TABLE `Race` DROP FOREIGN KEY `FK_RaceId_Race`;
ALTER TABLE `Race` DROP FOREIGN KEY `FK_Race_Race_RacialEnemyId`;
ALTER TABLE `Race` DROP FOREIGN KEY `FK_Race_Race_RacialPreferenceId`;
ALTER TABLE `Ship` DROP FOREIGN KEY `FK_BaseShip_Ship`;
ALTER TABLE `Ship` DROP FOREIGN KEY `FK_JumpDrive_Ship`;
ALTER TABLE `Ship` DROP FOREIGN KEY `FK_Shield_Ship`;
ALTER TABLE `Ship` DROP FOREIGN KEY `FK_System_Ship`;
ALTER TABLE `Ship` DROP FOREIGN KEY `FK_Weapon_Ship`;
ALTER TABLE `ShipGood` DROP FOREIGN KEY `FK_Good_ShipGood`;
ALTER TABLE `ShipGood` DROP FOREIGN KEY `FK_Ship_ShipGood`;
ALTER TABLE `SystemGood` DROP FOREIGN KEY `FK_Good_SystemGood`;
ALTER TABLE `SystemGood` DROP FOREIGN KEY `FK_System_SystemGood`;
ALTER TABLE `SystemJumpDriveUpgrade` DROP FOREIGN KEY `FK_JumpDrive_SystemJumpDriveUpgrade`;
ALTER TABLE `SystemJumpDriveUpgrade` DROP FOREIGN KEY `FK_System_SystemJumpDriveUpgrade`;
ALTER TABLE `SystemShieldUpgrade` DROP FOREIGN KEY `FK_Shield_SystemShieldUpgrade`;
ALTER TABLE `SystemShieldUpgrade` DROP FOREIGN KEY `FK_System_SystemShieldUpgrade`;
ALTER TABLE `SystemShip` DROP FOREIGN KEY `FK_BaseShip_SystemShip`;
ALTER TABLE `SystemShip` DROP FOREIGN KEY `FK_System_SystemShip`;
ALTER TABLE `SystemWeaponUpgrade` DROP FOREIGN KEY `FK_System_SystemWeaponUpgrade`;
ALTER TABLE `SystemWeaponUpgrade` DROP FOREIGN KEY `FK_Weapon_SystemWeaponUpgrade`;
";

$createFkSql = "
ALTER TABLE `BaseShip` ADD CONSTRAINT `FK_JumpDrive_BaseShip` FOREIGN KEY (`InitialJumpDriveId`) REFERENCES `JumpDrive`(`JumpDriveId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `BaseShip` ADD CONSTRAINT `FK_Shield_BaseShip` FOREIGN KEY (`InitialShieldId`) REFERENCES `Shield`(`ShieldId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `BaseShip` ADD CONSTRAINT `FK_Weapon_BaseShip` FOREIGN KEY (`InitialWeaponId`) REFERENCES `Weapon`(`WeaponId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `BuddyList` ADD CONSTRAINT `FK_User_BuddyList_1` FOREIGN KEY (`UserId`) REFERENCES `User`(`UserId`) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `BuddyList` ADD CONSTRAINT `FK_User_BuddyList_2` FOREIGN KEY (`FriendId`) REFERENCES `User`(`UserId`) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `Combat` ADD CONSTRAINT `FK_Ship_Combat_AttackerShipId` FOREIGN KEY (`AttackerShipId`) REFERENCES `Ship`(`ShipId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Combat` ADD CONSTRAINT `FK_Ship_Combat_DefenderShipId` FOREIGN KEY (`DefenderShipId`) REFERENCES `Ship`(`ShipId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `CombatGood` ADD CONSTRAINT `FK_Combat_CombatGood_CombatId` FOREIGN KEY (`CombatId`) REFERENCES `Combat`(`CombatId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `CombatGood` ADD CONSTRAINT `FK_Good_CombatGood_GoodId` FOREIGN KEY (`GoodId`) REFERENCES `Good`(`GoodId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `IgnoreList` ADD CONSTRAINT `FK_User_IgnoreList_1` FOREIGN KEY (`UserId`) REFERENCES `User`(`UserId`) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `IgnoreList` ADD CONSTRAINT `FK_User_IgnoreList_2` FOREIGN KEY (`AntiFriendId`) REFERENCES `User`(`UserId`) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `Message` ADD CONSTRAINT `FK_User_Message_1` FOREIGN KEY (`SenderUserId`) REFERENCES `User`(`UserId`) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `Message` ADD CONSTRAINT `FK_User_Message_2` FOREIGN KEY (`RecipientUserId`) REFERENCES `User`(`UserId`) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `Npc` ADD CONSTRAINT `FK_Ship_Npc_LastShipAttackedId` FOREIGN KEY (`LastAttackedShipId`) REFERENCES `Ship`(`ShipId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Npc` ADD CONSTRAINT `FK_System_Npc_LastVisitedSystemId` FOREIGN KEY (`LastVisitedSystemId`) REFERENCES `System`(`SystemId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Npc` ADD CONSTRAINT `FK_Race_Npc` FOREIGN KEY (`RaceId`) REFERENCES `Race`(`RaceId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Npc` ADD CONSTRAINT `FK_Ship_Npc` FOREIGN KEY (`ShipId`) REFERENCES `Ship`(`ShipId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Player` ADD CONSTRAINT `FK_Race_Player` FOREIGN KEY (`RaceId`) REFERENCES `Race`(`RaceId`) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `Player` ADD CONSTRAINT `FK_Ship_Player` FOREIGN KEY (`ShipId`) REFERENCES `Ship`(`ShipId`) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `Player` ADD CONSTRAINT `FK_User_Player` FOREIGN KEY (`UserId`) REFERENCES `User`(`UserId`) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `PlayerRecord` ADD CONSTRAINT `FK_Player_PlayerRecord_PlayerId` FOREIGN KEY (`PlayerId`) REFERENCES `Player`(`PlayerId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Race` ADD CONSTRAINT `FK_System_Race_HomeSystemId` FOREIGN KEY (`HomeSystemId`) REFERENCES `System`(`SystemId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Race` ADD CONSTRAINT `FK_RaceId_Race` FOREIGN KEY (`RaceId`) REFERENCES `Race`(`RaceId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Race` ADD CONSTRAINT `FK_Race_Race_RacialEnemyId` FOREIGN KEY (`RacialEnemyId`) REFERENCES `Race`(`RaceId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Race` ADD CONSTRAINT `FK_Race_Race_RacialPreferenceId` FOREIGN KEY (`RacialPreferenceId`) REFERENCES `Race`(`RaceId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Ship` ADD CONSTRAINT `FK_BaseShip_Ship` FOREIGN KEY (`BaseShipId`) REFERENCES `BaseShip`(`BaseShipId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Ship` ADD CONSTRAINT `FK_JumpDrive_Ship` FOREIGN KEY (`JumpDriveId`) REFERENCES `JumpDrive`(`JumpDriveId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Ship` ADD CONSTRAINT `FK_Shield_Ship` FOREIGN KEY (`ShieldId`) REFERENCES `Shield`(`ShieldId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Ship` ADD CONSTRAINT `FK_System_Ship` FOREIGN KEY (`SystemId`) REFERENCES `System`(`SystemId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `Ship` ADD CONSTRAINT `FK_Weapon_Ship` FOREIGN KEY (`WeaponId`) REFERENCES `Weapon`(`WeaponId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `ShipGood` ADD CONSTRAINT `FK_Good_ShipGood` FOREIGN KEY (`GoodId`) REFERENCES `Good`(`GoodId`) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `ShipGood` ADD CONSTRAINT `FK_Ship_ShipGood` FOREIGN KEY (`ShipId`) REFERENCES `Ship`(`ShipId`) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `SystemGood` ADD CONSTRAINT `FK_Good_SystemGood` FOREIGN KEY (`GoodId`) REFERENCES `Good`(`GoodId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `SystemGood` ADD CONSTRAINT `FK_System_SystemGood` FOREIGN KEY (`SystemId`) REFERENCES `System`(`SystemId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `SystemJumpDriveUpgrade` ADD CONSTRAINT `FK_JumpDrive_SystemJumpDriveUpgrade` FOREIGN KEY (`JumpDriveId`) REFERENCES `JumpDrive`(`JumpDriveId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `SystemJumpDriveUpgrade` ADD CONSTRAINT `FK_System_SystemJumpDriveUpgrade` FOREIGN KEY (`SystemId`) REFERENCES `System`(`SystemId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `SystemShieldUpgrade` ADD CONSTRAINT `FK_Shield_SystemShieldUpgrade` FOREIGN KEY (`ShieldId`) REFERENCES `Shield`(`ShieldId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `SystemShieldUpgrade` ADD CONSTRAINT `FK_System_SystemShieldUpgrade` FOREIGN KEY (`SystemId`) REFERENCES `System`(`SystemId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `SystemShip` ADD CONSTRAINT `FK_BaseShip_SystemShip` FOREIGN KEY (`BaseShipId`) REFERENCES `BaseShip`(`BaseShipId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `SystemShip` ADD CONSTRAINT `FK_System_SystemShip` FOREIGN KEY (`SystemId`) REFERENCES `System`(`SystemId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `SystemWeaponUpgrade` ADD CONSTRAINT `FK_System_SystemWeaponUpgrade` FOREIGN KEY (`SystemId`) REFERENCES `System`(`SystemId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `SystemWeaponUpgrade` ADD CONSTRAINT `FK_Weapon_SystemWeaponUpgrade` FOREIGN KEY (`WeaponId`) REFERENCES `Weapon`(`WeaponId`) ON UPDATE RESTRICT ON DELETE RESTRICT;
";

/* You have to manuaully drop the fks
echo "Dropping FKs...<br/>";
$res = $targetDb->query($dropFkSql);
if (!$res) {
    echo "PDO::errorInfo(): <br />";
    print_r($targetDb->errorInfo());
	return;
}
*/

echo "Disable FKs.<br />";
$targetDb->query("SET foreign_key_constraints=0");

foreach ($tableList as $table) {
	echo "<p>Copying $table... ";	
	$res = $targetDb->query("DELETE FROM $table");
	if (!$res) {
		echo "PDO::errorInfo(): <br />";
		print_r($targetDb->errorInfo());
	}

	$stmt = NULL;
	$rows = 0;
	
	$result = mssql_query("SELECT * FROM [$table]", $sourceDb);
	while ($row = mssql_fetch_assoc($result)) {
		$columnNames = array_keys($row);
		
		if (!$stmt) {
			$insertQuery = "INSERT INTO $table (" . implode(",", $columnNames) . ") VALUES (" 
				. implode(",", array_fill(0, count($columnNames), "?")) . ")";
			$stmt = $targetDb->prepare($insertQuery);
		}
		
		$i = 1;
		foreach ($columnNames as $columnName) {
			$data = $row[$columnName];
			// Example date format: Apr  4 2009 02:27:10:057AM
			if (
				(strstr($data, "2009") || strstr($data, "2008"))
				&&
				(substr($data, -2) == "AM" || substr($data, -2) == "PM")
			) {
				$end = substr($data, -2);
				$date = substr($data, 0, strlen($data)-2-4) . " " . $end;
				$data = strftime("%Y-%m-%d %H:%M:%S", strtotime($date));
			}
			//echo "<pre>$table - $rows: $columnName = $data</pre>";
			
			$stmt->bindValue($i++, $data);
		}
		$stmt->execute();
		$rows++;
	}
	
	echo "$rows rows</p>";
	// Clean up
	mssql_free_result($result);
}

echo "Re-enable FKs.<br />";
$targetDb->query("SET foreign_key_constraints=1");

echo "Re-creating FKs...<br/>";
$res = $targetDb->query($createFkSql);
if (!$res) {
    echo "PDO::errorInfo(): <br />";
    print_r($targetDb->errorInfo());
}

?>