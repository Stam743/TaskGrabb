<?php
    session_start();
    include ('config/db_connect.php');

    if ($_SESSION['login'] != TRUE){
    header('Location: login.php');
    }

    $errors = array('UserEmail' => '', 'UserRating' => '');

    $user_id = $_SESSION['ses_MemberID'];
    $task_id = $_SESSION['ses_TaskID'];

    //get all tasks posted by UserEmail
    $worker_sql = "SELECT * FROM memberlist WHERE MemberID IN
                 (SELECT Worker_MemberID FROM completes_task WHERE TaskID = '$task_id' )";
    $worker_result = mysqli_query($db, $worker_sql);
    //$worker_query = mysqli_fetch_all($worker_result, MYSQLI_ASSOC);
    $worker_requests = array();
        
    $inc = 0;
    while($row = mysqli_fetch_assoc($worker_result))
    {
                
        $worker_requests[$inc] = $row;
        $inc = $inc + 1;
    }
    $worker_json = json_encode($worker_requests);
    
            


    //rate user request
    if(isset($_POST['submit'])){
        $check_email = $_POST['UserEmail'];

        //get the list of emails of workers for task
        $sql = "SELECT * FROM memberlist WHERE MemberID IN
                       (SELECT Worker_MemberID FROM completes_task WHERE TaskID = '$task_id' )";
        $result = mysqli_query($db, $sql);
        $members = mysqli_fetch_all($result, MYSQLI_ASSOC);

        //Get the id of the user that will be rated
        $sql = "SELECT MemberID FROM memberlist WHERE MemEmail = '$check_email' ";
        $result = mysqli_query($db, $sql);
        $query = mysqli_fetch_row($result);
        $rated_user_id = $query[0];
       
        
        //by default has error
        $errors['UserEmail'] = 'Please enter a valid task ID<br/>';

        foreach($members as $member){
             //Checks if the taskID given belongs to a task owned by another user
            if($member['MemEmail'] == $check_email){
                //If it's a successful validation, the error will be cleared and thus it would be validated
                $errors['UserEmail'] = '';
              }
        }

        if(empty($_POST['UserEmail'])){
            $errors['UserEmail'] = 'Please enter a valid task ID<br/>';
            }

        if(empty($_POST['UserRating'])){
            $errors['UserRating'] = 'Please enter a valid Rating between 1-10<br/>';
            } else {
                $rating = $_POST['UserRating'];

             }
                 


        //insert rating if no errors
        if(array_filter($errors)){
            } else {
                
            //create JSON for rating   
            $rate_results = array ('Poster_MemberID' => '', 'Worker_MemberID' => '', 'Rating' => '');
            $rate_results['Poster_MemberID'] = $user_id;
            $rate_results['Worker_MemberID'] = $rated_user_id;
            $rate_results['Rating'] = $rating;
            $rate_json = json_encode($rate_results);
            $_SESSION['request'] = 'rateusers';
            


            }

    }

?>


<?php


if ($_SESSION['request'] == 'rateusers'){
    $rate_php = json_decode($rate_json, true);
    $user_id = $rate_php['Poster_MemberID'];
    $rated_user_id = $rate_php['Worker_MemberID'];
    $rating = $rate_php['Rating'];
    $rate_sql = "INSERT INTO rates(Poster_MemberID, Worker_MemberID, Rating) VALUES ('$user_id', '$rated_user_id', '$rating')";

    //This section calculates the current rating and updates the memberlist
    $myrating = "SELECT Rating FROM rates WHERE Worker_MemberID = '$rated_user_id' ";
    $rateresult = mysqli_query($db,$myrating);
    $rateTotal = mysqli_fetch_all($rateresult, MYSQLI_ASSOC);
    $i = 0;
    $ratingCount = 0;
    foreach($rateTotal as $rates){
        $i++;
        $ratingCount+= $rates['Rating'];
    }
    $newCount = ($ratingCount/$i);

    //This section also updates the number of tasks finished by them

    foreach($members as $member){
        $newNum = ($member['Num_of_Jobs_Done'] + 1);
    }

    $sql = "UPDATE memberlist SET Avg_Rating='$newCount', Num_of_Jobs_Done = $newNum WHERE MemberID = '$rated_user_id' ";
    mysqli_query($db, $sql);

    //save to db and check
    if(mysqli_query($db, $rate_sql)){
        header('Location: yourtasks.php');
    } else {
        echo 'query error: ' . mysqli_error($db);
    }
    
    $_SESSION['request'] = '';
    
}

?>


<!DOCTYPE html>
<html>
  <?php include ('templates/memberheader.php') ?>
    <h1>Rate Users</h1>
    <h2 class = 'center'>Users who worked on this task</h2>
    <section class="container grey-text">
      <form class="white">


        <?php
        //https://www.w3schools.com/php/php_mysql_select.asp
        //source for table format
            $worker_php = json_decode($worker_json, true);

            //create table
            if (count($worker_php) > 0) {
               echo "<table><tr><th>Username</th><th>Phone</th><th>E-mail</th></tr>";
              foreach($worker_php as $row){
                echo "<tr><td>".$row['MemName']."</td><td>".$row['PhoneNum']."</td><td>".$row['MemEmail']."</td><td>";
              }
              echo "</table>";
            } else {
              echo "0 results";
            }
            


        ?>


    </form>
    </section>

    <h4 class = 'center'>Rate user</h4>
    <section class="container grey-text">
     <form class="white" action = "rateusers.php" method="POST">

        <label class = "black-text"> Enter the email of the user you want to rate: </label>
            <input type="text" name="UserEmail">
        <div class = "red-text"><?php echo $errors['UserEmail'] ?></div>

        <label class = "black-text"> Enter Rating (1-10):</label>
            <input type="number" name="UserRating" min= "1" max="10">
        <div class = "red-text"><?php echo $errors['UserRating'] ?></div>


        <div class="center">
            <input type="submit" name="submit" value="sumbit" class="btn brand z-depth-0">
        </div>
     </form>
    </section>

  <?php include ('templates/memberfooter.php') ?>

</html>
