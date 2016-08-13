<?php

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $success = true;
  $token = $_POST['token'];

  if ($token != $_SESSION['token']) {
    $msg = "Token invalid."; $success = false;
  }

  if (isset($_POST['confirm_password'])) {

   $old_pw = $_POST['old_pw'];
   $pw = $_POST['new_pw'];
   $pw2 = $_POST['new_pw2'];

   if (strlen($pw) < 10 ) {$msg = "Passwords to short."; $success = false;}
   if (strlen($pw) > 160 ) {$msg = "Passwords are to long."; $success = false;}
   if ($pw != $pw2) {$msg = "Passwords not equal."; $success = false;}

   $stmt = $database->prepare("SELECT password FROM users WHERE id = ?");
   $stmt->bind_param('i', $USER_ID);
   $stmt->execute();
   $stmt->bind_result($password_db);
   $stmt->fetch();
   $stmt->close();

   if ($password_db == NULL) {
     session_unset();
     session_destroy();
     header('Location: index.php');
   }

   if ($success == true) {
     if (password_verify($old_pw, $password_db)) {

       $hash = password_hash($pw, PASSWORD_DEFAULT);

       $stmt = $database->prepare("UPDATE users SET password = ?  WHERE id = ?");
       $stmt->bind_param('si',$hash,$_SESSION['user_id']);
       $stmt->execute();
       $stmt->close();

       $msg = "Password changed.";
       $success = true;
     } else {
       $msg = "Old Password is incorrect.";
       $success = false;
     }
   }
  }
}

?>

  <div class="col-md-4 col-md-offset-4 base-box">
  <form class="form-horizontal"  action="index.php?page=dashboard?account" method="post" >
    <h3>Account Password</h3>
    <?php
      if ($msg != "" and $success == false) {
        echo'<div class="alert alert-danger col-md-12" style="text-align: center;">
              <h2>Error!</h3>
              <p>'.$msg.'</p>
              </div>';
      } elseif ($msg != "" and $success == true) {
        echo'<div class="alert alert-success col-md-12" style="text-align: center;">
              <h2>Okay!</h3>
              <p>'.$msg.'</p>
              </div>';
      }
     ?>
   <div class="form-group">
       <label for="inputEmail" class="control-label col-xs-2">Old:</label>
       <div class="col-xs-6">
           <input type="password" class="form-control" placeholder="Old Password" name="old_pw">
       </div>
   </div>
   <div class="form-group">
       <label for="inputPassword" class="control-label col-xs-2">New:</label>
       <div class="col-xs-6">
           <input type="password" class="form-control" placeholder="New Password" name="new_pw">
       </div>
   </div>
   <div class="form-group">
       <label for="inputPassword" class="control-label col-xs-2">Repeat:</label>
       <div class="col-xs-6">
           <input type="password" class="form-control" placeholder="Repeat Password" name="new_pw2">
       </div>
   </div>
   <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>" />
   <div class="form-group">
       <div class="col-xs-offset-2 col-xs-10">
           <button type="submit" name="confirm_password" class="btn btn-primary">Save</button>
       </div>
   </div>
</form>

  </div>
