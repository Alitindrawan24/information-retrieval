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
		for ($j=0; $j < count($cari) ; $j++) { 
			$query = mysqli_query($conn,
				"SELECT judul,kata,jumlah,DF FROM relasi				
				LEFT JOIN artikel ON id_artikel = artikel_id
				WHERE kata = '$cari[$j]' AND artikel_id = '$id_artikel[$i]'"
			);
			if(mysqli_fetch_assoc($query) > 0){
				$query = mysqli_query($conn,
					"SELECT judul,kata,jumlah, DF FROM relasi					
					LEFT JOIN artikel ON id_artikel = artikel_id
					WHERE kata = '$cari[$j]' AND artikel_id = '$id_artikel[$i]'"
				);
				while($row = mysqli_fetch_assoc($query)){
					$jumlah[$i][$j] = $row['jumlah'];
					$df[$i][$j] = $row['DF'];
				}
			}
			else{
				$jumlah[$i][$j] = 0;
				$query = mysqli_query($conn,
					"SELECT DF FROM relasi										
					WHERE kata = '$cari[$j]'"
				);
				while($row = mysqli_fetch_assoc($query)){					
					$df[$i][$j] = $row['DF'];					
				}				
			}			
		}
	}		
	$tfidf = [];
	$total = [];
	for ($i=0; $i < $c; $i++) { 
		$tfidf[$i] = [];		
		$total[$i] = 0;
		for ($j=0; $j < count($cari) ; $j++) {
			//Perhitungan TF IDF			
			if($jumlah[$i][$j] > 0){
				$jumlah[$i][$j] = (log($jumlah[$i][$j])+1);
				$tfidf[$i][$j] = $jumlah[$i][$j] * log($c/$df[$i][$j]);
			}
			else{
				$jumlah[$i][$j] = 0;
				$tfidf[$i][$j] = 0;
			}
						

			$total[$i] += $tfidf[$i][$j];
		}
	}

	for ($i=0; $i < $c; $i++) { 
		for ($j=$i+1; $j < $c; $j++){
			if($total[$j] > $total[$i]){
				$temp = $total[$j];
				$total[$j] = $total[$i];
				$total[$i] = $temp;

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

	for ($i=-1; $i <count($id_artikel) ; $i++) { 
		if($i == -1){
			$data .= "<tr>";
			for ($j=-1; $j <=count($cari) ; $j++) { 
				if($j == -1){
					$data .= "<th></th>";
				}
				else if($j == count($cari)){
					$data .= "<th>TOTAL</th>";	
				}
				else
					$data .= "<th>".$cari[$j]."</th>";
			}
			$data .= "</tr>";
		}
		else{
			$data .= "<tr>";
			for ($j=-1; $j <=count($cari) ; $j++) { 
				if($j == -1){
					$data .= "<td>".$judul[$i]."</td>";
				}
				else if($j == count($cari)){
					$data .= "<th>".$total[$i]."</th>";	
				}
				else
					$data .= "<td>".$tfidf[$i][$j]."</td>";
			}
			$data .= "</tr>";
		}
	}

	echo $data;
?>