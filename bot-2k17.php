<?php
// Dibuat oleh GoogleX
//
//  __|                 |    \ \  /     / /\ \     __  /_)     |        
// (_ |  _ \  _ \  _` | |  -_)>  <     < <  > >       /  |(_-<   \   -_)
//\___|\___/\___/\__, |_|\___|_/\_\     \_\ _/     ____|_|___/_| _|\___|
//               ____/                   
//
// Copyright 2017
//                                                    [-CREDIT & BUG-]
// Bug:
//-Mode reaction_* (Can't store login session)
//
// Credit:
//-GoogleX
//-Zishe
//
// THANKS TO YOU AND EVERYBODY WHO SUPPORT THIS SCRIPT

/* Setelan */
$nama_setting = "setting-botfb-".date("Y").".json";
$isi_setting = array(
	'version' => 'RELEASE#1-Patch7.0', //Versi botnya
	'graphver' => 'v2.8', //Facebook graph version (OPTIONAL) *VERSI TERBARU v2.8
	'mode' => 'post_wall', //NOTE: Masukan mode sesuai yang ada dibawah
	'username' => 'a@b.c', //Isi username jika mode anda reaction_* (USERNAME / EMAIL FACEBOOK)
	'password' => '', //Isi password jika mode anda reaction_* (PASSWORD FACEBOOK)
	'fbid' => '', //FBID anda atau seseorang (Bisa juga dengan username facebook)
	'groupID' => '', //Isi ini, jika mode anda *_group
	'pageID' => '', //Isi ini, jika mode anda *_page
	'access_token' => 'EAAAACZAVC6ygBAG4sW1ZAN4QX7rt3DMGqZAXwd9m1kpI4BjJz18TMxyNCRSJm1vVwZBBwb2zUZC4Qw7hKJR5VVhm5PBhLVUlKQn8xaaHmee8hJzf0KlzLI2UFP6ZBQJkDp2J7Apxd5ksCAruWYmP1BMd0D42EVqtsZD',
	'komentar' => '', //Isi ini, jika mode anda comment_*
	'attachment_url' => '', //Isi ini, jika mode anda comment_* (Ini untuk menambahkan gambar pada komentar. Masukan URL gambar)
	'post_message' => 'Ini buat status/posting.', //Isi ini, jika mode anda post_*
	'post_link' => 'http://127.0.0.1', //Link tautan untuk post (Jika ingin post dan menambahkan link website)
	'fields' => 'id,type,message,name', //JANGAN DIUBAH JIKA ANDA TIDAK MENGETAHUI !
	'reaction_type' => '2', //like = 1, love = 2, wow = 3, haha = 4, sad = 7, angry = 8     (*BUG/ERROR*)
	'limit' => '19' //Jumlah post yang akan di like, dan comment
);
if($_GET['update']=="1"){
	if(file_exists($nama_setting)){
		$get_setting = file_get_contents($nama_setting);
		$isi_setting = json_decode($get_setting, true);
	} else {
		die("File setting belum tersedia !");
	}
	$updt_setelan = $_GET['setelan'];
	$updt_value   = $_GET['value'];
	if(array_key_exists($updt_setelan, $isi_setting)){
		$isi_setting['version'] = rand();
		$isi_setting[$updt_setelan] = $updt_value;
		$updt_xx = "yes";
	} else {
		die("Setelan tidak diketahui !");
	}
}
if(!file_exists($nama_setting)){
	$fsetting    = fopen($nama_setting, "w");
	fwrite($fsetting, json_encode($isi_setting));
	fclose($fsetting);
	if(file_exists($nama_setting)){
		$get_setting = file_get_contents($nama_setting);
		$setting     = json_decode($get_setting);
	} else {
		die("Gagal buat file setelan !");
	}
} else {
	$get_setting = file_get_contents($nama_setting);
	$setting     = json_decode($get_setting);
}
if(!$setting->access_token){
	echo "<script>alert('Akses token kosong !, klik ok untuk mendapatkan');</script>";
	echo "<script>window.location='https://www.facebook.com/v1.0/dialog/oauth?redirect_uri=fbconnect://success&scope=user_videos,friends_photos,friends_videos,publish_actions,user_photos,friends_photos,user_activities,user_likes,user_status,friends_status,publish_stream,read_stream,status_update&response_type=token&client_id=41158896424&_rdr';</script>";
}

