<?php
    session_start();
    include ('config/db_connect.php');
    
    if ($_SESSION['login'] != TRUE){
    header('Location: login.php');
    }

    $check_task_id = $_SESSION['ses_MemberID'];
    //$sql = "SELECT * FROM memberlist WHERE MemberID = '$check_task_id'";
    $sql = "CALL checkID('$check_task_id')"; 
    $results = mysqli_query($db, $sql);
    while($row = mysqli_fetch_assoc($results)){
      if($row["MemberID"] == $check_task_id){
        $memName = $row['MemName'];
        $memExp = $row['MemExperience'];
        $memphone = $row['PhoneNum'];
        $memAddress = $row['MemAddress'];
        $memJobDone = $row['NumJobDone'];
        $memJobReq = $row['NumJobReq'];

      }
    }
    //echo"$memJobReq";
    mysqli_close($db);
    $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');

    //$profinfo = mysqli_query($db,$sql);
?>

<?php

    if ($_SESSION['request'] == 'addcity'){
        $addcity_json = $_SESSION['json'];
        $addcity_php = json_decode($addcity_json, true);
        $city_name = $addcity_php['city_name'];
        $num_of_coms = $addcity_php['num_com'];
        
        
      //add new task to database
      //$sql = "INSERT INTO city(City_Name, Num_Communities) VALUES('$city_name', '$num_of_coms')";
      $sql = "CALL addcity('$city_name', '$num_of_coms')";
      
      if(mysqli_query($db, $sql)){
        header('Location: adminindex.php');
      } else {
        echo 'query error: ' . mysqli_error($db);
      }
        $_SESSION['request'] = '';
    }

    if ($_SESSION['request'] == 'addcommunity'){
        $addcom_json = $_SESSION['json'];
        $addcom_php = json_decode($addcom_json, true);
        $community_name = $addcom_php['com_name'];
        $city_name = $addcom_php['city_name'];
        $num_of_collabs = $addcom_php['num_collabs'];
        $location = $addcom_php['location'];
       
       //add new task to database
        //$sql = "INSERT INTO communities(Community_Name, City_Name, Num_Collaborations, Location_In_City) VALUES('$community_name', '$city_name', '$num_of_collabs', '$location')";
        $sql = "CALL addCom('$community_name', '$city_name', '$num_of_collabs', '$location')";
        
        //save to db and check
        if(mysqli_query($db, $sql)){
        header('Location: adminindex.php'); 
        } else {
          echo 'query error: ' . mysqli_error($db);
        }
        
        
        $_SESSION['request'] = '';
    }

    if ($_SESSION['request'] == 'addadmin'){
      $addadmin_json = $_SESSION['json'];
      $addadmin_php = json_decode($addadmin_json, true);
      $memberid = $addadmin_php['memberID'];
      $community_name = $addadmin_php['com_name'];
      $start_date = $addadmin_php['start_date'];
      
      //add new task to database
      //$sql = "INSERT INTO community_leader(MemberID, Community_Name, Start_Date) VALUES('$memberid', '$community_name', '$start_date')";
      $sql = "CALL addadmin('$memberid', '$community_name', '$start_date')";
      //save to db and check
      if(mysqli_query($db, $sql)){
        header('Location: adminindex.php'); 
      } else {
        echo 'query error: ' . mysqli_error($db);
      }
        
        $_SESSION['request'] = '';
    }

    if ($_SESSION['request'] == 'admineditprofile'){
        $editadmin_json = $_SESSION['json'];
        $editadmin_php = json_decode($editadmin_json, true);
        $name = $editadmin_php['name'];
        $email = $editadmin_php['email'];
        $address = $editadmin_php['address'];
        $phone = $editadmin_php['phone'];
        $experience = $editadmin_php['experience'];
        $user_id = $_SESSION['ses_MemberID'];
        
        //$sql = "UPDATE memberlist SET MemName='$name', PhoneNum='$phone', MemAddress='$address', MemExperience='$experience', MemEmail='$email' WHERE MemberID = '$user_id' ";
        $sql = "CALL updateMember('$name', '$phone', '$address', '$experience', '$email', '$check_task_id')";
        //save to db and check
        if(mysqli_query($db, $sql)){
          header('Location: adminindex.php');
        } else {
            echo 'query error: ' . mysqli_error($db);
        }
        
        $_SESSION['request'] = '';
    }
    
    



?>

<!DOCTYPE html>
<html>

    <?php include ('templates/adminheader.php'); ?>
      <h1>Welcome to TaskGrabb</h2>
        <h2 class = 'center'>Where we get everything done!</h2>
          <h3 class = 'center'>Greetings admin <?php print_r($memName);?> </h3>

          <section class="container grey-text">
            <form class="white">

              <?php
                 //if (mysqli_num_rows($profinfo) > 0) {
                     echo "<table><tr><th>ID</th><th>Username</th><th>Skills</th><th>Phone Number</th><th>Address</th><th>Jobs Done</th><th>Jobs Requested</th></tr>";
                    
                      echo "<tr><td>".$check_task_id."</td><td>".$memName."</td><td>".$memExp."</td><td>".$memphone."</td><td>".$memAddress."</td><td>".$memJobDone."</td><td>".$memJobReq;
                    
                    echo "</table>";
                 // } else {
                   // echo "0 results";
                 // }
                  ?>

                  <tr>

                  </tr>
                </table>

            </form>
        </section>

    <?php include ('templates/adminfooter.php'); ?>

</html>
