<?php $role=current_user()['role']; $menus=[
'admin'=>[['dashboard','Dashboard','fa-house'],['users','Pengguna','fa-users'],['classes','Kelas','fa-school'],['subjects','Mata Pelajaran','fa-book'],['assignments','Plotting Guru','fa-diagram-project'],['reports','Laporan','fa-chart-column']],
'guru'=>[['dashboard','Dashboard','fa-house'],['modules','Modul Pembelajaran','fa-book-open'],['materials','Materi','fa-file-lines'],['tasks','Tugas & Praktikum','fa-clipboard-check'],['submissions','Penilaian Tugas','fa-marker'],['attendance','Absensi','fa-calendar-check']],
'siswa'=>[['dashboard','Dashboard','fa-house'],['learning','Ruang Belajar','fa-book-open'],['tasks','Tugas Saya','fa-clipboard-list'],['grades','Nilai Saya','fa-chart-line'],['attendance','Absensi','fa-calendar-check']]
]; ?>
<aside class="sidebar" id="sidebar">
 <a class="brand" href="<?= e(url('dashboard')) ?>"><span class="brand-mark"><i class="fa-solid fa-book-open"></i></span><span><b>LMS ASJ</b><small>Administrasi Sistem Jaringan</small></span></a>
 <div class="side-label">MENU <?= strtoupper(e($role)) ?></div>
 <nav><?php foreach($menus[$role] as [$key,$label,$icon]): ?>
 <a class="nav-link <?= (($page??'dashboard')===$key?'active':'') ?>" href="<?= e(url($key==='dashboard'?'dashboard':$key)) ?>"><i class="fa-solid <?= e($icon) ?>"></i><span><?= e($label) ?></span></a>
 <?php endforeach; ?></nav>
 <div class="sidebar-profile"><div class="avatar"><?= e(initials(current_user()['name'])) ?></div><div><b><?= e(current_user()['name']) ?></b><small><?= ucfirst(e($role)) ?></small></div></div>
</aside>