/* Version Check [Setelan] */
$version = $setting->version;
$v = $isi_setting['version'];
if($version != $v || $version==""){
	$update_setting = $isi_setting;
	if($updt_xx=="yes"){
	    unlink($nama_setting);
	    $fsetting = fopen($nama_setting, "w");
	    fwrite($fsetting, json_encode($update_setting));
	    fclose($fsetting);
	}
}

/* Variabel */
$mode = array(
'comment_group', //Auto komentar pada setiap post group
'comment_beranda', //Auto komentar pada setiap post beranda
'comment_fbid', //Auto komentar pada setiap post beranda seseorang dgn fbid
'comment_wall', //Auto komentar pada setiap post home
'comment_page', //Auto komentar pada setiap post halaman facebook
'get_beranda', //Mendapatkan post di beranda
'get_wallpost', //Mendapatkan post di home
'like_wall', //Like setiap post yang ada di home
'like_beranda', //Like setiap post yang ada di beranda
'like_fbid', //Like setiap post seseorang dgn fbid
'like_page', //Like setiap post di page atau halaman facebook
'like_group', //Like setiap post di group facebook
'post_wall', //Auto post home atau home
'post_group', //Harus masuk kedalam group terlebih dahulu
'post_page', //Hanya administrator dari halaman facebook yang bisa menggunakan mode ini
'post_fbid', //Auto post di beranda seseorang dgn fbid
'reaction_wall', //Auto reaction di home                (*BUG/ERROR*)
'reaction_group', //Auto reaction di group              (*BUG/ERROR*)
'reaction_page', //Auto reaction di halaman atau page   (*BUG/ERROR*)
'reaction_beranda', //Auto reaction di beranda          (*BUG/ERROR*)
'reaction_fbid' //Auto reaction di beranda seseorang dgn fbid (*BUG/ERROR*)
);
$graph_fb     = "https://graph.facebook.com/";
$graph_beta   = "https://graph.beta.facebook.com/";
$mobile_fb    = "https://mobile.facebook.com/";
$mobile_login = "https://mobile.facebook.com/login.php?refsrc=https%3A%2F%2Fm.facebook.com";
$fbid           = $setting->fbid;
$access_token   = $setting->access_token;
$message        = $setting->komentar;
$attachment_url = $setting->attachment_url;
$post_message   = $setting->post_message;
$post_link      = $setting->post_link;
$fields         = $setting->fields;
$limit          = $setting->limit;
$pageID         = $setting->pageID;
$groupID        = $setting->groupID;
$reaction_type  = $setting->reaction_type;
$username       = $setting->username;
$password       = $setting->password;
$fungsi         = new fungsi;
$login = array(
     'pass' => $password,
     'email' => $username,
     'login'  => 'Login'
);

/* Penentu mode */
$mm = $setting->mode;
foreach($mode as $m){
	if($m == $mm){
		$mods = $m;
	}
}

/* Fungsi */
class fungsi{
    public function kirimCoy($url,$post=null) {
		    $ch = curl_init($url);
		    if($post != null) {
	 	 	    curl_setopt($ch, CURLOPT_POST, true);
		  	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		    }
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); 
		  	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		  	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		  	curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		  	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		  	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		   	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		    return curl_exec($ch);
		  	curl_close($ch);
    }
    public function fetch_value($str, $find_start, $find_end){
		$start = strpos($str, $find_start);
		if ($start === false) {
				return "";
		}
		$length = strlen($find_start);
		$end = strpos(substr($str, $start + $length), $find_end);
		return trim(substr($str, $start + $length, $end));
	}
    public function get_wall_ANY($var1, $var2, $var3, $var4){
	    $url = $graph_fb.$var1."/feed?fields=".$var2."&limit=".$var3."&access_token=".$var4;
	    $data = kirimCoy($url);
	    $data = json_decode($data);
	    return $data;
    }
    public function githubraw($url){
	    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
	    return $data;
        curl_close($ch);
    }
}

