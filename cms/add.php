<?php
include 'conn.php';
include 'auth.php';
include 'validatie.php';

vereisLogin();

$project = [];
$fouten = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  [$project, $fouten] = valideerProject($_POST);

  if (!$fouten) {
    try {
      $project['afbeelding'] = uploadAfbeelding($_FILES['afbeelding'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
    } catch (Exception $ex) {
      $fouten[] = $ex->getMessage();
    }
  }

  if (!$fouten) {
    $stmt = $conn->prepare('
      INSERT INTO projecten (titel, omschrijving, type, jaar, afbeelding)
      VALUES (:titel, :omschrijving, :type, :jaar, :afbeelding)
    ');
    $stmt->execute($project);
    header('Location: ../index.php');
    exit;
  }
}

$knopTekst = 'Project toevoegen';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio Website - Project toevoegen</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
      crossorigin="anonymous"
    />
  </head>
  <body>
    <main>
      <div class="container my-4" style="max-width: 640px;">
        <h1 class="h3 mb-3">Project toevoegen</h1>
        <?php include 'form.php'; ?>
      </div>
    </main>
  </body>
</html>
