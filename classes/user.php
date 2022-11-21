<?php

class User
{
	public string $first_name;
	public string $last_name;
	public int $age;
	public string $pseudo;
	public string $email;
	private string $password;
	
	public function __construct($array)
	{
		$this->first_name = $array['first_name'];
		$this->last_name = $array['last_name'];
		$this->age = $array['age'];
		$this->pseudo = $array['pseudo'];
		$this->email = $array['email'];
		$this->setPassword($array['password']);
	}
	
	public function getID()
	{
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare('SELECT id FROM user WHERE email=:email');
		$request->execute(['email' => $this->email]);
		return $request->fetchAll()[0];
	}
	
	public function getPassword(): string
	{
		return $this->password;
	}
	
	public function setPassword($string): void
	{
		$this->password = md5($string . "p€@NÜt-_-BüTt€R");
	}
	
	public function register(): bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare('INSERT INTO user
			(first_name, last_name, age, pseudo, email, password)
    		VALUES
    		(:first_name, :last_name, :age, :pseudo, :email, :password)');
		return $request->execute([
			'first_name' => htmlspecialchars($this->first_name),
			'last_name' => htmlspecialchars($this->last_name),
			'age' => $this->age,
			'pseudo' => htmlspecialchars($this->pseudo),
			'email' => htmlspecialchars($this->email),
			'password' => $this->getPassword()
		]);
	}
	
	public function alreadyEmail(): bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare('SELECT * FROM user WHERE email=:email');
		$request->execute(['email' => $this->email]);
		return (sizeof($request->fetchAll())>0);
	}
	
	public function alreadyPseudo(): bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare('SELECT * FROM user WHERE pseudo=:pseudo');
		$request->execute(['pseudo' => $this->pseudo]);
		return (sizeof($request->fetchAll())>0);
	}
	
}