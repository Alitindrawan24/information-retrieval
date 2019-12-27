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
          <form method="post" id="form" name="form">
            <div class="form-row">
              <div class="col-12 col-md-9 mb-2 mb-md-0">
                <input type="text" class="form-control form-control-lg" placeholder="Masukkan kata yang ingin dicari..." name="cari">
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
            url : 'cari.php',
            data : data,
            success : function(data){               
              $('.table').empty();
              $('.table').append(data);
              $('.table').DataTable();
            }
        });
        e.preventDefault();
      });
  });
</script>