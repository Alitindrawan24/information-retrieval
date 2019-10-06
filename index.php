<?php include 'header.php'; ?>

  <!-- Masthead -->
  <header class="masthead text-white text-center">
    <div class="overlay"></div>
    <div class="container">
      <div class="row">
        <div class="col-xl-9 mx-auto">
          <h1 class="mb-5">Build a landing page for your business or project and generate more leads!</h1>
        </div>
        <div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
          <form>
            <div class="form-row">
              <div class="col-12 col-md-9 mb-2 mb-md-0">
                <input type="email" class="form-control form-control-lg" placeholder="Masukkan kata yang ingin dicari...">
              </div>
              <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-block btn-lg btn-primary">Cari</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </header>  

  <section>    
    <div class="container">
      <div class="row">
        <div class="col-xl-12 mx-auto">
          <table class="table">
            <tr style="text-align: center;">
              <th>Terms 1</th>
              <th>Terms 2</th>
              <th>Terms 3</th>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>
</body>
</html>

<?php	
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'koneksi.php';
	require_once 'stemmer.php';
	
	// stem
	$sentence = 'Perekonomian Indonesia sedang dalam pertumbuhan yang membanggakan';
	$output   = $stemmer->stem($sentence);		

	$file = file_get_contents('data/file1.txt'); //Proses pengambilan file text	
	$file = strtolower($file);	//Proses case folding	
	$word = preg_split("/[\s,.:;-_()!@#$%^&*?<>'–|0123456789]+/",$file); //Proses pemecehan text menjadi kata	
	
	$input = file_get_contents('data/stop_words.txt'); //Proses pengambilan stopwords
	$stop_words = explode("\n",$input);	
	
	// $word = array_unique($word); //Menghilangkan kata yang sama atau lebih dari 1
	for($i=0;$i<count($word);$i++){
		if(isset($word[$i])){
			// if(!in_array($word[$i], $stop_words)) // Pengecekan kata dengan stopwords
					// echo $word[$i]."<br>";
			// echo $stemmer->stem($word[$i]) . "<br>";
		}
	}

?>