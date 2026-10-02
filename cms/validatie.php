<?php
// Leest de projectvelden uit $_POST en geeft [velden, fouten] terug
function valideerProject(array $invoer): array
{
  $project = [
    'titel' => trim($invoer['titel'] ?? ''),
    'omschrijving' => trim($invoer['omschrijving'] ?? ''),
    'type' => trim($invoer['type'] ?? ''),
    'jaar' => trim($invoer['jaar'] ?? ''),
  ];
  $fouten = [];

  if ($project['titel'] === '' || mb_strlen($project['titel']) > 20) {
    $fouten[] = 'Titel is verplicht en mag maximaal 20 tekens zijn.';
  }
  if ($project['omschrijving'] === '' || mb_strlen($project['omschrijving']) > 255) {
    $fouten[] = 'Omschrijving is verplicht en mag maximaal 255 tekens zijn.';
  }
  if ($project['type'] === '' || mb_strlen($project['type']) > 10) {
    $fouten[] = 'Type is verplicht en mag maximaal 10 tekens zijn.';
  }
  if (!filter_var($project['jaar'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1900, 'max_range' => 2100]])) {
    $fouten[] = 'Vul een geldig jaar in.';
  }

  return [$project, $fouten];
}
