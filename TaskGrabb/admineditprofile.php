<?php
//todo: Clean up code
//todo: Comment
//todo: if time improve system for editing, ie not requiring entries in all fields for submit

    session_start();
    include ('config/db_connect.php');
    
    if ($_SESSION['login'] != TRUE){
    header('Location: login.php');
    }
    
    $user_id = $_SESSION['ses_MemberID'];
    $user_pass = $_SESSION['ses_Password'];
    $user_email = $_SESSION['ses_MemEmail'];
    
    $profile_sql = "SELECT * FROM memberlist WHERE MemberID = '$user_id' ";
    $profile_result = mysqli_query($db, $profile_sql);
    $profile_query = mysqli_fetch_row($profile_result);
    
    $name = $email = $address = $phone = $experience = '';
    $errors1 = array('name' => '', 'email' => '', 'address' => '', 'phone' => '');
    $errors2 = array('password_1' =>'', 'password_2' => '');
    $errors3 = array('password_3' =>'');
    
  $curr_name = $profile_query[1];
  $curr_email = $profile_query[8];
  $curr_address = $profile_query[4];
  $curr_phone = $profile_query[3];
  $curr_experience = $profile_query[7];
    
    
 //edit profile request
 //if field is left empty will default to previous/current profile info except experience
 if(isset($_POST['submit'])){
    
    //Checks for valid name entry
    if(empty($_POST['name'])){
      $name = $curr_name;
    } else {
      $name = $_POST['name'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $name)){
        $errors1['name'] = 'Name must be Letters and spaces only <br />';
      }
    }

    //Checks for valid email entry
    if(empty($_POST['email'])){
      $email = $curr_email;
    } else {
      $email = $_POST['email'];
      if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors1['email'] = 'email must be a valid email address <br />';
      }
        
    }

    //Check if email is already in use
    $emailcheck = mysqli_real_escape_string($db, $_POST['email']);
    $user_check_query = "SELECT MemberID, MemEmail FROM memberlist WHERE MemEmail = '$email' ";
    $results = mysqli_query($db, $user_check_query);
    $members = mysqli_fetch_all($results, MYSQLI_ASSOC);
    
    foreach($members as $member){

      //Checks if the email matches an email in use
        if($member['MemEmail'] == $emailcheck && $member['MemberID'] != $user_id){
            $errors1['email'] = 'That email is already in use <br/>';
          } else{
        }
    }
    
    //Checks for valid phone number
    if(empty($_POST['address'])){
      $address = $curr_address;
    } else {
      $address = $_POST['address'];
    }
    
    //Checks for valid phone number
    if(empty($_POST['phone'])){
      $phone = $curr_phone;
    } else {
      $phone = $_POST['phone'];
    }

    if(!empty($_POST['experience'])){
      $experience = $_POST['experience'];
    }
            
        if(array_filter($errors1)){
            } else {

            //update member in database
            //$sql = "UPDATE memberlist SET MemName='$name', PhoneNum='$phone', MemAddress='$address', MemExperience='$experience', MemEmail='$email' WHERE MemberID = '$user_id' "; 
            $sql = "CALL updateMember('$name', '$phone', '$address', '$experience', '$email', '$user_id')";
            //save to db and check
            
                          //for Json
            $_SESSION['request'] = 'editprofile';
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['address'] = $address;
            $_SESSION['phone'] = $phone;
            $_SESSION['experience'] = $experience;
            
            if(mysqli_query($db, $sql)){
              header('Location: adminindex.php');
            } else {
              echo 'query error: ' . mysqli_error($db);
            }
        }
    }

    //change password request
    if(isset($_POST['submit_pass'])){
        
        //Check if empty if not get input
        if(empty($_POST['password_1'])){
          $errors2['password_1'] = 'Please enter a password <br />';
        } else {
          $password_1 = $_POST['password_1'];
        }

        if(empty($_POST['password_2'])){
          $errors2['password_2'] = 'Please confirm your password <br />';
        } else {
          $password_2 = $_POST['password_2'];
        }
        
        if($password_1 != $password_2){
            $errors2['password_2'] ='Passwords do no match <br />';
        }
        
            if(array_filter($errors2)){
            } else {
            //enter user into database
                  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);            
                  //update member in database
                  //$sql = "UPDATE memberlist SET MemPassword='$password_1' WHERE MemberID = '$user_id' "; 
                  $sql = "CALL updatePass('$password_1', '$user_id')";
                  //save to db and check
                  if(mysqli_query($db, $sql)){
                    header('Location: adminindex.php');
                  } else {
                    echo 'query error: ' . mysqli_error($db);
                  }

            }
        }
    
    //delete account request
    if(isset($_POST['submit_delete'])){
        if(empty($_POST['password_3'])){
          $errors3['password_3'] = 'Please enter a password <br />';
        } else {
          $password_3 = $_POST['password_3'];
        }
        
        if($password_3 != $user_pass){
          $errors3['password_3'] ='not correct password<br />';
        }

        
        if(array_filter($errors3)){
            } else {
                  //delete user
                  //$sql = "DELETE FROM memberlist WHERE MemberID = '$user_id' "; 
                  $sql = "CALL deleteMember('$user_id')";
                  //save to db and check
                  
                  if(mysqli_query($db, $sql)){
                    header('Location: logout.php');
                  } else {
                    echo 'query error: ' . mysqli_error($db);
                  }

            }
            
    }

    
    mysqli_close($db);

