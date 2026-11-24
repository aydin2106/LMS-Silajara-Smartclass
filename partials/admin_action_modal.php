<?php
$modalTeachers=$pdo->query("SELECT id,name FROM users WHERE role='guru' AND is_active=1 ORDER BY name")->fetchAll();
$modalClasses=$pdo->query('SELECT id,name FROM classes ORDER BY name')->fetchAll();
$modalSubjects=$pdo->query('SELECT id,name FROM subjects ORDER BY name')->fetchAll();
?>
<div class="modal" id="adminEditModal" aria-hidden="true"><form class="modal-card" id="adminEditForm">
<h3 id="adminEditTitle">Edit Data</h3><input type="hidden" name="entity"><input type="hidden" name="id">
<div id="adminEditFields"></div><div class="modal-actions"><button type="button" class="btn btn-light" data-close> Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div></form></div>
<script>
window.ADMIN_EDIT_OPTIONS = <?= json_encode(['teachers'=>$modalTeachers,'classes'=>$modalClasses,'subjects'=>$modalSubjects], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>;
</script>
