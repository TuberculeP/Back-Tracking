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
		return $request->fetchAll()[0]['id'];
	}
	
	static function getName($id)
	{
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare('SELECT first_name,last_name,pseudo FROM user WHERE id=:id');
		$request->execute(['id' => $id]);
		return $request->fetchAll()[0];
	}
	
	static function search($query): bool|array
	{
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare("SELECT pseudo, id FROM user WHERE pseudo LIKE '".$query."%'");
		$request->execute();
		return $request->fetchAll();
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
	
	public function createAlbum($name): array|bool
	{
		require_once 'connection.php';
		$db = new Connection();
		
		//créer l'album
		$request = $db->PDO->prepare('INSERT INTO album (name) VALUES (:name)');
		$request->execute(['name' => $name]);
		
		//récupérer l'ID
		$request = $db->PDO->prepare('SELECT * FROM album WHERE name=:name ORDER BY id DESC');
		$result = $request->execute(['name' => $name]);
		if($result !== false){
			$album = $request->fetchAll()[0];
			$this->contribute($album['id']);
		}
		return $album ?? false;
	}
	
	public function contribute($album_id): bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare('INSERT INTO album_by (user_id, album_id) VALUES (:u, :a)');
		return $request->execute([
			'u' => $this->getID(),
			'a' => $album_id
		]);
	}
	
	public function link_generate($album_id, $key): bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare('INSERT INTO
    										invitation(`key`, album_id, user_id,created_at)
											VALUES(:key, :album_id, :user_id, NOW())');
		return $request->execute([
			'key' => $key,
			'album_id' => $album_id,
			'user_id' => $this->getID()
		]);
	}
	
	public function link_clear(): bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare('DELETE FROM invitation WHERE NOW() - 24*3600 > created_at -1');
		return $request->execute();
	}
	
	public function link_get($key): array|bool
	{
		require_once 'connection.php';
		$db = new Connection();
		//virer les liens expirés
		$this->link_clear();
		//
		$request = $db->PDO->prepare('SELECT * FROM invitation WHERE `key`=:key');
		$request->execute([
			'key' => $key
		]);
		$result =  $request->fetchAll();
		if(sizeof($result)>0){
			return $result[0];
		}else{
			return [];
		}
	}
	
	public function link_getAll($album_id): bool|array
	{
		require_once 'connection.php';
		$db = new Connection();
		//virer les liens expirés
		$this->link_clear();
		//
		$request = $db->PDO->prepare('SELECT * FROM invitation WHERE `album_id`=:album_id');
		$request->execute([
			'album_id' => $album_id
		]);
		$result =  $request->fetchAll();
		if(sizeof($result)>0){
			return $result[0];
		}else{
			return [];
		}
	}
	
	public function isContributor($album_specs): bool
	{
		foreach($album_specs['contributor'] as $contributor){
			if($contributor['id'] === $this->getID()){
				return true;
			}
		}
		return false;
	}
}