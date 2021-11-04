<?php
// Page for editing or deleting tasks

  session_start();
  include ('config/db_connect.php');
  
    if ($_SESSION['login'] != TRUE){
    header('Location: login.php');
    }

  //get taskID and memberID from logged in user
  $task_id = $_SESSION['ses_TaskID'];
  $user_id = $_SESSION['ses_MemberID'];
  $user_pass = $_SESSION['ses_Password'];

  //initialize variables
  $ttitle = $tlocation = $skills = $description = $difficulty = $num_of_workers = $payment_type = '';

  //check errors lists
  $errors = array('skills' => '', 'description' => '', 'difficulty' => '', 'num_of_workers' => '', 'payment_type' =>'');
  $errors2 = array('password_0' => '', 'password_1' =>'', 'password_2' => '');

  //get requested task info
  $task_sql = "SELECT * FROM tasks WHERE TaskID = '$task_id' ";
  $task_result = mysqli_query($db, $task_sql);
  $task_query = mysqli_fetch_row($task_result);

  //get save current task info
  $curr_title = $task_query[1];
  $curr_location= $task_query[2];
  $curr_skills = $task_query[3];
  $curr_description = $task_query[4];
  $curr_difficulty = $task_query[5];
  $curr_num_of_workers = $task_query[6];
  $curr_payment_type = $task_query[9];

  //edit task request
  if(isset($_POST['submit'])){

    //Checks for valid entry
    //do nothing if empy
    if(empty($_POST['ttitle'])){
      $errors['ttitle'] = 'A title is necessary <br />';
    } else {
      $ttitle = $_POST['ttitle'];
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
    } else {
      $skills = $_POST['skills'];
      if(!preg_match('/^([a-zA-Z\s]+)(,\s*[a-zA-Z\s]*)*$/', $skills)){
        $errors['skills'] = 'skills must be a comma separated list <br />';
      }
    }

    if(empty($_POST['description'])){
    } else {
      $description = $_POST['description'];
      if(!preg_match('/^[a-zA-Z1-9\s]+$/', $description)){
        $errors['description'] = 'The description can only contain Letters, Spaces, and numbers <br />';
      }

    }

    if(empty($_POST['difficulty'])){
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
    } else {
      $num_of_workers = $_POST['num_of_workers'];
      if(!preg_match('/^[0-9\s]+$/', $num_of_workers)){
        $errors['num_of_workers'] = 'Please enter a valid number of workers/>';
      }
    }

    if(empty($_POST['payment_type'])){
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
      //if entries are empty default to current task info
      if(empty($_POST['ttitle'])){
          $ttitle = $curr_title;
      } else {
          $ttitle = mysqli_real_escape_string($db, $_POST['ttitle']);
      }

      if(empty($_POST['tlocation'])){
          $tlocation = $curr_location;
      } else {
          $tlocation = mysqli_real_escape_string($db, $_POST['tlocation']);
      }

        if(empty($_POST['skills'])){
            $skills = $curr_skills;
        } else {
            $skills = mysqli_real_escape_string($db, $_POST['skills']);
        }

        if(empty($_POST['description'])){
            $description = $curr_description;
        } else {
            $description = mysqli_real_escape_string($db, $_POST['description']);
        }

        if(empty($_POST['difficulty'])){
            $difficulty = $curr_difficulty;
        } else {
            $difficulty = mysqli_real_escape_string($db, $_POST['difficulty']);
        }

        if(empty($_POST['num_of_workers'])){
            $num_of_workers = $curr_num_of_workers;
        } else {
            $num_of_workers = mysqli_real_escape_string($db, $_POST['num_of_workers']);
        }

        if(empty($_POST['payment_type'])){
            $payment_type = $curr_payment_type;
        } else {
            $payment_type = mysqli_real_escape_string($db, $_POST['payment_type']);
        }
          
    
        $edit_results = array ('ttitle' => '', 'tlocation' => '', 'skills' => '', 'description' => '', 'difficulty' => '', 'num_of_workers' => '', 'payment_type' => '');
        $edit_results['ttitle'] = $ttitle;
        $edit_results['tlocation'] = $tlocation;
        $edit_results['skills'] = $skills;
        $edit_results['description'] =  $description;
        $edit_results['difficulty'] =  $difficulty;
        $edit_results['num_of_workers'] =  $num_of_workers;
        $edit_results['payment_type'] =  $payment_type;
        $edit_results['task_id'] = $task_id;
        $edit_results['user_id'] = $user_id;
        
        $edit_json = json_encode($edit_results);
        $_SESSION['request'] = 'edittask';
        $_SESSION['json'] = $edit_json;
   
        header('Location: index.php');

    }

  }

  //delete task request
  if(isset($_POST['submit_delete'])){

    if(empty($_POST['password_1'])){
      $errors2['password_1'] = 'Please enter a password <br />';
    } else {
      $password_1 = $_POST['password_1'];
    }

    if($password_1 != $user_pass){
        $errors2['password_1'] ='not correct password<br />';
    }

    if(array_filter($errors2)){
        } else {
            
              //delete user
              //$sql = "DELETE FROM tasks WHERE Poster_MemberID = '$user_id' AND TaskID = '$task_id' ";
              $sql = "CALL deleteTasks('$user_id', '$task_id')";
              //save to db and check
              if(mysqli_query($db, $sql)){
                header('Location: index.php');
              } else {
                echo 'query error: ' . mysqli_error($db);
              }

        }
    }
    mysqli_close($db);

