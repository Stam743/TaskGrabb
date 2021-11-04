<?php
  session_start();
  include ('config/db_connect.php');

  if ($_SESSION['login'] != TRUE){
  header('Location: login.php');
  }

  $ttitle = $tlocation = $skills = $description = $difficulty = $num_of_workers = $payment_type = '';
  $MemberID = $_SESSION['ses_MemberID'];
  $CommunityLeaderID = 2;
  $errors = array('ttitle' => '', 'tlocation' => '','skills' => '', 'description' => '', 'difficulty' => '', 'num_of_workers' => '', 'payment_type' =>'');

  if(isset($_POST['post'])){

    //Checks for valid entry
    if(empty($_POST['ttitle'])){
      $errors['ttitle'] = 'A title is necessary <br />';
    } else {
      $ttitle = $_POST['ttitle'];

      $sql = "SELECT * FROM tasks WHERE Poster_MemberID = '$MemberID' ";
      $results = mysqli_query($db, $sql);
      $members = mysqli_fetch_all($results, MYSQLI_ASSOC);

      foreach($members as $member){
        if($member['Task_Title'] == $ttitle){
            //If it's a successful validation, the error will be cleared and thus it would be validated
            $errors['ttitle'] = 'Task Title already in use';
        }

      }

      if(!preg_match('/^([a-zA-Z\s]+)(,\s*[a-zA-Z\s]*)*$/', $ttitle)){
        $errors['ttitle'] = 'A Title must be a comma separated list or a word <br />';
      }
    }

    if(empty($_POST['tlocation'])){
      $errors['tlocation'] = 'The location is necessary <br />';
    } else {
      $tlocation = $_POST['tlocation'];
      if(!preg_match('/^([a-zA-Z\s]+)(,\s*[a-zA-Z\s]*)*$/', $tlocation)){
        $errors['skills'] = 'A Location must be a comma separated list or a word <br />';
      }
    }

    if(empty($_POST['skills'])){
      $errors['skills'] = 'At least one skill is required <br />';
    } else {
      $skills = $_POST['skills'];
      if(!preg_match('/^([a-zA-Z\s]+)(,\s*[a-zA-Z\s]*)*$/', $skills)){
        $errors['skills'] = 'Skills must be a comma separated list <br />';
      }
    }

    if(empty($_POST['description'])){
      $errors['description'] = 'Please enter a description of your tasks <br />';
    } else {
      $description = $_POST['description'];
      if(!preg_match('/^[a-zA-Z1-9\s]+$/', $description)){
        $errors['description'] = 'The description can only contain Letters, Spaces, and numbers <br />';
      }

    }

    if(empty($_POST['difficulty'])){
      $errors['difficulty'] = 'Please enter the difficulty of your task <br />';
    } else {
      $difficulty = $_POST['difficulty'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $difficulty)){
        $errors['difficulty'] = 'The difficulty can only consist of Letters <br />';
      }
    }

    if($difficulty != 'Easy' && $difficulty != 'Medium' && $difficulty != 'Hard' && $difficulty != 'Very Difficult') {
      $errors['difficulty'] = 'Please enter one of the following: Easy, Medium, Hard, or Very Difficult';
    }

    if(empty($_POST['num_of_workers'])){
      $errors['num_of_workers'] = 'Please enter the number of workers you require for your task <br />';
    } else {
      $num_of_workers = $_POST['num_of_workers'];
      if(!preg_match('/^[0-9\s]+$/', $num_of_workers)){
        $errors['num_of_workers'] = 'Please enter a valid number of workers/>';
      }
    }

    if(empty($_POST['payment_type'])){
      $errors['payment_type'] = 'Please enter the number of workers you require for your task <br />';
    } else {
      $payment_type = $_POST['payment_type'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $payment_type)){
        $errors['payment_type'] = 'The Payment Type can only consist of Letters <br />';
      }
    }

    if($payment_type != "One Time Payment" && $payment_type != "Hourly Pay" && $payment_type != "Volunteer Work") {
      $errors['payment_type'] = 'Please enter one of the following: One Time Payment, Hourly Pay, or Volunteer Work';
    }

    //bring to home page if no errors
    if(array_filter($errors)){
      //echo 'there are errors in the form';
    } else {
      //enter task into database

      // $ttitle = mysqli_real_escape_string($db, $_POST['ttitle']);
      // $tlocation = mysqli_real_escape_string($db, $_POST['tlocation']);
      // $skills = mysqli_real_escape_string($db, $_POST['skills']);
      // $description = mysqli_real_escape_string($db, $_POST['description']);
      // $difficulty = mysqli_real_escape_string($db, $_POST['difficulty']);
      // $num_of_workers = mysqli_real_escape_string($db, $_POST['num_of_workers']);
      // $payment_type = mysqli_real_escape_string($db, $_POST['payment_type']);
      
      
      $task_results = array ('ttitle' => '', 'tlocation' => '', 'skills' => '', 'description' => '', 'difficulty' => '', 'num_of_workers' => '', 'payment_type' => '');
      $task_results['ttitle'] = mysqli_real_escape_string($db, $_POST['ttitle']);
      $task_results['tlocation'] = mysqli_real_escape_string($db, $_POST['tlocation']);
      $task_results['skills'] = mysqli_real_escape_string($db, $_POST['skills']);
      $task_results['description'] = mysqli_real_escape_string($db, $_POST['description']);
      $task_results['difficulty'] =  mysqli_real_escape_string($db, $_POST['difficulty']);
      $task_results['community_leader_id'] = $CommunityLeaderID;
      $task_results['num_of_workers'] =  mysqli_real_escape_string($db, $_POST['num_of_workers']);
      $task_results['payment_type'] =  mysqli_real_escape_string($db, $_POST['payment_type']);
      $task_results['user_id'] = $_SESSION['ses_MemberID'];
        
      $task_json = json_encode($task_results);
      $_SESSION['request'] = 'posttask';
      $_SESSION['json'] = $task_json;
   
      header('Location: index.php');


    }


  }

