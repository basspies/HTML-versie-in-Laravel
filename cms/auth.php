<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function isIngelogd(): bool
{
  return isset($_SESSION['user_id']);
}

// Gebruik bovenaan pagina's die alleen voor ingelogde gebruikers zijn
function vereisLogin(): void
{
  if (!isIngelogd()) {
    header('Location: login.php');
    exit;
  }
}

function e(?string $tekst): string
{
  return htmlspecialchars((string) $tekst, ENT_QUOTES, 'UTF-8');
}

// Verwerkt een geüploade afbeelding. Geeft de nieuwe bestandsnaam terug,
// null als er geen bestand is gekozen, of gooit een Exception bij een fout.
function uploadAfbeelding(array $bestand): ?string
{
  if ($bestand['error'] === UPLOAD_ERR_NO_FILE) {
    return null;
  }
  if ($bestand['error'] !== UPLOAD_ERR_OK) {
    throw new Exception('Uploaden van de afbeelding is mislukt.');
  }
  if ($bestand['size'] > 5 * 1024 * 1024) {
    throw new Exception('De afbeelding mag maximaal 5 MB zijn.');
  }

  $toegestaan = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
  ];
  $mime = mime_content_type($bestand['tmp_name']);
  if (!isset($toegestaan[$mime])) {
    throw new Exception('Alleen JPG, PNG, GIF of WEBP afbeeldingen zijn toegestaan.');
  }

  $map = __DIR__ . '/../uploads/';
  if (!is_dir($map)) {
    mkdir($map, 0755, true);
  }

  $naam = bin2hex(random_bytes(8)) . '.' . $toegestaan[$mime];
  if (!move_uploaded_file($bestand['tmp_name'], $map . $naam)) {
    throw new Exception('De afbeelding kon niet worden opgeslagen.');
  }
  return $naam;
}

function verwijderAfbeelding(?string $naam): void
{
  if ($naam) {
    $pad = __DIR__ . '/../uploads/' . basename($naam);
    if (is_file($pad)) {
      unlink($pad);
    }
  }
}