?>

<html>
  <?php include ('templates/memberheader.php'); ?>
     <h1>Task Manager</h1>
     <h2 class="center">Edit Task Info</h2>
    <section class="container grey-text">
     <form class="white" action="edittask.php" method="POST">

          <label class = "black-text"> Task Title : </label>
          <input type="text" name="ttitle" placeholder="eg. Dogsitting" value="<?php print_r($task_query[0]) ?>">
          <div class = "red-text"><?php echo $errors['ttitle'] ?></div>

          <label class = "black-text"> Location of Task: </label>
          <input type="text" name="tlocation" placeholder="eg. Calgary" value="<?php print_r($task_query[1]) ?>">
          <div class = "red-text"><?php echo $errors['tlocation'] ?></div>

          <label class = "black-text"> Skills Required : </label>
          <input type="text" name="skills" placeholder="eg. customer service" value="<?php print_r($task_query[3]) ?>">
          <div class = "red-text"><?php echo $errors['skills'] ?></div>

          <label class = "black-text"> Task Description : </label>
          <input type="text" name="description" placeholder="I need Someone to Shovel my driveway" value="<?php print_r($task_query[4]) ?>">
          <div class = "red-text"><?php echo $errors['description'] ?></div>

          <label class = "black-text"> Difficulty : </label>
          <input type="text" name="difficulty" placeholder="Easy" value="<?php print_r($task_query[5]) ?>">
          <div class = "red-text"><?php echo $errors['difficulty'] ?></div>

          <label class = "black-text"> Number of workers needed : </label>
          <input type="number" name="num_of_workers" value="<?php print_r($task_query[6]) ?>">
          <div class = "red-text"><?php echo $errors['num_of_workers'] ?></div>

          <label class = "black-text"> Payment Type </label>
          <input type="text" name="payment_type" value="<?php print_r($task_query[9]) ?>">
          <div class = "red-text"><?php echo $errors['payment_type'] ?></div>

          <div class="center">
            <input type="submit" name="submit" value="sumbit" class="btn brand z-depth-0">
          </div>
      </form>
    </section>



    <h2 class="center">Delete Task</h2>
        <section class="container grey-text">
         <form class="white" action="edittask.php" method="POST">

          <label class = "black-text"> Password : </label>
          <input type="password" name="password_1">
          <div class = "red-text"><?php echo $errors2['password_1'] ?></div>

          <div class="center">
            <input type="submit" name="submit_delete" value="submit" class="btn brand z-depth-0">
          </div>

        </form>
      </section>




  <?php include ('templates/memberfooter.php'); ?>
</html>
