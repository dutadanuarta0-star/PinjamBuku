<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$flash_success = $this->session->flashdata('success');
$flash_error = $this->session->flashdata('error');
$uri = uri_string();
$cartCount = isset($cart_count) ? (int) $cart_count : 0;
?><!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= html_escape(isset($page_title) ? $page_title : $settings['store_name']) ?></title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
<header class="bg-white border-b border-slate-200 sticky top-0 z-20">
	<div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap items-center gap-3">
		<a href="<?= site_url() ?>" class="flex items-center gap-2 mr-auto">
			<span class="w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold text-lg">P</span>
			<span class="font-bold text-lg text-slate-900"><?= html_escape($settings['store_name']) ?></span>
		</a>

		<nav class="flex flex-wrap items-center gap-1 text-sm">
			<a href="<?= site_url() ?>" class="px-3 py-2 rounded-lg <?= $uri === '' ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-600 hover:bg-slate-100' ?>">Beranda</a>
			<a href="<?= site_url('katalog') ?>" class="px-3 py-2 rounded-lg <?= (strpos($uri, 'katalog') === 0 OR strpos($uri, 'buku/') === 0) ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-600 hover:bg-slate-100' ?>">Katalog</a>
			<a href="<?= site_url('tentang') ?>" class="px-3 py-2 rounded-lg <?= $uri === 'tentang' ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-600 hover:bg-slate-100' ?>">Tentang</a>
		</nav>

		<div class="flex items-center gap-2 text-sm">
			<a href="<?= site_url('keranjang') ?>" class="relative inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
				<span>Keranjang</span>
				<?php if ($cartCount > 0): ?>
					<span class="inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-sky-600 text-white text-xs font-semibold"><?= $cartCount ?></span>
				<?php endif; ?>
			</a>

			<?php if ($current_user): ?>
				<a href="<?= site_url('peminjaman') ?>" class="hidden sm:inline-flex px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Peminjaman Saya</a>
				<span class="hidden md:inline text-slate-500"><?= html_escape($current_user['name']) ?></span>
				<?= form_open('keluar', array('class' => 'inline')) ?>
					<button type="submit" class="px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-100">Keluar</button>
				<?= form_close() ?>
			<?php else: ?>
				<a href="<?= site_url('masuk') ?>" class="px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Masuk</a>
				<a href="<?= site_url('daftar') ?>" class="px-3 py-2 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700">Daftar</a>
			<?php endif; ?>
		</div>
	</div>
</header>

<?php if ($current_user && ((int) $user_alert_overdue > 0 OR (int) $user_alert_fine > 0)): ?>
	<div class="border-b border-amber-200 bg-amber-50">
		<div class="max-w-6xl mx-auto px-4 py-2.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-amber-900">
			<span class="font-semibold">Peringatan:</span>
			<?php if ((int) $user_alert_overdue > 0): ?>
				<span><?= (int) $user_alert_overdue ?> buku melewati jatuh tempo.</span>
			<?php endif; ?>
			<?php if ((int) $user_alert_fine > 0): ?>
				<span>Denda belum dibayar <?= rupiah($user_alert_fine) ?>.</span>
			<?php endif; ?>
			<a href="<?= site_url('peminjaman') ?>" class="ml-auto font-semibold text-amber-900 underline">Lihat peminjaman</a>
		</div>
	</div>
<?php endif; ?>

<main class="max-w-6xl mx-auto px-4 py-8">
	<?php if ($flash_success): ?>
		<div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
			<?= html_escape($flash_success) ?>
		</div>
	<?php endif; ?>
	<?php if ($flash_error): ?>
		<div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
			<?= html_escape($flash_error) ?>
		</div>
	<?php endif; ?>
