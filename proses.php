<?php			
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'koneksi.php';
	require_once 'stemmer.php';	
	
	$file = file_get_contents($_FILES['file']['tmp_name']); //Proses pengambilan file text	
	$file = strtolower($file);	//Proses case folding	
	$word = preg_split("/[\s,.:;-_()!@#$%^&*?<>'–|0123456789]+/",$file); //Proses pemecehan text menjadi kata
	$input = file_get_contents('data/stop_words.txt'); //Proses pengambilan stopwords
	$stop_words = explode("\n",$input);	
	$result = [];
	for($i=0;$i<count($word);$i++){
		if(isset($word[$i])){
			if(!in_array($word[$i], $stop_words)) // Pengecekan kata dengan stopwords					
				array_push($result, $stemmer->stem($word[$i]));				
		}
	}
	$query = mysqli_query($conn,"INSERT INTO artikel VALUES('','".$_FILES['file']['name']."')");
	$id_artikel = $conn->insert_id;

	for($i=0;$i<count($result)-1;$i++){

		$check = mysqli_query($conn,"SELECT * FROM kata WHERE kata = '".$result[$i]."'");

		if(mysqli_fetch_assoc($check) > 0){
			$check = mysqli_query($conn,"SELECT * FROM kata WHERE kata = '".$result[$i]."'");
			while($cek = mysqli_fetch_assoc($check)){
				$id_kata = $cek['id_kata'];
			}

			$check = mysqli_query($conn,"SELECT jumlah FROM relasi WHERE kata_id = $id_kata AND artikel_id = $id_artikel");

			if(mysqli_fetch_assoc($check) > 0){
				$check = mysqli_query($conn,"SELECT jumlah FROM relasi WHERE kata_id = $id_kata AND artikel_id = $id_artikel");
				while($cek = mysqli_fetch_assoc($check)){
					$jumlah = $cek['jumlah']+1;
					$query = mysqli_query($conn,"UPDATE relasi SET jumlah = $jumlah WHERE kata_id = $id_kata AND artikel_id = $id_artikel");
				}
			}
			else{				
				$query = mysqli_query($conn,"INSERT INTO relasi VALUES('','".$id_kata."','".$id_artikel."','1')");
			}
		}

		else{			
			$query = mysqli_query($conn,"INSERT INTO kata VALUES('','".$result[$i]."')");
			$id_kata = $conn->insert_id;
			$query = mysqli_query($conn,"INSERT INTO relasi VALUES('','".$id_kata."','".$id_artikel."','1')");
		}

	}
?>