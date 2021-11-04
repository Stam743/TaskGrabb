<?php
  session_start();
  include ('config/db_connect.php');
  
    if ($_SESSION['login'] != TRUE){
    header('Location: login.php');
    }

  $city_name = $num_of_coms = '';
  $errors = array('city_name' => '','num_of_coms' => '');

   //get all cities in database
   $city_sql = "SELECT * FROM city";
   $city_result = mysqli_query($db, $city_sql);

  if(isset($_POST['add'])){

    if(empty($_POST['city_name'])){
      $errors['city_name'] = 'Please enter a City <br />';
    } else {
      $city_name = $_POST['city_name'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $city_name)){
        $errors['city_name'] = 'Please enter a valid City <br />';
      }

    }

    if(empty($_POST['num_of_coms'])){
      $errors['num_of_coms'] = 'Please enter the number of communities your city has <br />';
    } else {
      $num_of_coms = $_POST['num_of_coms'];
      if(!preg_match('/^[0-9\s]+$/', $num_of_coms)){
        $errors['num_of_coms'] = 'Please enter a valid number of communitiess/>';
      }
    }

    //bring to home page if no errors
    if(array_filter($errors)){
      //echo 'there are errors in the form';
    } else {
      //enter task into database
      $city_name = mysqli_real_escape_string($db, $_POST['city_name']);
      $num_of_coms = mysqli_real_escape_string($db, $_POST['num_of_coms']);
      
      $addcity_results = array ('city_name' => '', 'num_com' =>'');
      $addcity_results['city_name'] = $city_name;
      $addcity_results['num_com'] = $num_of_coms;
      $addcity_json = json_encode($addcity_results);
    
      $_SESSION['json'] = $addcity_json;
      $_SESSION['request'] = 'addcity';

      
      header('Location: adminindex.php');
     

    }
  
    



  }

?>



<!DOCTYPE html>
<html>
  <?php include ('templates/adminheader.php'); ?>
  <h3 class = 'center'>Cities:</h3>
    <section class="container grey-text">
      <form class="white">
        <?php
        //https://www.w3schools.com/php/php_mysql_select.asp
        //source for table format

            //create table
            if (mysqli_num_rows($city_result) > 0) {
               echo "<table><tr><th>City Name</th><th>Number of Communities</th></tr>";
              while($row = mysqli_fetch_row($city_result)) {
                echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[7]."</td></tr>";
              }
              echo "</table>";
            } else {
              echo "0 results";
            }
        ?>
      </form>
    </section>

    <h2 class="center"> Add a City:</h2>

    <section class="container grey-text">
     <form class="white" action="addcity.php" method="POST">

          <label class = "black-text"> City Name : </label>
          <input type="text" name="city_name" placeholder="Calgary" value="<?php echo htmlspecialchars($city_name) ?>">
          <div class = "red-text"><?php echo $errors['city_name'] ?></div>

          <label class = "black-text"> Number of Communities : </label>
          <input type="number" name="num_of_coms" placeholder="1" value="<?php echo htmlspecialchars($num_of_coms) ?>">
          <div class = "red-text"><?php echo $errors['num_of_coms'] ?></div>

          <div class="center">
            <input type="submit" name="add" value="add" class="btn brand z-depth-0">
          </div>


      </form>
    </section>

  <?php include ('templates/adminfooter.php'); ?>
</html>
