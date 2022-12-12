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
	
	static function find($id): User|bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare('SELECT * FROM user WHERE id=:id');
		$request->execute(['id' => $id]);
		return new User($request->fetchAll()[0]);
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
		$result = $request->execute([
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
	
	public function createAlbum($name, $is_public): Album|bool
	{
		require_once 'connection.php';
		require_once './classes/album.php';
		$db = new Connection();
		
		//créer l'album
		$request = $db->PDO->prepare('INSERT INTO album (`name`, is_public) VALUES (:name, :is_public)');
		$request->execute(['name' => $name, 'is_public' => (int)$is_public]);
		
		//récupérer l'ID
		$request = $db->PDO->prepare('SELECT * FROM album WHERE name=:name ORDER BY id DESC');
		$result = $request->execute(['name' => $name]);
		if($result !== false){
			$data = $request->fetchAll()[0];
			var_dump($data);
			$album = new Album($data);
			$this->contribute($album->id);
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
	
	public function isContributor($stuff): bool
	{
		foreach($stuff['contributor'] as $contributor){
			if($contributor['id'] === $this->getID()){
				return true;
			}
		}
		return false;
	}
	
	public function getStuff(): array
	{
		require_once 'connection.php';
		$db = new Connection();
		$query = $db->PDO->prepare('SELECT * FROM profile WHERE user_id='.$this->getID());
		$query->execute();
		$result = $query->fetchAll();
		return sizeof($result)=== 0? $result: $result[0];
	}
	
	public function updateStuff($stuff): bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$want_adult = $stuff['want_adult']??0;
		$stuff['want_adult'] = (int)($stuff['want_adult'] === 'on');
		$new_stuff = [
			'u'=>$this->getID(),
			'd'=>$stuff['description'],
			'a'=> $want_adult
		];
		var_dump($new_stuff);
		
		//vérifions qu'il a un profil, sinon le créer
		$verif = $db->PDO->prepare('SELECT * FROM profile WHERE user_id = '.$this->getID());
		$verif->execute();
		if(sizeof($verif->fetchAll())===0){
			$query = $db->PDO->prepare(
				'INSERT INTO profile(user_id, description, want_adult) VALUES (:u,:d,:a)');
			return $query->execute();
		}else{
			$query = $db->PDO->prepare('UPDATE profile
		SET description = :description, want_adult = :want_adult WHERE user_id='.$this->getID());
			return $query->execute([
				'description'=>$stuff['description'],
				'want_adult'=>$stuff['want_adult']
			]);
		}
	}
	
	public function rember($movie_id, $table): bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$query = $db->PDO->prepare('INSERT INTO '.$table.'(user_id, movie_id) VALUES (:u, :m)');
		return $query->execute([
			'm'=>$movie_id,
			'u'=>$this->getID()
		]);
	}
	
	public function forgor($movie_id, $table): bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$query = $db->PDO->prepare('DELETE FROM '.$table.' WHERE user_id = :u AND movie_id = :m');
		return $query->execute([
			'm'=>$movie_id,
			'u'=>$this->getID()
		]);
	}
	
	public function getSeen(): Array
	{
		$db = new Connection();
		$query = $db->PDO->prepare('SELECT movie_id FROM seen where user_id='.$this->getID());
		$query->execute();
		$result = [];
		foreach($query->fetchAll() as $arr){
			$result[] = $arr['movie_id'];
		}
		return $result;
	}
	
	public function getWanted(): Array
	{
		$db = new Connection();
		$query = $db->PDO->prepare('SELECT movie_id FROM wanted where user_id='.$this->getID());
		$query->execute();
		$result = [];
		foreach($query->fetchAll() as $arr){
			$result[] = $arr['movie_id'];
		}
		return $result;
	}
	
	public function hasLiked($album_id){
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare('SELECT * FROM like_by WHERE album_id = :a AND user_id = :u');
		$request->execute(['a'=>$album_id, 'u'=>$this->getID()]);
		return sizeof($request->fetchAll())>0;
	}
}