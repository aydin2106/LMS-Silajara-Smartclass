<?php
$title='Dashboard'; $user=current_user(); $pdo=Database::connection();
require __DIR__.'/../partials/head.php';
?>
<div class="app-shell"><?php require __DIR__.'/../partials/sidebar.php'; ?><main class="main-area"><?php require __DIR__.'/../partials/header.php'; ?><div class="content-area">
