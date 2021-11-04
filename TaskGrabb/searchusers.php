<?php
    session_start();
    include ('config/db_connect.php');

    if ($_SESSION['login'] != TRUE){
    header('Location: login.php');
    }

    //For the Table Filter search, this is a major reference
    //https://www.w3schools.com/howto/howto_js_filter_table.asp


    //create json
    $user_id = $_SESSION['ses_MemberID'];
    $user_sql = "SELECT * FROM memberlist WHERE MemberID != '$user_id' ";
    $user_json_result = mysqli_query($db, $user_sql);
    $user_requests = array();
    $inc = 0;
    while($row = mysqli_fetch_assoc($user_json_result))
    {
        $user_requests[$inc] = $row;
        $inc = $inc + 1;
   }
   $user_json = json_encode($user_requests);  

?>

<html>
  <?php include ('templates/memberheader.php'); ?>

  <h2 class = 'center'>Userlist</h2>
    <h3 class = 'center'>Looking for a certain user?</h3>
      <section class="container grey-text">
       <form class="white" action = "TaskSearch.php" method="POST">

          <label class = "black-text"> Username: </label>
          <input type="text"id ="myusername" onkeyup="mySearch1()" placeholder="Search for Username..">

          <label class = "black-text"> Rating: </label>
          <input type="text" id="myrating" onkeyup="mySearch2()" placeholder="Search for Rating..">


          <label class = "black-text"> E-mail: </label>
          <input type="text" id="myemail" onkeyup="mySearch3()" placeholder="Search for E-mail..">


          <label class = "black-text"> Skills: </label>
          <input type="text" id="myskills" onkeyup="mySearch4()" placeholder="Search for Skills..">


        </section>
      </form>



      <h2 class = 'center'>Userbase</h2>

      <section class="container grey-text">
        <form class="white">
            <table id ="myTable">
              <tr class="header">
                  


                    <th>Username</th>
                    <th>Rating</th>
                    <th>E-mail</th>
                    <th>Skills</th>
                    <th style="width:10%;">
                    

              </tr>
              <tr>
                <?php 

                //decode json
                $user_php = json_decode($user_json,true);

                if (count($user_php) > 0) {
                     
                  foreach($user_php as $row){
                    echo "<tr><td>".$row['MemName']."</td><td>".$row['Avg_Rating']."</td><td>".$row['MemEmail']."</td><td>".$row['MemExperience']."</td><td>";
                  }
                }echo "</table";
                
                  
                  
                
               
                
                
                // if (mysqli_num_rows($task_result) > 0) {
                  // while($row = mysqli_fetch_row($task_result)){
                    // echo "<tr><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[8]."</td><td>".$row[7]."</td><td>";}
                // }echo"</table";
                ?>
                </td></td>
              </tr>
            </table>

        </form>
    </section>


  <?php include ('templates/footer.php'); ?>

  <script>
  //https://www.w3schools.com/howto/tryit.asp?filename=tryhow_js_filter_table

  function mySearch1() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myusername");
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

  function mySearch2() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myrating");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[2];
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
    input = document.getElementById("myemail");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[8];
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
    input = document.getElementById("myskills");
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

</script>


</html>
