<?php

class Connection
{
	public PDO $PDO;
	
	
	public function __construct()
	
	{
		$this->PDO = new PDO(
			'mysql:dbname=tartiflette;host=127.0.0.1',
			'root',
			'root');
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
		return $request->fetchAll()[0];
	}
	
}