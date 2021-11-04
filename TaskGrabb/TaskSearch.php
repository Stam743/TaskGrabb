<?php
    session_start();
    include ('config/db_connect.php');
    
    if ($_SESSION['login'] != TRUE){
    header('Location: login.php');
    }

    //error checks
    $errors = array('taskID' => '');
    
    $user_id = $_SESSION['ses_MemberID'];
    
    //create json
    $task_json_sql = "SELECT * FROM tasks WHERE Poster_MemberID != '$user_id' ";
    $task_json_result = mysqli_query($db, $task_json_sql);
    $ytasks_requests = array();
    $inc = 0;
    while($row = mysqli_fetch_assoc($task_json_result))
    {
        $ytasks_requests[$inc] = $row;
        $inc = $inc + 1;
   }
   $ytasks_json = json_encode($ytasks_requests);  

    //Accept Task
    if(isset($_POST['accept'])){
        //print_r("Gah<p>");
        $check_task_id = $_POST['taskID'];
        //$sql = "SELECT * FROM tasks WHERE Poster_MemberID != '$user_id' ";
        $sql = "CALL findTask('$user_id')";
        $results = mysqli_query($db, $sql);
        //$members = mysqli_fetch_all($results, MYSQLI_ASSOC);
        while($members = mysqli_fetch_assoc($results)){
          if($members['Poster_MemberID'] == $user_id){
            $errors['taskID'] = '';
          }
        }
        mysqli_close($db);
        $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');
        $errors['taskID'] = 'Please enter a valid task ID<br/>';
        //by default has error
        //if($members){
        //  foreach($members as $member){
            /*
            print_r("= in the Data<p>");
            print_r($members['TaskID']);
            print_r("= in the Data<p>");
            print_r($member['TaskID']);
            print_r("= in the Data<p>");
            print_r($check_task_id);
            print_r("= Your ID<p>");
            //*/
        //    $errors['taskID'] = '';
            
        //  }
        //}
        /*
        foreach($members as $member){
             //Checks if the taskID given belongs to a task owned by another user
            if($member['TaskID'] == $check_task_id){
                //If it's a successful validation, the error will be cleared and thus it would be validated
            $errors['taskID'] = '';
            }
        }*/

        if(empty($_POST['taskID'])){
            $errors['taskID'] = 'Please enter a valid task ID<br/>';
          } else{
          }
          
        if(array_filter($errors)){
        //echo 'there are errors in the form';
            }else{  
                
                
            $task_id = $_POST['taskID'];
            //$sql = "INSERT INTO completes_task(Worker_MemberID, TaskID) VALUES ('$user_id', '$task_id')";
            $sql = "CALL finishTask('$user_id', '$task_id')";  
              //save to db and check
              if(mysqli_query($db, $sql)){
              } else {
                echo 'query error: ' . mysqli_error($db);
              }
          
          }

    }

?>

<!DOCTYPE html>
<html>

  <?php include ('templates/memberheader.php') ?>
  <h4 class = 'center'>Accept Task</h4>

  <section class="container grey-text">
    <form class="white" action = "TaskSearch.php" method="POST">
      <div class="center">
        <label class = "black-text" for="taskID"> Enter Task ID : </label>
          <input type="number" name="taskID" placeholder="1" value="<?php echo htmlspecialchars($taskID) ?>">
        <div class = "red-text"><?php echo $errors['taskID'] ?></div>
        <div class="center">
          <input type="submit" name="accept" value="submit" class="btn brand z-depth-0">
        </div>
      </div>
    </form>
  </section>



    <h2 class = 'center'>Task Search</h2>
      <h3 class = 'center'>Search for a Task here!</h3>
        <section class="container grey-text">
         <form class="white" action = "TaskSearch.php" method="POST">

            <label class = "black-text"> Title: </label>
            <input type="text"id ="ttitle" onkeyup="mySearch1()" placeholder="Search for Title..">

            <label class = "black-text"> Location: </label>
            <input type="text" id="tlocation" onkeyup="mySearch2()" placeholder="Search for Location..">


            <label class = "black-text"> Skill Requirements: </label>
            <input type="text" id="mySkill" onkeyup="mySearch3()" placeholder="Search for Skill Requirements..">


            <label class = "black-text"> Difficulty: </label>
            <input type="text" id="myDiff" onkeyup="mySearch4()" placeholder="Search for Difficulty..">


            <label class = "black-text"> Payment Type: </label>
            <input type="text" id="myPay" onkeyup="mySearch5()" placeholder="Search for Payment..">


            <label class = "black-text"> #No of People: </label>
            <input type="text" id="myNum" onkeyup="mySearch6()" placeholder="Search for #No of people..">

          </section>
        </form>


        <h2 class = 'center'>Existing Tasks</h2>

        <section class="container grey-text">
          <form class="white">
              <table id ="myTable">
                <tr class="header">
                <th>Title</th>
                   

                      
                      <th>Location</th>
                      <th>ID</th>
                      <th>Skills Required</th>
                      <th>Description</th>
                      <th>Difficulty</th>
                      <th># of workers needed</th>
                      <th>Payment Type</th>
                </tr>
                <tr>
                <?php 

                
                //decode json
                $ytasks_php = json_decode($ytasks_json,true);

                if (count($ytasks_php) > 0) {
                     
                  foreach($ytasks_php as $row){
                    echo "<tr><td>".$row['Task_Title']."</td><td>".$row['Task_Location']."</td><td>".$row['TaskID']."</td><td>".$row['Skills_Required']."</td><td>".$row['Task_Description']."</td><td>".$row['Difficulty']."</td><td>".$row['Num_Workers_Needed']."</td><td>".$row['Payment_Type'];
                  }
                  echo "</table>";
                }
                  
                  
                ?>
                  </td></td>
                </tr>
              </table>

          </form>
      </section>

  <?php include ('templates/memberfooter.php') ?>

<script>
//https://www.w3schools.com/howto/tryit.asp?filename=tryhow_js_filter_table

function mySearch1() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ttitle");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function mySearch2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("tlocation");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function mySearch3() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("mySkill");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[3];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function mySearch4() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myDiff");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[5];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function mySearch5() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myPay");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[7];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function mySearch6() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myNum");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[6];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

</script>

</html>
