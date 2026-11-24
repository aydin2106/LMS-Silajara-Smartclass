<?php
require __DIR__ . '/config/bootstrap.php';
$pdo = Database::connection();
$users = [
 ['7301032106980001','Administrator','admin','Admin123!','-'],
 ['199806212024211009','Wahyudin Nur, S.Pd., Gr.','guru','Guru123!','-'],
 ['0061234567','Andi Saputra','siswa','Siswa123!','XI TJKT 1'],
];
foreach ($users as [$username,$name,$role,$password,$class]) {
    $q=$pdo->prepare('SELECT id FROM users WHERE username=?'); $q->execute([$username]);
    $existing = $q->fetch();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($existing) {
        // Sinkronkan password dan data akun demo agar instalasi ulang memperbaiki kredensial.
        $upd = $pdo->prepare('UPDATE users SET name=?, role=?, password=?, class_name=?, is_active=1 WHERE username=?');
        $upd->execute([$name, $role, $hash, $class, $username]);
    } else {
        $ins=$pdo->prepare('INSERT INTO users(username,name,role,password,class_name,is_active) VALUES(?,?,?,?,?,1)');
        $ins->execute([$username,$name,$role,$hash,$class]);
    }
}
$pdo->exec("INSERT IGNORE INTO classes(name,grade,major) VALUES ('X TJKT 1','X','TJKT'),('X TJKT 2','X','TJKT'),('XI TJKT 1','XI','TJKT'),('XI TJKT 2','XI','TJKT'),('XII TJKT 1','XII','TJKT')");
$pdo->exec("INSERT IGNORE INTO subjects(name,code) VALUES ('Administrasi Sistem Jaringan','KJ01'),('Infrastruktur Jaringan','KJ02'),('Keamanan Jaringan','KJ03')");
echo '<h2>Instalasi LMS berhasil.</h2><p>Akun demo telah dibuat jika belum ada.</p><p><a href="'.e(url('login')).'">Buka halaman login</a></p><strong>Hapus install.php setelah instalasi.</strong>';
