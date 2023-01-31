<?php
class playList
{

	private function getData(){
		$musicData = json_decode(file_get_contents("./json/allMusic.json"), true);
		$playlistdata = json_decode(file_get_contents("./json/myMusicList.json"), true);
		return array(
			"musiclist" => $musicData,
			"playlist" => $playlistdata
		);
	}

	//정보 전송
	public function printplaylistinfo(){
		$musiclist = $this->getData()["musiclist"];
		$playlist = $this->getData()["playlist"];

		foreach ($playlist as $value){
			foreach ($musiclist as $value1){
				if($value["id"] === $value1["id"]){
					$ra[] = array(
						"id"        => (int)$value1["id"],
						"title"     => $value1["title"],
						"artist"    => $value1["artist"],
						"songtime"  => $value1["songTime"],
						"order"     => (int)$value["order"]
					);
				}
			}
		}
		return $ra;
	}


	//플레이 리스트 정보 삭제
	public function deleteplaylist($id){
		if(!isset($id) || !$id){
			return array(
				"state" => "failure",
				"errorCode" => "000"
			);
		}

		$playlist = $this->getData()["playlist"];

		if(!is_array($playlist) || !count($playlist)){
			$playlist = array();
		}

		usort($playlist, function($a, $b) {return strcmp($a->order, $b->order);});

		$count = 0;
		foreach ($playlist as $value){
			if( $value["id"] != $id ){
				$data[] = array(
					"id" => (int)$value["id"],
					"order" => $count
				);
				$count++;
			}
		}

		file_put_contents("./json/myMusicList.json",json_encode($data));

		return array(
			"state"     => "success"
		);
	}


	//플레이 리스트 순서 변경
	public function updateMyMusicList($param){
		if( !is_array($param) ){
			return array(
				"state" => "failure",
				"errorCode" => "000"
			);
		}

		$playlist = $this->getData()["playlist"];

		if(count($param) != count($playlist)){
			return array(
				"state" => "failure",
				"errorCode" => "001"
			);
		}

		foreach ($playlist as $value){
			if(!in_array($value["id"], $param)){
				return array(
					"state" => "failure",
					"errorCode" => "002"
				);
			}
		}

		for($i = 0; $i < count($param); $i++){
			$arr[] = array(
				"id"       => (int)$param[$i],
				"order"    => $i
			);
		}


		file_put_contents("./json/myMusicList.json",json_encode($arr));

		return array(
			"state"     => "success"
		);
	}



	//검색 기능
	public function searchplaylist($search){
		if(!isset($search) || !$search){
			return array(
				"state" => "failure",
				"errorCode" => "000"
			);
		}


		$playlist = $this->printplaylistinfo();

		foreach ($playlist as $value) {
			if(stristr($value["title"], $search)){
				$ra[] = $value;
			}
		}

		if(!is_array($ra) || !count($ra)){
			return array(
				"state" => "failure",
				"errorCode" => "002"
			);
		}

		return array(
			"state" => "success",
			"info" => $ra
		);
	}
}