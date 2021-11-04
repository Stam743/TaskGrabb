<?php

    session_start();
    include ('config/db_connect.php');
    
    if ($_SESSION['login'] != TRUE){
    header('Location: login.php');
    }

    //$login_results = array ('email' => '', 'password' =>'');
    //$login_results['email'] = $_SESSION['ses_MemEmail'];
    //$login_results['password'] = $_SESSION['ses_Password'];
    //$login_json = json_encode($login_results);
    
    
    //json for tasks (edit, post, accept task)
    $task_results = array ('ttitle' => '', 'tlocation' => '', 'skills' => '', 'description' => '', 'difficulty' => '', 'num_of_workers' => '', 'payment_type' => '');
    $task_results['ttitle'] = $_SESSION['ttitle'];
    $task_results['tlocation'] = $_SESSION['tlocation'];
    $task_results['skills'] = $_SESSION['skills'];
    $task_results['description'] =  $_SESSION['description'];
    $task_results['difficulty'] =  $_SESSION['difficulty'];
    $task_results['num_of_workers'] =  $_SESSION['num_of_workers'];
    $task_results['payment_type'] =  $_SESSION['payment_type'];
    $task_json = json_encode($task_results);
    
    //json for editprofile
    $edit_results = array ('name' => '', 'email' => '', 'address' => '', 'phone' => '', 'experience' => '');
    $edit_results['name'] = $_SESSION['name'];
    $edit_results['email'] = $_SESSION['email'];
    $edit_results['address'] = $_SESSION['address'];
    $edit_results['phone'] = $_SESSION['phone'];
    $edit_results['experience'] = $_SESSION['experience'];
    $edit_json = json_encode($edit_results);

    //Tracks whether or not someone is a regular user
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
    echo"$memJobReq";
    mysqli_close($db);
    $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');


    //Tracks the current amount of existing ratings
    //$myrating = "SELECT Rating FROM rates WHERE Worker_MemberID = '$check_task_id' ";
    $myrating = "CALL myRating('$check_task_id')";
    $rateresult = mysqli_query($db,$myrating);
    //$rateTotal = mysqli_fetch_all($rateresult, MYSQLI_ASSOC);
    While($row = mysqli_fetch_assoc($raterresult)){
      $i = 0;
      $ratingCount = 0;
      foreach($rateTotal as $rates){
        $i++;
        $ratingCount+= $rates['Rating'];
     }
    }
    mysqli_close($db);
    $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');
   /* $i = 0;
    $ratingCount = 0;
    foreach($rateTotal as $rates){
        $i++;
        $ratingCount+= $rates['Rating'];
    }*/




   //if came from posttask request
   if ($_SESSION['request'] == 'posttask'){
    $post_json = $_SESSION['json'];
    echo $post_json;
    $post_php = json_decode($post_json, true);
      
    $ttitle = $post_php['ttitle'];
    $tlocation = $post_php['tlocation'];
    $skills = $post_php['skills'];
    $description = $post_php['description'];
    $difficulty = $post_php['difficulty'];
    $num_of_workers = $post_php['num_of_workers'];
    $payment_type = $post_php['payment_type'];
    $MemberID = $post_php['user_id'];
    $CommunityLeaderID = $post_php['community_leader_id'];
    
      
    //add new task to database
    //$sql = "INSERT INTO tasks(Task_Title, Task_Location, Skills_Required, Task_Description, Difficulty, Num_Workers_Needed, Poster_MemberID, Community_LeaderID, Payment_Type) VALUES('$ttitle','$tlocation','$skills', '$description', '$difficulty', '$num_of_workers', '$MemberID', '$CommunityLeaderID', '$payment_type')";
    
    $sql = "CALL postTask('$ttitle','$tlocation','$skills', '$description', '$difficulty', '$num_of_workers', '$MemberID', '$CommunityLeaderID', '$payment_type')";
    mysqli_query($db, $sql);

    $memJobReq = $memJobReq + 1;

    mysqli_close($db);
    $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');
/*
    //$check_user_id = $MemberID;
    echo "Memberid: $MemberID ";
    //$requester = "SELECT * FROM memberlist WHERE MemberID = $check_user_id";
    $requester = "CALL checkID('$MemberID')";
    $reqVal = mysqli_query($db, $requester);
    //$members = mysqli_fetch_all($reqVal, MYSQLI_ASSOC);
    echo "$reqVal";
    while($row = mysqli_fetch_assoc($reqVal)){
      if($row["MemberID"] == $check_user_id){
        $newNum = $row['Num_of_Jobs_Requested'];
        echo "$newNum";
        $newNum =  $newNum + 1;
        echo "$newNum";
      }
    }
    mysqli_close($db);
    $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');
    /*foreach($members as $member){
        $newNum = ($member['Num_of_Jobs_Requested'] + 1);
    }*/

    //$newsql = "UPDATE memberlist SET Num_of_Jobs_Requested = $newNum WHERE MemberID = '$check_user_id' ";
    //echo "Updating member: $check_task_id with num jobs: $memJobReq";
    $newsql = "CALL updateNumJob('$memJobReq', '$check_task_id')";
    mysqli_query($db, $newsql);
    mysqli_close($db);
    $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');
    
    //insert into associated labour type tables
    //$getid_sql = "SELECT TaskID FROM tasks WHERE Poster_MemberID = '$MemberID' AND Task_Title = '$ttitle' ";
    $getid_sql = "CALL checkTaskID('$MemberID', '$$ttitle')";
    $getid_result = mysqli_query($db, $getid_sql);
    //$getid_query = mysqli_fetch_row($getid_result);
    While($taskrow = mysqli_fetch_assoc($getid_result)){
      if($taskrow['Poster_MemberID'] == $MemberID and $taskrow['Task_Title'] == $ttile){
        $task_id = $taskrow['TaskID']; 
        $payment_type = $taskrow['Payment_Type'];
      } 
    }
    mysqli_close($db);
    $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');
    
    if ($payment_type == 'Volunteer Work'){
      //$volunteersql = "INSERT INTO volunteer_work ( TaskID ) VALUES('$task_id') ";
      $volunteersql = "CALL postVolTask('$task_id')";
      mysqli_query($db, $volunteersql);

      mysqli_close($db);
      $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');
    } else {
      //$paidsql = "INSERT INTO paid_work ( TaskID ) VALUES('$task_id') ";
      $paidsql = "CALL postPaidTask('$task_id')";
      mysqli_query($db, $paidsql);

      mysqli_close($db);
      $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');
    }
    
      
      
      $_SESSION['request'] = '';
      
      
      $_SESSION['request'] = '';
  }
    
    if ($_SESSION['request'] == 'edittask'){
      $edit_json = $_SESSION['json'];
      echo $edit_json;
      $edit_php = json_decode($edit_json, true);
        
      $ttitle = $edit_php['ttitle'];
      $tlocation = $edit_php['tlocation'];
      $skills = $edit_php['skills'];
      $description = $edit_php['description'];
      $difficulty = $edit_php['difficulty'];
      $num_of_workers = $edit_php['num_of_workers'];
      $payment_type = $edit_php['payment_type'];
      $task_id = $edit_php['task_id'];
      $user_id = $edit_php['user_id'];
        
              //add new task to database
      /*$sql = "UPDATE tasks SET
      Task_Title = '$ttitle',
      Task_Location = '$tlocation',
      Skills_Required = '$skills',
      Task_Description = '$description',
      Difficulty = '$difficulty',
      Num_Workers_Needed = '$num_of_workers',
      Poster_MemberID = '$user_id',
      Payment_Type = '$payment_type'
      WHERE TaskID = '$task_id' ";*/

      $sql = "CALL updateTask('$ttitle', '$tlocation', '$skills', '$description', '$difficulty', '$num_of_workers', '$payment_type', '$task_id')";
        
        if(mysqli_query($db, $sql)){
          } else {
            echo 'query error: ' . mysqli_error($db);
          }
        
        $_SESSION['request'] = '';
    }
    
    if ($_SESSION['request'] == 'editprofile'){
      $profile_json = $_SESSION['json'];
      echo $profile_json;
      $profile_php = json_decode($profile_json, true);
      
      $name = $profile_php['name'];
      $email = $profile_php['email'];
      $address = $profile_php['address'];
      $phone = $profile_php['phone'];
      $experience = $profile_php['experience'];
      $user_id = $profile_php['user_id'];
         
      //update member in database
      //$sql = "UPDATE memberlist SET MemName='$name', PhoneNum='$phone', MemAddress='$address', MemExperience='$experience', MemEmail='$email' WHERE MemberID = '$user_id' "; 
      $sql = "CALL updateMember('$name', '$phone', '$address', '$experience', '$email', '$user_id')";
      //save to db and check   
      if(mysqli_query($db, $sql)){
      } else {
      echo 'query error: ' . mysqli_error($db);
      }
  
  
      $_SESSION['request'] = '';
 }
    
    
