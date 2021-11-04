<?php

  session_start();
  include ('config/db_connect.php');

  $name = $email = $address = $phone = $experience = '';
  $errors = array('name' => '', 'email' => '', 'address' => '', 'phone' => '', 'password_1' =>'', 'password_2' => '');

  if(isset($_POST['submit'])){

    //Checks for valid entry
    if(empty($_POST['name'])){
      $errors['name'] = 'Please enter your name <br />';
    } else {
      $name = $_POST['name'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $name)){
        $errors['name'] = 'Name must be Letters and spaces only <br />';
      }
    }

    if(empty($_POST['email'])){
      $errors['email'] = 'Please enter your email <br />';
    } else {
      $email = $_POST['email'];
      if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = 'email must be a valid email address <br />';
      }

    }
    //check for duplicate emails
    //check for duplicate emails
    $emailcheck = mysqli_real_escape_string($db, $_POST['email']);
    
    //$user_check_query = "SELECT MemEmail FROM memberlist WHERE MemEmail = '$emailcheck'";
    
    $user_check_query = "CALL emailCheck('$email')";
    $query_result = mysqli_query($db, $user_check_query);
    while($row = mysqli_fetch_assoc($query_result)){
      if($row['MemEmail'] == $emailcheck){
        $errors['email'] = 'This email is already registered! <br />';
      }
    }
    mysqli_close($db);
    $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');
    /*
    $results = mysqli_query($db, $user_check_query);
    $members = mysqli_fetch_all($results, MYSQLI_ASSOC);
    mysqli_free_result($results);
    foreach($members as $member) {
      if($member['MemEmail'] == $emailcheck){
        $errors['email'] = 'This email is already registered! <br />'; }
    }*/

    if(empty($_POST['address'])){
      $errors['address'] = 'Please enter your address <br />';
    } else {
      $address = $_POST['address'];
      if(!preg_match('/^[a-zA-Z1-9\s]+$/', $address)){
        $errors['address'] = 'Please enter a valid address <br />';
      }
    }

    if(empty($_POST['phone'])){
      $errors['phone'] = 'Please enter your phone number <br />';
    } else {
      $phone = $_POST['phone'];
    }

    if(!empty($_POST['experience'])){
      $experience = $_POST['experience'];
    }

    if(empty($_POST['password_1'])){
      $errors['password_1'] = 'Please enter a password <br />';
    } else {
      $password_1 = $_POST['password_1'];
    }

    if(empty($_POST['password_2'])){
      $errors['password_2'] = 'Please confirm your password <br />';
    } else {
      $password_2 = $_POST['password_2'];
    }

    if($password_1 != $password_2){
      $errors['password_2'] ='Passwords do no match <br />';
    }

    //bring to home page if no errors
    if(array_filter($errors)){
      //echo 'there are errors in the form';
    } else {
      //create json
      $register_results = array ('name' => '', 'email' => '', 'address' => '', 'phone' => '', 'experience' => '');
      $register_results['name'] = mysqli_real_escape_string($db, $_POST['name']);
      $register_results['email'] = mysqli_real_escape_string($db, $_POST['email']);
      $register_results['address'] = mysqli_real_escape_string($db, $_POST['address']);
      $register_results['phone'] = mysqli_real_escape_string($db, $_POST['phone']);
      $register_results['experience'] = mysqli_real_escape_string($db, $_POST['experience']);
      $register_results['password_1'] = mysqli_real_escape_string($db, $_POST['password_1']);
      $register_json = json_encode($register_results);
      
      
      $_SESSION['request'] = 'register';
      $_SESSION['json'] = $register_json;
      
     header('Location: login.php');
      
      //for Json
      // $_SESSION['request'] = 'register';
      // $_SESSION['name'] = $name;
      // $_SESSION['email'] = $email;
      // $_SESSION['address'] = $address;
      // $_SESSION['phone'] = $phone;
      // $_SESSION['experience'] = $experience;
      // $_SESSION['password'] = $password_1;
      

      // //create 
      // $register_results = array ('name' => '', 'email' => '', 'address' => '', 'phone' => '', 'experience' => '');
      // $register_results['name'] = $name;
      // $register_results['email'] = $email;
      // $register_results['address'] = $address;
      // $register_results['phone'] =  $phone;
      // $register_results['experience'] = $experience;
      // $register_results['password'] = $password_1;
      // $register_json = json_encode($register_results);
      
      // $register_php = json_decode($register_json, true);
      
      // $name = $register_php['name'];
      // $email = $register_php['email'];
      // $address = $register_php['address'];
      // $phone = $register_php['phone'];
      // $experience = $register_php['experience'];
      // $password_1 = $register_php['password_1'];
      
      

      //add new member to database
     //$sql = "INSERT INTO memberlist (MemName, PhoneNum, MemAddress, MemExperience, MemEmail, MemPassword)VALUES('$name', '$phone', '$address', '$experience', '$email', '$password_1')";
      //$sql = "CALL addMember('$name', '$phone', '$address', '$experience', '$email', '$password_1')";   

    
      //save to db and check
      // if(mysqli_query($db, $sql)){
        // header('Location: login.php');
      // } else {
        // echo 'query error: ' . mysqli_error($db);
      // }




    }


  }

?>


<!DOCTYPE html>
<html>

  <?php include ('templates/header.php'); ?>

      <h1>Welcome to TaskGrabb</h2>
      <h2 class="center">Register</h2>

   <section class="container grey-text">
     <form class="white" action="register.php" method="POST">

          <label class = "black-text"> Name : </label>
          <input type="text" name="name" placeholder="James Curtis" value="<?php echo htmlspecialchars($name) ?>">
          <div class = "red-text"><?php echo $errors['name'] ?></div>

          <label class = "black-text"> Email : </label>
          <input type="email" name="email" placeholder="jamescurtis@gmail.com" value="<?php echo htmlspecialchars($email) ?>">
          <div class = "red-text"><?php echo $errors['email'] ?></div>

          <label class = "black-text">Address : </label>
          <input type="text" name="address" placeholder="123456 Signal Hill Dr" value="<?php echo htmlspecialchars($address) ?>">
          <div class = "red-text"><?php echo $errors['address'] ?></div>

          <label class = "black-text"> Phone Number : </label>
          <input type="tel" name="phone" placeholder="123-456-7890" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" value="<?php echo htmlspecialchars($phone) ?>">
          <div class = "red-text"><?php echo $errors['phone'] ?></div>

          <label class = "black-text"> Experience : </label>
          <input type="text" name="experience" placeholder="mowing lawns, gardening, etc." value="<?php echo htmlspecialchars($experience) ?>">

          <label class = "black-text"> Password : </label>
          <input type="password" name="password_1">
          <div class = "red-text"><?php echo $errors['password_1'] ?></div>

          <label class = "black-text"> Confirm Password : </label>
          <input type="password" name="password_2">
          <div class = "red-text"><?php echo $errors['password_2'] ?></div>

          <div class="center">
            <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
          </div>

        <p class = "black-text">Already a user?<a href="login.php"><b>Login</b></a></p>

      </form>
    </section>



  <?php include ('templates/footer.php'); ?>
</html>
