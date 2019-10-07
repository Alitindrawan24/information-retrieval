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
        <form id="form" enctype="multipart/form-data" method="post">
          <div class="form-row">
            <div class="col-12 col-md-9 mb-2 mb-md-0">
              <input type="file" class="form-control form-control-lg" name="file" id="file">
            </div>
            <div class="col-12 col-md-3">
              <button type="submit" class="btn btn-block btn-lg btn-primary">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</header>  

<section style="margin-top: 50px;display: none;" id="hasil">
  <div class="container">
    <div class="row">
      <div class="col-xl-6 mx-auto">
        <center><h2>Hasil Pengolahan File</h2></center>
        <table class="table" style="text-align: center;">
          <tr>
            <th>Terms</th>
            <th>Jumlah Terms</th>            
          </tr>
        </table>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>

<!-- <script type="text/javascript">
  $(document).ready(function(){
    $('html').animate({
      scrollTop : $('#hasil').offset().top
    }, 2000);
  });
</script> -->

<script type="text/javascript">
  $(function(){
    $('#form').submit(function(e){
      var data = new FormData();
      data.append('file', $('#file')[0].files[0]);
      $.ajax({
          method : 'POST',          
          cache : false,
          contentType : false,
          processData : false,
          url : 'proses.php',
          data : data,
          success : function(data){

          }
      });
      e.preventDefault();
    });    
  });
</script>