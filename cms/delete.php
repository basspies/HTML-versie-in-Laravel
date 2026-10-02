<?php
include 'conn.php';
include 'auth.php';

vereisLogin();

// Alleen verwijderen via het formulier (POST), niet via een link
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../index.php');
  exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id) {
  $stmt = $conn->prepare('SELECT afbeelding FROM projecten WHERE id = :id');
  $stmt->execute(['id' => $id]);
  $afbeelding = $stmt->fetchColumn();

  $stmt = $conn->prepare('DELETE FROM projecten WHERE id = :id');
  $stmt->execute(['id' => $id]);

  verwijderAfbeelding($afbeelding ?: null);
}

header('Location: ../index.php');
exit;
