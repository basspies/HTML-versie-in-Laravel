<?php
include 'conn.php';
include 'auth.php';

if (isIngelogd()) {
  header('Location: ../index.php');
  exit;
}

$fout = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $wachtwoord = $_POST['wachtwoord'] ?? '';

  if ($email === '' || $wachtwoord === '') {
    $fout = 'Vul je e-mailadres en wachtwoord in.';
  } else {
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($wachtwoord, $user['wachtwoord'])) {
      session_regenerate_id(true);
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['naam'] = $user['naam'];
      header('Location: ../index.php');
      exit;
    }
    $fout = 'Onjuist e-mailadres of wachtwoord.';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio Website - Login</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
      crossorigin="anonymous"
    />
  </head>
  <body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin m-auto" style="max-width: 360px; width: 100%;">
      <form method="POST" action="">
        <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

        <?php if ($fout): ?>
          <div class="alert alert-danger"><?= e($fout) ?></div>
        <?php endif; ?>

        <div class="form-floating">
          <input
            type="email"
            name="email"
            class="form-control"
            id="floatingInput"
            placeholder="name@example.com"
            value="<?= e($email) ?>"
            required
          />
          <label for="floatingInput">Email address</label>
        </div>
        <div class="form-floating">
          <input
            type="password"
            name="wachtwoord"
            class="form-control"
            id="floatingPassword"
            placeholder="Password"
            required
          />
          <label for="floatingPassword">Password</label>
        </div>

        <button class="btn btn-primary w-100 py-2 mt-2" type="submit">
          Sign in
        </button>
        <a href="../index.php" class="btn btn-link w-100 mt-2">Terug naar overzicht</a>
      </form>
    </main>
  </body>
</html>
