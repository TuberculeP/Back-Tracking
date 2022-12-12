<?php

class Album
{
	public int $id;
	public string $name;
	public int $view;
	public int $like;
	public bool $is_public;
	
	public function __construct($array)
	{
		$this->id = $array['id'];
		$this->name = $array['name'];
		$this->view = $array['view'];
		$this->like = $array['like'];
		$this->is_public = $array['is_public']===1;
	}
	
	static function find($id): bool|Album
	{
		require_once 'connection.php';
		$db = new Connection();
		
		$query = $db->PDO->prepare('SELECT * FROM album WHERE id='.$id);
		$query->execute();
		$result = $query->fetchAll();
		if(sizeof($result)>0){
			return new self($result[0]);
		}else{
			return false;
		}
	}
	
	static function all($user_id){
		
		require_once 'connection.php';
		$db = new Connection();
		
		$request = $db->PDO->prepare(
			'SELECT * FROM album JOIN album_by ab on album.id = ab.album_id WHERE ab.user_id='.$user_id
		);
		$request->execute();
		$list = [];
		foreach($request->fetchAll() as $album){
			$list[] = new self($album);
		}
		
		return $list;
	}
	
	public function getStuff(): array|bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare(
			'SELECT album.*, ma.movie_id FROM album
    				LEFT JOIN movie_album ma ON ma.album_id = album.id
                	WHERE album.id='.$this->id
		);
		$request->execute();
		$list = $request->fetchAll();
		
		if($list !== false){
			$stuff = [
				'movie' => [],
				'contributor' => []
			];
			foreach ($list as $row){
				$stuff['movie'][] = $row['movie_id'];
			}
			$request = $db->PDO->prepare(
				'SELECT pseudo, user.id FROM user JOIN album_by ab on user.id = ab.user_id WHERE album_id='
				.$this->id
			);
			$request->execute();
			foreach($request->fetchAll() as $user){
				$stuff['contributor'][] = $user;
			}
		}
		
		return $stuff ?? false;
	}
	
	public function contains($movie_id): bool
	{
		require_once 'connection.php';
		$db = new Connection();
		
		$request = $db->PDO->prepare(
			'SELECT * FROM movie_album WHERE album_id=:ai AND movie_id=:mi'
		);
		$request->execute([
			'ai' => $this->id,
			'mi' => $movie_id
		]);
		return (sizeof($request->fetchAll())>0);
	}
	
	public function add($movie_id){
		return $this->update('insert', $movie_id);
	}
	public function delete($movie_id){
		return $this->update('delete', $movie_id);
	}
	
	private function update($query, $movie_id): bool
	{
		require_once 'connection.php';
		$db = new Connection();
		
		if($query === 'delete'){
			$request = $db->PDO->prepare(
				'DELETE FROM movie_album WHERE movie_id=:m AND album_id=:a'
			);
		}else{
			$request = $db->PDO->prepare(
				'INSERT INTO movie_album(movie_id, album_id) VALUES (:m, :a)'
			);
		}
		$result = $request->execute(['m' => $movie_id, 'a' => $this->id]);
		
		//vérifions que l'album existe
		$request = $db->PDO->prepare('SELECT * FROM movie_album WHERE album_id=:album_id');
		$request->execute(['album_id' => $this->id]);
		
		//si l'album est vide après cela, supprimer l'album
		if(sizeof($request->fetchAll()) === 0){
			$request = $db->PDO->prepare('DELETE FROM album_by WHERE album_id=:album_id');
			$request->execute(['album_id' => $this->id]);
			$request = $db->PDO->prepare('DELETE FROM album WHERE id=:album_id');
			$request->execute(['album_id' => $this->id]);
		}
		
		return $result;
	}
	
	public function getThumbnail(): string
	{
		$first_movie = $this->getStuff()['movie'][0];
		
		$url_name = 'https://api.themoviedb.org/3/movie/' . $first_movie
			. '?api_key=d3151e4e15cfce47f5840fd3c57988df&language=fr';
		$ch_session = curl_init();
		curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch_session, CURLOPT_URL, $url_name);
		$result_url = curl_exec($ch_session);
		return 'https://image.tmdb.org/t/p/w500'.json_decode($result_url, true)['backdrop_path'];
		
	}
	
	static function deleteAll($album_id):bool
	{
		require_once 'connection.php';
		$db = new Connection();
		$query = $db->PDO->prepare('DELETE FROM album WHERE id='.$album_id);
		return $query->execute();
	}
	
	public function toggleLike($user_id)
	{
		require_once 'connection.php';
		$db = new Connection();
		//chercher si l'utilisateur a liké ou pas
		$request = $db->PDO->prepare('SELECT * FROM like_by WHERE album_id = :a AND user_id = :u');
		$request->execute(['a'=>$this->id, 'u'=>$user_id]);
		$arr = $request->fetchAll();
		
		if(sizeof($arr)>0){
			$query1 = 'DELETE FROM like_by WHERE user_id = :u AND album_id = :a';
			$query2 = 'UPDATE album SET album.like = album.like - 1 WHERE id = '.$this->id;
		}else{
			$query1 = 'INSERT INTO like_by(user_id, album_id) VALUES (:u, :a)';
			$query2 = 'UPDATE album SET album.like = album.like + 1 WHERE id = '.$this->id;
		}
		$request = $db->PDO->prepare($query2);
		$request->execute();
		
		$request = $db->PDO->prepare($query1);
		return $request->execute(['a'=>$this->id, 'u'=>$user_id]);
		
	}
	public function addView(){
		require_once 'connection.php';
		$db = new Connection();
		$request = $db->PDO->prepare('UPDATE album SET view = view +1 WHERE id = '.$this->id);
		$result = $request->execute();
		if($result){
			$this->view += 1;
		}
		return $result;
		
	}
	
	static function getLiked($user_id){
		require_once 'connection.php';
		$db = new Connection();
		
		$request = $db->PDO->prepare(
			'SELECT * FROM album JOIN like_by ab on album.id = ab.album_id WHERE ab.user_id='.$user_id
		);
		$request->execute();
		$list = [];
		foreach($request->fetchAll() as $album){
			$list[] = new self($album);
		}
		
		return $list;
	}
}