<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-wrap items-end justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-slate-900">Kategori</h1>
		<p class="text-sm text-slate-500 mt-1"><?= count($categories) ?> kategori terdaftar.</p>
	</div>
	<a href="<?= site_url('admin/kategori/tambah') ?>" class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">Tambah kategori</a>
</div>

<div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
	<?php if (empty($categories)): ?>
		<p class="px-5 py-8 text-sm text-slate-500">Belum ada kategori. Tambahkan kategori pertama.</p>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="w-full text-sm">
				<thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
					<tr>
						<th class="text-left font-semibold px-5 py-3">Nama</th>
						<th class="text-left font-semibold px-5 py-3">Slug</th>
						<th class="text-left font-semibold px-5 py-3">Buku</th>
						<th class="text-right font-semibold px-5 py-3">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-slate-100">
					<?php foreach ($categories as $category): ?>
						<tr>
							<td class="px-5 py-3">
								<span class="block font-medium text-slate-800"><?= html_escape($category['name']) ?></span>
								<?php if ( ! empty($category['description'])): ?>
									<span class="block text-xs text-slate-500 mt-0.5 max-w-md"><?= html_escape($category['description']) ?></span>
								<?php endif; ?>
							</td>
							<td class="px-5 py-3 text-slate-500 font-mono text-xs"><?= html_escape($category['slug']) ?></td>
							<td class="px-5 py-3 text-slate-600"><?= (int) $category['book_count'] ?></td>
							<td class="px-5 py-3">
								<div class="flex items-center justify-end gap-2">
									<a href="<?= site_url('admin/kategori/'.(int) $category['id'].'/ubah') ?>" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Ubah</a>
									<?= form_open('admin/kategori/'.(int) $category['id'].'/hapus', array('onsubmit' => "return confirm('Hapus kategori ini?');")) ?>
										<button type="submit" class="px-3 py-1.5 rounded-lg border border-rose-200 text-xs font-semibold text-rose-700 hover:bg-rose-50" <?= (int) $category['book_count'] > 0 ? 'disabled title="Masih memiliki buku"' : '' ?>>Hapus</button>
									<?= form_close() ?>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>
