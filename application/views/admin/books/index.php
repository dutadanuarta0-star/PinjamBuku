<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-wrap items-end justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-slate-900">Buku & Stok</h1>
		<p class="text-sm text-slate-500 mt-1"><?= (int) $total ?> buku ditemukan.</p>
	</div>
	<a href="<?= site_url('admin/buku/tambah') ?>" class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">Tambah buku</a>
</div>

<form action="<?= site_url('admin/buku') ?>" method="get" class="mb-5 grid gap-2 sm:grid-cols-2 lg:grid-cols-[1fr_180px_160px_auto]">
	<input type="text" name="q" value="<?= html_escape($filters['q']) ?>" placeholder="Cari judul, penulis, ISBN..." aria-label="Cari buku"
		class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
	<select name="kategori" aria-label="Filter kategori" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
		<option value="">Semua kategori</option>
		<?php foreach ($categories as $category): ?>
			<option value="<?= html_escape($category['slug']) ?>" <?= $filters['category'] === $category['slug'] ? 'selected' : '' ?>><?= html_escape($category['name']) ?></option>
		<?php endforeach; ?>
	</select>
	<select name="status" aria-label="Filter status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
		<option value="">Semua status</option>
		<option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
		<option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
	</select>
	<div class="flex gap-2">
		<button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Filter</button>
		<a href="<?= site_url('admin/buku') ?>" class="px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
	</div>
</form>

<div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
	<?php if (empty($books)): ?>
		<p class="px-5 py-8 text-sm text-slate-500">Tidak ada buku yang cocok dengan filter.</p>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="w-full text-sm">
				<thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
					<tr>
						<th class="text-left font-semibold px-5 py-3">Buku</th>
						<th class="text-left font-semibold px-5 py-3">Kategori</th>
						<th class="text-left font-semibold px-5 py-3">Varian</th>
						<th class="text-left font-semibold px-5 py-3">Stok</th>
						<th class="text-left font-semibold px-5 py-3">Status</th>
						<th class="text-right font-semibold px-5 py-3">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-slate-100">
					<?php foreach ($books as $book): ?>
						<?php $cover = ! empty($book['cover_url']) ? $book['cover_url'] : base_url('assets/img/placeholder.svg'); ?>
						<tr>
							<td class="px-5 py-3">
								<div class="flex items-center gap-3">
									<img src="<?= html_escape($cover) ?>" alt="" class="w-9 h-12 rounded object-cover bg-slate-100 border border-slate-200" loading="lazy" onerror="this.onerror=null;this.src='<?= base_url('assets/img/placeholder.svg') ?>';">
									<div class="min-w-0">
										<span class="block font-medium text-slate-800 truncate max-w-xs"><?= html_escape($book['title']) ?></span>
										<span class="block text-xs text-slate-500 truncate max-w-xs"><?= html_escape($book['author']) ?></span>
									</div>
								</div>
							</td>
							<td class="px-5 py-3 text-slate-600"><?= html_escape($book['category_name']) ?></td>
							<td class="px-5 py-3 text-slate-600"><?= (int) $book['variant_count'] ?></td>
							<td class="px-5 py-3">
								<span class="font-semibold <?= (int) $book['stock_available'] > 0 ? 'text-emerald-700' : 'text-rose-600' ?>"><?= (int) $book['stock_available'] ?></span>
								<span class="text-slate-400">/ <?= (int) $book['stock_total'] ?></span>
							</td>
							<td class="px-5 py-3">
								<?php if ($book['is_active']): ?>
									<span class="inline-flex rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
								<?php else: ?>
									<span class="inline-flex rounded-full bg-slate-100 border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-500">Nonaktif</span>
								<?php endif; ?>
							</td>
							<td class="px-5 py-3">
								<div class="flex items-center justify-end gap-2">
									<a href="<?= site_url('buku/'.$book['slug']) ?>" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Lihat</a>
									<a href="<?= site_url('admin/buku/'.(int) $book['id'].'/ubah') ?>" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Ubah</a>
									<?php if ($book['is_active']): ?>
										<?= form_open('admin/buku/'.(int) $book['id'].'/hapus', array('onsubmit' => "return confirm('Nonaktifkan buku ini? Buku tidak akan tampil di katalog.');")) ?>
											<button type="submit" class="px-3 py-1.5 rounded-lg border border-rose-200 text-xs font-semibold text-rose-700 hover:bg-rose-50">Nonaktifkan</button>
										<?= form_close() ?>
									<?php endif; ?>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="px-5 py-4">
			<?= $pagination ?>
		</div>
	<?php endif; ?>
</div>
