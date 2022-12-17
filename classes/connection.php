<?php

class Connection
{
	public PDO $PDO;
	
	
	public function __construct()
	
	{
		$this->PDO = new PDO(
			'mysql:dbname=tartiflette;host=127.0.0.1',
			'root',
			'root'
			);
	}
	
	public static function getPDO(): PDO
	{
		static $database = null;
		if ($database === null) {
			$database = new PDO(
				'mysql:dbname=tartiflette;host=127.0.0.1',
				'root',
				'root'
			);
		}
		return $database;
	}
	
	public function getAllUsers(): bool|array
	{
		$request = $this->PDO->prepare('SELECT * FROM user');
		$request->execute();
		return $request->fetchAll();
	}
	
	public function getFromEmail($email): bool|array
	{
		$request = $this->PDO->prepare('SELECT * FROM user WHERE email=:email');
		$request->execute(['email' => $email]);
		$result = $request->fetchAll();
		if(sizeof($result) === 0){
			return false;
		}else{
			return $result[0];
		}
	}
}