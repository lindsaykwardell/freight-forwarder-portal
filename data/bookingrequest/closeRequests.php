<?php
$servername = "";
$username = "";
$password = "";
$dbname = "";

// DB Class

class DB {
  public $conn;

  function __construct($servername, $username, $password, $dbname)
  {
    $this->conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($this->conn->connect_error) {
        die("Connection failed: " . $conn->connect_error
    );
    }
  }
}

$db = new DB($servername, $username, $password, $dbname);

$date = date('Y-m-d', strtotime(date('Y-m-d') . ' - 10 days'));

echo "Preparing Closeout for requests older than " . $date . "... ";
$stmt = $db->conn->prepare("UPDATE bookingrequest SET bookingrequestType = 'END' WHERE bookingrequestType = 'DONE' AND lastUpdated < ?");
$stmt->bind_param('s', $date);
if ($stmt->execute()) {
  echo "Closeout Success!";
} else {
  echo "Closeout Failed: " . $stmt->error;
}
?>
