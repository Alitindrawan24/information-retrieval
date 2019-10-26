<?php include 'header.php'; ?>
<?php
	include 'koneksi.php';
	$query = mysqli_query($conn,'SELECT * FROM artikel');
	$data = [];		
	while($row=mysqli_fetch_assoc($query)){		
		array_push($data, $row['judul']);
	}
?>

  <!-- Masthead -->
  <header class="masthead text-white text-center">
    <div class="overlay"></div>
    <div class="container">
      <div class="row">
        <div class="col-xl-9 mx-auto">
          <h1 class="mb-5">Build a landing page for your business or project and generate more leads!</h1>
        </div>
        <div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
          <form method="post" id="form" name="form">
          	<label>Pilih dokumen</label>
            <div class="form-row">            
              <div class="col-6 col-md-6 mb-2 mb-md-0">
                <select name="doc_1" class="form-control" required>
                	<option selected disabled hidden value="">Pilih artikel</option>
                	<?php for($i=0;$i<count($data);$i++): ?>
                		<option value="<?php echo $data[$i] ?>"><?php echo $data[$i]; ?></option>
                	<?php endfor; ?>
                </select>                
              </div>
              <div class="col-6 col-md-6 mb-2 mb-md-0">
                <select name="doc_2" class="form-control" required>
                	<option selected disabled hidden value="">Pilih artikel</option>
                	<?php for($i=0;$i<count($data);$i++): ?>
                		<option value="<?php echo $data[$i] ?>"><?php echo $data[$i]; ?></option>
                	<?php endfor; ?>
                </select>
              </div>
              <div class="col-12 col-md-3 centre">
                <button type="submit" class="btn btn-block btn-lg btn-primary">Bandingkan</button>
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
          <table class="table" style="text-align: center;">
            
          </table>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>
</body>
</html>

<script type="text/javascript">
  $(function(){
    $('#form').submit(function(e){
        var data = $(this).serializeArray();        
          $.ajax({
            method : 'POST',            
            url : 'cossim.php',
            data : data,
            success : function(data){  
              $('.table').empty();
              $('.table').append(data);
            }
        });
        e.preventDefault();
      });
  });
</script>