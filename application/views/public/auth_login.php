<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="max-w-md mx-auto">
	<div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
		<h1 class="text-2xl font-bold text-slate-900">Masuk</h1>
		<p class="mt-1 text-sm text-slate-500">Gunakan email dan password akunmu.</p>

		<?php if (validation_errors()): ?>
			<div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
				<?= validation_errors() ?>
			</div>
		<?php endif; ?>

		<?= form_open('masuk', array('class' => 'mt-6 space-y-4')) ?>
			<div>
				<label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
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

		<p class="mt-5 text-sm text-slate-500 text-center">Belum punya akun? <a href="<?= site_url('daftar') ?>" class="font-semibold text-sky-700 hover:underline">Daftar sekarang</a></p>
	</div>
</div>
