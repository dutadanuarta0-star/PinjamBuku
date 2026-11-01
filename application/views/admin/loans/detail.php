<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$status = $loan['status'];
$hasUnpaidFine = FALSE;

foreach ($loan['items'] as $item)
{
	if ( ! $item['fine_paid'] && $item['fine_preview'] > 0)
	{
		$hasUnpaidFine = TRUE;
	}
}
?>
<div class="mb-6">
	<a href="<?= site_url('admin/peminjaman') ?>" class="text-sm font-semibold text-sky-700 hover:underline">&larr; Kembali ke daftar peminjaman</a>
	<div class="mt-2 flex flex-wrap items-center gap-3">
		<h1 class="text-2xl font-bold text-slate-900"><?= html_escape($loan['code'] ?: 'Reservasi #'.(int) $loan['id']) ?></h1>
		<span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold <?= loan_status_class($status) ?>"><?= html_escape(loan_status_label($status)) ?></span>
	</div>
</div>

<div class="grid gap-4 lg:grid-cols-[1fr_320px] items-start">
	<div class="space-y-4">
		<div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
			<div class="px-5 py-4 border-b border-slate-100">
				<h2 class="font-semibold text-slate-900">Buku yang dipinjam</h2>
			</div>
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
						<tr>
							<th class="text-left font-semibold px-5 py-3">Buku</th>
							<th class="text-left font-semibold px-5 py-3">Durasi</th>
							<th class="text-left font-semibold px-5 py-3">Jatuh tempo</th>
							<th class="text-left font-semibold px-5 py-3">Denda</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-slate-100">
						<?php foreach ($loan['items'] as $item): ?>
							<tr>
								<td class="px-5 py-3">
									<span class="block font-medium text-slate-800"><?= html_escape($item['title']) ?></span>
									<span class="block text-xs text-slate-500"><?= html_escape($item['variant_name']) ?> &middot; <?= html_escape($item['category_name']) ?></span>
								</td>
								<td class="px-5 py-3 text-slate-600"><?= (int) $item['duration_days'] ?> hari</td>
								<td class="px-5 py-3">
									<?php if ( ! empty($item['due_date'])): ?>
										<span class="<?= $item['late_days'] > 0 && $status === 'borrowed' ? 'text-rose-700 font-semibold' : 'text-slate-600' ?>"><?= format_datetime($item['due_date'], FALSE) ?></span>
										<?php if ($item['late_days'] > 0): ?>
											<span class="block text-xs text-rose-600">Terlambat <?= (int) $item['late_days'] ?> hari</span>
										<?php endif; ?>
									<?php else: ?>
										<span class="text-slate-400">Menunggu pengambilan</span>
									<?php endif; ?>
								</td>
								<td class="px-5 py-3">
									<?php if ((int) $item['fine_preview'] > 0): ?>
										<span class="font-semibold <?= $item['fine_paid'] ? 'text-slate-500' : 'text-rose-700' ?>"><?= rupiah($item['fine_preview']) ?></span>
										<span class="block text-xs <?= $item['fine_paid'] ? 'text-emerald-600' : 'text-rose-500' ?>"><?= $item['fine_paid'] ? 'Lunas' : 'Belum dibayar' ?></span>
									<?php else: ?>
										<span class="text-slate-400">Tidak ada</span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between text-sm">
				<span class="text-slate-500">Total denda</span>
				<span class="font-bold <?= (int) $loan['total_fine'] > 0 ? 'text-rose-700' : 'text-slate-700' ?>"><?= rupiah($loan['total_fine']) ?></span>
			</div>
		</div>

		<div class="rounded-2xl bg-white border border-slate-200 p-5">
			<h2 class="font-semibold text-slate-900">Riwayat status</h2>
			<ol class="mt-3 space-y-3 text-sm">
				<li class="flex gap-3">
					<span class="w-2 h-2 mt-2 rounded-full bg-slate-400 shrink-0"></span>
					<div>
						<p class="font-medium text-slate-800">Reservasi diajukan</p>
						<p class="text-xs text-slate-500"><?= format_datetime($loan['created_at']) ?></p>
					</div>
				</li>
				<?php if ( ! empty($loan['pickup_deadline'])): ?>
					<li class="flex gap-3">
						<span class="w-2 h-2 mt-2 rounded-full bg-slate-400 shrink-0"></span>
						<div>
							<p class="font-medium text-slate-800">Batas konfirmasi/pengambilan</p>
							<p class="text-xs text-slate-500"><?= format_datetime($loan['pickup_deadline']) ?></p>
						</div>
					</li>
				<?php endif; ?>
				<?php if ( ! empty($loan['picked_up_at'])): ?>
					<li class="flex gap-3">
						<span class="w-2 h-2 mt-2 rounded-full bg-indigo-500 shrink-0"></span>
						<div>
							<p class="font-medium text-slate-800">Buku diambil</p>
							<p class="text-xs text-slate-500"><?= format_datetime($loan['picked_up_at']) ?></p>
						</div>
					</li>
				<?php endif; ?>
				<?php if ( ! empty($loan['returned_at'])): ?>
					<li class="flex gap-3">
						<span class="w-2 h-2 mt-2 rounded-full bg-emerald-500 shrink-0"></span>
						<div>
							<p class="font-medium text-slate-800">Buku dikembalikan</p>
							<p class="text-xs text-slate-500"><?= format_datetime($loan['returned_at']) ?></p>
						</div>
					</li>
				<?php endif; ?>
				<?php if ( ! empty($loan['rejection_reason'])): ?>
					<li class="flex gap-3">
						<span class="w-2 h-2 mt-2 rounded-full bg-rose-500 shrink-0"></span>
						<div>
							<p class="font-medium text-slate-800">Alasan penolakan</p>
							<p class="text-xs text-slate-500"><?= html_escape($loan['rejection_reason']) ?></p>
						</div>
					</li>
				<?php endif; ?>
			</ol>
		</div>
	</div>

	<div class="space-y-4">
		<div class="rounded-2xl bg-white border border-slate-200 p-5 text-sm">
			<h2 class="font-semibold text-slate-900">Customer</h2>
			<p class="mt-2 font-medium text-slate-800"><?= html_escape($loan['user_name']) ?></p>
			<p class="text-slate-500 break-all"><?= html_escape($loan['user_email']) ?></p>
		</div>

		<div class="rounded-2xl bg-white border border-slate-200 p-5 space-y-2">
			<h2 class="font-semibold text-slate-900 mb-2">Tindakan</h2>

			<?php if ($status === 'pending'): ?>
				<?= form_open('admin/peminjaman/'.(int) $loan['id'].'/setujui') ?>
					<button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">Setujui reservasi</button>
				<?= form_close() ?>

				<?= form_open('admin/peminjaman/'.(int) $loan['id'].'/tolak', array('class' => 'space-y-2 pt-2', 'onsubmit' => "return confirm('Tolak reservasi ini? Stok akan dikembalikan.');")) ?>
					<label for="reason" class="block text-xs font-medium text-slate-600">Alasan penolakan (opsional)</label>
					<input type="text" name="reason" id="reason" maxlength="255" placeholder="Contoh: stok sedang diperbaiki"
						class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
					<button type="submit" class="w-full px-4 py-2.5 rounded-xl border border-rose-300 text-rose-700 text-sm font-semibold hover:bg-rose-50">Tolak reservasi</button>
				<?= form_close() ?>
			<?php elseif ($status === 'approved'): ?>
				<?= form_open('admin/peminjaman/'.(int) $loan['id'].'/ambil', array('onsubmit' => "return confirm('Tandai buku sudah diambil customer?');")) ?>
					<button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Tandai sudah diambil</button>
				<?= form_close() ?>
				<p class="text-xs text-slate-500">Jatuh tempo tiap buku dihitung otomatis dari tanggal pengambilan dan durasi yang dipilih.</p>
			<?php elseif ($status === 'borrowed'): ?>
				<?= form_open('admin/peminjaman/'.(int) $loan['id'].'/kembali', array('onsubmit' => "return confirm('Tandai semua buku sudah dikembalikan?');")) ?>
					<button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">Tandai sudah dikembalikan</button>
				<?= form_close() ?>
				<p class="text-xs text-slate-500">Denda dihitung otomatis dari jumlah hari terlambat &times; <?= rupiah($fine_per_day) ?> per buku.</p>
			<?php else: ?>
				<p class="text-xs text-slate-500">Tidak ada tindakan lanjutan untuk status ini.</p>
			<?php endif; ?>

			<?php if ($hasUnpaidFine): ?>
				<?= form_open('admin/peminjaman/'.(int) $loan['id'].'/denda-lunas', array('class' => 'pt-2')) ?>
					<button type="submit" class="w-full px-4 py-2.5 rounded-xl border border-amber-300 text-amber-800 text-sm font-semibold hover:bg-amber-50">Tandai denda lunas</button>
				<?= form_close() ?>
			<?php endif; ?>
		</div>
	</div>
</div>
