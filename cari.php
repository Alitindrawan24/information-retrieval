<?php
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'koneksi.php';
	require_once 'stemmer.php';	
	
	$word = strtolower($_POST['cari']);
	$word = preg_split("/[\s,.:;-_()!@#$%^&*?<>'–|0123456789]+/",$word); //Proses pemecehan text menjadi kata		

	$input = file_get_contents('data/stop_words.txt'); //Proses pengambilan stopwords
	$stop_words = explode("\n",$input);

	$cari = [];
	for($i=0;$i<count($word);$i++){
		if(isset($word[$i])){
			if(!in_array($word[$i], $stop_words)) // Pengecekan kata dengan stopwords					
				array_push($cari, $stemmer->stem($word[$i]));				
		}
	}

	$judul = [];	
	$jumlah = [];
	$df = [];
	$id_artikel = [];
	$ap = [];
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
		$df[$i] = [];
		$ap[$i] = [];
		$fp[$i] = [];
		$lp[$i] = [];
		for ($j=0; $j < count($cari) ; $j++) { 
			$query = mysqli_query($conn,
				"SELECT judul,kata,jumlah,DF FROM relasi				
				LEFT JOIN artikel ON id_artikel = artikel_id
				WHERE kata = '$cari[$j]' AND artikel_id = '$id_artikel[$i]'"
			);
			if(mysqli_fetch_assoc($query) > 0){
				$query = mysqli_query($conn,
					"SELECT judul,kata,jumlah, DF, AP,FP,LP FROM relasi					
					LEFT JOIN artikel ON id_artikel = artikel_id
					WHERE kata = '$cari[$j]' AND artikel_id = '$id_artikel[$i]'"
				);
				while($row = mysqli_fetch_assoc($query)){
					$jumlah[$i][$j] = $row['jumlah'];
					$df[$i][$j] = $row['DF'];
					$ap[$i][$j] = $row['AP'];
					$fp[$i][$j] = $row['FP'];
					$lp[$i][$j] = $row['LP'];
				}
			}
			else{
				$jumlah[$i][$j] = 0;
				$query = mysqli_query($conn,
					"SELECT DF,AP,FP,LP FROM relasi										
					WHERE kata = '$cari[$j]'"
				);
				while($row = mysqli_fetch_assoc($query)){					
					$df[$i][$j] = $row['DF'];
					$ap[$i][$j] = $row['AP'];
					$fp[$i][$j] = $row['FP'];
					$lp[$i][$j] = $row['LP'];
				}				
			}			
		}
	}		
	$tfidf = [];
	$total = [];
	$total2 = [];
	$total3 = [];
	$total4 = [];
	$tfidfap = [];
	$tfidffp = [];
	$tfidflp = [];
	for ($i=0; $i < $c; $i++) { 
		$tfidfap[$i] = [];
		$tfidf[$i] = [];
		$total[$i] = 0;
		$total2[$i] = 0;
		$total3[$i] = 0;
		$total4[$i] = 0;
		for ($j=0; $j < count($cari) ; $j++) {
			//Perhitungan TF IDF			
			if($jumlah[$i][$j] > 0){
				$jumlah[$i][$j] = (log($jumlah[$i][$j])+1);
				$tfidf[$i][$j] = $jumlah[$i][$j] * ($c/$df[$i][$j]);
				$tfidfap[$i][$j] = $tfidf[$i][$j] * $ap[$i][$j];
				$tfidffp[$i][$j] = $tfidf[$i][$j] * $fp[$i][$j];
				$tfidflp[$i][$j] = $tfidf[$i][$j] * $lp[$i][$j];
			}
			else{
				$jumlah[$i][$j] = 0;
				$tfidf[$i][$j] = 0;
				$tfidfap[$i][$j] = 0;
				$tfidffp[$i][$j] = 0;
				$tfidflp[$i][$j] = 0;
			}
						

			$total[$i] += $tfidf[$i][$j];
			$total2[$i] += $tfidfap[$i][$j];
			$total3[$i] += $tfidffp[$i][$j];
			$total4[$i] += $tfidflp[$i][$j];
		}
	}

	for ($i=0; $i < $c; $i++) { 
		for ($j=$i+1; $j < $c; $j++){
			if($total[$j] > $total[$i]){
				$temp = $total[$j];
				$total[$j] = $total[$i];
				$total[$i] = $temp;

				$temp = $total2[$j];
				$total2[$j] = $total2[$i];
				$total2[$i] = $temp;

				$temp = $total3[$j];
				$total3[$j] = $total3[$i];
				$total3[$i] = $temp;

				$temp = $total4[$j];
				$total4[$j] = $total4[$i];
				$total4[$i] = $temp;

				$temp = $tfidf[$i];
				$tfidf[$i] = $tfidf[$j];
				$tfidf[$j] = $temp;

				$temp = $df[$i];
				$df[$i] = $df[$j];
				$df[$j] = $temp;

				$temp = $judul[$i];
				$judul[$i] = $judul[$j];
				$judul[$j] = $temp;
			}
		}			
	}

	$data = "";

	// for ($i=-1; $i <count($id_artikel) ; $i++) { 
	// 	if($i == -1){
	// 		$data .= "<tr>";
	// 		for ($j=-1; $j <=count($cari) ; $j++) { 
	// 			if($j == -1){
	// 				$data .= "<th></th>";
	// 			}
	// 			else if($j == count($cari)){
	// 				$data .= "<th>TOTAL</th>";					
	// 			}
	// 			else
	// 				$data .= "<th>".$cari[$j]."</th>";
	// 		}
	// 		$data .= "</tr>";
	// 	}
	// 	else{
	// 		$data .= "<tr>";
	// 		for ($j=-1; $j <=count($cari) ; $j++) { 
	// 			if($j == -1){
	// 				$data .= "<td>".$judul[$i]."</td>";
	// 			}
	// 			else if($j == count($cari)){
	// 				$data .= "<th>".$total[$i]." / ".$total2[$i]."</th>";	
	// 			}
	// 			else
	// 				$data .= "<td>".$tfidf[$i][$j]."</td>";
	// 		}
	// 		$data .= "</tr>";
	// 	}
	// }

	$tipe = ['TF-IDF','TF-IDF-AP','TF-IDF-FP','TF-IDF-LP'];
	$result = [$total, $total2, $total3, $total4];


	for ($i=-1; $i <count($id_artikel) ; $i++) { 
		if($i == -1){
			$data .= "<thead><tr>";
			for ($j=-1; $j <4 ; $j++) { 
				if($j == -1){
					$data .= "<th>Judul</th>";
				}
				else{
					$data .= "<th>".$tipe[$j]."</th>";
				}
			}
			$data .= "</tr></thead>";
		}		
		else{
			$data .= "<tr>";
			for ($j=-1; $j <4 ; $j++) { 
				if($j == -1){
					$data .= "<td>".$judul[$i]."</td>";
				}
				else{
					$data .= "<td>".$result[$j][$i]."</td>";
				}
			}
			$data .= "</tr>";
		}		
	}

	echo $data;
?>