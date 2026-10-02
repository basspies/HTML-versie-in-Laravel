<?php
include 'cms/conn.php';
include 'cms/auth.php';

$search = trim($_GET['search'] ?? '');
$jaar = filter_input(INPUT_GET, 'jaar', FILTER_VALIDATE_INT) ?: null;
$page = max(1, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1);
$perPage = 5;

// Zoeken op titel en filteren op jaar
$where = 'WHERE titel LIKE :search';
$params = ['search' => '%' . $search . '%'];
if ($jaar) {
  $where .= ' AND jaar = :jaar';
  $params['jaar'] = $jaar;
}

// Pagination: totaal aantal projecten tellen
$stmt = $conn->prepare("SELECT COUNT(*) FROM projecten $where");
$stmt->execute($params);
$totaal = (int) $stmt->fetchColumn();
$totaalPaginas = max(1, (int) ceil($totaal / $perPage));
$page = min($page, $totaalPaginas);
$offset = ($page - 1) * $perPage;

$stmt = $conn->prepare("
  SELECT *
  FROM projecten
  $where
  ORDER BY titel ASC
  LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Alle jaren voor de filter-dropdown
$jaren = $conn->query('SELECT DISTINCT jaar FROM projecten ORDER BY jaar DESC')->fetchAll(PDO::FETCH_COLUMN);

// Maakt een link naar een pagina, met behoud van zoekterm en filter
function paginaLink(int $nummer): string
{
  $query = array_filter([
    'search' => $_GET['search'] ?? '',
    'jaar' => $_GET['jaar'] ?? '',
    'page' => $nummer,
  ], fn($waarde) => $waarde !== '');
  return '?' . http_build_query($query);
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
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">
          <div>
            <?php if (isIngelogd()): ?>
              <a href="cms/add.php" class="btn btn-success">+ Add project</a>
            <?php endif; ?>
          </div>
          <div class="d-flex align-items-center gap-2">
            <?php if (isIngelogd()): ?>
              <span>Ingelogd als <strong><?= e($_SESSION['naam']) ?></strong></span>
              <a href="cms/logout.php" class="btn btn-outline-danger">Logout</a>
            <?php else: ?>
              <a href="cms/login.php" class="btn btn-primary">Login</a>
            <?php endif; ?>
          </div>
        </div>

        <div class="d-flex justify-content-center align-items-center m-4">
          <nav aria-label="search and filter">
            <form method="GET" action="" class="d-flex flex-wrap gap-2">
              <input
                type="text"
                name="search"
                class="form-control w-auto"
                placeholder="Zoek projecten..."
                value="<?= e($search) ?>"
              >
              <select name="jaar" class="form-select w-auto">
                <option value="">Alle jaren</option>
                <?php foreach ($jaren as $j): ?>
                  <option value="<?= e((string) $j) ?>" <?= (int) $j === $jaar ? 'selected' : '' ?>><?= e((string) $j) ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-outline-primary">Zoeken</button>
              <?php if ($search !== '' || $jaar): ?>
                <a href="index.php" class="btn btn-outline-secondary">Reset</a>
              <?php endif; ?>
            </form>
          </nav>
        </div>

        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 g-1 projects">
          <?php foreach ($projects as $project): ?>
            <div class="project card shadow-sm card-body m-2">
              <div class="d-flex gap-3 flex-wrap">
                <?php if (!empty($project['afbeelding'])): ?>
                  <img src="uploads/<?= e($project['afbeelding']) ?>" alt="<?= e($project['titel']) ?>" class="rounded" style="width: 160px; height: 120px; object-fit: cover;">
                <?php endif; ?>
                <div class="card-text">
                  <h2><?= htmlspecialchars((string) ($project['titel'] ?? 'Project'), ENT_QUOTES, 'UTF-8') ?></h2>
                  <?php foreach ($project as $field => $value): ?>
                    <?php if (!in_array(strtolower((string) $field), ['titel', 'id', 'type', 'jaar', 'afbeelding'], true)): ?>
                      <div>
                        <strong><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $field)), ENT_QUOTES, 'UTF-8') ?>:</strong>
                        <?= htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8') ?>
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="btn-group">
                  <a href="cms/detail.php?id=<?= urlencode((string) ($project['id'] ?? '')) ?>" class="btn btn-sm btn-outline-secondary">Details</a>
                  <?php if (isIngelogd()): ?>
                    <a href="cms/edit.php?id=<?= urlencode((string) $project['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <form method="POST" action="cms/delete.php" onsubmit="return confirm('Weet je zeker dat je dit project wilt verwijderen?');">
                      <input type="hidden" name="id" value="<?= e((string) $project['id']) ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">Delete</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (!$projects): ?>
            <p>Er zijn geen projecten gevonden.</p>
          <?php endif; ?>
        </div>

        <?php if ($totaalPaginas > 1): ?>
          <div class="d-flex justify-content-center align-items-center m-4">
            <nav aria-label="Page navigation">
              <ul class="pagination">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                  <a class="page-link" href="<?= e(paginaLink($page - 1)) ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $totaalPaginas; $i++): ?>
                  <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= e(paginaLink($i)) ?>"><?= $i ?></a>
                  </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totaalPaginas ? 'disabled' : '' ?>">
                  <a class="page-link" href="<?= e(paginaLink($page + 1)) ?>">Next</a>
                </li>
              </ul>
            </nav>
          </div>
        <?php endif; ?>

      </div>
    </main>
    <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"
  ></script>
  </body>
</html>