/* Core */
if($mods == "comment_group"){
	$url  = $graph_fb.$groupID."/feed?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$numb = $i+1;
		$pID    = $data->data[$i]->id;
		$url    = $graph_fb.$pID."/comments?method=post&message=".$message."&attachment_url=".$attachment_url."&access_token=".$access_token;
		$result = $fungsi->kirimCoy($url);
		echo "No: ".$numb."<br>";
		echo "ID: ".$pID."<br>";
		echo "Result: ".$result."<br><br>";
	}
} elseif($mods == "comment_fbid"){
	$url  = $graph_fb.$fbid."/feed?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$numb = $i+1;
		$pID    = $data->data[$i]->id;
		$url    = $graph_fb.$pID."/comments?method=post&message=".$message."&attachment_url=".$attachment_url."&access_token=".$access_token;
		$result = $fungsi->kirimCoy($url);
		echo "No: ".$numb."<br>";
		echo "ID: ".$pID."<br>";
		echo "Result: ".$result."<br><br>";
	}
} elseif($mods == "comment_page"){
	$url  = $graph_fb.$pageID."/feed?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$numb = $i+1;
		$pID    = $data->data[$i]->id;
		$url    = $graph_fb.$pID."/comments?method=post&message=".$message."&attachment_url=".$attachment_url."&access_token=".$access_token;
		$result = $fungsi->kirimCoy($url);
		echo "No: ".$numb."<br>";
		echo "ID: ".$pID."<br>";
		echo "Result: ".$result."<br><br>";
	}
} elseif($mods == "comment_wall"){
	$url  = $graph_fb."me/feed?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$numb = $i+1;
		$pID    = $data->data[$i]->id;
		$url    = $graph_fb.$pID."/comments?method=post&message=".$message."&attachment_url=".$attachment_url."&access_token=".$access_token;
		$result = $fungsi->kirimCoy($url);
		echo "No: ".$numb."<br>";
		echo "ID: ".$pID."<br>";
		echo "Result: ".$result."<br><br>";
	}
} elseif($mods == "comment_beranda"){
	$url  = $graph_fb."me/home?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$numb = $i+1;
		$pID    = $data->data[$i]->id;
		$url    = $graph_fb.$pID."/comments?method=post&message=".$message."&attachment_url=".$attachment_url."&access_token=".$access_token;
		$result = $fungsi->kirimCoy($url);
		echo "No: ".$numb."<br>";
		echo "ID: ".$pID."<br>";
		echo "Result: ".$result."<br><br>";
	}
} elseif($mods == "get_beranda"){
	$url  = $graph_fb.$fbid."/home?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$data_message = $data->data[$i]->message;
		$data_message = str_replace("\n", "<br>", $data_message);
		$numb = $i+1;
		echo "No: ".$numb."<br>";
		echo "ID: ".$data->data[$i]->id
		."<br>";
		echo "Time created: ".$data->data[$i]->created_time
		."<br>";
		echo "Post type: ".$data->data[$i]->type
		."<br>";
		echo "Name: ".$data->data[$i]->name
		."<br>";
		echo "Message: <br>".$data_message
		."<br><br><br>";
	}
} elseif($mods == "get_wallpost"){
	$url  = $graph_fb.$fbid."/feed?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$data_message = $data->data[$i]->message;
		$data_message = str_replace("\n", "<br>", $data_message);
		$numb = $i+1;
		echo "No: ".$numb."<br>";
		echo "ID: ".$data->data[$i]->id
		."<br>";
		echo "Time created: ".$data->data[$i]->created_time
		."<br>";
		echo "Post type: ".$data->data[$i]->type
		."<br>";
		echo "Name: ".$data->data[$i]->name
		."<br>";
		echo "Message: <br>".$data_message
		."<br><br><br>";
	}
} elseif($mods == "like_wall"){
	$url = $graph_fb."me/feed?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$numb = $i+1;
		$pID    = $data->data[$i]->id;
		$url    = $graph_fb.$pID."/likes?method=post&access_token=".$access_token;
		$result = $fungsi->kirimCoy($url);
		echo "No: ".$numb."<br>";
		echo "ID: ".$pID."<br>";
		echo "Result: ".$result."<br><br>";
	}
} elseif($mods == "like_fbid"){
	$url = $graph_fb.$fbid."/feed?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$numb = $i+1;
		$pID    = $data->data[$i]->id;
		$url    = $graph_fb.$pID."/likes?method=post&access_token=".$access_token;
		$result = $fungsi->kirimCoy($url);
		echo "No: ".$numb."<br>";
		echo "ID: ".$pID."<br>";
		echo "Result: ".$result."<br><br>";
	}
} elseif($mods == "like_group"){
	$url = $graph_fb.$groupID."/feed?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$numb = $i+1;
		$pID    = $data->data[$i]->id;
		$url    = $graph_fb.$pID."/likes?method=post&access_token=".$access_token;
		$result = $fungsi->kirimCoy($url);
		echo "No: ".$numb."<br>";
		echo "ID: ".$pID."<br>";
		echo "Result: ".$result."<br><br>";
	}
} elseif($mods == "like_page"){
	$url = $graph_fb.$pageID."/feed?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$numb = $i+1;
		$pID    = $data->data[$i]->id;
		$url    = $graph_fb.$pID."/likes?method=post&access_token=".$access_token;
		$result = $fungsi->kirimCoy($url);
		echo "No: ".$numb."<br>";
		echo "ID: ".$pID."<br>";
		echo "Result: ".$result."<br><br>";
	}
} elseif($mods == "like_beranda"){
	$url = $graph_fb."me/home?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$numb = $i+1;
		$pID    = $data->data[$i]->id;
		$url    = $graph_fb.$pID."/likes?method=post&access_token=".$access_token;
		$result = $fungsi->kirimCoy($url);
		echo "No: ".$numb."<br>";
		echo "ID: ".$pID."<br>";
		echo "Result: ".$result."<br><br>";
	}
} elseif($mods == "post_fbid"){
	if(!$post_link){
		$url = $graph_fb.$fbid."/feed?method=post&message=".$post_message."&access_token=".$access_token;
	} else {
		$url = $graph_fb.$fbid."/feed?method=post&message=".$post_message."&link=".$post_link."&access_token=".$access_token;
	}
	$data = $fungsi->kirimCoy($url);
	echo $data;
} elseif($mods == "post_group"){
	if(!$post_link){
		$url = $graph_fb.$groupID."/feed?method=post&message=".$post_message."&access_token=".$access_token;
	} else {
		$url = $graph_fb.$groupID."/feed?method=post&message=".$post_message."&link=".$post_link."&access_token=".$access_token;
	}
	$data = $fungsi->kirimCoy($url);
	echo $data;
} elseif($mods == "post_page"){
	if(!$post_link){
		$url = $graph_fb.$pageID."/feed?method=post&message=".$post_message."&access_token=".$access_token;
	} else {
		$url = $graph_fb.$pageID."/feed?method=post&message=".$post_message."&link=".$post_link."&access_token=".$access_token;
	}
	$data = $fungsi->kirimCoy($url);
	echo $data;
} elseif($mods == "post_wall"){
	if(!$post_link){
		$url = $graph_fb."me/feed?method=post&message=".$post_message."&access_token=".$access_token;
	} else {
		$url = $graph_fb."me/feed?method=post&message=".$post_message."&link=".$post_link."&access_token=".$access_token;
	}
	$data = $fungsi->kirimCoy($url);
	echo $data;
} elseif($mods == "reaction_wall"){
	$url  = $graph_fb."me/feed?fields=".$fields."&limit=".$limit."&access_token=".$access_token;
	$data = $fungsi->kirimCoy($url);
	$data = json_decode($data);
	for($i=0;$i<$limit;$i++){
		$numb = $i+1;
		$pID  = $data->data[$i]->id;
		$ddd  = explode("_", $pID);
		//$login  = kirimCoy($mobile_login, "lsd=AVpI36s1&version=1&ajax=0&width=0&pxr=0&gps=0&dimensions=0&m_ts=1483804348&li=qg5xWAUZXopBIK0ABg1Dtlzt&email=".$username."&pass=".$password."&login=Masuk");
		//$login  = get_contents($mobile_fb."login.php",1,$login);
		//$login  = curl($mobile_login, "lsd=AVpI36s1&version=1&ajax=0&width=0&pxr=0&gps=0&dimensions=0&m_ts=1483804348&li=qg5xWAUZXopBIK0ABg1Dtlzt&email=".$username."&pass=".$password."&login=Masuk");
		$url    = $mobile_fb."reactions/picker/?ft_id=".$ddd[1];
		$source = kirimCoy($url);
		$source = str_replace('&amp;','&',$source);
		$first  = '/ufi/reaction/?ft_ent_identifier='.$ddd[1].'&reaction_type='.$reaction_type;
		$cari   = fetch_value($source, $first, '" style="display:block">');
		$urL    = $mobile_fb.'/ufi/reaction/?ft_ent_identifier='.$ddd[1].'&reaction_type='.$reaction_type.$cari;
		$result = kirimCoy($urL);
		echo "No: ".$numb."<br>";
		echo "ID: ".$pID."<br>";
		echo "Result: ".$result."<br><br>";
	}
}

?>