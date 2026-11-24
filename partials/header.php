<header class="topbar">
 <button class="icon-btn mobile-menu" id="menuToggle" aria-label="Buka menu"><i class="fa-solid fa-bars"></i></button>
 <div class="top-title"><span>Selamat datang kembali,</span><h2><?= e(current_user()['name']) ?></h2><small><?= e(SCHOOL_NAME) ?></small></div>
 <div class="top-search"><i class="fa-solid fa-magnifying-glass"></i><input id="globalSearch" placeholder="Cari menu, modul, tugas..."></div>
 <div class="top-actions"><span class="date-chip"><i class="fa-regular fa-calendar"></i> <?= date('d M Y') ?></span><a class="icon-btn" href="<?= e(url('logout')) ?>" title="Keluar"><i class="fa-solid fa-right-from-bracket"></i></a></div>
</header>