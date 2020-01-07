<?php

	require __DIR__.'\vendor\autoload.php';
	use Spatie\PdfToText\Pdf;	

	$path = 'c:/Program Files/Git/mingw64/bin/pdftotext';	

	// $pdf = Pdf::getText('data/45001-181-102316-1-10-20190317.pdf', $path, ['layout', 'x 96']);	
	$pdf = (new Pdf($path))
		    ->setPdf('data/4.pdf')		    
		    ->text();


	$pdf = str_replace('', "<br><br>", $pdf);
	// $pdf = str_replace("\n", "<br>", $pdf);
	// $pdf = str_replace('p-ISSN', '', $pdf);
	$del = ["p-ISSN","Jurnal Elektronik Ilmu Komputer","e-ISSN"];
	

	for ($i=0; $i <count($del) ; $i++) { 

		$temp = strpos($pdf, $del[$i]);
		if($temp != null){
			while($pdf[$temp] != "\n"){
				$temp++;
			}
			
			$text = cut_string_between($pdf,strpos($pdf, $del[$i]), $temp);
			$pdf = str_replace($text, '', $pdf);
		}
	}	

	$pdf = str_replace("\n", "<br>", $pdf);

	echo $pdf;

	function get_string_between($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}

	function cut_string_between($string, $start, $end){
		$text = '';
		for ($i=$start; $i <= $end ; $i++) { 
			$text .= $string[$i];
		}

		return $text;
	}

