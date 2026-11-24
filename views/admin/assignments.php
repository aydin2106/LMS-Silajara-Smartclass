<?php
$title='Plotting Guru'; $page='assignments'; $pdo=Database::connection();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf(); $action=$_POST['form_action']??''; $id=(int)($_POST['id']??0);
    try {
        if ($action==='create') {
            $q=$pdo->prepare('INSERT IGNORE INTO teacher_assignments(teacher_id,class_id,subject_id) VALUES(?,?,?)');
            $q->execute([(int)($_POST['teacher_id']??0),(int)($_POST['class_id']??0),(int)($_POST['subject_id']??0)]);
            flash($q->rowCount()?'success':'error',$q->rowCount()?'Penugasan guru disimpan.':'Penugasan tersebut sudah ada atau data tidak valid.');
        } elseif ($action==='update' && $id>0) {
            $q=$pdo->prepare('UPDATE teacher_assignments SET teacher_id=?,class_id=?,subject_id=? WHERE id=?');
            $q->execute([(int)($_POST['teacher_id']??0),(int)($_POST['class_id']??0),(int)($_POST['subject_id']??0),$id]);flash('success','Penugasan guru berhasil diperbarui.');
        } elseif ($action==='delete' && $id>0) {
            $q=$pdo->prepare('DELETE FROM teacher_assignments WHERE id=?');$q->execute([$id]);flash('success','Penugasan guru berhasil dihapus.');
        }
    } catch(PDOException $ex){error_log($ex->getMessage());flash('error','Penugasan gagal disimpan. Periksa guru, kelas, mata pelajaran, atau kemungkinan data duplikat.');}
    redirect('assignments');
}
$teachers=$pdo->query("SELECT id,name FROM users WHERE role='guru' AND is_active=1 ORDER BY name")->fetchAll();
$classes=$pdo->query('SELECT * FROM classes ORDER BY name')->fetchAll();
$subjects=$pdo->query('SELECT * FROM subjects ORDER BY name')->fetchAll();
$rows=$pdo->query('SELECT a.id,a.teacher_id,a.class_id,a.subject_id,u.name teacher,c.name class_name,s.name subject_name FROM teacher_assignments a JOIN users u ON u.id=a.teacher_id JOIN classes c ON c.id=a.class_id JOIN subjects s ON s.id=a.subject_id ORDER BY c.name')->fetchAll();
require __DIR__.'/../_dashboard_start.php'; ?>
<div class="page-heading"><div><h1>Plotting Guru</h1><p>Hubungkan guru dengan kelas dan mata pelajaran yang diampu.</p></div></div>
<section class="panel"><form method="post" class="form-grid four"><?= csrf_field() ?><input type="hidden" name="form_action" value="create"><label>Guru<select name="teacher_id" required><?php foreach($teachers as $t): ?><option value="<?= (int)$t['id'] ?>"><?= e($t['name']) ?></option><?php endforeach; ?></select></label><label>Kelas<select name="class_id" required><?php foreach($classes as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></label><label>Mata Pelajaran<select name="subject_id" required><?php foreach($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></label><button class="btn btn-primary">Simpan Penugasan</button></form></section>
<section class="panel"><h3>Daftar Penugasan</h3><div class="table-wrap"><table><thead><tr><th>Guru</th><th>Kelas</th><th>Mata Pelajaran</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= e($r['teacher']) ?></td><td><?= e($r['class_name']) ?></td><td><?= e($r['subject_name']) ?></td><td class="action-cell"><button type="button" class="table-action edit" title="Edit" aria-label="Edit" onclick="openEditAssignment(<?= (int)$r['id'] ?>,<?= (int)$r['teacher_id'] ?>,<?= (int)$r['class_id'] ?>,<?= (int)$r['subject_id'] ?>)"><i class="fa-solid fa-pen"></i></button><form method="post" class="inline-form" onsubmit="return confirm('Yakin ingin menghapus penugasan guru <?= e(addslashes($r['teacher'])) ?> untuk kelas <?= e(addslashes($r['class_name'])) ?> – <?= e(addslashes($r['subject_name'])) ?>?');"><?= csrf_field() ?><input type="hidden" name="form_action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="table-action delete" title="Hapus" aria-label="Hapus"><i class="fa-solid fa-trash"></i></button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="4" class="empty-state">Belum ada penugasan guru.</td></tr><?php endif; ?></tbody></table></div></section>
<div class="modal" id="assignmentEditModal" role="dialog" aria-modal="true" aria-labelledby="assignmentEditTitle"><form method="post" class="modal-card"><?= csrf_field() ?><input type="hidden" name="form_action" value="update"><input type="hidden" name="id" id="assignmentEditId"><h3 id="assignmentEditTitle">Edit Penugasan Guru</h3><label>Guru<select name="teacher_id" id="assignmentEditTeacher" required><?php foreach($teachers as $t): ?><option value="<?= (int)$t['id'] ?>"><?= e($t['name']) ?></option><?php endforeach; ?></select></label><label>Kelas<select name="class_id" id="assignmentEditClass" required><?php foreach($classes as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></label><label>Mata Pelajaran<select name="subject_id" id="assignmentEditSubject" required><?php foreach($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></label><div class="modal-actions"><button type="button" class="btn btn-light" onclick="closeEditModal('assignmentEditModal')">Batal</button><button class="btn btn-primary">Simpan Perubahan</button></div></form></div>
<script>
function openEditAssignment(id,teacher,classId,subject){if(!confirm('Anda akan mengubah penugasan guru ini. Lanjutkan ke formulir edit?'))return;document.getElementById('assignmentEditId').value=id;document.getElementById('assignmentEditTeacher').value=teacher;document.getElementById('assignmentEditClass').value=classId;document.getElementById('assignmentEditSubject').value=subject;document.getElementById('assignmentEditModal').classList.add('show');}
function closeEditModal(id){document.getElementById(id).classList.remove('show');}
</script>
<?php require __DIR__.'/../_dashboard_end.php'; ?>
