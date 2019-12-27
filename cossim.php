<?php
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'koneksi.php';
	require_once 'stemmer.php';	
	
	$input = file_get_contents('data/stop_words.txt'); //Proses pengambilan stopwords
	$stop_words = explode("\n",$input);	
	$max = 2;

	for($x=1;$x<=2;$x++){
		$d[$x] = 0;		
		$id = $_POST['doc_'.$x];		
		$temp['kata'][$x] = [];
		$temp['jumlah'][$x] = [];
		$temp['df'][$x] = [];
		$temp['ap'][$x] = [];

		//Mengambil semua data kata, tf, dan df dari database
		$query = mysqli_query($conn,"SELECT * FROM relasi WHERE artikel_id = $id");
		while($row=mysqli_fetch_assoc($query)){
			array_push($temp['kata'][$x], $row['kata']);
	    	array_push($temp['jumlah'][$x], $row['jumlah']);
	    	array_push($temp['df'][$x], $row['DF']);
	    	array_push($temp['ap'][$x], $row['AP']);

	    	//Menghitung nilai tfidf dari semua kata perdokumen
	    	$d[$x] += (($row['jumlah']) *($max/$row['DF'])) * (($row['jumlah']) *($max/$row['DF']));
		}	
	}

	//Proses mencari kata-kata yang sama antara 2 dokumen
	$hasil = array_intersect($temp['kata'][1],$temp['kata'][2]);

	$tabel = "<tr>
		<th>Kata</th>
		<th>d1</th>
		<th>d2</th>
		<th>d1*d2</th>
	</tr>";
	$total = 0;	
	foreach ($hasil as $idx => $val) {
		$idx2 = array_search($val, $temp['kata'][2]);

		//Mendhitung tfidf untuk kata yang sama
		$tfidf[1] = (($temp['jumlah'][1][$idx]) * ($max/$temp['df'][1][$idx]));
		$tfidf[2] = (($temp['jumlah'][2][$idx2]) * ($max/$temp['df'][2][$idx2]));

		$tabel .= "<tr>";
		$tabel .= "<td>".$val."</td>";
		$tabel .= "<td>".$tfidf[1]."</td>";
		$tabel .= "<td>".$tfidf[2]."</td>";
		$tabel .= "<td>".$tfidf[1]*$tfidf[2]."</td>";
		$tabel .= "</tr>";

		//Menghitung total tfidf
		$total += $tfidf[1]*$tfidf[2];
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
?>