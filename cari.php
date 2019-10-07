<?php
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'koneksi.php';
	require_once 'stemmer.php';	

	$cari = $stemmer->stem($_POST['cari']);	
	$cari = preg_split("/[\s,.:;-_()!@#$%^&*?<>'–|0123456789]+/",$cari); //Proses pemecehan text menjadi kata
	// echo print_r($cari);

	$judul = [];	
	$jumlah = [];
	$id_artikel = [];
	$artikel = mysqli_query($conn,'SELECT * FROM artikel');
	$c = 0;
	while($row = mysqli_fetch_assoc($artikel)){
		array_push($judul, $row['judul']);
		array_push($id_artikel, $row['id_artikel']);
		$c++;
	}

	$artikel = mysqli_query($conn,'SELECT * FROM artikel');

	for ($i=0; $i < $c; $i++) { 
		$jumlah[$i] = [];
		for ($j=0; $j < count($cari) ; $j++) { 
			$query = mysqli_query($conn,
				"SELECT judul,kata,jumlah FROM relasi
				LEFT JOIN kata ON id_kata = kata_id
				LEFT JOIN artikel ON id_artikel = artikel_id
				WHERE kata = '$cari[$j]' AND artikel_id = '$id_artikel[$i]'"
			);
			if(mysqli_fetch_assoc($query) > 0){
				$query = mysqli_query($conn,
					"SELECT judul,kata,jumlah FROM relasi
					LEFT JOIN kata ON id_kata = kata_id
					LEFT JOIN artikel ON id_artikel = artikel_id
					WHERE kata = '$cari[$j]' AND artikel_id = '$id_artikel[$i]'"
				);
				while($row = mysqli_fetch_assoc($query)){
					$jumlah[$i][$j] = $row['jumlah'];
				}
			}
			else{
				$jumlah[$i][$j] = 0;
			}			
		}
	}

	$data = "";

	for ($i=-1; $i <count($id_artikel) ; $i++) { 
		if($i == -1){
			$data .= "<tr>";
			for ($j=-1; $j <count($cari) ; $j++) { 
				if($j == -1){
					$data .= "<th></th>";
				}else
					$data .= "<th>".$cari[$j]."</th>";
			}
			$data .= "</tr>";
		}
		else{
			$data .= "<tr>";
			for ($j=-1; $j <count($cari) ; $j++) { 
				if($j == -1){
					$data .= "<td>".$judul[$i]."</td>";
				}
				else
					$data .= "<td>".$jumlah[$i][$j]."</td>";
			}
			$data .= "</tr>";
		}
	}

	echo $data;
?>