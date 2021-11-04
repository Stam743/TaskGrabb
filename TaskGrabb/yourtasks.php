<?php
    session_start();

    include ('config/db_connect.php');
    
    if ($_SESSION['login'] != TRUE){
    header('Location: login.php');
    }



    $ytasks_requests = array();

    
    //get memberID of login user
    $user_id = $_SESSION['ses_MemberID'];

    $task_id = '';

    //check for errors list
    $errors = array('TaskID' => '', 'TaskID_rate' => '');

    //get all tasks for JSON
    //adaped from https://www.kodingmadesimple.com/2015/01/convert-mysql-to-json-using-php.html
    $task_json_sql = "SELECT * FROM tasks WHERE Poster_MemberID = '$user_id' ";
    $task_json_result = mysqli_query($db, $task_json_sql);
    $ytasks_requests = array();
    $inc = 0;
    while($row = mysqli_fetch_assoc($task_json_result))
    {
                
        $ytasks_requests[$inc] = $row;
        $inc = $inc + 1;
    }
    $ytasks_json = json_encode($ytasks_requests);
            
    
    //get all tasks posted by userID
    $com_sql = "SELECT * FROM tasks WHERE TaskID IN
                (SELECT TaskID FROM completes_task WHERE TaskID IN 
                (SELECT TaskID FROM tasks WHERE Poster_MemberID = '$user_id')) ";
    $com_result = mysqli_query($db, $com_sql);
    

    //edit task request
    if(isset($_POST['submit'])){
        $check_task_id = $_POST['TaskID'];
        $sql = "SELECT TaskID FROM tasks WHERE Poster_MemberID = '$user_id' ";
        $results = mysqli_query($db, $sql);
        $members = mysqli_fetch_all($results, MYSQLI_ASSOC);

        //by default has error
        $errors['TaskID'] = 'Please enter a valid task ID<br/>';

        foreach($members as $member){
             //Checks if the taskID given belongs to a task owned by another user
            if($member['TaskID'] == $check_task_id){
                //If it's a successful validation, the error will be cleared and thus it would be validated
                $errors['TaskID'] = '';
              }
        }

        if(empty($_POST['TaskID'])){
            $errors['TaskID'] = 'Please enter a valid task ID<br/>';
            }

        if(array_filter($errors)){
            } else {

              //get task ID from submit and pass it to the edittask page
              $_SESSION['ses_TaskID'] = $_POST['TaskID'];
              header('Location: edittask.php');

            }

    }
  
    //rate user request
    if(isset($_POST['submit_rate'])){
        $check_task_id = $_POST['TaskID'];
        $sql = "SELECT TaskID FROM completes_task WHERE TaskID IN 
                (SELECT TaskID FROM tasks WHERE Poster_MemberID = '$user_id') ";
        $results = mysqli_query($db, $sql);
        $members = mysqli_fetch_all($results, MYSQLI_ASSOC);

        //by default has error
        $errors['TaskID_rate'] = 'Please enter a valid task ID<br/>';

        foreach($members as $member){
             //Checks if the taskID given belongs to a task owned by another user
            if($member['TaskID'] == $check_task_id){
                //If it's a successful validation, the error will be cleared and thus it would be validated
                $errors['TaskID_rate'] = '';
              }
        }

        if(empty($_POST['TaskID'])){
            $errors['TaskID'] = 'Please enter a valid task ID<br/>';
            }

        if(array_filter($errors)){
            } else {

              //get task ID from submit and pass it to the edittask page
              $_SESSION['ses_TaskID'] = $_POST['TaskID'];
              header('Location: rateusers.php');

            }

    }
?>

<?php
echo $_SESSION['echo'];

?>



<!DOCTYPE html>
<html>
  <?php include ('templates/memberheader.php') ?>
      <h1>Your Tasks</h1>
      <h2 class = 'center'>Requested Tasks</h2>
    <section class="container grey-text">
      <form class="white">
        <?php
        //https://www.w3schools.com/php/php_mysql_select.asp
        //source for table format
        
            //decode json
            $ytasks_php = json_decode($ytasks_json,true);
            
            //create table
            if (count($ytasks_php) > 0) {
               echo "<table><tr><th>Title</th><th>Location</th><th>ID</th><th>Skills Required</th><th>Description</th><th>Difficulty</th><th>Number of workers needed</th><th>Payment Type</th></tr>";
              foreach($ytasks_php as $row){
                echo "<tr><td>".$row['Task_Title']."</td><td>".$row['Task_Location']."</td><td>".$row['TaskID']."</td><td>".$row['Skills_Required']."</td><td>".$row['Task_Description']."</td><td>".$row['Difficulty']."</td><td>".$row['Num_Workers_Needed']."</td><td>".$row['Payment_Type'];
              }
              echo "</table>";
            } else {
              echo "0 results";
            }
            
        ?>
    </form>
    </section>
    <h4 class = 'center'>Edit a Posted Task</h4>
    <section class="container grey-text">
     <form class="white" action = "yourtasks.php" method="POST">

       <label class = "black-text"> Enter The ID of the task you want to edit : </label>
            <input type="text" name="TaskID">
        <div class = "red-text"><?php echo $errors['TaskID'] ?></div>

        <div class="center">
            <input type="submit" name="submit" value="sumbit" class="btn brand z-depth-0">
        </div>
     </form>
    </section>
    
    
    <h2 class = 'center'>Requested Tasks in Progress</h2>
    <section class="container grey-text">
      <form class="white">
        <?php
        //https://www.w3schools.com/php/php_mysql_select.asp
        //source for table format

            //create table
            if (mysqli_num_rows($com_result) > 0) {
               echo "<table><tr><th>Title</th><th>Location</th><th>ID</th><th>Skills Required</th><th>Description</th><th>Difficulty</th><th>Number of workers needed</th><th>Payment Type</th></tr>";
              while($row = mysqli_fetch_row($com_result)){
                echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[9];
              }
              echo "</table>";
            } else {
              echo "0 results";
            }
        ?>
      </form>
    </section>
    <h4 class = 'center'>Rate Task Performance</h4>
    <section class="container grey-text">
     <form class="white" action = "yourtasks.php" method="POST">

       <label class = "black-text"> Enter the ID of the task that you want to rate performance: </label>
            <input type="text" name="TaskID">
        <div class = "red-text"><?php echo $errors['TaskID_rate'] ?></div>

        <div class="center">
            <input type="submit" name="submit_rate" value="sumbit" class="btn brand z-depth-0">
        </div>
     </form>
    </section>

  <?php include ('templates/memberfooter.php') ?>

</html>
