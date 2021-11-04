<?php

  session_start();
  include ('config/db_connect.php');



  $email = $password = '';
  $errors = array('email' => '', 'password' =>'');


  if(isset($_POST['submit'])){


    if(empty($_POST['email'])){
      $errors['email'] = 'Please enter your email <br />';

    } else {
      $email = $_POST['email'];
      $errors['email'] = 'Email not found. Have you registered yet?<br />';

      if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = 'email must be a valid email address <br />';
      }

    }

    //check if email is a registered member
    $emailcheck = mysqli_real_escape_string($db, $_POST['email']);
    $passwordcheck = mysqli_real_escape_string($db, $_POST['password']);
    
    /*
   // $user_check_query = "CALL checkMemberTest('$emailcheck', '$passwordcheck', '$Result')"; 
    //$results = mysqli_query($db, $user_check_query);
    

    $storedProc = "CALL checkMemberTest(?, ?, @Result)";
    $stmt = mysqli_prepare($db, $storedProc);
    mysqli_stmt_bind_param($stmt,"ss",$emailcheck, $passwordcheck);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    $sql = "SELECT @Result";
    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_row($result);
    if($row[0] == 1){
      $errors = [];
      
    }

   // echo $Result;*/

    //$user_check_query = "SELECT MemPassword, MemEmail FROM memberlist WHERE MemEmail = '$emailcheck' ";
    $user_check_query = "CALL checkMember('$emailcheck')";
    $results = mysqli_query($db, $user_check_query);
    //$members = mysqli_fetch_all($results, MYSQLI_ASSOC);
    while($row = mysqli_fetch_assoc($results)){
      if($row['MemEmail'] == $emailcheck){
        $errors['email'] = '';
      }
      if($row['MemPassword'] != $passwordcheck){
        $errors['password'] = 'Either your email or password is incorrect!!!! <br />';
      }
      else{
        $user_id = $row['MemberID'];
      }
    }
    mysqli_close($db);
    $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');

    /*
    //This loop begins with the error section being full. If there is no email found, it will skip this for loop
    //and would simply display an error. If it enters the for loop and clears it, the error section will be cleared
    foreach($members as $member){

      //Checks if the meail matches. If there's no match, it simply skips
        if($member['MemEmail'] == $emailcheck){
            //If it's a successful validation, the error will be cleared and thus it would be validated
            $errors['email'] = '';
          }
      //Checks for matching passwords. If it's a match, it skips this. Otherwise, displays the error
        if($member['MemPassword'] != $passwordcheck){
          $errors['password'] = 'Either your email or password is incorrect!!!! <br />';
        }else{
        }
    }*/


  //If there is nothing typed, it would ask for a password
    if(empty($_POST['password'])){
      $errors['password'] = 'Please enter a password <br />';
    } else {
      $password = $_POST['password'];
    }

    //bring to home page if no errors
    if(array_filter($errors)){
    } else {

        //get memberID from the user
        //$get_userID_query = "SELECT MemberID FROM memberlist WHERE MemEmail = '$email' ";
        //$get_userID_query = "CALL getMemberID('$email')";
        //$results = mysqli_query($db, $get_userID_query);
        //$user_id = mysqli_fetch_row($results);

        //store memberID, email and password for the session ie while they are logged in
        $_SESSION['ses_MemEmail'] = $email;
        $_SESSION['ses_MemberID'] = $user_id;
        $_SESSION['ses_Password'] = $password;


        //Checks whether the ID exists in the community leader section
        //If it does, it enters the admin index. If it doesn't it enters the regular index
        //$admin_query = "SELECT MemberID FROM community_leader WHERE MemberID = '$user_id' ";
        $admin_query = "CALL adminCheck('$user_id')";
        $admincheck = mysqli_query($db, $admin_query);
        //$memberss = mysqli_fetch_all($admincheck, MYSQLI_ASSOC);
        echo "Back from the Database";
        while($row = mysqli_fetch_assoc($admincheck)){
          echo "UserID: $user_id ";
          $tempID = $row["MemberID"];
          echo "TempID: $tempID";
        }


         $_SESSION['login'] = TRUE;
        if($tempID == $user_id){
          header('Location: adminindex.php');
        }
        else{
          header('Location: index.php');
        }
    }
    mysqli_free_result($result);
    mysqli_close($db);
    $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');
  }

    //pass register json to login, enter json into db
    if ($_SESSION['request'] == 'register'){
        $register_json = $_SESSION['json'];
        echo $register_json;
        $register_php = json_decode($register_json, true);
        
        
        $name = $register_php['name'];
        $email = $register_php['email'];
        $address = $register_php['address'];
        $phone = $register_php['phone'];
        $experience = $register_php['experience'];
        $password_1 = $register_php['password_1'];
      
       //$sql = "INSERT INTO memberlist (MemName, PhoneNum, MemAddress, MemExperience, MemEmail, MemPassword)VALUES('$name', '$phone', '$address', '$experience', '$email', '$password_1')";
        $sql = "CALL addMember('$name', '$phone', '$address', '$experience', '$email', '$password_1')";   


        $user_email_query = "CALL emailCheck('$email')";
        $query_result = mysqli_query($db, $user_email_query);
        while($row = mysqli_fetch_assoc($query_result)){
          if($row['MemEmail'] == $email){
            $emailTest = $row['MemEmail'];
          } 
        }
        echo"$emailTest";
        mysqli_close($db);
        $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');
         //save to db and check 
        if($emailTest == $email) {
          echo"account already exists";
        } else{
          if(mysqli_query($db, $sql)){
          
          } else {
              echo 'query error: ' . mysqli_error($db);
          }
        }
      $_SESSION['request'] == '';
               
    }

?>

<!DOCTYPE html>
<html>
  <?php include ('templates/header.php') ?>
      <h1>Welcome to TaskGrabb</h2>
      <h2 class = 'center'>Login</h2>
    <section class="container grey-text">
      <form class="white" action = "login.php" method="POST">
        <label class = "black-text"> Email : </label>
        <input type="email" name="email" placeholder="jamescurtis@gmail.com" value="<?php echo htmlspecialchars($email) ?>">
        <div class = "red-text"><?php echo $errors['email'] ?></div>

        <label class = "black-text" for="password"> Password : </label>
        <input type="password" name="password">
        <div class = "red-text"><?php echo $errors['password'] ?></div>


        <div class="center">
          <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
        </div>

         <p>Not a user?<a href="register.php"><b>Register here</b></a></p>

      </form>
    </section>
  <?php include ('templates/footer.php') ?>

</html>
