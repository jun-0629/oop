<?php
class music
{

	private $id = null,
			$title = null,
			$artist = null,
			$songTime = null;

	public function __construct($id)
	{
		$data = $this->getData();
		foreach ($data["musiclist"] as $value){
			if($value["id"] == $id){
				$this->id = $id;
				$this->title = $value["title"];
				$this->artist = $value["artist"];
				$this->songTime = $value["songTime"];
			}
		}
	}

	private function getData(){
		$musicData = json_decode(file_get_contents("./json/allMusic.json"), true);
		$playlistdata = json_decode(file_get_contents("./json/myMusicList.json"), true);
		return array(
			"musiclist" => $musicData,
			"playlist" => $playlistdata
		);
	}

	//정보 전송
	public function printMusicInfo(){
		$ra = array(
			"id"        => $this->id,
			"title"     => $this->title,
			"artist"    => $this->artist,
			"songtime"  => $this->songTime
		);
		return $ra;
	}

	//플레이 리스트 곡 추가
	public function addPlayList(){
		$playlist = $this->getData()["playlist"];

		if(!is_array($playlist) || !count($playlist)){
			$playlist = array();
		}

		foreach ($playlist as $value){
			if( $value["id"] === $this->id ){
				return array(
					"state"     => "failure",
					"errorCode" => "001"
				);
			}
		}

		$playlist[] = array(
			"id" => $this->id,
			"order" => count($playlist)
		);

		file_put_contents("./json/myMusicList.json",json_encode($playlist));

		return array(
			"state"     => "success"
		);
	}

}


