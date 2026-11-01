<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-wrap items-end justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
		<p class="text-sm text-slate-500 mt-1">Ringkasan aktivitas toko hari ini.</p>
	</div>
	<a href="<?= site_url('admin/peminjaman?status=pending') ?>" class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">Konfirmasi reservasi</a>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
	<div class="rounded-2xl bg-white border border-slate-200 p-5">
		<p class="text-sm text-slate-500">Pengunjung hari ini</p>
		<p class="mt-2 text-3xl font-bold text-slate-900"><?= (int) $stats['today'] ?></p>
		<p class="mt-1 text-xs text-slate-400"><?= (int) $stats['total_today'] ?> kunjungan halaman</p>
	</div>
	<div class="rounded-2xl bg-white border border-slate-200 p-5">
		<p class="text-sm text-slate-500">Pengunjung 7 hari</p>
		<p class="mt-2 text-3xl font-bold text-slate-900"><?= (int) $stats['week'] ?></p>
		<p class="mt-1 text-xs text-slate-400">Total <?= (int) $stats['total_views'] ?> kunjungan</p>
	</div>
	<div class="rounded-2xl bg-white border border-slate-200 p-5">
		<p class="text-sm text-slate-500">Reservasi menunggu</p>
		<p class="mt-2 text-3xl font-bold text-amber-600"><?= (int) $pending_loans ?></p>
		<p class="mt-1 text-xs text-slate-400">Perlu dikonfirmasi</p>
	</div>
	<div class="rounded-2xl bg-white border border-slate-200 p-5">
		<p class="text-sm text-slate-500">Sedang dipinjam</p>
		<p class="mt-2 text-3xl font-bold text-sky-700"><?= (int) $active_loans ?></p>
		<p class="mt-1 text-xs <?= (int) $overdue_items > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400' ?>"><?= (int) $overdue_items ?> buku terlambat</p>
	</div>
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-3">
	<div class="rounded-2xl bg-white border border-slate-200 p-5">
		<p class="text-sm text-slate-500">Buku aktif</p>
		<p class="mt-2 text-2xl font-bold text-slate-900"><?= (int) $total_books ?></p>
		<a href="<?= site_url('admin/buku') ?>" class="mt-3 inline-flex text-sm font-semibold text-sky-700 hover:underline">Kelola buku</a>
	</div>
	<div class="lg:col-span-2 rounded-2xl bg-white border border-slate-200 p-5">
		<div class="flex items-center justify-between gap-3">
			<h2 class="font-semibold text-slate-900">Buku paling banyak dilihat (30 hari)</h2>
		</div>

		<?php if (empty($top_books)): ?>
			<p class="mt-3 text-sm text-slate-500">Belum ada data kunjungan buku.</p>
		<?php else: ?>
			<ol class="mt-3 space-y-2">
				<?php foreach ($top_books as $index => $topBook): ?>
					<li class="flex items-center gap-3 text-sm">
						<span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold"><?= $index + 1 ?></span>
						<a href="<?= site_url('buku/'.$topBook['slug']) ?>" target="_blank" rel="noopener" class="flex-1 truncate text-slate-800 hover:text-sky-700"><?= html_escape($topBook['title']) ?></a>
						<span class="text-xs font-semibold text-slate-500"><?= (int) $topBook['views'] ?>x</span>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</div>

<div class="mt-4 rounded-2xl bg-white border border-slate-200">
	<div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
		<h2 class="font-semibold text-slate-900">Reservasi terbaru menunggu konfirmasi</h2>
		<a href="<?= site_url('admin/peminjaman?status=pending') ?>" class="text-sm font-semibold text-sky-700 hover:underline">Lihat semua</a>
	</div>

	<?php if (empty($recent_pending)): ?>
		<p class="px-5 py-6 text-sm text-slate-500">Tidak ada reservasi yang menunggu. Kerja bagus!</p>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="w-full text-sm">
				<thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
					<tr>
						<th class="text-left font-semibold px-5 py-3">Kode</th>
						<th class="text-left font-semibold px-5 py-3">Customer</th>
						<th class="text-left font-semibold px-5 py-3">Diajukan</th>
						<th class="text-right font-semibold px-5 py-3">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-slate-100">
					<?php foreach ($recent_pending as $pending): ?>
						<tr>
							<td class="px-5 py-3 font-semibold text-slate-800"><?= html_escape($pending['code'] ?: '-') ?></td>
							<td class="px-5 py-3">
								<span class="block text-slate-800"><?= html_escape($pending['user_name']) ?></span>
								<span class="block text-xs text-slate-500"><?= html_escape($pending['user_email']) ?></span>
							</td>
							<td class="px-5 py-3 text-slate-600"><?= format_datetime($pending['created_at']) ?></td>
							<td class="px-5 py-3 text-right">
								<a href="<?= site_url('admin/peminjaman/'.(int) $pending['id']) ?>" class="text-sky-700 font-semibold hover:underline">Tinjau</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>
