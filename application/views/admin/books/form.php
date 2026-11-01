<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$cover = ! empty($book['cover_url']) ? $book['cover_url'] : base_url('assets/img/placeholder.svg');
?>
<div class="mb-6">
	<a href="<?= site_url('admin/buku') ?>" class="text-sm font-semibold text-sky-700 hover:underline">&larr; Kembali ke daftar buku</a>
	<h1 class="mt-2 text-2xl font-bold text-slate-900"><?= $is_edit ? 'Ubah' : 'Tambah' ?> Buku</h1>
	<?php if ($is_edit): ?>
		<p class="text-sm text-slate-500 mt-1">Slug: <span class="font-mono text-xs"><?= html_escape($book['slug']) ?></span></p>
	<?php endif; ?>
</div>

<?php if (validation_errors()): ?>
	<div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= validation_errors() ?></div>
<?php endif; ?>
<?php if ( ! empty($form_error)): ?>
	<div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= html_escape($form_error) ?></div>
<?php endif; ?>

<?= form_open($is_edit ? 'admin/buku/'.(int) $book['id'].'/ubah' : 'admin/buku/tambah', array('class' => 'space-y-4')) ?>
	<div class="grid gap-4 lg:grid-cols-[1fr_320px] items-start">
		<div class="rounded-2xl bg-white border border-slate-200 p-5 sm:p-6 space-y-4">
			<h2 class="font-semibold text-slate-900">Informasi buku</h2>

			<div class="grid gap-4 sm:grid-cols-2">
				<div class="sm:col-span-2">
					<label for="title" class="block text-sm font-medium text-slate-700 mb-1">Judul <span class="text-rose-600">*</span></label>
					<input type="text" name="title" id="title" value="<?= html_escape(set_value('title', $book['title'])) ?>" required maxlength="200"
						class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				</div>
				<div>
					<label for="author" class="block text-sm font-medium text-slate-700 mb-1">Penulis <span class="text-rose-600">*</span></label>
					<input type="text" name="author" id="author" value="<?= html_escape(set_value('author', $book['author'])) ?>" required maxlength="150"
						class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				</div>
				<div>
					<label for="category_id" class="block text-sm font-medium text-slate-700 mb-1">Kategori <span class="text-rose-600">*</span></label>
					<select name="category_id" id="category_id" required
						class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
						<option value="">Pilih kategori</option>
						<?php foreach ($categories as $category): ?>
							<option value="<?= (int) $category['id'] ?>" <?= (string) set_value('category_id', $book['category_id']) === (string) $category['id'] ? 'selected' : '' ?>><?= html_escape($category['name']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div>
					<label for="publisher" class="block text-sm font-medium text-slate-700 mb-1">Penerbit</label>
					<input type="text" name="publisher" id="publisher" value="<?= html_escape(set_value('publisher', (string) $book['publisher'])) ?>" maxlength="150"
						class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				</div>
				<div>
					<label for="publish_year" class="block text-sm font-medium text-slate-700 mb-1">Tahun terbit</label>
					<input type="number" name="publish_year" id="publish_year" value="<?= html_escape(set_value('publish_year', (string) $book['publish_year'])) ?>" min="1" max="2099"
						class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				</div>
				<div>
					<label for="isbn" class="block text-sm font-medium text-slate-700 mb-1">ISBN</label>
					<input type="text" name="isbn" id="isbn" value="<?= html_escape(set_value('isbn', (string) $book['isbn'])) ?>" maxlength="30"
						class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				</div>
				<div class="sm:col-span-2">
					<label for="description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
					<textarea name="description" id="description" rows="5" maxlength="5000"
						class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300"><?= html_escape(set_value('description', (string) $book['description'])) ?></textarea>
				</div>
			</div>
		</div>

		<div class="rounded-2xl bg-white border border-slate-200 p-5 space-y-4">
			<h2 class="font-semibold text-slate-900">Publikasi</h2>

			<div>
				<label for="is_active" class="block text-sm font-medium text-slate-700 mb-1">Status buku</label>
				<select name="is_active" id="is_active"
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
					<option value="1" <?= (string) set_value('is_active', $book['is_active']) === '1' ? 'selected' : '' ?>>Aktif (tampil di katalog)</option>
					<option value="0" <?= (string) set_value('is_active', $book['is_active']) === '0' ? 'selected' : '' ?>>Nonaktif</option>
				</select>
			</div>

			<div>
				<label for="cover_url" class="block text-sm font-medium text-slate-700 mb-1">URL cover</label>
				<input type="url" name="cover_url" id="cover_url" value="<?= html_escape(set_value('cover_url', (string) $book['cover_url'])) ?>" maxlength="500" placeholder="https://..."
					class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				<p class="mt-1 text-xs text-slate-500">Kosongkan untuk memakai cover placeholder.</p>
			</div>

			<img src="<?= html_escape($cover) ?>" alt="Pratinjau cover" class="w-full max-w-[140px] aspect-[3/4] object-cover rounded-lg border border-slate-200 bg-slate-100" onerror="this.onerror=null;this.src='<?= base_url('assets/img/placeholder.svg') ?>';">
		</div>
	</div>

	<div class="rounded-2xl bg-white border border-slate-200 p-5 sm:p-6">
		<div class="flex items-center justify-between gap-3">
			<div>
				<h2 class="font-semibold text-slate-900">Varian & stok</h2>
				<p class="text-xs text-slate-500 mt-1">Minimal satu varian. Stok tersedia dihitung otomatis dari total dikurangi yang sedang dipinjam/direservasi.</p>
			</div>
			<button type="button" id="add-variant" class="shrink-0 px-3 py-2 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">+ Varian</button>
		</div>

		<div class="mt-4 space-y-3" id="variant-rows">
			<?php foreach ($variants as $variant): ?>
				<div class="variant-row grid gap-2 sm:grid-cols-[1fr_120px_150px_auto] items-end rounded-xl border border-slate-200 bg-slate-50 p-3">
					<input type="hidden" name="variant_id[]" value="<?= (int) $variant['id'] ?>">
					<div>
						<label class="block text-xs font-medium text-slate-600 mb-1">Nama varian</label>
						<input type="text" name="variant_name[]" value="<?= html_escape($variant['name']) ?>" maxlength="80" required
							class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
					</div>
					<div>
						<label class="block text-xs font-medium text-slate-600 mb-1">Stok total</label>
						<input type="number" name="variant_total[]" value="<?= (int) $variant['stock_total'] ?>" min="0" required
							class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
					</div>
					<div>
						<label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
						<select name="variant_status[]" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
							<option value="1" <?= (int) $variant['is_active'] === 1 ? 'selected' : '' ?>>Aktif</option>
							<option value="0" <?= (int) $variant['is_active'] === 0 ? 'selected' : '' ?>>Nonaktif</option>
						</select>
					</div>
					<button type="button" class="remove-variant px-3 py-2 rounded-lg border border-rose-200 text-xs font-semibold text-rose-700 hover:bg-rose-50">Hapus</button>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="flex flex-wrap items-center gap-3">
		<button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">Simpan buku</button>
		<a href="<?= site_url('admin/buku') ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
	</div>
<?= form_close() ?>

<template id="variant-row-template">
	<div class="variant-row grid gap-2 sm:grid-cols-[1fr_120px_150px_auto] items-end rounded-xl border border-slate-200 bg-slate-50 p-3">
		<input type="hidden" name="variant_id[]" value="0">
		<div>
			<label class="block text-xs font-medium text-slate-600 mb-1">Nama varian</label>
			<input type="text" name="variant_name[]" value="" maxlength="80" required
				class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
		</div>
		<div>
			<label class="block text-xs font-medium text-slate-600 mb-1">Stok total</label>
			<input type="number" name="variant_total[]" value="1" min="0" required
				class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
		</div>
		<div>
			<label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
			<select name="variant_status[]" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
				<option value="1" selected>Aktif</option>
				<option value="0">Nonaktif</option>
			</select>
		</div>
		<button type="button" class="remove-variant px-3 py-2 rounded-lg border border-rose-200 text-xs font-semibold text-rose-700 hover:bg-rose-50">Hapus</button>
	</div>
</template>

<script>
	(function () {
		var rows = document.getElementById('variant-rows');
		var template = document.getElementById('variant-row-template');

		document.getElementById('add-variant').addEventListener('click', function () {
			rows.appendChild(template.content.cloneNode(true));
		});

		rows.addEventListener('click', function (event) {
			var button = event.target.closest('.remove-variant');

			if ( ! button) {
				return;
			}

			var row = button.closest('.variant-row');
			var isExisting = row.querySelector('input[name="variant_id[]"]').value !== '0';

			if (isExisting) {
				window.alert('Varian yang sudah tersimpan tidak bisa dihapus, cukup ubah stoknya menjadi 0 atau nonaktifkan.');
				return;
			}

			row.remove();
		});
	})();
</script>
