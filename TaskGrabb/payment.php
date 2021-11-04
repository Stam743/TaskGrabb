<?php 
  session_start();
  include ('config/db_connect.php');
  
    if ($_SESSION['login'] != TRUE){
    header('Location: login.php');
    }


  $user_id = $_SESSION['ses_MemberID'];
    
  $task_id = '';
  
  $taskid = $payment_type = $amount = $recieved = '';
  $errors = array('taskid' => '','payment_type' => '', 'amount' => '');

  if(isset($_POST['send'])){
   
    if(empty($_POST['taskid'])){
      $errors['taskid'] = 'Please enter the task you are paying for! <br />';
    } else {
      $taskid = $_POST['taskid'];
      if(!preg_match('/^[0-9\s]+$/', $taskid)){
        $errors['taskid'] = 'Please a valid taskid <br />'; 
      }
        
    }

    $check_task_id = $_POST['taskid'];
    $taskid_sql = "SELECT TaskID FROM tasks WHERE Poster_MemberID = '$user_id' ";
    $results = mysqli_query($db, $taskid_sql);
    $members = mysqli_fetch_all($results, MYSQLI_ASSOC);

    //by default has error
    $errors['taskid'] = 'Please enter a valid task ID<br/>';
    
    foreach($members as $member){
          //Checks if the taskID given belongs to a task owned by another user
        if($member['TaskID'] == $check_task_id){
            //If it's a successful validation, the error will be cleared and thus it would be validated
            $errors['taskid'] = '';
          }
    }

    if(empty($_POST['payment_type'])){
      $errors['payment_type'] = 'Please enter the number of communities your city has <br />';
    } else {
      $payment_type = $_POST['payment_type'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $payment_type)){
        $errors['payment_type'] = 'Please enter a valid Payment Type! <br />';
      }
    }

    if($payment_type != "Cash" && $payment_type != "Check" && $payment_type != "Paypal" && $payment_type != "Credit Card") {
        $errors['payment_type'] = 'Please enter one of the following: Cash, Check, Paypal, Volunteer Work or Credit Card';
      }

    if(empty($_POST['amount'])){
      $errors['amount'] = 'Please enter the amount you are paying! <br />';
    } else {
      $amount = $_POST['amount'];
      if(!preg_match('/^[0-9\s]+$/', $amount)){
        $errors['amount'] = 'Please a valid amount! <br />'; 
      }
        
    }

   
            
    //bring to home page if no errors
    if(array_filter($errors)){
      //echo 'there are errors in the form';
    } else {
      //enter task into database
      $taskid = mysqli_real_escape_string($db, $_POST['taskid']);
      $payment_type = mysqli_real_escape_string($db, $_POST['payment_type']);
      $amount = mysqli_real_escape_string($db, $_POST['amount']);
      $recieved = mysqli_real_escape_string($db, $_POST['amount']);
      
      
       //json for payment
       $payment_results = array ('taskid' => '', 'payment_type' => '', 'amount' => '', 'recieved' => '');
       $payment_results['taskid'] = $taskid;
       $payment_results['payment_type'] = $payment_type;
       $payment_results['amount'] = $amount;
       $payment_results['recieved'] = $recieved;
       $payment_json = json_encode($payment_results);
       
       $_SESSION['json'] = $payment_json;
       $_SESSION['request'] = 'payment';

       header('Location: index.php');
        
    }
  }

?>




<!DOCTYPE html>
<html>
  <?php include ('templates/memberheader.php'); ?>
    
    <h1>Task Payment</h2>
    <h2 class="center"> Send Payment:</h2>
    
    <section class="container grey-text">
     <form class="white" action="payment.php" method="POST">

          <label class = "black-text"> Task ID : </label>
          <input type="number" name="taskid" placeholder="1" value="<?php echo htmlspecialchars($taskid) ?>">
          <div class = "red-text"><?php echo $errors['taskid'] ?></div>

          <label class = "black-text"> Payment Type : </label>
          <input type="text" name="payment_type" placeholder="Cash" value="<?php echo htmlspecialchars($payment_type) ?>">
          <div class = "red-text"><?php echo $errors['payment_type'] ?></div>

          <label class = "black-text"> Amount to Send : </label>
          <input type="number" name="amount" placeholder="1" value="<?php echo htmlspecialchars($amount) ?>">
          <div class = "red-text"><?php echo $errors['amount'] ?></div>
        
          <div class="center">
            <input type="submit" name="send" value="send" class="btn brand z-depth-0">
          </div>


      </form>
    </section>
  
  <?php include ('templates/memberfooter.php'); ?>
</html>