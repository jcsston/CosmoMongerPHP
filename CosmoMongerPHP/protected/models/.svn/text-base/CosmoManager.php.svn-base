<?php

class CosmoManager 
{
	public static function getCodeVersion()
	{
		return '2.0';
	}
	
	/// <summary>
	/// Gets the database version. Ex. The liquibase changelog number
	/// </summary>
	/// <returns>Database version of connected database</returns>
	public static function getDatabaseVersion()
	{
		$command = Yii::app()->db->createCommand("SELECT MAX(CAST(Id AS DECIMAL)) AS 'DbVer' FROM DATABASECHANGELOG");
		$reader = $command->query();
		foreach ($reader as $row) {
			return $row['DbVer'];
		}
	}
}
