<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="mb-6">
	<a href="<?= site_url('admin/kategori') ?>" class="text-sm font-semibold text-sky-700 hover:underline">&larr; Kembali ke kategori</a>
	<h1 class="mt-2 text-2xl font-bold text-slate-900"><?= $is_edit ? 'Ubah' : 'Tambah' ?> Kategori</h1>
</div>

<?php if (validation_errors()): ?>
	<div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= validation_errors() ?></div>
<?php endif; ?>

<div class="max-w-2xl rounded-2xl bg-white border border-slate-200 p-5 sm:p-6">
	<?= form_open($is_edit ? 'admin/kategori/'.(int) $category['id'].'/ubah' : 'admin/kategori/tambah', array('class' => 'space-y-4')) ?>
		<div>
			<label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama kategori</label>
			<input type="text" name="name" id="name" value="<?= html_escape(set_value('name', $category['name'])) ?>" required maxlength="100"
				class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
			<p class="mt-1 text-xs text-slate-500">Slug dibuat otomatis dari nama.</p>
		</div>
		<div>
			<label for="description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
			<textarea name="description" id="description" rows="4" maxlength="1000"
				class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300"><?= html_escape(set_value('description', (string) $category['description'])) ?></textarea>
		</div>
		<div class="flex items-center gap-3">
			<button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">Simpan</button>
			<a href="<?= site_url('admin/kategori') ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
		</div>
	<?= form_close() ?>
</div>
