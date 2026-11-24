<?php $title='Login'; $flash=get_flash(); require __DIR__.'/../../partials/head.php'; ?>
<main class="login-shell">
 <section class="login-visual">
  <div class="login-brand"><div class="brand-mark large"><i class="fa-solid fa-book-open"></i></div><div><b>LMS ASJ</b><small>Learning Management System</small></div></div>
  <div class="login-copy"><span class="eyebrow">E-LEARNING TJKT</span><h1>Selamat Datang di<br><em>LMS Administrasi<br>Sistem Jaringan</em></h1><p>Platform pembelajaran online untuk mendukung proses belajar yang efektif, interaktif, dan fleksibel.</p></div>
  <div class="login-illustration"><div class="floating f1"><i class="fa-solid fa-book-open"></i></div><div class="floating f2"><i class="fa-solid fa-comments"></i></div><div class="floating f3"><i class="fa-solid fa-clipboard-check"></i></div><div class="laptop"><div class="screen"><div class="screen-bar">LMS ASJ <span>Dashboard</span></div><div class="screen-content"><b>Halo, Siswa!</b><div class="mini-stats"><i></i><i></i><i></i></div><div class="mini-lines"></div><div class="mini-lines short"></div></div></div><div class="keyboard"></div></div><div class="books"><b>JARINGAN KOMPUTER</b><b>ADMINISTRASI SERVER</b><b>SISTEM OPERASI LINUX</b></div></div>
  <blockquote>“Belajar hari ini, menguasai teknologi esok hari.”</blockquote>
 </section>
 <section class="login-panel"><form class="login-card" method="post" action="<?= e(url('login')) ?>">
  <?= csrf_field() ?><div class="lock-icon"><i class="fa-solid fa-lock"></i></div><h2>Login ke Akun Anda</h2><p class="muted center">Masuk untuk melanjutkan ke sistem pembelajaran.</p>
  <?php if($flash): ?><div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
  <label>Username / NIP / NISN<input name="username" required autocomplete="username" placeholder="Masukkan username atau NISN"></label>
  <label>Password<div class="password-wrap"><input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password"><button type="button" class="show-pass" id="togglePassword"><i class="fa-regular fa-eye"></i></button></div></label>
  <div class="login-options"><label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya</label><a href="mailto:administrator@smkn1selayar.sch.id">Hubungi administrator</a></div>
  <button class="btn btn-primary btn-block" type="submit"><i class="fa-solid fa-right-to-bracket"></i> Login</button>
  <div class="or"><span>atau masuk sebagai</span></div><div class="role-hints"><div><i class="fa-solid fa-user-graduate"></i>Siswa</div><div><i class="fa-solid fa-user-group"></i>Guru</div><div><i class="fa-solid fa-shield-halved"></i>Admin</div></div>
  <p class="login-help">Belum memiliki akun? Hubungi <b>administrator sistem</b>.</p>
 </form></section>
</main><script>document.getElementById('togglePassword').addEventListener('click',()=>{const p=document.getElementById('password');p.type=p.type==='password'?'text':'password'});</script></body></html>