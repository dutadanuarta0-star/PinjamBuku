<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="mb-6">
	<h1 class="text-2xl font-bold text-slate-900">Pengaturan Toko</h1>
	<p class="text-sm text-slate-500 mt-1">Informasi ini tampil di storefront dan dipakai untuk aturan peminjaman.</p>
</div>

<?php if (validation_errors()): ?>
	<div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= validation_errors() ?></div>
<?php endif; ?>
<?php if ( ! empty($form_error)): ?>
	<div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= html_escape($form_error) ?></div>
<?php endif; ?>

<?= form_open('admin/pengaturan', array('class' => 'space-y-4')) ?>
	<div class="grid gap-4 lg:grid-cols-2 items-start">
		<div class="rounded-2xl bg-white border border-slate-200 p-5 sm:p-6 space-y-4">
			<h2 class="font-semibold text-slate-900">Profil toko</h2>

			<div>
				<label for="store_name" class="block text-sm font-medium text-slate-700 mb-1">Nama toko <span class="text-rose-600">*</span></label>
				<input type="text" name="store_name" id="store_name" value="<?= html_escape($form_settings['store_name']) ?>" required maxlength="100"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			</div>
			<div>
				<label for="store_tagline" class="block text-sm font-medium text-slate-700 mb-1">Tagline</label>
				<input type="text" name="store_tagline" id="store_tagline" value="<?= html_escape($form_settings['store_tagline']) ?>" maxlength="150"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			</div>
			<div>
				<label for="store_description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi singkat</label>
				<textarea name="store_description" id="store_description" rows="4" maxlength="2000"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300"><?= html_escape($form_settings['store_description']) ?></textarea>
			</div>
			<div>
				<label for="store_hours" class="block text-sm font-medium text-slate-700 mb-1">Jam buka</label>
				<input type="text" name="store_hours" id="store_hours" value="<?= html_escape($form_settings['store_hours']) ?>" maxlength="100"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			</div>
		</div>

		<div class="rounded-2xl bg-white border border-slate-200 p-5 sm:p-6 space-y-4">
			<h2 class="font-semibold text-slate-900">Kontak</h2>

			<div>
				<label for="store_address" class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
				<input type="text" name="store_address" id="store_address" value="<?= html_escape($form_settings['store_address']) ?>" maxlength="255"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			</div>
			<div>
				<label for="store_phone" class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
				<input type="text" name="store_phone" id="store_phone" value="<?= html_escape($form_settings['store_phone']) ?>" maxlength="30"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			</div>
			<div>
				<label for="store_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
				<input type="email" name="store_email" id="store_email" value="<?= html_escape($form_settings['store_email']) ?>" maxlength="150"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			</div>
		</div>
	</div>

	<div class="rounded-2xl bg-white border border-slate-200 p-5 sm:p-6">
		<h2 class="font-semibold text-slate-900">Aturan peminjaman</h2>
		<p class="text-xs text-slate-500 mt-1">Perubahan berlaku untuk reservasi baru.</p>

		<div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div>
				<label for="fine_per_day" class="block text-sm font-medium text-slate-700 mb-1">Denda per hari per buku</label>
				<input type="number" name="fine_per_day" id="fine_per_day" value="<?= html_escape($form_settings['fine_per_day']) ?>" min="0" required
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				<p class="mt-1 text-xs text-slate-500">Rupiah, tanpa titik.</p>
			</div>
			<div>
				<label for="min_loan_days" class="block text-sm font-medium text-slate-700 mb-1">Durasi minimum</label>
				<input type="number" name="min_loan_days" id="min_loan_days" value="<?= html_escape($form_settings['min_loan_days']) ?>" min="3" max="7" required
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			</div>
			<div>
				<label for="max_loan_days" class="block text-sm font-medium text-slate-700 mb-1">Durasi maksimum</label>
				<input type="number" name="max_loan_days" id="max_loan_days" value="<?= html_escape($form_settings['max_loan_days']) ?>" min="3" max="7" required
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			</div>
			<div>
				<label for="pickup_deadline_days" class="block text-sm font-medium text-slate-700 mb-1">Batas ambil (hari)</label>
				<input type="number" name="pickup_deadline_days" id="pickup_deadline_days" value="<?= html_escape($form_settings['pickup_deadline_days']) ?>" min="0" max="7" required
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				<p class="mt-1 text-xs text-slate-500">Reservasi batal otomatis setelah lewat.</p>
			</div>
		</div>
	</div>

	<button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">Simpan pengaturan</button>
<?= form_close() ?>
