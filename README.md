# HTML-versie-in-Laravel


<!-- <?php
include 'cms/conn.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <?php

// require_once 'conn.php';

$sql = "SELECT id, titel, omschrijving FROM projecten";
$stmt = $conn->query($sql);

$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($projects as $project) {
    echo "ID: " . $project['id'] . "<br>";
    echo "Titel: " . $project['titel'] . "<br>";
    echo "Omschrijving: " . $project['omschrijving'] . "<br><br>";
}
?>
</body>
</html> -->