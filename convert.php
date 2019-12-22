<?php

	require __DIR__.'\vendor\autoload.php';
	use Spatie\PdfToText\Pdf;

	$path = 'c:/Program Files/Git/mingw64/bin/pdftotext';	

	// $pdf = Pdf::getText('data/45001-181-102316-1-10-20190317.pdf', $path, ['layout', 'x 96']);
	$pdf = (new Pdf($path))
		    ->setPdf('data/44996-181-102314-1-10-20190317.pdf')
		    ->text();
	// $pdf = str_replace('', "<br><br>", $pdf);	
	$pdf = str_replace("\n", "ntr123", $pdf);	
	$pdf = strtolower($pdf);
	// $pdf = substr($pdf, strpos($pdf, 'abstract'));

	$pdf = get_string_between($pdf, 'abstrak', '');
	$pdf = get_string_between($pdf, 'ntr123', 'ntr123');

	echo $pdf;

	function get_string_between($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}