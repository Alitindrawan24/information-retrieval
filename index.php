<!DOCTYPE html>
<html>
<head>
	<title>Information Retrieval</title>
</head>
<body>

</body>
</html>

<?php	
	// $file = file_get_contents('data/file1.txt'); //Proses pengambilan file text
	// $file = strtolower($file);	//Proses case folding
	// $word = preg_split("/[\s,.:;-_()!@#$%^&*?<>'–|0123456789]+/",$file); //Proses pemecehan text menjadi kata
	
	// $input = file_get_contents('data/stop_words.txt'); //Proses pengambilan stopwords
	// $stop_words = explode("\n",$input);
	
	// $word = array_unique($word); //Menghilangkan kata yang sama atau lebih dari 1
	// for($i=0;$i<count($word);$i++){
	// 	if(isset($word[$i])){
	// 		if(!in_array($word[$i], $stop_words)) // Pengecekan kata dengan stopwords
	// 				echo $word[$i]."<br>";
	// 	}
	// }

	require_once __DIR__ . '/vendor/autoload.php';

	// create stemmer
	// cukup dijalankan sekali saja, biasanya didaftarkan di service container
	$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
	$stemmer  = $stemmerFactory->createStemmer();

	// stem
	$sentence = 'Perekonomian Indonesia sedang dalam pertumbuhan yang membanggakan';
	$output   = $stemmer->stem($sentence);

	// echo $output . "\n";
	// ekonomi indonesia sedang dalam tumbuh yang bangga

	echo $stemmer->stem('Mereka meniru-nirukannya') . "\n";

?>