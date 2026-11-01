<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$flash_success = $this->session->flashdata('success');
$flash_error = $this->session->flashdata('error');
$uri = uri_string();
$pendingCount = isset($pending_count) ? (int) $pending_count : 0;
?><!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= html_escape(isset($page_title) ? $page_title : 'Admin') ?></title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
<div class="lg:flex min-h-screen">
	<aside class="w-full lg:w-64 bg-slate-900 text-slate-200">
		<div class="p-5 border-b border-slate-800">
			<a href="<?= site_url('admin') ?>" class="flex items-center gap-2">
				<span class="w-9 h-9 rounded-xl bg-sky-500 text-white flex items-center justify-center font-bold">P</span>
				<span>
					<span class="block font-bold text-white"><?= html_escape($settings['store_name']) ?></span>
					<span class="block text-xs text-slate-400">Panel Admin</span>
				</span>
			</a>
		</div>
		<nav class="p-3 space-y-1 text-sm">
			<a href="<?= site_url('admin') ?>" class="flex items-center justify-between px-3 py-2 rounded-lg <?= $uri === 'admin' ? 'bg-sky-600 text-white' : 'hover:bg-slate-800' ?>">
				<span>Dashboard</span>
			</a>
			<a href="<?= site_url('admin/kategori') ?>" class="flex items-center justify-between px-3 py-2 rounded-lg <?= strpos($uri, 'admin/kategori') === 0 ? 'bg-sky-600 text-white' : 'hover:bg-slate-800' ?>">
				<span>Kategori</span>
			</a>
			<a href="<?= site_url('admin/buku') ?>" class="flex items-center justify-between px-3 py-2 rounded-lg <?= strpos($uri, 'admin/buku') === 0 ? 'bg-sky-600 text-white' : 'hover:bg-slate-800' ?>">
				<span>Buku & Stok</span>
			</a>
			<a href="<?= site_url('admin/peminjaman') ?>" class="flex items-center justify-between px-3 py-2 rounded-lg <?= strpos($uri, 'admin/peminjaman') === 0 ? 'bg-sky-600 text-white' : 'hover:bg-slate-800' ?>">
				<span>Peminjaman</span>
				<?php if ($pendingCount > 0): ?>
					<span class="inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-amber-400 text-amber-950 text-xs font-bold"><?= $pendingCount ?></span>
				<?php endif; ?>
			</a>
			<a href="<?= site_url('admin/pengaturan') ?>" class="flex items-center justify-between px-3 py-2 rounded-lg <?= strpos($uri, 'admin/pengaturan') === 0 ? 'bg-sky-600 text-white' : 'hover:bg-slate-800' ?>">
				<span>Pengaturan</span>
			</a>
		</nav>
		<div class="p-3 mt-2 border-t border-slate-800 text-sm">
			<a href="<?= site_url() ?>" target="_blank" rel="noopener" class="block px-3 py-2 rounded-lg hover:bg-slate-800">Lihat Situs &nearr;</a>
			<?= form_open('admin/keluar') ?>
				<button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-rose-300 hover:bg-slate-800">Keluar</button>
			<?= form_close() ?>
			<p class="px-3 pt-3 text-xs text-slate-500">Masuk sebagai <?= html_escape($current_admin['name']) ?></p>
		</div>
	</aside>

	<main class="flex-1 min-w-0 p-4 lg:p-8">
		<?php if ($flash_success): ?>
			<div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"><?= html_escape($flash_success) ?></div>
		<?php endif; ?>
		<?php if ($flash_error): ?>
			<div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= html_escape($flash_error) ?></div>
		<?php endif; ?>