?>

<!DOCTYPE html>
<html>

  <?php include ('templates/adminheader.php'); ?>
    <h1>Account Manager</h1>
    <h2 class="center">Edit Profile Info</h2>
    
    <section class="container grey-text">
         <form class="white" action="admineditprofile.php" method="POST">

          <label class = "black-text"> Edit Name : </label>
          <input type="text" name="name" placeholder="James Curtis" value="<?php print_r($profile_query[1]) ?>">
          <div class = "red-text"><?php echo $errors1['name'] ?></div>

          <label class = "black-text"> Edit Email : </label>
          <input type="email" name="email" placeholder="jamescurtis@gmail.com" value="<?php print_r($profile_query[8]) ?>">
          <div class = "red-text"><?php echo $errors1['email'] ?></div>

          <label class = "black-text"> Edit Address : </label>
          <input type="text" name="address" placeholder="123456 Signal Hill Dr" value="<?php print_r($profile_query[4]) ?>">
          <div class = "red-text"><?php echo $errors1['address'] ?></div>

          <label class = "black-text"> Edit Phone Number : </label>
          <input type="tel" name="phone" placeholder="123-456-7890" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" value="<?php print_r($profile_query[3]) ?>">
          <div class = "red-text"><?php echo $errors1['phone'] ?></div>

          <label class = "black-text"> Edit Experience : </label>
          <input type="text" name="experience" placeholder="mowing lawns, gardening, etc." value="<?php print_r($profile_query[7]) ?>">

          <div class="center">
            <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
          </div>

      </form>
    
    </section>
    
    <h2 class="center">Change Password</h2>
    <section class="container grey-text">
         <form class="white" action="admineditprofile.php" method="POST">
          
          <label class = "black-text"> New Password : </label>
          <input type="password" name="password_1">
          <div class = "red-text"><?php echo $errors2['password_1'] ?></div>

          <label class = "black-text"> Confirm New Password : </label>
          <input type="password" name="password_2">
          <div class = "red-text"><?php echo $errors2['password_2'] ?></div>

          <div class="center">
            <input type="submit" name="submit_pass" value="submit" class="btn brand z-depth-0">
          </div>

      </form>
    
    </section> 
    <h2 class="center">Delete Account</h2>
        <section class="container grey-text">
         <form class="white" action="admineditprofile.php" method="POST">
          
          
          <label class = "black-text"> Password : </label>
          <input type="password" name="password_3">
          <div class = "red-text"><?php echo $errors3['password_3'] ?></div>

          <div class="center">
            <input type="submit" name="submit_delete" value="submit" class="btn brand z-depth-0">
          </div>

      </form>  


  <?php include ('templates/adminfooter.php'); ?>
</html>