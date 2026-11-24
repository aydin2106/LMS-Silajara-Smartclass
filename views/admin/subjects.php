<?php
$title='Mata Pelajaran'; $page='subjects'; $pdo=Database::connection();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf(); $action=$_POST['form_action']??''; $id=(int)($_POST['id']??0);
    try {
        if ($action==='create') {
            $q=$pdo->prepare('INSERT INTO subjects(name,code) VALUES(?,?)');
            $q->execute([trim($_POST['name']??''),strtoupper(trim($_POST['code']??''))]);
            flash('success','Mata pelajaran ditambahkan.');
        } elseif ($action==='update' && $id>0) {
            $q=$pdo->prepare('UPDATE subjects SET name=?,code=? WHERE id=?');
            $q->execute([trim($_POST['name']??''),strtoupper(trim($_POST['code']??'')),$id]);
            flash('success','Mata pelajaran berhasil diperbarui.');
        } elseif ($action==='delete' && $id>0) {
            $q=$pdo->prepare('DELETE FROM subjects WHERE id=?'); $q->execute([$id]);
            flash('success','Mata pelajaran berhasil dihapus.');
        }
    } catch (PDOException $ex) {
        error_log($ex->getMessage());
        flash('error', $action==='delete' ? 'Mata pelajaran tidak dapat dihapus karena masih digunakan pada modul atau data pembelajaran.' : 'Perubahan gagal. Pastikan kode mata pelajaran belum digunakan.');
    }
    redirect('subjects');
}
$rows=$pdo->query('SELECT * FROM subjects ORDER BY name')->fetchAll();
require __DIR__.'/../_dashboard_start.php'; ?>
<div class="page-heading"><div><h1>Mata Pelajaran</h1><p>Kelola daftar mata pelajaran LMS ASJ.</p></div></div>
<div class="dashboard-grid two"><section class="panel"><h3>Tambah Mata Pelajaran</h3><form method="post" class="form-grid"><?= csrf_field() ?><input type="hidden" name="form_action" value="create"><label>Nama mapel<input name="name" required maxlength="120"></label><label>Kode mapel<input name="code" required maxlength="30"></label><button class="btn btn-primary">Simpan</button></form></section>
<section class="panel"><h3>Daftar Mata Pelajaran</h3><div class="table-wrap"><table><thead><tr><th>Kode</th><th>Nama</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= e($r['code']) ?></td><td><?= e($r['name']) ?></td><td class="action-cell"><button type="button" class="table-action edit" title="Edit" aria-label="Edit" onclick='openEditSubject(<?= (int)$r['id'] ?>, <?= json_encode($r['name'],JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) ?>, <?= json_encode($r['code'],JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) ?>)'><i class="fa-solid fa-pen"></i></button><form method="post" class="inline-form" onsubmit="return confirm('Yakin ingin menghapus mata pelajaran <?= e(addslashes($r['name'])) ?>? Data yang masih digunakan tidak dapat dihapus.');"><?= csrf_field() ?><input type="hidden" name="form_action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="table-action delete" title="Hapus" aria-label="Hapus"><i class="fa-solid fa-trash"></i></button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="3" class="empty-state">Belum ada mata pelajaran.</td></tr><?php endif; ?></tbody></table></div></section></div>
<div class="modal" id="subjectEditModal" role="dialog" aria-modal="true" aria-labelledby="subjectEditTitle"><form method="post" class="modal-card"><?= csrf_field() ?><input type="hidden" name="form_action" value="update"><input type="hidden" name="id" id="subjectEditId"><h3 id="subjectEditTitle">Edit Mata Pelajaran</h3><label>Nama mapel<input name="name" id="subjectEditName" required maxlength="120"></label><label>Kode mapel<input name="code" id="subjectEditCode" required maxlength="30"></label><div class="modal-actions"><button type="button" class="btn btn-light" onclick="closeEditModal('subjectEditModal')">Batal</button><button class="btn btn-primary">Simpan Perubahan</button></div></form></div>
<script>
function openEditSubject(id,name,code){if(!confirm('Anda akan mengubah data mata pelajaran ini. Lanjutkan ke formulir edit?'))return;document.getElementById('subjectEditId').value=id;document.getElementById('subjectEditName').value=name;document.getElementById('subjectEditCode').value=code;document.getElementById('subjectEditModal').classList.add('show');}
function closeEditModal(id){document.getElementById(id).classList.remove('show');}
</script>
<?php require __DIR__.'/../_dashboard_end.php'; ?>