?>



<!DOCTYPE html>
<html>
  <?php include ('templates/memberheader.php'); ?>

    <h1>Post a Task</h2>
    <h2 class="center"> Create a Task:</h2>

    <section class="container grey-text">
     <form class="white" action="posttask.php" method="POST">

          <label class = "black-text"> Task Title : </label>
          <input type="text" name="ttitle" placeholder="eg. Dogsitting" value="<?php echo htmlspecialchars($ttitle) ?>">
          <div class = "red-text"><?php echo $errors['ttitle'] ?></div>

          <label class = "black-text"> Location of Task: </label>
          <input type="text" name="tlocation" placeholder="eg. Calgary" value="<?php echo htmlspecialchars($tlocation) ?>">
          <div class = "red-text"><?php echo $errors['tlocation'] ?></div>

          <label class = "black-text"> Skills Required : </label>
          <input type="text" name="skills" placeholder="eg. Customer service" value="<?php echo htmlspecialchars($skills) ?>">
          <div class = "red-text"><?php echo $errors['skills'] ?></div>

          <label class = "black-text"> Task Description : </label>
          <input type="text" name="description" placeholder="I need Someone to Shovel my driveway" value="<?php echo htmlspecialchars($description) ?>">
          <div class = "red-text"><?php echo $errors['description'] ?></div>

          <label class = "black-text"> Difficulty : </label>
          <input type="text" name="difficulty" placeholder="Easy" value="<?php echo htmlspecialchars($difficulty) ?>">
          <div class = "red-text"><?php echo $errors['difficulty'] ?></div>

          <label class = "black-text"> Number of workers needed : </label>
          <input type="number" name="num_of_workers" placeholder="1" value="<?php echo htmlspecialchars($num_of_workers) ?>">
          <div class = "red-text"><?php echo $errors['num_of_workers'] ?></div>

          <label class = "black-text"> Payment Type </label>
          <input type="text" name="payment_type" placeholder="One Time Payment" value="<?php echo htmlspecialchars($payment_type) ?>">
          <div class = "red-text"><?php echo $errors['payment_type'] ?></div>

          <div class="center">
            <input type="submit" name="post" value="post" class="btn brand z-depth-0">
          </div>


      </form>
    </section>

  <?php include ('templates/memberfooter.php'); ?>
</html>
