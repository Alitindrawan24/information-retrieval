<?php			
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'koneksi.php';
	require_once 'stemmer.php';		
	use Spatie\PdfToText\Pdf;	

	$path = 'c:/Program Files/Git/mingw64/bin/pdftotext';	
	
	// $file = file_get_contents($_FILES['file']['tmp_name']); //Proses pengambilan file text		
	$pdf = (new Pdf($path))
		    ->setPdf('data/'.$_FILES['file']['name'])
		    ->text();

	$del = ["p-ISSN","Jurnal Elektronik Ilmu Komputer","e-ISSN"];

	for ($i=0; $i <count($del) ; $i++) { 
		if(strpos($pdf, $del[$i])){
			$temps = strpos($pdf, $del[$i]);
			if($temps != null){
				while($pdf[$temps] != "\n"){
					$temps++;
				}
				
				$text = cut_string_between($pdf,strpos($pdf, $del[$i]), $temps);
				$pdf = str_replace($text, '', $pdf);
			}
		}
	}	

	$file = $pdf;	

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

	// $result = array_filter(array_map('trim', $result));

	// return print_r($result);

	$query = mysqli_query($conn,"INSERT INTO artikel VALUES('','".$_FILES['file']['name']."')");
	$id_artikel = $conn->insert_id;

	$temp['kata'] = [];
	$temp['jumlah'] = [];
	$temp['ap'] = [];
	$temp['fp'] = [];
	$temp['lp'] = [];

	$ap_status = [];
	$ap_value = [];
	$fp_value = [];
	$lp_value = [];

	$c = 0;
	
	// Perhitungan AP
	for ($i=0; $i < count($result); $i++) { 
		if($result[$i] != ''){
			$firstPosition = 0;
			$lastPosition = 0;
			$AP = 0;

			if(!isset($ap_status[$result[$i]])){
				$firstPosition = ($i+1);
				$firstPosition = (($firstPosition+1)/(count($result) - $firstPosition));
				$lastPosition = (count($result) - (count($result) - (array_search($result[$i], array_reverse($result)))));				
				$lastPosition = (($lastPosition+1)/(count($result) - $lastPosition));
				$AP = 1 / ($firstPosition + $lastPosition);				;

				$ap_status[$result[$i]] = true;
				$ap_value[$result[$i]] = $AP;
				$fp_value[$result[$i]] = 1 / ($firstPosition);
				$lp_value[$result[$i]] = 1 / ($lastPosition);

			}

		}
	}

	// return print_r($ap_value);

	for ($i=0; $i < count($result); $i++) { 
		if($result[$i] != ''){
			if(in_array($result[$i], $temp['kata'])){
				$tmp = array_search($result[$i], $temp['kata']);			
				$temp['jumlah'][$tmp]++;
			}
			else{
				$temp['kata'][$c] = $result[$i];
				$temp['jumlah'][$c] = 1;
				$temp['ap'][$c] = $ap_value[$result[$i]];
				$temp['fp'][$c] = $fp_value[$result[$i]];
				$temp['lp'][$c] = $lp_value[$result[$i]];
				$c++;
			}
		}
	}	

	$data = "";	

	for($i=0;$i<$c;$i++){		
		$query = mysqli_query($conn, "INSERT INTO relasi VALUES('".$temp['kata'][$i]."',".$id_artikel.",".$temp['jumlah'][$i].",0,".$temp['ap'][$i].",".$temp['fp'][$i].",".$temp['lp'][$i].")");

		$max = mysqli_query($conn, "SELECT count(kata) FROM relasi WHERE kata = '".$temp['kata'][$i]."'");

		$max = mysqli_fetch_array($max,MYSQLI_NUM);
	
		$query = mysqli_query($conn,"UPDATE relasi set DF = ".$max[0]." WHERE kata = '".$temp['kata'][$i]."'");

		$data .= "<tr>";
		$data .= "<td>".$temp['kata'][$i]."</td>";
		$data .= "<td>".$temp['jumlah'][$i]."</td>";		
		$data .= "</tr>";

	}

	mysqli_query($conn,"DELETE FROM relasi WHERE DF = 0");


	echo $data;

	function cut_string_between($string, $start, $end){
		$text = '';
		for ($i=$start; $i <= $end ; $i++) { 
			$text .= $string[$i];
		}

		return $text;
	}
?>
