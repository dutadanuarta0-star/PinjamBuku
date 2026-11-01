<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<section class="rounded-3xl bg-gradient-to-br from-sky-700 via-sky-600 to-cyan-500 text-white p-8 md:p-12 overflow-hidden relative">
	<div class="max-w-2xl relative z-10">
		<span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold tracking-wide">PERPUSTAKAAN DIGITAL</span>
		<h1 class="mt-4 text-3xl md:text-5xl font-bold leading-tight"><?= html_escape($settings['store_name']) ?></h1>
		<p class="mt-3 text-sky-50 text-lg"><?= html_escape($settings['store_tagline']) ?></p>
		<p class="mt-2 text-sm text-sky-100 max-w-xl">Cari buku, pilih varian, tentukan durasi pinjam, lalu ambil di toko. Praktis tanpa antre.</p>

		<form action="<?= site_url('katalog') ?>" method="get" class="mt-6 flex flex-col sm:flex-row gap-2 bg-white p-2 rounded-2xl max-w-xl">
			<input type="text" name="q" value="" placeholder="Cari judul, penulis, atau ISBN..." class="flex-1 px-4 py-2.5 rounded-xl text-slate-800 outline-none focus:ring-2 focus:ring-sky-300" aria-label="Cari buku">
			<button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800">Cari Buku</button>
		</form>
	</div>
</section>

<section class="mt-8">
	<div class="flex items-center justify-between gap-4 mb-3">
		<h2 class="text-lg font-bold text-slate-900">Jelajahi kategori</h2>
		<a href="<?= site_url('katalog') ?>" class="text-sm font-semibold text-sky-700 hover:underline">Lihat semua</a>
	</div>
	<div class="flex flex-wrap gap-2">
		<?php foreach ($categories as $category): ?>
			<a href="<?= site_url('katalog?kategori='.urlencode($category['slug'])) ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 text-sm text-slate-700 hover:border-sky-300 hover:text-sky-700">
				<span><?= html_escape($category['name']) ?></span>
				<span class="text-xs text-slate-400"><?= (int) $category['book_count'] ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</section>

<section class="mt-10">
	<div class="flex items-end justify-between gap-4 mb-4">
		<div>
			<h2 class="text-xl font-bold text-slate-900">Buku terbaru</h2>
			<p class="text-sm text-slate-500">Pilihan terbaru yang bisa kamu pinjam.</p>
		</div>
		<a href="<?= site_url('katalog') ?>" class="text-sm font-semibold text-sky-700 hover:underline">Semua buku</a>
	</div>

	<?php if (empty($books)): ?>
		<div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-500">Belum ada buku yang tersedia.</div>
	<?php else: ?>
		<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
			<?php foreach ($books as $book): ?>
				<?php $this->load->view('public/book_card', array('book' => $book)); ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>

<section class="mt-10 grid gap-4 md:grid-cols-3">
	<div class="bg-white border border-slate-200 rounded-2xl p-5">
		<div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">1</div>
		<h3 class="mt-3 font-semibold">Pilih buku & varian</h3>
		<p class="mt-1 text-sm text-slate-500">Temukan buku favoritmu, lalu pilih format yang tersedia.</p>
	</div>
	<div class="bg-white border border-slate-200 rounded-2xl p-5">
		<div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">2</div>
		<h3 class="mt-3 font-semibold">Atur durasi pinjam</h3>
		<p class="mt-1 text-sm text-slate-500">Pilih <?= (int) $settings['min_loan_days'] ?>–<?= (int) $settings['max_loan_days'] ?> hari per buku, lalu ajukan reservasi.</p>
	</div>
	<div class="bg-white border border-slate-200 rounded-2xl p-5">
		<div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">3</div>
		<h3 class="mt-3 font-semibold">Ambil & kembalikan</h3>
		<p class="mt-1 text-sm text-slate-500">Setelah dikonfirmasi admin, ambil buku di toko. Denda keterlambatan <?= rupiah($settings['fine_per_day']) ?>/hari/buku.</p>
	</div>
</section>
