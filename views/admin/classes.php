<?php
$title='Manajemen Kelas'; $page='classes'; $pdo=Database::connection();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf(); $action=$_POST['form_action']??''; $id=(int)($_POST['id']??0);
    try {
        if ($action==='create') {
            $q=$pdo->prepare('INSERT INTO classes(name,grade,major) VALUES(?,?,?)');
            $q->execute([trim($_POST['name']??''),$_POST['grade']??'X',trim($_POST['major']??'TJKT')]); flash('success','Kelas ditambahkan.');
        } elseif ($action==='update' && $id>0) {
            $old=$pdo->prepare('SELECT name FROM classes WHERE id=?');$old->execute([$id]);$oldName=$old->fetchColumn();
            if(!$oldName) throw new RuntimeException('Kelas tidak ditemukan.');
            $newName=trim($_POST['name']??''); $grade=$_POST['grade']??'X'; $major=trim($_POST['major']??'TJKT');
            $pdo->beginTransaction();
            $q=$pdo->prepare('UPDATE classes SET name=?,grade=?,major=? WHERE id=?');$q->execute([$newName,$grade,$major,$id]);
            if($newName!==$oldName){$u=$pdo->prepare('UPDATE users SET class_name=? WHERE class_name=? AND role="siswa"');$u->execute([$newName,$oldName]);}
            $pdo->commit(); flash('success','Data kelas berhasil diperbarui.');
        } elseif ($action==='delete' && $id>0) {
            $check=$pdo->prepare('SELECT name FROM classes WHERE id=?');$check->execute([$id]);$className=$check->fetchColumn();
            if(!$className) throw new RuntimeException('Kelas tidak ditemukan.');
            $used=$pdo->prepare('SELECT (SELECT COUNT(*) FROM modules WHERE class_id=?) + (SELECT COUNT(*) FROM attendance WHERE class_id=?) + (SELECT COUNT(*) FROM teacher_assignments WHERE class_id=?)');$used->execute([$id,$id,$id]);
            if((int)$used->fetchColumn()>0){flash('error','Kelas tidak dapat dihapus karena masih memiliki penugasan guru, modul, atau riwayat absensi.');}
            else {$pdo->beginTransaction();$u=$pdo->prepare('UPDATE users SET class_name=NULL WHERE class_name=? AND role="siswa"');$u->execute([$className]);$d=$pdo->prepare('DELETE FROM classes WHERE id=?');$d->execute([$id]);$pdo->commit();flash('success','Kelas berhasil dihapus.');}
        }
    } catch(Throwable $ex){if($pdo->inTransaction())$pdo->rollBack();error_log($ex->getMessage());if(!isset($_SESSION['_flash']))flash('error','Operasi kelas gagal. Periksa kembali data yang dimasukkan.');}
    redirect('classes');
}
$rows=$pdo->query('SELECT c.*,COUNT(u.id) total FROM classes c LEFT JOIN users u ON u.class_name=c.name AND u.role="siswa" GROUP BY c.id ORDER BY c.grade,c.name')->fetchAll();
require __DIR__.'/../_dashboard_start.php'; ?>
<div class="page-heading"><div><h1>Manajemen Kelas</h1><p>Daftar rombongan belajar X–XII TJKT.</p></div></div>
<div class="dashboard-grid two"><section class="panel"><h3>Tambah Kelas</h3><form method="post" class="form-grid"><?= csrf_field() ?><input type="hidden" name="form_action" value="create"><label>Nama kelas<input name="name" placeholder="XI TJKT 1" required maxlength="80"></label><label>Tingkat<select name="grade"><option>X</option><option>XI</option><option>XII</option></select></label><label>Program keahlian<input name="major" value="TJKT" maxlength="30"></label><button class="btn btn-primary">Simpan Kelas</button></form></section>
<section class="panel"><h3>Daftar Kelas</h3><div class="table-wrap"><table><thead><tr><th>Kelas</th><th>Tingkat</th><th>Jumlah Siswa</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['grade']) ?></td><td><?= e($r['total']) ?></td><td class="action-cell"><button type="button" class="table-action edit" title="Edit" aria-label="Edit" onclick='openEditClass(<?= (int)$r['id'] ?>,<?= json_encode($r['name'],JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) ?>,<?= json_encode($r['grade'],JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) ?>,<?= json_encode($r['major'],JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) ?>)'><i class="fa-solid fa-pen"></i></button><form method="post" class="inline-form" onsubmit="return confirm('Yakin ingin menghapus kelas <?= e(addslashes($r['name'])) ?>? Penghapusan hanya bisa dilakukan jika kelas belum digunakan.');"><?= csrf_field() ?><input type="hidden" name="form_action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="table-action delete" title="Hapus" aria-label="Hapus"><i class="fa-solid fa-trash"></i></button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="4" class="empty-state">Belum ada kelas.</td></tr><?php endif; ?></tbody></table></div></section></div>
<div class="modal" id="classEditModal" role="dialog" aria-modal="true" aria-labelledby="classEditTitle"><form method="post" class="modal-card"><?= csrf_field() ?><input type="hidden" name="form_action" value="update"><input type="hidden" name="id" id="classEditId"><h3 id="classEditTitle">Edit Data Kelas</h3><label>Nama kelas<input name="name" id="classEditName" required maxlength="80"></label><label>Tingkat<select name="grade" id="classEditGrade"><option>X</option><option>XI</option><option>XII</option></select></label><label>Program keahlian<input name="major" id="classEditMajor" required maxlength="30"></label><div class="modal-actions"><button type="button" class="btn btn-light" onclick="closeEditModal('classEditModal')">Batal</button><button class="btn btn-primary">Simpan Perubahan</button></div></form></div>
<script>
function openEditClass(id,name,grade,major){if(!confirm('Anda akan mengubah data kelas ini. Lanjutkan ke formulir edit?'))return;document.getElementById('classEditId').value=id;document.getElementById('classEditName').value=name;document.getElementById('classEditGrade').value=grade;document.getElementById('classEditMajor').value=major;document.getElementById('classEditModal').classList.add('show');}
function closeEditModal(id){document.getElementById(id).classList.remove('show');}
</script>
<?php require __DIR__.'/../_dashboard_end.php'; ?>
