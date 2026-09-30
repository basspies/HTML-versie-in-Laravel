<?php
include 'conn.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$project = false;

if ($id !== false && $id !== null) {
  $stmt = $conn->prepare('SELECT * FROM projecten WHERE id = :id');
  $stmt->execute(['id' => $id]);
  $project = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio Website - Overzichtspagina</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
      crossorigin="anonymous"
    />
  </head>
  <body>
    <main>
      <div class="container">
        <?php if ($project): ?>
          <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 g-1 projects">
            <div class="project card shadow-sm card-body m-2">
              <div class="card-text">
                <h2><?= htmlspecialchars((string) ($project['titel'] ?? 'Project'), ENT_QUOTES, 'UTF-8') ?></h2>
                <?php foreach ($project as $field => $value): ?>
                  <?php if (!in_array(strtolower((string) $field), ['titel', 'id'], true)): ?>
                    <div>
                      <strong><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $field)), ENT_QUOTES, 'UTF-8') ?>:</strong>
                      <?= htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php else: ?>
          <p>Dit project is niet gevonden.</p>
        <?php endif; ?>
        <a href="../index.php" class="btn btn-sm btn-outline-secondary mt-3">Terug naar overzicht</a>
      </div>
    </main>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
