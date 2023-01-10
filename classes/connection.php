<?php

class Connection
{
	public PDO $PDO;
	
	
	public function __construct()
	
	{
		$this->PDO = new PDO('mysql:host=localhost;dbname=tartiflette;charset=utf8',
			'root',
			'root',
			array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		
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