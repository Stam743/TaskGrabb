<?php
  session_start();
  include ('config/db_connect.php');
  
  if ($_SESSION['login'] != TRUE){
  header('Location: login.php');
  }

  $memberid = $community_name = $start_date = '';
  $errors = array('memberid' => '','community_name' => '', 'start_date' => '');

   //get all admins in database
   $admin_sql = "SELECT * FROM community_leader";
   $admin_result = mysqli_query($db, $admin_sql);

   //get all members in database
   $member_sql = "SELECT * FROM memberlist";
   $member_result = mysqli_query($db, $member_sql);

   //get all communities in database
   $community_sql = "SELECT * FROM communities";
   $community_result = mysqli_query($db, $community_sql);

  if(isset($_POST['add'])){

    if(empty($_POST['memberid'])){
      $errors['memberid'] = 'Please enter a memberid <br />';
    } else {
      $memberid = $_POST['memberid'];
      if(!preg_match('/^[0-9\s]+$/', $memberid)){
        $errors['memberid'] = 'Please enter a valid member <br />';
      }

    }

    $check_member_id = $_POST['memberid'];
    $memberid_sql = "SELECT MemberID FROM memberlist";
    $results = mysqli_query($db, $memberid_sql);
    $members = mysqli_fetch_all($results, MYSQLI_ASSOC);

    //by default has error
    $errors['memberid'] = 'Please enter a valid Member ID<br/>';

    foreach($members as $member){
          //Checks if the memberID given is a valid member ID
        if($member['MemberID'] == $check_member_id){
            //If it's a successful validation, the error will be cleared and thus it would be validated
            $errors['memberid'] = '';
          }
    }

    if(empty($_POST['community_name'])){
      $errors['community_name'] = 'Please enter the name of the community<br />';
    } else {
      $community_name = $_POST['community_name'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $community_name)){
        $errors['community_name'] = 'Please enter a valid community name/>';
      }
    }
    $check_community_name = $_POST['community_name'];
    $com_sql = "SELECT Community_Name FROM communities";
    $com_results = mysqli_query($db, $com_sql);
    $communities = mysqli_fetch_all($com_results, MYSQLI_ASSOC);

    //by default has error
    $errors['community_name'] = 'Please enter a Community in the Database<br/>';

    foreach($communities as $community){
          //Checks if the memberID given is a valid member ID
        if($community['Community_Name'] == $check_community_name){
            //If it's a successful validation, the error will be cleared and thus it would be validated
            $errors['community_name'] = '';
          }
    }

    if(empty($_POST['start_date'])){
      $errors['start_date'] = 'Please enter a start date <br />';
    } else {
      $start_date = $_POST['start_date'];
    }

    //bring to home page if no errors
    if(array_filter($errors)){
      //echo 'there are errors in the form';
    } else {
      //enter task into database
      $memberid = mysqli_real_escape_string($db, $_POST['memberid']);
      $community_name = mysqli_real_escape_string($db, $_POST['community_name']);
      $start_date = mysqli_real_escape_string($db, $_POST['start_date']);
      
       //json for addadmin
       $addadmin_results = array ('name' => '', 'email' => '', 'address' => '', 'phone' => '', 'experience' => '');
       $addadmin_results['memberID'] = $memberid;
       $addadmin_results['com_name'] = $community_name;
       $addadmin_results['start_date'] = $start_date;
       $addadmin_json = json_encode($addadmin_results);
       $_SESSION['json'] = $addadmin_json;
       $_SESSION['request'] = 'addadmin';


        header('Location: adminindex.php');


    }


  }

?>



<!DOCTYPE html>
<html>
  <?php include ('templates/adminheader.php'); ?>
  <h3 class = 'center'>Admins:</h3>
    <section class="container grey-text">
      <form class="white">
        <?php
        //https://www.w3schools.com/php/php_mysql_select.asp
        //source for table format

            //create table
            if (mysqli_num_rows($admin_result) > 0) {
               echo "<table><tr><th>MemberID</th><th>Community Name</th><th>Start Date</th></tr>";
              while($row = mysqli_fetch_row($admin_result)) {
                echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td></tr>";
              }
              echo "</table>";
            } else {
              echo "0 results";
            }
        ?>
      </form>
    </section>

    <h3 class = 'center'>Members:</h3>
    <section class="container grey-text">
      <form class="white">
        <?php
        //https://www.w3schools.com/php/php_mysql_select.asp
        //source for table format

            //create table
            if (mysqli_num_rows($member_result) > 0) {
               echo "<table><tr><th>MemberID</th><th>Member Name</th><th>Phone Number</th><th>Address</th><th>Email</th></tr>";
              while($row = mysqli_fetch_row($member_result)) {
                echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[8]."</td></tr>";
              }
              echo "</table>";
            } else {
              echo "0 results";
            }
        ?>
      </form>
    </section>

    <h3 class = 'center'>Communities:</h3>
    <section class="container grey-text">
      <form class="white">
        <?php
        //https://www.w3schools.com/php/php_mysql_select.asp
        //source for table format

            //create table
            if (mysqli_num_rows($community_result) > 0) {
               echo "<table><tr><th>Community Name</th><th>City Name</th><th>Location in the City</th></tr>";
              while($row = mysqli_fetch_row($community_result)) {
                echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[4]."</td></tr>";
              }
              echo "</table>";
            } else {
              echo "0 results";
            }
        ?>
      </form>
    </section>


    <h2 class="center"> Add an Admin:</h2>

    <section class="container grey-text">
     <form class="white" action="addadmin.php" method="POST">

          <label class = "black-text"> MemberID : </label>
          <input type="number" name="memberid" placeholder="4" value="<?php echo htmlspecialchars($memberid) ?>">
          <div class = "red-text"><?php echo $errors['memberid'] ?></div>

          <label class = "black-text"> Community Name : </label>
          <input type="text" name="community_name" placeholder="Sunridge" value="<?php echo htmlspecialchars($community_name) ?>">
          <div class = "red-text"><?php echo $errors['community_name'] ?></div>

          <label class = "black-text"> Start Date : </label>
          <input type="date" name="start_date" placeholder="2021-03-19" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="<?php echo htmlspecialchars($start_date) ?>">
          <div class = "red-text"><?php echo $errors['start_date'] ?></div>

          <div class="center">
            <input type="submit" name="add" value="add" class="btn brand z-depth-0">
          </div>


      </form>
    </section>

  <?php include ('templates/adminfooter.php'); ?>
</html>
