<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$cover = ! empty($book['cover_url']) ? $book['cover_url'] : base_url('assets/img/placeholder.svg');
$available = isset($book['stock_available']) ? (int) $book['stock_available'] : 0;
?>
<article class="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
	<a href="<?= site_url('buku/'.$book['slug']) ?>" class="block aspect-[3/4] bg-slate-100 overflow-hidden">
		<img src="<?= html_escape($cover) ?>" alt="Cover <?= html_escape($book['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.onerror=null;this.src='<?= base_url('assets/img/placeholder.svg') ?>';">
	</a>
	<div class="p-4">
		<p class="text-xs font-medium text-sky-700 mb-1"><?= html_escape($book['category_name']) ?></p>
		<h2 class="font-semibold text-slate-900 leading-snug line-clamp-2">
			<a href="<?= site_url('buku/'.$book['slug']) ?>" class="hover:text-sky-700"><?= html_escape($book['title']) ?></a>
		</h2>
		<p class="mt-1 text-sm text-slate-500 line-clamp-1"><?= html_escape($book['author']) ?></p>
		<div class="mt-3 flex items-center justify-between text-xs">
			<?php if ($available > 0): ?>
				<span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-emerald-700 font-medium">Tersedia <?= $available ?></span>
			<?php else: ?>
				<span class="inline-flex items-center rounded-full bg-slate-100 border border-slate-200 px-2.5 py-1 text-slate-500 font-medium">Stok habis</span>
			<?php endif; ?>
			<a href="<?= site_url('buku/'.$book['slug']) ?>" class="font-semibold text-sky-700 hover:underline">Detail</a>
		</div>
	</div>
</article>
