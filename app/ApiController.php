<?php
Auth::requireLogin();
$pdo = Database::connection();
$user = current_user();
$op = $_GET['action'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') verify_csrf();

try {
 if ($op === 'users-search' && $user['role']==='admin') {
   $q='%'.trim($_GET['q'] ?? '').'%';
   $s=$pdo->prepare("SELECT id,username,name,role,class_name,is_active FROM users WHERE name LIKE ? OR username LIKE ? ORDER BY role,name LIMIT 100");
   $s->execute([$q,$q]); json_response(['ok'=>true,'data'=>$s->fetchAll()]);
 }
 if ($op === 'grade' && $user['role']==='guru' && $_SERVER['REQUEST_METHOD']==='POST') {
   $id=(int)($_POST['submission_id']??0); $score=(float)($_POST['score']??-1); $feedback=trim($_POST['feedback']??'');
   if ($score<0 || $score>100) json_response(['ok'=>false,'message'=>'Nilai harus 0–100.'],422);
   $s=$pdo->prepare("SELECT s.id FROM submissions s JOIN tasks t ON t.id=s.task_id JOIN modules m ON m.id=t.module_id WHERE s.id=? AND m.teacher_id=?");
   $s->execute([$id,$user['id']]); if(!$s->fetch()) json_response(['ok'=>false,'message'=>'Pengumpulan tidak ditemukan.'],404);
   $u=$pdo->prepare('UPDATE submissions SET score=?,feedback=?,graded_by=?,graded_at=NOW() WHERE id=?');
   $u->execute([$score,$feedback,$user['id'],$id]); json_response(['ok'=>true,'message'=>'Nilai berhasil disimpan.']);
 }
 if ($op === 'submit-task' && $user['role']==='siswa' && $_SERVER['REQUEST_METHOD']==='POST') {
   $task=(int)($_POST['task_id']??0); $answer=trim($_POST['answer_text']??'');
   $s=$pdo->prepare("SELECT t.id FROM tasks t JOIN modules m ON m.id=t.module_id JOIN users u ON u.id=? AND (u.class_name=(SELECT name FROM classes WHERE id=m.class_id)) WHERE t.id=?");
   $s->execute([$user['id'],$task]); if(!$s->fetch()) json_response(['ok'=>false,'message'=>'Tugas tidak tersedia untuk akun ini.'],403);
   $relativePath = null;
   if (!empty($_FILES['attachment']['name'])) {
     if ($_FILES['attachment']['error'] !== UPLOAD_ERR_OK) json_response(['ok'=>false,'message'=>'Upload lampiran gagal.'],422);
     if ($_FILES['attachment']['size'] > MAX_UPLOAD_BYTES) json_response(['ok'=>false,'message'=>'Ukuran lampiran maksimal 10 MB.'],422);
     $finfo = new finfo(FILEINFO_MIME_TYPE);
     $mime = $finfo->file($_FILES['attachment']['tmp_name']);
     $allowed = ['application/pdf'=>'pdf','application/zip'=>'zip','application/x-zip-compressed'=>'zip',
       'application/msword'=>'doc','application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>'docx',
       'application/vnd.ms-powerpoint'=>'ppt','application/vnd.openxmlformats-officedocument.presentationml.presentation'=>'pptx',
       'application/vnd.ms-excel'=>'xls','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=>'xlsx',
       'text/plain'=>'txt','image/jpeg'=>'jpg','image/png'=>'png'];
     if (!isset($allowed[$mime])) json_response(['ok'=>false,'message'=>'Format file tidak diizinkan. Gunakan PDF, Office, ZIP, TXT, JPG, atau PNG.'],422);
     if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0750, true);
     $safeName = bin2hex(random_bytes(18)) . '.' . $allowed[$mime];
     if (!move_uploaded_file($_FILES['attachment']['tmp_name'], UPLOAD_DIR . '/' . $safeName)) json_response(['ok'=>false,'message'=>'Tidak dapat menyimpan lampiran.'],500);
     $relativePath = 'storage/uploads/' . $safeName;
   }
   if($answer==='' && $relativePath===null) json_response(['ok'=>false,'message'=>'Isi jawaban atau lampirkan file.'],422);
   $q=$pdo->prepare("INSERT INTO submissions(task_id,student_id,answer_text,file_path) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE answer_text=VALUES(answer_text),file_path=COALESCE(VALUES(file_path),file_path),submitted_at=NOW(),score=NULL,feedback=NULL,graded_by=NULL,graded_at=NULL");
   $q->execute([$task,$user['id'],$answer,$relativePath]); json_response(['ok'=>true,'message'=>'Jawaban berhasil dikumpulkan.']);
 }
 if ($op === 'attendance' && $user['role']==='guru' && $_SERVER['REQUEST_METHOD']==='POST') {
   $class=(int)($_POST['class_id']??0); $student=(int)($_POST['student_id']??0);
   $status=$_POST['status']??''; $date=$_POST['attendance_date']??date('Y-m-d');
   if(!in_array($status,['Hadir','Izin','Sakit','Alpa'],true)) json_response(['ok'=>false,'message'=>'Status tidak valid.'],422);
   $check=$pdo->prepare("SELECT u.id FROM users u JOIN classes c ON c.name=u.class_name WHERE u.id=? AND c.id=?"); $check->execute([$student,$class]);
   if(!$check->fetch()) json_response(['ok'=>false,'message'=>'Siswa bukan anggota kelas tersebut.'],403);
   $q=$pdo->prepare("INSERT INTO attendance(student_id,class_id,teacher_id,attendance_date,status) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status),teacher_id=VALUES(teacher_id)");
   $q->execute([$student,$class,$user['id'],$date,$status]); json_response(['ok'=>true,'message'=>'Absensi tersimpan.']);
 }
 json_response(['ok'=>false,'message'=>'Aksi tidak tersedia.'],404);
} catch(Throwable $e) {
 error_log($e->getMessage()); json_response(['ok'=>false,'message'=>'Terjadi kesalahan pada server.'],500);
}
