let searchCheck = false;

// 웹 로드시 기본 데이터 받아오기
$.ajax({
	url:'api.php',
	type:'post',
	data:{
		'start' : true
	},
	dataType: "json",
	success : function(data){
		let allview = "",
			myview = "";
		if(data.all !== null){
			data.all.forEach(function (value){
				allview += AddAllListView(value.id, value.title, value.artist, value.songtime);
			});
		}

		if(data.my !== null) {
			playlist = data.my.sort((a, b) => a.order - b.order);
			playlist.forEach(function (value) {
				myview += addplaylistview(value.id, value.title, value.artist, value.songtime);
			});
		}
		$("#view .allMusicList ul").append(allview);
		$("#view .myMusicList ul").append(myview);
	}
});


// 플레이 리스트 추가 하기
function addplaylistbtn(id){
	if(!String(id).length){
		alert("잘못된 클릭입니다.");
		return false;
	}

	$.ajax({
		url:'api.php',
		type:'post',
		data:{
			'addplaylist' : true,
			'id' : id
		},
		dataType: "json",
		success : function(data){
			if(data.state === "success"){
				$("#view .myMusicList ul").append(addplaylistview(data.info.id, data.info.title, data.info.artist, data.info.songtime));
			}else if(data.state === "failure"){
				if(data.errorCode === "000"){
					alert("잘못된 접근입니다.");
				}else if(data.errorCode === "001"){
					alert("이미 플레이 리스트에 추가되어 있습니다.")
				}
			}
		}
	});
}


//플레이 리스트 제거
function deleteplaylistbtn(id, obj){
	$.ajax({
		url:'api.php',
		type:'post',
		data:{
			'delplaylist' : true,
			'id' : id,
		},
		dataType: "json",
		success : function(data){
			if(data.state === "success"){
				$(obj).parents("li").remove();
			}else if(data.state === "errorCode"){
				if(data.errorCode === "000"){
					alert("잘못된 접근입니다.");
				}
			}
		}
	});
}

// 플레이 리스트 요소 추가
function addplaylistview(id, title, artist, songtime){
	return '\
			<li data-id="'+id+'">\
				<div>\
					<p>제목 : '+title+'</p>\
					<p>아티스트 : '+artist+'</p>\
					<p>노래 길이 : '+songtime+'</p>\
				</div>\
				<div>\
					<img onclick="deleteplaylistbtn(\''+id+'\', this)" src="image/x.svg">\
				</div>\
			</li>\
			';
}


// 전체곡 요소 추가
function AddAllListView(id, title, artist, songtime){
	return '\
		<li>\
			<div>\
				<p>제목 : ' + title + '</p>\
				<p>아티스트 : ' + artist + '</p>\
				<p>노래 길이 : ' + songtime + '</p>\
			</div>\
			<div>\
				<img onclick="addplaylistbtn(\''+id+'\')" src="image/chevron-left-solid.svg">\
			</div>\
		</li>\
		';
}

$(function() {
	$(".myMusicList ul").sortable({
		stop: function() {
			if(!searchCheck) {
				let update = new Array();
				$(".myMusicList ul").children().each(function () {
					update.push($(this).data("id"));
				});
				$.ajax({
					url: 'api.php',
					type: 'post',
					data: {
						'updateplaylist': true,
						'value': update
					},
					success : function(data){
						if(data.state == "failure"){
							if(data.errorCode == "000"){
								alert("데이터 전송에 실패했습니다.");
								return;
							}

							if(data.errorCode == "001"){
								alert("데이터 값이 틀립니다.");
								return;
							}

							if(data.errorCode == "002"){
								alert("존재하지 않는 값이 있습니다.");
								return;
							}
						}
					}
				});
			}
		}
	});
});


function searchplaylist(){
	const search = $("#search").val();

	if(search.length > 0) {
		$.ajax({
			url: 'api.php',
			type: 'post',
			data: {
				'searchplaylist': true,
				'search': search
			},
			dataType: "json",
			success: function (data) {
				$("#view .myMusicList ul").empty();
				if (data.state == "success") {
					let myview = "";
					for (let i = 0; i < data.info.length; i++){
						myview += addplaylistview(data.info[i].id, data.info[i].title, data.info[i].artist, data.info[i].songtime);
					}
					searchCheck = true;
					$("#view .myMusicList ul").append(myview);
				}
			}
		});
	}else{
		$.ajax({
			url:'api.php',
			type:'post',
			data:{
				'start' : true
			},
			dataType: "json",
			success : function(data){
				let myview = "";
				if(data.my !== null) {
					playlist = data.my.sort((a, b) => a.order - b.order);
					playlist.forEach(function (value) {
						myview += addplaylistview(value.id, value.title, value.artist, value.songtime);
					});
				}
				$("#view .myMusicList ul").empty();
				$("#view .myMusicList ul").append(myview);
			}
		});
		searchCheck = false;
	}
}