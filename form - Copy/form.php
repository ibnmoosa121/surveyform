<?php


$email = $_POST['email'];
$username = $_POST['username'];
$ref = $_POST['ref'];
$msg = $_POST['message'];


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header("Location: index.html?error=Email address '$email' is considered in-valid.");
}



$PDO=ConnectDatabase(); 


if (CheckData($PDO, 'Email', $email) OR CheckData($PDO, 'Username', $username)) {

   echo('Exist');
  
   header('Location: index.html?error=User aleady exists.');


} else {
    
  SaveData($PDO, array($email,$username));  
  //echo('Sent Email');
  header('Location: index.html?result=check your mail for reference');


  $msg .= "\nYour reference number is ".$ref;
  SendEmail($email, $msg);
  


}



















// functions

function ConnectDatabase() {

  $DB = array(
    'Server'=>'localhost',
    'User'=>'root',
    'Password'=>'',
    'Database'=>'form'
  );

  try {
      $Conn = new PDO("mysql:host=".$DB['Server'].";dbname=".$DB['Database'], $DB['User'], $DB['Password']);
      // set the PDO error mode to exception
      $Conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      echo("Connected Successfully");
      return $Conn;
  }
  catch(PDOException $Error)
  {
      echo("Connection Failed: " . $Error->getMessage());
      return FALSE;
  }

}

function SaveData($PDO,$Data) {
  $SQL = "INSERT INTO users (Email, Username) VALUES (?,?)";
  $PDO->prepare($SQL)->execute($Data);
 // echo("Saved Successfully");
  header('Location: index.html?result=check your mail for reference');
}

function CheckData($PDO,$Type,$Data) {
  $STMT = $PDO->prepare("SELECT * FROM users WHERE $Type=?");
  $STMT->execute([$Data]);
  $User = $STMT->fetch();
  return $User;
}

function GetData($PDO,$Key,$Type,$Data) {
  $Query = $PDO->prepare("SELECT $Key FROM users WHERE $Type=?");
  $Query->execute([$Data]);
  return $Query->fetchColumn();
}

function SendEmail($email, $msg) {

    // use wordwrap() if lines are longer than 70 characters
    $msg = wordwrap($msg,70);

    $headers = 'Cc: mohammedkhurram14@gmail.com' . "\r\n";

    // send email
    $sent = mail($email,"Project",$msg, $headers);

    if (!$sent) {
        $errorMessage = error_get_last();
        echo $errorMessage;
    }

    return $sent;
}




?>
