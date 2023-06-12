<?php
  /**
  Paynow integration id: 12569
  Paynow integration key: 12c032be-541e-44eb-9f8b-75d92c941ad2
  **/
  require 'config/config.php';
  $data = [];

  if (isset($_GET['success'])&&$_GET['success']=='true') {
    echo "Paynow was successful";
  }elseif (isset($_GET['cancel'])&&$_GET['cancel']=='true') {
    echo "Paynow was unsuccessful";
  }
  
  if(isset($_POST['search'])) {
    // Get data from FORM
    $keywords = $_POST['keywords'];
    $location = $_POST['location'];

    //keywords based search
    $keyword = explode(',', $keywords);
    $concats = "(";
    $numItems = count($keyword);
    $i = 0;
    foreach ($keyword as $key => $value) {
      # code...
      if(++$i === $numItems){
         $concats .= "'".$value."'";
      }else{
        $concats .= "'".$value."',";
      }
    }
    $concats .= ")";
  //end of keywords based search
  
  //location based search
    $locations = explode(',', $location);
    $loc = "(";
    $numItems = count($locations);
    $i = 0;
    foreach ($locations as $key => $value) {
      # code...
      if(++$i === $numItems){
         $loc .= "'".$value."'";
      }else{
        $loc .= "'".$value."',";
      }
    }
    $loc .= ")";

  //end of location based search
    
    try {
      //foreach ($keyword as $key => $value) {
        # code...

        $stmt = $connect->prepare("SELECT * FROM room_rental_registrations_apartment WHERE country IN $concats OR country IN $loc OR state IN $concats OR state IN $loc OR city IN $concats OR city IN $loc OR address IN $concats OR address IN $loc OR rooms IN $concats OR landmark IN $concats OR landmark IN $loc OR rent IN $concats OR deposit IN $concats");
        $stmt->execute();
        $data2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $connect->prepare("SELECT * FROM room_rental_registrations WHERE country IN $concats OR country IN $loc OR state IN $concats OR state IN $loc OR city IN $concats OR city IN $loc OR rooms IN $concats OR address IN $concats OR address IN $loc OR landmark IN $concats OR rent IN $concats OR deposit IN $concats");
        $stmt->execute();
        $data8 = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = array_merge($data2, $data8);

    }catch(PDOException $e) {
      $errMsg = $e->getMessage();
    }
  }elseif (isset($_GET['room'])&&isset($_GET['email'])) {
    $room = $_GET['room'];
  //test for valid email.
    if (filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
      $e = $_GET['email'];

      //end of location based search
      
      try {
        //foreach ($keyword as $key => $value) {
          # code...

          $stmt = $connect->prepare("SELECT * FROM room_rental_registrations_apartment");
          $stmt->execute();
          $data2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

          $stmt = $connect->prepare("SELECT * FROM room_rental_registrations");
          $stmt->execute();
          $data8 = $stmt->fetchAll(PDO::FETCH_ASSOC);

          $data = array_merge($data2, $data8);

      }catch(PDOException $e) {
        $errMsg = $e->getMessage();
      }
      //check value of room in database.
      foreach ($data as $key => $value) {
        if ($value['id']==$room) {
          //go to paynow with the amount.
          $a = $value['rent'];
          //fix rent
          $a = ltrim($a,'$');

          $r = 'rent_for_room:_ '.$room;

          $ar = "id=12594&amount=".$a."&f1=".$r."&f2=".$e."&l=1";
          $c = base64_encode ($ar);
          $c = str_replace("=", "%3D", $c);
          $link = "https://www.paynow.co.zw/payment/billpaymentlink/".$e."?q=".$c;
          // $link = "https://www.paynow.co.zw/payment/link/".$e."?q=".$c;
          header("Location: $link");
          exit;
        }
      } 

    }else{
      echo "Error message: Email not valid!!!";
      die();
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>App</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'>

    <!-- Custom styles for this template -->
    <link href="assets/css/rent.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
  </head>

  <body id="page-top">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
      <div class="container">
      <a class="navbar-brand js-scroll-trigger" href="#page-top">N.U.S.T StuRents</a>
        
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          Menu
          <i class="fa fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav text-uppercase ml-auto">
           
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#search">Search</a>
            </li>
            
            <?php 
              if(empty($_SESSION['username'])){
                echo '<li class="nav-item">';
                  echo '<a class="nav-link" href="./auth/login.php">Login</a>';
                echo '</li>';
              }else{
                echo '<li class="nav-item">';
                 echo '<a class="nav-link" href="./auth/dashboard.php">Home</a>';
               echo '</li>';
              }
            ?>
            

            <li class="nav-item">
              <a class="nav-link" href="./auth/register.php">Register</a>
            </li>

          </ul>
        </div>
      </div>
    </nav>

    <!-- Header -->
    <header class="masthead">
      <div class="container">
        <div class="intro-text">
        <div class="intro-heading text-uppercase">Find your next perfect stay...<br></div>
          <!--<div class="intro-lead-in">Welcome To NUST StuRents Registration!</div> -->
          
        </div>
      </div>
    </header>

     <!-- Search -->
    <section id="search">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 text-center">
            <h2 class="section-heading text-uppercase">Search</h2>
            <h3 class="section-subheading text-muted">Search rooms or homes for hire.</h3>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <form action="" method="POST" class="center" novalidate>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <input class="form-control" id="keywords" name="keywords" type="text" placeholder="Key words(Ex: rent..)" required data-validation-required-message="Please enter keywords">
                    <p class="help-block text-danger"></p>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <input class="form-control" id="location" type="text" name="location" placeholder="Location" required data-validation-required-message="Please enter location.">
                    <p class="help-block text-danger"></p>
                  </div>
                </div>         

                <div class="col-md-2">
                  <div class="form-group">
                    <button id="" class="btn btn-success btn-md text-uppercase" name="search" value="search" type="submit">Search</button>
                  </div>
                </div>
              </div>
            </form>

            <?php
              if(isset($errMsg)){
                echo '<div style="color:#FF0000;text-align:center;font-size:17px;">'.$errMsg.'</div>';
              }
              if(count($data) !== 0){
                echo "<h2 class='text-center'>List of Apartment Details</h2>";
              }else{
                //echo "<h2 class='text-center' style='color:red;'>Try Some other keywords</h2>";
              }
            ?>        
            <?php 
                $b_count = 0;
                foreach ($data as $key => $value) {
                  $b_count +=1;           
                  echo '<div class="card card-inverse card-info mb-3" style="padding:1%;">          
                        <div class="card-block">';
                          // echo '<a class="btn btn-warning float-right" href="update.php?id='.$value['id'].'&act=';if(isset($value['ap_number_of_plats'])){ echo "ap"; }else{ echo "indi"; } echo '">Edit</a>';
                         echo   '<div class="row">
                            <div class="col-4">
                            <h4 class="text-center">Owner Details</h4>';
                              echo '<p><b>Owner Name: </b>'.$value['fullname'].'</p>';
                              echo '<p><b>Mobile Number: </b>'.$value['mobile'].'</p>';
                              echo '<p><b>Alternate Number: </b>'.$value['alternat_mobile'].'</p>';
                              echo '<p><b>Email: </b>'.$value['email'].'</p>';
                              
                              if ($value['image'] !== 'uploads/') {
                                # code...
                                echo '<img src="app/'.$value['image'].'" width="100">';
                              }

                          echo '</div>
                            <div class="col-5">
                            <h4 class="text-center">Room Details</h4>';
                              // echo '<p><b>Country: </b>'.$value['country'].'<b> State: </b>'.$value['state'].'<b> City: </b>'.$value['city'].'</p>';
                              echo '<p><b>Address: </b>'.$value['address'].'</p>';
                              echo '<p><b>Monthly Rent: </b>'.$value['rent'].'</p>';
                              echo '<p><b>Deposit: </b>'.$value['deposit'].'</p>';

                              if(isset($value['sale'])){
                                echo '<p><b>Sale: </b>'.$value['sale'].'</p>';
                              } 
                              
                                if(isset($value['apartment_name']))                         
                                  echo '<div class="alert alert-success" role="alert"><p><b>Apartment Name: </b>'.$value['apartment_name'].'</p></div>';

                                if(isset($value['ap_number_of_plats']))
                                  echo '<div class="alert alert-success" role="alert"><p><b>Plat Number: </b>'.$value['ap_number_of_plats'].'</p></div>';

                             
                          echo '</div>
                            <div class="col-3">
                            <h4>Other Details</h4>';
                            
                            
                            echo '<p><b>Description: </b>'.$value['description'].'</p>';
                            
                              if($value['vacant'] == 0){ 
                                echo '<div class="alert alert-danger" role="alert"><p><b>Occupied</b></p></div>';
                              }
                              
                              elseif($_SESSION['role'] == 'admin'){
                                //do not  butto
                                echo ' <div class="alert alert-success" role="alert">
                                <p><b>Vacant</b></p>
                              </div>';
                              }

                              else{
                                echo 
                                '
                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal'.$b_count.'" tabindex="-1" aria-labelledby="exampleModalLabel'.$b_count.'" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel'.$b_count.'">Pay with Paynow</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                        </button>
                                      </div>

                                      <div class="modal-body">
                                         <div class="row pl-2 pb-2">
                                            <img src="icons/Paynow-Badge-vector-hires.svg" alt="" width="100%" height="100%" title="paynow" class="mx-auto">
                                          </div>
                                          <div class="row pl-4">
                                            
                                            <form action="" method="post" >
                                              <label for="exampleFormControlInput'.$b_count.'" class="form-label">Email Address</label>
                                              <input class="form-control pb-2" type="email" placeholder="Email address" id="exampleFormControlInput'.$b_count.'">
                                              <br>
                                              <a href="index.php?room='.$value['id'].'&email=" id="paynow_js'.$b_count.'" type="button" class="btn btn-danger pt-2">Pay now</a>
                                            </form>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <!-- End modal -->


                                <div class="alert alert-success" role="alert">
                                  <p><b>Vacant</b></p>
                                </div>
                                <div class="row pt-1 pl-3">
                                  <button class="btn btn-success" data-toggle="modal" data-target="#exampleModal'.$b_count.'">
                                    Book Now
                                  </button>
                                </div>';
                              } 
                            echo '</div>
                          </div>              
                         </div>
                      </div>';
                }
              ?> 
              
              
              
          </div>
        </div>
      </div>
      <br><br><br><br><br><br>
    </section>    

    <!-- Footer -->
    <footer style="background-color: #ccc;">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <span class="copyright">Copyright &copy; NUST StuRents</span>
          </div>
          <div class="col-md-4">
            <ul class="list-inline social-buttons">
              <li class="list-inline-item">
                <a href="#">
                  <i class="fa fa-twitter"></i>
                </a>
              </li>
              <li class="list-inline-item">
                <a href="#">
                  <i class="fa fa-facebook"></i>
                </a>
              </li>
              <li class="list-inline-item">
                <a href="#">
                  <i class="fa fa-linkedin"></i>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
   
    <!-- Bootstrap core JavaScript -->
    <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="assets/plugins/jquery-easing/jquery.easing.min.js"></script>

    <!-- Contact form JavaScript -->
    <script src="assets/js/jqBootstrapValidation.js"></script>
    <script src="assets/js/contact_me.js"></script>

    <!-- Custom scripts for this template -->
    <script src="assets/js/rent.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){

        <?php
        $b_count = 50;
        for ($i=0; $i < $b_count; $i++) { 
        
          echo '
            $(document).on("click","#paynow_js'.$i.'", function(event){
                event.preventDefault();
                var email = $("#exampleFormControlInput'.$i.'").val();
                //check email
                var oldUrl = $("#paynow_js'.$i.'").attr("href");
                var newUrl = oldUrl+email;
                window.location.href = newUrl;
            });';
        }

        ?>

        //end of ready
      });
    </script>
  </body>
</html>