?>
<?php
    if ($_SESSION['request'] == 'payment'){
      $payment_json = $_SESSION['json'];
      $payment_php = json_decode($payment_json, true);
      $taskid = $payment_php['taskid']; 
      $payment_type = $payment_php['payment_type'];
      $amount = $payment_php['amount'];
      $recieved = $payment_results['recieved'];
      
      
      //$sql = "INSERT INTO payment(TaskID, Payment_Type, Amount_Sent, Amount_Recieved) VALUES('$taskid', '$payment_type', '$amount', '$recieved')";
      $sql = "CALL makePayment('$taskid', '$payment_type', '$amount', '$recieved')";
      //save to db and check
      if(mysqli_query($db, $sql)){
        header('Location: index.php');
      } else {
        echo 'query error: ' . mysqli_error($db);
      }
      
      
      $_SESSION['request'] = '';
    }


?>

<!DOCTYPE html>
<html>

    <?php include ('templates/memberheader.php'); ?>
      <h1>Welcome to TaskGrabb</h2>
        <h2 class = 'center'>Where we get everything done!</h2>
          <h3 class = 'center'>Greetings <?php print_r($memName);?> </h3>

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


          <div class = 'center'>Your current rating is <?php
          $tempRating = (round(($ratingCount/$i),2) . "<br>");
           print_r($tempRating);

           ?></div>



    <?php include ('templates/memberfooter.php'); ?>

</html>
