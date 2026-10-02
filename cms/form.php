<?php
// Gedeeld formulier voor add.php en edit.php.
// Verwacht: $project (array met velden), $fouten (array), $knopTekst (string)
?>
<?php if ($fouten): ?>
  <div class="alert alert-danger">
    <?php foreach ($fouten as $fout): ?>
      <div><?= e($fout) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
  <div class="mb-3">
    <label for="titel" class="form-label">Titel</label>
    <input type="text" class="form-control" id="titel" name="titel" maxlength="20" required value="<?= e($project['titel'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label for="omschrijving" class="form-label">Omschrijving</label>
    <textarea class="form-control" id="omschrijving" name="omschrijving" maxlength="255" rows="3" required><?= e($project['omschrijving'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label for="type" class="form-label">Type</label>
    <input type="text" class="form-control" id="type" name="type" maxlength="10" required value="<?= e($project['type'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label for="jaar" class="form-label">Jaar</label>
    <input type="number" class="form-control" id="jaar" name="jaar" min="1900" max="2100" required value="<?= e((string) ($project['jaar'] ?? date('Y'))) ?>">
  </div>
  <div class="mb-3">
    <label for="afbeelding" class="form-label">Afbeelding</label>
    <?php if (!empty($project['afbeelding'])): ?>
      <div class="mb-2">
        <img src="../uploads/<?= e($project['afbeelding']) ?>" alt="Huidige afbeelding" class="img-thumbnail" style="max-height: 150px;">
        <div class="form-text">Kies een nieuwe afbeelding om deze te vervangen.</div>
      </div>
    <?php endif; ?>
    <input type="file" class="form-control" id="afbeelding" name="afbeelding" accept="image/jpeg,image/png,image/gif,image/webp">
  </div>
  <button type="submit" class="btn btn-primary"><?= e($knopTekst) ?></button>
  <a href="../index.php" class="btn btn-outline-secondary">Annuleren</a>
</form>
