<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$cover = ! empty($book['cover_url']) ? $book['cover_url'] : base_url('assets/img/placeholder.svg');
$totalAvailable = 0;

foreach ($variants as $variant)
{
	$totalAvailable += (int) $variant['stock_available'];
}

$minDays = (int) $settings['min_loan_days'];
$maxDays = (int) $settings['max_loan_days'];
?>
<nav class="text-sm text-slate-500 mb-5">
	<a href="<?= site_url() ?>" class="hover:text-sky-700">Beranda</a>
	<span class="mx-2">/</span>
	<a href="<?= site_url('katalog') ?>" class="hover:text-sky-700">Katalog</a>
	<span class="mx-2">/</span>
	<a href="<?= site_url('katalog?kategori='.urlencode($book['category_slug'])) ?>" class="hover:text-sky-700"><?= html_escape($book['category_name']) ?></a>
	<span class="mx-2">/</span>
	<span class="text-slate-700"><?= html_escape($book['title']) ?></span>
</nav>

<div class="grid gap-8 lg:grid-cols-[280px_1fr]">
	<div>
		<div class="aspect-[3/4] rounded-2xl overflow-hidden bg-slate-100 border border-slate-200">
			<img src="<?= html_escape($cover) ?>" alt="Cover <?= html_escape($book['title']) ?>" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='<?= base_url('assets/img/placeholder.svg') ?>';">
		</div>
	</div>

	<div>
		<span class="inline-flex items-center rounded-full bg-sky-50 border border-sky-200 px-3 py-1 text-xs font-semibold text-sky-700"><?= html_escape($book['category_name']) ?></span>
		<h1 class="mt-3 text-3xl font-bold text-slate-900 leading-tight"><?= html_escape($book['title']) ?></h1>
		<p class="mt-2 text-slate-600">oleh <span class="font-medium text-slate-800"><?= html_escape($book['author']) ?></span></p>

		<div class="mt-5 flex flex-wrap items-center gap-2 text-xs">
			<?php if ($totalAvailable > 0): ?>
				<span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 font-semibold text-emerald-700">Tersedia <?= $totalAvailable ?> eksemplar</span>
			<?php else: ?>
				<span class="inline-flex items-center rounded-full bg-slate-100 border border-slate-200 px-3 py-1 font-semibold text-slate-500">Stok habis</span>
			<?php endif; ?>
			<span class="inline-flex items-center rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-slate-600">Pinjam <?= $minDays ?>&ndash;<?= $maxDays ?> hari</span>
			<span class="inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-amber-800">Denda <?= rupiah($settings['fine_per_day']) ?>/hari/buku</span>
		</div>

		<dl class="mt-6 grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
			<div>
				<dt class="text-slate-500">Penerbit</dt>
				<dd class="font-medium text-slate-800"><?= html_escape($book['publisher'] ?: '-') ?></dd>
			</div>
			<div>
				<dt class="text-slate-500">Tahun terbit</dt>
				<dd class="font-medium text-slate-800"><?= html_escape($book['publish_year'] ?: '-') ?></dd>
			</div>
			<div>
				<dt class="text-slate-500">ISBN</dt>
				<dd class="font-medium text-slate-800"><?= html_escape($book['isbn'] ?: '-') ?></dd>
			</div>
			<div>
				<dt class="text-slate-500">Jumlah varian</dt>
				<dd class="font-medium text-slate-800"><?= count($variants) ?></dd>
			</div>
		</dl>

		<div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
			<h2 class="font-semibold text-slate-900">Ringkasan</h2>
			<p class="mt-2 text-sm leading-relaxed text-slate-600"><?= nl2br(html_escape($book['description'] ?: 'Belum ada deskripsi untuk buku ini.')) ?></p>
		</div>

		<?php if ($current_user): ?>
			<?php if (! empty($variants) && $totalAvailable > 0): ?>
				<?= form_open('keranjang/tambah', array('class' => 'mt-6 rounded-2xl border border-slate-200 bg-white p-5 space-y-4')) ?>
					<input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
					<h2 class="font-semibold text-slate-900">Pinjam buku ini</h2>

					<div class="grid gap-4 sm:grid-cols-2">
						<div>
							<label for="variant_id" class="block text-sm font-medium text-slate-700 mb-1">Varian</label>
							<select name="variant_id" id="variant_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300" required>
								<?php foreach ($variants as $variant): ?>
									<option value="<?= (int) $variant['id'] ?>" <?= (int) $variant['stock_available'] < 1 ? 'disabled' : '' ?>>
										<?= html_escape($variant['name']) ?>
										(<?= (int) $variant['stock_available'] > 0 ? 'tersedia '.((int) $variant['stock_available']) : 'habis' ?>)
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div>
							<label for="duration_days" class="block text-sm font-medium text-slate-700 mb-1">Durasi pinjam</label>
							<select name="duration_days" id="duration_days" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300" required>
								<?php for ($day = $minDays; $day <= $maxDays; $day++): ?>
									<option value="<?= $day ?>"><?= $day ?> hari</option>
								<?php endfor; ?>
							</select>
						</div>
					</div>

					<p class="text-xs text-slate-500">Jatuh tempo dihitung mulai tanggal buku diambil. Denda keterlambatan <?= rupiah($settings['fine_per_day']) ?> per hari per buku.</p>

					<button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700">Tambah ke keranjang</button>
				<?= form_close() ?>
			<?php else: ?>
				<div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-5 text-sm text-rose-800">
					Stok buku ini sedang kosong. Silakan cek katalog untuk buku lain.
				</div>
			<?php endif; ?>
		<?php else: ?>
			<div class="mt-6 rounded-2xl border border-sky-200 bg-sky-50 p-5">
				<p class="text-sm text-sky-900">Masuk terlebih dahulu untuk menambahkan buku ini ke keranjang.</p>
				<a href="<?= site_url('masuk') ?>" class="inline-flex mt-3 px-5 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">Masuk / Daftar</a>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php if (! empty($related)): ?>
	<section class="mt-12">
		<div class="flex items-end justify-between gap-4 mb-4">
			<div>
				<h2 class="text-xl font-bold text-slate-900">Buku serupa</h2>
				<p class="text-sm text-slate-500">Masih dari kategori <?= html_escape($book['category_name']) ?>.</p>
			</div>
			<a href="<?= site_url('katalog?kategori='.urlencode($book['category_slug'])) ?>" class="text-sm font-semibold text-sky-700 hover:underline">Lihat semua</a>
		</div>
		<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
			<?php foreach ($related as $relatedBook): ?>
				<?php $this->load->view('public/book_card', array('book' => $relatedBook)); ?>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>
