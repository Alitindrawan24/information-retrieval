<?php
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'koneksi.php';
	require_once 'stemmer.php';	
	
	$input = file_get_contents('data/stop_words.txt'); //Proses pengambilan stopwords
	$stop_words = explode("\n",$input);	

	for($x=1;$x<=2;$x++){
		$d[$x] = 0;
		$file = file_get_contents(__DIR__."/data/".$_POST['doc_'.$x]);
		$file = strtolower($file);
		$word = preg_split("/[\s,.:;-_()!@#$%^&*?<>'–|0123456789]+/",$file);
		$result = [];
		for($i=0;$i<count($word);$i++){
			if(isset($word[$i])){
				if(!in_array($word[$i], $stop_words))
					array_push($result, $stemmer->stem($word[$i]));				
			}
		}	

		$temp['kata'][$x] = [];
		$temp['jumlah'][$x] = [];
		$c = 0;

		for ($i=0; $i < count($result); $i++) { 
			if($result[$i] != ''){
				if(in_array($result[$i], $temp['kata'][$x])){
					$tmp = array_search($result[$i], $temp['kata'][$x]);
					$temp['jumlah'][$x][$tmp]++;
				}
				else{

					$temp['kata'][$x][$c] = $result[$i];
					$temp['jumlah'][$x][$c] = 1;
					$c++;
				}				
			}
		}

		for ($i=0; $i < $c; $i++) { 
			$d[$x] += ($temp['jumlah'][$x][$i]*$temp['jumlah'][$x][$i]);
		}
	}
	$hasil = array_intersect($temp['kata'][1],$temp['kata'][2]);

	$tabel = "<tr>
		<th>Kata</th>
		<th>d1</th>
		<th>d2</th>
		<th>d1*d2</th>
	</tr>";
	$total = 0;
	// $d1 = 0;
	// $d2 = 0;
	foreach ($hasil as $idx => $val) {
		$idx2 = array_search($val, $temp['kata'][2]);
		$tabel .= "<tr>";
		$tabel .= "<td>".$val."</td>";
		$tabel .= "<td>".$temp['jumlah'][1][$idx]."</td>";
		$tabel .= "<td>".$temp['jumlah'][2][$idx2]."</td>";
		$tabel .= "<td>".$temp['jumlah'][1][$idx] * $temp['jumlah'][2][$idx2]."</td>";
		$tabel .= "</tr>";
		$total += $temp['jumlah'][1][$idx] * $temp['jumlah'][2][$idx2];
		// $d1 += ($temp['jumlah'][1][$idx]*$temp['jumlah'][1][$idx]);
		// $d2 += ($temp['jumlah'][2][$idx2]*$temp['jumlah'][2][$idx2]);
	}
	$tabel .= "<tr>
		<th>Total</th>
		<th></th>
		<th></th>
		<th>".$total."</th>
	</tr>";
	$tabel .= "<tr>
		<th>||d1||</th>
		<th></th>
		<th></th>
		<th>".sqrt($d[1])."</th>
	</tr>";
	$tabel .= "<tr>
		<th>||d2||</th>
		<th></th>
		<th></th>
		<th>".sqrt($d[2])."</th>
	</tr>";
	$tabel .= "<tr>
		<th>cos(d1,d2)</th>
		<th></th>
		<th></th>
		<th>".($total/sqrt($d[1] * $d[2]))."</th>
	</tr>";
	echo $tabel;
	// echo print_r($hasil);
?>