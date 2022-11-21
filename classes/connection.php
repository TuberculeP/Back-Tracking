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
		$result = $request->fetchAll();
		if(sizeof($result) === 0){
			return false;
		}else{
			return $result[0];
		}
	}
	
	public function getAlbums($user_id): array|bool
	{
		$request = $this->PDO->prepare(
			'SELECT * FROM album JOIN album_by ab on album.id = ab.album_id WHERE ab.user_id='.$user_id
		);
		$request->execute();
		return $request->fetchAll();
	}
	
	public function getAlbumSpec($album_id): array|bool
	{
		$request = $this->PDO->prepare(
			'SELECT album.*, ma.movie_id FROM album
    				LEFT JOIN movie_album ma ON ma.album_id = album.id
                	WHERE album.id='.$album_id
		);
		$request->execute();
		$list = $request->fetchAll();
		
		if($list !== false){
			$album = [
				"movie" => []
			];
			foreach ($list as $row){
				array_push($album['movie'], $row['movie_id']);
			}
			unset($list[0]['movie_id']);
			$album['info'] = $list[0];
			
			$request = $this->PDO->prepare(
				'SELECT pseudo, user.id FROM user JOIN album_by ab on user.id = ab.user_id WHERE album_id='
				.$album['info']['id']
			);
			$request->execute();
			$album['contributor'] = [];
			foreach($request->fetchAll() as $user){
				array_push($album['contributor'], $user);
			}
		}
		
		return $album ?? false;
	}
	
	public function albumUpdate($query, $movie_id, $album_id){
		if($query === 'delete'){
			$request = $this->PDO->prepare(
				'DELETE FROM movie_album WHERE movie_id=:m AND album_id=:a'
			);
		}else{
			$request = $this->PDO->prepare(
				'INSERT INTO movie_album(movie_id, album_id) VALUES (:m, :a)'
			);
		}
		return $request->execute(['m' => $movie_id, 'a' => $album_id]);
	}
	
	public function movieInAlbum($movie_id, $album_id): bool
	{
		$request = $this->PDO->prepare(
			'SELECT * FROM movie_album WHERE album_id=:ai AND movie_id=:mi'
		);
		$request->execute([
			'ai' => $album_id,
			'mi' => $movie_id
		]);
		return (sizeof($request->fetchAll())>0);
	}
}