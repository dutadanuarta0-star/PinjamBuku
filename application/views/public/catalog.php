<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-slate-900">Katalog Buku</h1>
		<p class="text-sm text-slate-500 mt-1"><?= (int) $total ?> buku ditemukan<?= $filters['q'] !== '' ? ' untuk "'.html_escape($filters['q']).'"' : '' ?>.</p>
	</div>

	<form action="<?= site_url('katalog') ?>" method="get" class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
		<?php if ($filters['category'] !== ''): ?>
			<input type="hidden" name="kategori" value="<?= html_escape($filters['category']) ?>">
		<?php endif; ?>
		<input type="text" name="q" value="<?= html_escape($filters['q']) ?>" placeholder="Cari judul atau penulis..." class="w-full sm:w-72 px-4 py-2.5 rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-sky-300" aria-label="Cari buku">
		<button type="submit" class="px-4 py-2.5 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700">Cari</button>
	</form>
</div>

<div class="flex flex-wrap gap-2 mb-6">
	<?php
	$allQuery = $filters['q'] !== '' ? '?'.http_build_query(array('q' => $filters['q'])) : '';
	?>
	<a href="<?= site_url('katalog').$allQuery ?>" class="px-4 py-2 rounded-full text-sm border <?= $filters['category'] === '' ? 'bg-sky-600 border-sky-600 text-white font-semibold' : 'bg-white border-slate-200 text-slate-700 hover:border-sky-300' ?>">Semua</a>
	<?php foreach ($categories as $category): ?>
		<?php
		$query = array('kategori' => $category['slug']);
		if ($filters['q'] !== '')
		{
			$query['q'] = $filters['q'];
		}
		?>
		<a href="<?= site_url('katalog').'?'.http_build_query($query) ?>" class="px-4 py-2 rounded-full text-sm border <?= $filters['category'] === $category['slug'] ? 'bg-sky-600 border-sky-600 text-white font-semibold' : 'bg-white border-slate-200 text-slate-700 hover:border-sky-300' ?>">
			<?= html_escape($category['name']) ?>
		</a>
	<?php endforeach; ?>
</div>

<?php if (empty($books)): ?>
	<div class="bg-white border border-slate-200 rounded-2xl p-10 text-center">
		<p class="font-semibold text-slate-800">Buku tidak ditemukan.</p>
		<p class="text-sm text-slate-500 mt-1">Coba kata kunci lain atau pilih kategori yang berbeda.</p>
		<a href="<?= site_url('katalog') ?>" class="inline-flex mt-4 px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">Reset filter</a>
	</div>
<?php else: ?>
	<div class="grid grid-cols-2 md:grid-cols-3 gap-4">
		<?php foreach ($books as $book): ?>
			<?php $this->load->view('public/book_card', array('book' => $book)); ?>
		<?php endforeach; ?>
	</div>
	<?= $pagination ?>
<?php endif; ?>
