<?php
  session_start();
  include ('config/db_connect.php');
  
  if ($_SESSION['login'] != TRUE){
  header('Location: login.php');
  }

  $community_name = $city_name = $num_of_collabs = $location = '';
  $errors = array('community_name' => '', 'city_name' => '','num_of_collabs' => '', 'location' => '');

  //get all cities in database
  $community_sql = "SELECT * FROM communities";
  $community_result = mysqli_query($db, $community_sql);

  if(isset($_POST['add'])){

    //Checks for valid entry
    if(empty($_POST['community_name'])){
      $errors['community_name'] = 'Please enter a Community <br />';
    } else {
      $community_name = $_POST['community_name'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $community_name)){
        $errors['community_name'] = 'Please enter a valid community name <br />';
      }
    }

    if(empty($_POST['city_name'])){
      $errors['city_name'] = 'Please enter a City <br />';
    } else {
      $city_name = $_POST['city_name'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $city_name)){
        $errors['city_name'] = 'Please enter a valid City <br />';
      }

    }

    if(empty($_POST['num_of_collabs'])){
      $errors['num_of_colabs'] = 'Please enter the number of collaborations your community has <br />';
    } else {
      $num_of_collabs = $_POST['num_of_collabs'];
      if(!preg_match('/^[0-9\s]+$/', $num_of_collabs)){
        $errors['num_of_collabs'] = 'Please enter a valid number of collaborations/>';
      }
    }

    if(empty($_POST['location'])){
      $errors['location'] = 'Please enter where your community is located <br />';
    } else {
      $location = $_POST['location'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $location)){
        $errors['location'] = 'Please enter a valid location <br />';
      }
    }

    if($location != "North" && $location != "East" && $location != "South" && $location != "West" && $location != "North East" && $location != "South East" && $location != "North West" && $location != "South West") {
      $errors['location'] = 'Please enter one of the following: North, East, South, West, North East, South East, North West, or South West';
    }

    //bring to home page if no errors
    if(array_filter($errors)){
      //echo 'there are errors in the form';
    } else {
      //enter task into database
      $community_name = mysqli_real_escape_string($db, $_POST['community_name']);
      $city_name = mysqli_real_escape_string($db, $_POST['city_name']);
      $num_of_collabs = mysqli_real_escape_string($db, $_POST['num_of_collabs']);
      $location = mysqli_real_escape_string($db, $_POST['location']);
      
          //json for addcommunity
      $addcom_results = array ('com_name' => '', 'city_name' => '', 'num_collabs' =>'', 'location' =>'');
      $addcom_results['com_name'] = $community_name;
      $addcom_results['city_name'] = $city_name;
      $addcom_results['num_collabs'] = $num_of_collabs;
      $addcom_results['location'] = $location;
      $addcom_json = json_encode($addcom_results);
      
      $_SESSION['json'] = $addcom_json;
      $_SESSION['request'] = 'addcommunity';

      header('Location: adminindex.php');

    }


  }

?>



<!DOCTYPE html>
<html>
  <?php include ('templates/adminheader.php'); ?>

  <h3 class = 'center'>Communities:</h3>
    <section class="container grey-text">
      <form class="white">
        <?php
        //https://www.w3schools.com/php/php_mysql_select.asp
        //source for table format

            //create table
            if (mysqli_num_rows($community_result) > 0) {
               echo "<table><tr><th>Community Name</th><th>City Name</th><th>Number of Collaborations</th><th>Location in the City</th></tr>";
              while($row = mysqli_fetch_row($community_result)) {
                echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[7]."</td></tr>";
              }
              echo "</table>";
            } else {
              echo "0 results";
            }
        ?>
      </form>
    </section>
    <h2 class="center"> Add a Community:</h2>

    <section class="container grey-text">
     <form class="white" action="addcommunity.php" method="POST">

          <label class = "black-text"> Community Name : </label>
          <input type="text" name="community_name" placeholder="eg. Westhills" value="<?php echo htmlspecialchars($community_name) ?>">
          <div class = "red-text"><?php echo $errors['community_name'] ?></div>

          <label class = "black-text"> City Name : </label>
          <input type="text" name="city_name" placeholder="Calgary" value="<?php echo htmlspecialchars($city_name) ?>">
          <div class = "red-text"><?php echo $errors['city_name'] ?></div>

          <label class = "black-text"> Number of Collaborations : </label>
          <input type="number" name="num_of_collabs" placeholder="1" value="<?php echo htmlspecialchars($num_of_collabs) ?>">
          <div class = "red-text"><?php echo $errors['num_of_collabs'] ?></div>

          <label class = "black-text"> Location in City </label>
          <input type="text" name="location" placeholder="South West" value="<?php echo htmlspecialchars($location) ?>">
          <div class = "red-text"><?php echo $errors['location'] ?></div>

          <div class="center">
            <input type="submit" name="add" value="add" class="btn brand z-depth-0">
          </div>


      </form>
    </section>

  <?php include ('templates/adminfooter.php'); ?>
</html>
