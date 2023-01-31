<?php
include_once "class/music.class.php";
include_once "class/playlist.class.php";

$json_string = file_get_contents("./json/allMusic.json");
$musicData = json_decode($json_string, true);
$json_string = file_get_contents("./json/myMusicList.json");
$playlistdata = json_decode($json_string, true);

foreach ($musicData as $value){
	$music[] = new music($value["id"]);
}

$playlist = new playList();

if(isset($_POST["start"]) && $_POST["start"]){
	foreach ($music as $value){
		$ml[] = $value->printMusicInfo();
	}
	$pl = $playlist->printplaylistinfo();
	$arr = array(
		"all" => $ml,
		"my"  => $pl,
	);
	echo json_encode($arr);
	exit();
}

//플레이 리스트 추가
if(isset($_POST["addplaylist"]) && $_POST["addplaylist"]){

	$msg = null;

	if( !isset($_POST["id"]) ) {
		$msg = array(
			"state" => "failure",
			"errorCode" => "000"
		);
	}

	foreach ($music as $value){
		$data = $value->printMusicInfo();
		if( $data["id"] == $_POST["id"] ){
			$msg = $value->addPlayList();
			if($msg["state"] === "success"){
				$msg["info"] = $data;
			}
		}
	}
	echo json_encode($msg);
	exit();
}



//플레이 리스트 삭제
if(isset($_POST["delplaylist"]) && $_POST["delplaylist"]){
	$msg = null;
	if( !isset($_POST["id"]) ) {
		$msg = array(
			"state" => "failure",
			"errorCode" => "000"
		);
	}

	$msg = $playlist->deleteplaylist($_POST["id"]);

	echo json_encode($msg);
	exit();
}



//순서 변경
if(isset($_POST["updateplaylist"]) && $_POST["updateplaylist"]){
	echo json_encode($playlist->updateMyMusicList($_POST["value"]));
	exit();
}


//플레이 리스트 검색
if(isset($_POST["searchplaylist"]) && $_POST["searchplaylist"]){
	echo json_encode($playlist->searchplaylist($_POST["search"]));
	exit();
}