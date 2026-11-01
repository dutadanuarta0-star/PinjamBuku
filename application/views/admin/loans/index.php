<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$statusLabels = array(
	''         => 'Semua',
	'pending'  => 'Menunggu',
	'approved' => 'Siap diambil',
	'borrowed' => 'Dipinjam',
	'returned' => 'Selesai',
	'rejected' => 'Ditolak',
	'cancelled'=> 'Dibatalkan',
	'expired'  => 'Kedaluwarsa',
);
?>
<div class="flex flex-wrap items-end justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-slate-900">Peminjaman</h1>
		<p class="text-sm text-slate-500 mt-1"><?= (int) $total ?> reservasi ditemukan.</p>
	</div>
</div>

<div class="flex flex-wrap gap-1.5 mb-4">
	<?php foreach ($statusLabels as $key => $label): ?>
		<?php
		$query = array();
		if ($key !== '')
		{
			$query['status'] = $key;
		}
		if ($filters['q'] !== '')
		{
			$query['q'] = $filters['q'];
		}
		?>
		<a href="<?= site_url('admin/peminjaman').($query ? '?'.http_build_query($query) : '') ?>"
			class="px-3 py-1.5 rounded-full text-xs font-semibold border <?= $filters['status'] === $key ? 'bg-slate-900 border-slate-900 text-white' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300' ?>">
			<?= html_escape($label) ?>
		</a>
	<?php endforeach; ?>
</div>

<form action="<?= site_url('admin/peminjaman') ?>" method="get" class="mb-5 flex flex-col sm:flex-row gap-2">
	<?php if ($filters['status'] !== ''): ?>
		<input type="hidden" name="status" value="<?= html_escape($filters['status']) ?>">
	<?php endif; ?>
	<input type="text" name="q" value="<?= html_escape($filters['q']) ?>" placeholder="Cari kode, nama, atau email customer..." aria-label="Cari peminjaman"
		class="flex-1 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
	<button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Cari</button>
	<a href="<?= site_url('admin/peminjaman') ?>" class="px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 text-center">Reset</a>
</form>

<div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
	<?php if (empty($loans)): ?>
		<p class="px-5 py-8 text-sm text-slate-500">Tidak ada reservasi pada filter ini.</p>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="w-full text-sm">
				<thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
					<tr>
						<th class="text-left font-semibold px-5 py-3">Kode</th>
						<th class="text-left font-semibold px-5 py-3">Customer</th>
						<th class="text-left font-semibold px-5 py-3">Buku</th>
						<th class="text-left font-semibold px-5 py-3">Status</th>
						<th class="text-left font-semibold px-5 py-3">Diajukan</th>
						<th class="text-left font-semibold px-5 py-3">Batas ambil</th>
						<th class="text-right font-semibold px-5 py-3">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-slate-100">
					<?php foreach ($loans as $loan): ?>
						<tr>
							<td class="px-5 py-3 font-semibold text-slate-800 whitespace-nowrap"><?= html_escape($loan['code'] ?: '-') ?></td>
							<td class="px-5 py-3">
								<span class="block text-slate-800"><?= html_escape($loan['user_name']) ?></span>
								<span class="block text-xs text-slate-500"><?= html_escape($loan['user_email']) ?></span>
							</td>
							<td class="px-5 py-3 text-slate-600"><?= (int) $loan['item_count'] ?> buku</td>
							<td class="px-5 py-3">
								<span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold <?= loan_status_class($loan['status']) ?>"><?= html_escape(loan_status_label($loan['status'])) ?></span>
							</td>
							<td class="px-5 py-3 text-slate-600 whitespace-nowrap"><?= format_datetime($loan['created_at']) ?></td>
							<td class="px-5 py-3 text-slate-600 whitespace-nowrap">
								<?= in_array($loan['status'], array('pending', 'approved'), TRUE) ? format_datetime($loan['pickup_deadline']) : '-' ?>
							</td>
							<td class="px-5 py-3 text-right">
								<a href="<?= site_url('admin/peminjaman/'.(int) $loan['id']) ?>" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
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
