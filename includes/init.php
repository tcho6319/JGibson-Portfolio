<?php
// vvv DO NOT MODIFY/REMOVE vvv

// check current php version to ensure it meets 2300's requirements
function check_php_version()
{
  if (version_compare(phpversion(), '7.0', '<')) {
    define(VERSION_MESSAGE, "PHP version 7.0 or higher is required for 2300. Make sure you have installed PHP 7 on your computer and have set the correct PHP path in VS Code.");
    echo VERSION_MESSAGE;
    throw VERSION_MESSAGE;
  }
}
check_php_version();

function config_php_errors()
{
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 0);
  error_reporting(E_ALL);
}
config_php_errors();

// open connection to database
function open_or_init_sqlite_db($db_filename, $init_sql_filename)
{
  if (!file_exists($db_filename)) {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (file_exists($init_sql_filename)) {
      $db_init_sql = file_get_contents($init_sql_filename);
      try {
        $result = $db->exec($db_init_sql);
        if ($result) {
          return $db;
        }
      } catch (PDOException $exception) {
        // If we had an error, then the DB did not initialize properly,
        // so let's delete it!
        unlink($db_filename);
        throw $exception;
      }
    } else {
      unlink($db_filename);
    }
  } else {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }
  return null;
}

function exec_sql_query($db, $sql, $params = array())
{
  $query = $db->prepare($sql);
  if ($query and $query->execute($params)) {
    return $query;
  }
  return null;
}

// ^^^ DO NOT MODIFY/REMOVE ^^^

// You may place any of your code here.

$db = open_or_init_sqlite_db('secure/site.sqlite', 'secure/init.sql');

$messages = array();

// function to record and display a message
function session_alert($message) {
  global $messages;
  array_push($messages, $message);
}

// login and logout

/* Source: lab 8 solution (init.php) by Kyle Harms */

// A function to login
function log_in($username, $password) {
  global $db;
  global $current_user;

  if (isset($username) && isset($password)) {
    // username and password should exist in the database
  $sql = "SELECT * FROM users WHERE username = :username;";
  $params = array (':username' => $username );
  $records = exec_sql_query ($db, $sql, $params) -> fetchAll();

  if ($records){
    // there should be only one record because user name is unique
    if (password_verify($password, $records[0]['password'])){
      // password is checked in database, and then session is generated/stored
    $session = session_create_id();
    $sql = "UPDATE users SET session = :session WHERE id=:user_id;";
    $params = array(':user_id' => $records[0]['id'],
                    ':session' => $session);
    $result = exec_sql_query($db, $sql, $params);
      // session is stored in db

    if ($result) {
      // session successfully stored in db
        setcookie("session", $session, time()+3600);
        // 3600 sec is 60sec * 60min, which is 1 hour

        session_alert("Logged in as $username");
        return $username;
        $current_user = $records[0];
        return $current_user;

      } else{
        session_alert("Log in failed.");
      }

    } else {
      session_alert("Invalid username or password.");
    }

  } else {
    session_alert ("Invalid username or password.");
  }

} else {
  session_alert ("No username or password given.");
}

return NULL;
$current_user = NULL;
}

// a logout function
function log_out() {
  global $current_user;
  global $db;

  if ($current_user){
    $sql = "UPDATE users SET session = :session WHERE username = :username;";
    $params = array ('username' => $current_user, ':session' => NULL);
    if (!exec_sql_query($db, $sql, $params)) {
      record_alert("Log out failed.");
    }
  }
  // expire the session
  setcookie("session","", time()-3600);
  $current_user=NULL;
}

// a function to get an ID that is currently logged in
function get_id(){
  global $db;

  if (isset ($_COOKIE["session"])) {
    $session = $_COOKIE["session"];
    $sql= "SELECT *FROM users WHERE session = :session;";
    $params = array(':session'=>$session);
    $records= exec_sql_query($db, $sql, $params) -> fetchAll();

    if ($records){
      // there should be only one record because user name is unique
      return $records[0]['id'];
    }
  }
  return NULL;
}


// a function to check whether a user is logged in or not
function check_user_log_in () {
  global $current_user;
  // if $current_user is not null, a user should be logged in
  return ($current_user != NULL);
}

// check if the user should be logged in or not
if (isset ($_POST['login'])) {
  $username= filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
  $username= trim($username);
  $password= filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
  $current_user= log_in($username, $password);

} else {
  $current_user = check_user_log_in();
}

if (!empty($current_user)) {
  $current_user_id = get_id();
}







?>
