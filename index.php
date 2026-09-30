<?php
include 'cms/conn.php';

$search = $_GET['search'] ?? '';

$stmt = $conn->prepare("
    SELECT *
  FROM projecten
  WHERE titel LIKE ?
  ORDER BY titel ASC
");

$stmt->execute(["%" . $search . "%"]);

$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <div class="d-flex justify-content-center align-items-center m-4">
          <nav aria-label="search and filter">
            <form method="GET" action="">
              <input 
                type="text" 
               name="search" 
                placeholder="Zoek projecten..."
        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
    >
    <button type="submit">Zoeken</button>
</form>
        </nav>
        </div>
        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 g-1 projects">
          <?php foreach ($projects as $project): ?>
            <div class="project card shadow-sm card-body m-2">
              <div class="card-text">
                <h2><?= htmlspecialchars((string) ($project['titel'] ?? 'Project'), ENT_QUOTES, 'UTF-8') ?></h2>
                <?php foreach ($project as $field => $value): ?>
                  <?php if (!in_array(strtolower((string) $field), ['titel', 'id', 'type', 'jaar'], true)): ?>
                    <div>
                      <strong><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $field)), ENT_QUOTES, 'UTF-8') ?>:</strong>
                      <?= htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
              <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="btn-group">
                  <a href="cms/detail.php?id=<?= urlencode((string) ($project['id'] ?? '')) ?>" class="btn btn-sm btn-outline-secondary">Details</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (!$projects): ?>
            <p>Er zijn nog geen projecten gevonden.</p>
          <?php endif; ?>
        </div>

        <div class="d-flex justify-content-center align-items-center m-4">
          <nav aria-label="Page navigation example">
            <ul class="pagination">
              <li class="page-item">
                <a class="page-link" href="#">Previous</a>
              </li>
              <li class="page-item"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
          </nav>
        </div>

      </div>
    </main>
    <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"
  ></script>
  </body>
</html>
