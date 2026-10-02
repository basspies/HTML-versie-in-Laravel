<?php
include 'conn.php';
include 'auth.php';
include 'validatie.php';

vereisLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $conn->prepare('SELECT * FROM projecten WHERE id = :id');
$stmt->execute(['id' => $id ?: 0]);
$bestaand = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bestaand) {
  header('Location: ../index.php');
  exit;
}

$project = $bestaand;
$fouten = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  [$project, $fouten] = valideerProject($_POST);
  $project['afbeelding'] = $bestaand['afbeelding'];

  $nieuweAfbeelding = null;
  if (!$fouten) {
    try {
      $nieuweAfbeelding = uploadAfbeelding($_FILES['afbeelding'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
    } catch (Exception $ex) {
      $fouten[] = $ex->getMessage();
    }
  }

  if (!$fouten) {
    if ($nieuweAfbeelding) {
      $project['afbeelding'] = $nieuweAfbeelding;
    }

    $stmt = $conn->prepare('
      UPDATE projecten
      SET titel = :titel, omschrijving = :omschrijving, type = :type, jaar = :jaar, afbeelding = :afbeelding
      WHERE id = :id
    ');
    $stmt->execute($project + ['id' => $id]);

    // Oude afbeelding opruimen als er een nieuwe is geüpload
    if ($nieuweAfbeelding) {
      verwijderAfbeelding($bestaand['afbeelding']);
    }

    header('Location: ../index.php');
    exit;
  }
}

$knopTekst = 'Wijzigingen opslaan';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio Website - Project bewerken</title>
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
        <h1 class="h3 mb-3">Project bewerken</h1>
        <?php include 'form.php'; ?>
      </div>
    </main>
  </body>
</html>
