<?php
session_start();
session_destroy();
$_SESSION['login'] = FALSE;


?>



<html>
    <?php include ('templates/header.php'); ?>

    <h2 class = 'center'>Thank you for using Taskgrabb!</h2>
    <h3 class = 'center'>See you again next time! </h3>

    <?php include ('templates/footer.php'); ?>
</html>
