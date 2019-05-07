<?php
include("includes/init.php");

//To show messages
$feedbacks = array();

if (isset($_POST['submit_r'])){
  $submit_respond = TRUE;

  $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);
  $user_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
  $user_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
  if (preg_match('/^[a-zA-Z0-9._%]+@\w+\.[a-zA-Z]{2,4}$/', $user_email)) {
  } else {
    $user_email = NULL;
    $submit_respond = FALSE;
  }

  $user_phone = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_STRING);
  if (preg_match("/^[0-9\-]|[\+0-9]|[0-9\s]|[0-9()]*$/", $user_phone)) {
    // this reg ex is the format the html date input creates
  } else {
    $user_phone = NULL;
    $submit_respond = FALSE;
  }

  $comment = filter_input(INPUT_POST, "comment", FILTER_SANITIZE_STRING);
  if ($submit_respond == TRUE) {
    $to = "jeg256@gmail.com";
    $subject = "Form Submission";
    $message = $user_name . " wrote the following message:" . "\n\nReason: " . $reason . "\n\nComment: " . $comment . "\n\nPhone Number: " . $user_phone;
    $headers = "From:" . $user_email;
    mail($to, $subject, $message, $headers);

    $sql1 = "INSERT INTO submissions (reason, name, email, phone, comment) VALUES (:reason, :user_name, :user_email, :user_phone, :comment);";
    $params1 = array(
      ':reason' => $reason,
      ':user_name' => $user_name,
      ':user_email' => $user_email,
      ':user_phone' => $user_phone,
      ':comment' => $comment
    );
    $result = exec_sql_query($db, $sql1, $params1);
    if ($result) {
      // echo "<p class=\"subtitle1\">Your response was successfully submitted!</p>\n";
      array_push($feedbacks, "Your response was successfully submitted!");
    } else {
      // echo "<p class=\"subtitle1\">Failed to submit your response, please try again</p>";
      array_push($feedbacks, "Failed to submit your response, please try again");
    }
  }
}

function print_record($record) {
?>
<tr>
  <td><?php echo htmlspecialchars($record["reason"], ENT_COMPAT);?></td>
  <td><?php echo htmlspecialchars($record["name"], ENT_COMPAT);?></td>
  <td><?php echo htmlspecialchars($record["email"], ENT_COMPAT);?></td>
  <td><?php echo htmlspecialchars($record["phone"], ENT_COMPAT);?></td>
  <td><?php echo htmlspecialchars($record["comment"], ENT_COMPAT);?></td>
</tr>
<?php
}

?>

<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php");?>



<body>
  <?php $contact="current_page"; ?>

  <?php include("includes/header.php");?>
  <div id="content-wrap">
  <div class="contact">

  <h3 class="subtitle2">Submit your question or concern through the form below</h3>

  <?php
    foreach($feedbacks as $feedback){
      echo "<p class='subtitle1'>" . htmlspecialchars($feedback) . "</p>\n";
    }
    ?>

  <div class="container">
  <form action="contact.php" method="post">

    <label for="reason"> </label>
      <select id="reason" name="reason" style="width: 100%;" required>
        <option value="" disabled selected hidden>Choose Your Reason</option>
        <option value="general">General Inquiry</option>
        <option value="purchasing">Purchasing item</option>
        <option value="exhibition">Exhibition</option>
      </select>

    <label for="name">Name:</label>
    <input type="text" id="name" name="name" placeholder="Required" style="width: 100%;" required/>

    <label for="email">Email:</label>
    <input type="text" id="email" name="email" placeholder="Required" style="width: 100%;" required/>

    <label for="phone">Phone Number:</label>
    <input type="text" id="phone" name="phone" placeholder="Required" style="width: 100%;" required/>

    <label for="Comment">Comment:</label>
    <textarea id="Comment" name="Comment" style="height:200px;width: 100%; "></textarea>

    <button class="button" type="submit" name="submit_r">Submit</button>
  </form>
  </div>






  </div>
  </div>
  <?php include("includes/footer.php");?>
</body>
</html>
