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

$date = date('Y-m-d', strtotime(date('Y-m-d') . ' - 7 days'));

echo "Preparing Deletion for requests deleted longer ago than " . $date . "... \n ";
$stmt = $db->conn->prepare("SELECT bookingrequestID FROM bookingrequest WHERE bookingrequestType = 'DELETE' AND lastUpdated < ?");
$stmt->bind_param('s', $date);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
  echo "Deleting " . $row['bookingrequestID'] . "... ";
  $stmt = $db->conn->prepare("DELETE FROM bookingrequest WHERE bookingrequestID = ?");
  $stmt->bind_param('i', $row['bookingrequestID']);
  $bkgIsDeleted = $stmt->execute();

  $stmt = $db->conn->prepare("DELETE FROM bkgreqcomment WHERE bkgreqID = ?");
  $stmt->bind_param('i', $row['bookingrequestID']);
  $bkgCommentsAreDeleted = $stmt->execute();
  if ($bkgIsDeleted && $bkgCommentsAreDeleted) {
    echo "Success!\n";
  } else {
    echo "Failure!\n";
  }
}
echo "Done!";
?>
