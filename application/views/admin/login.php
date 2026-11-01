<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$flash_error = $this->session->flashdata('error');
$flash_success = $this->session->flashdata('success');
?><!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= html_escape($page_title) ?></title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-900 text-slate-800 antialiased flex items-center justify-center px-4">
	<div class="w-full max-w-sm">
		<div class="text-center mb-6">
			<div class="inline-flex items-center gap-2">
				<span class="w-10 h-10 rounded-xl bg-sky-500 text-white flex items-center justify-center font-bold text-lg">P</span>
				<span class="text-white font-bold text-xl"><?= html_escape($settings['store_name']) ?></span>
			</div>
			<p class="mt-2 text-sm text-slate-400">Panel Admin</p>
		</div>

		<div class="rounded-2xl bg-white p-6 sm:p-7 shadow-xl">
			<h1 class="text-xl font-bold text-slate-900">Masuk Admin</h1>
			<p class="mt-1 text-sm text-slate-500">Khusus pengelola toko.</p>

			<?php if ($flash_error): ?>
				<div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= html_escape($flash_error) ?></div>
			<?php endif; ?>
			<?php if ($flash_success): ?>
				<div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"><?= html_escape($flash_success) ?></div>
			<?php endif; ?>
			<?php if (validation_errors()): ?>
				<div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= validation_errors() ?></div>
			<?php endif; ?>

			<?= form_open('admin/masuk', array('class' => 'mt-5 space-y-4')) ?>
				<div>
					<label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email admin</label>
					<input type="email" name="email" id="email" value="<?= html_escape(set_value('email')) ?>" autocomplete="email" required
						class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				</div>
				<div>
					<label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
					<input type="password" name="password" id="password" autocomplete="current-password" required
						class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				</div>
				<button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700">Masuk</button>
			<?= form_close() ?>
		</div>

		<p class="mt-4 text-center text-sm">
			<a href="<?= site_url() ?>" class="text-slate-400 hover:text-white">&larr; Kembali ke toko</a>
		</p>
	</div>
</body>
</html>
