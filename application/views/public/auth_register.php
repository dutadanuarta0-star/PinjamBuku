<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="max-w-md mx-auto">
	<div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
		<h1 class="text-2xl font-bold text-slate-900">Daftar akun</h1>
		<p class="mt-1 text-sm text-slate-500">Gratis. Cukup isi data di bawah ini.</p>

		<?php if (validation_errors()): ?>
			<div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
				<?= validation_errors() ?>
			</div>
		<?php endif; ?>

		<?= form_open('daftar', array('class' => 'mt-6 space-y-4')) ?>
			<div>
				<label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama lengkap</label>
				<input type="text" name="name" id="name" value="<?= html_escape(set_value('name')) ?>" autocomplete="name" required maxlength="100"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			</div>
			<div>
				<label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
				<input type="email" name="email" id="email" value="<?= html_escape(set_value('email')) ?>" autocomplete="email" required maxlength="150"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			</div>
			<div>
				<label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
				<input type="password" name="password" id="password" autocomplete="new-password" required minlength="8"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				<p class="mt-1 text-xs text-slate-500">Minimal 8 karakter.</p>
			</div>
			<div>
				<label for="password_confirm" class="block text-sm font-medium text-slate-700 mb-1">Ulangi password</label>
				<input type="password" name="password_confirm" id="password_confirm" autocomplete="new-password" required minlength="8"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			</div>
			<button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700">Buat akun</button>
		<?= form_close() ?>

		<p class="mt-5 text-sm text-slate-500 text-center">Sudah punya akun? <a href="<?= site_url('masuk') ?>" class="font-semibold text-sky-700 hover:underline">Masuk</a></p>
	</div>
</div>
