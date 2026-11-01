<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="flex flex-wrap items-end justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-slate-900">Peminjaman Saya</h1>
		<p class="text-sm text-slate-500 mt-1">Pantau status reservasi, jatuh tempo, dan denda di sini.</p>
	</div>
	<a href="<?= site_url('katalog') ?>" class="text-sm font-semibold text-sky-700 hover:underline">Pinjam buku lagi</a>
</div>

<?php if (empty($loans)): ?>
	<div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
		<p class="font-semibold text-slate-800">Belum ada reservasi.</p>
		<p class="text-sm text-slate-500 mt-1">Mulai dengan memilih buku di katalog.</p>
		<a href="<?= site_url('katalog') ?>" class="inline-flex mt-4 px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">Buka katalog</a>
	</div>
<?php else: ?>
	<div class="space-y-4">
		<?php foreach ($loans as $loan): ?>
			<?php
			$status = $loan['status'];
			$canCancel = in_array($status, array('pending', 'approved'), TRUE);
			$isOverdue = $status === 'borrowed' && (int) $loan['overdue_count'] > 0;
			?>
			<article class="rounded-2xl border <?= $isOverdue ? 'border-rose-200' : 'border-slate-200' ?> bg-white overflow-hidden">
				<div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-slate-100">
					<div class="mr-auto">
						<p class="text-xs text-slate-500">Kode reservasi</p>
						<p class="font-bold text-slate-900 tracking-wide"><?= html_escape($loan['code'] ?: '-') ?></p>
					</div>
					<span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold <?= loan_status_class($status) ?>"><?= html_escape(loan_status_label($status)) ?></span>
					<span class="text-xs text-slate-500">Diajukan <?= format_datetime($loan['created_at']) ?></span>
				</div>

				<div class="px-5 py-4 grid gap-4 md:grid-cols-[1fr_240px]">
					<div>
						<ul class="space-y-2">
							<?php foreach ($loan['items'] as $item): ?>
								<li class="flex flex-wrap items-start justify-between gap-2 text-sm">
									<div class="min-w-0">
										<a href="<?= site_url('buku/'.$item['book_slug']) ?>" class="font-medium text-slate-800 hover:text-sky-700"><?= html_escape($item['title']) ?></a>
										<p class="text-xs text-slate-500"><?= html_escape($item['variant_name']) ?> &middot; <?= (int) $item['duration_days'] ?> hari</p>
									</div>
									<div class="text-right text-xs">
										<?php if ( ! empty($item['due_date'])): ?>
											<p class="<?= $item['late_days'] > 0 ? 'text-rose-700 font-semibold' : 'text-slate-600' ?>">Jatuh tempo <?= format_datetime($item['due_date'], FALSE) ?></p>
										<?php endif; ?>
										<?php if ( ! empty($item['returned_at'])): ?>
											<p class="text-slate-500">Dikembalikan <?= format_datetime($item['returned_at'], FALSE) ?></p>
										<?php endif; ?>
										<?php if ($item['fine_preview'] > 0): ?>
											<p class="<?= $item['fine_paid'] ? 'text-slate-500' : 'text-rose-700 font-semibold' ?>">
												Denda <?= rupiah($item['fine_preview']) ?><?= $item['fine_paid'] ? ' (lunas)' : '' ?>
											</p>
										<?php endif; ?>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>

						<?php if ($status === 'pending'): ?>
							<p class="mt-3 text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">Menunggu konfirmasi admin. Reservasi otomatis kedaluwarsa jika tidak diproses sampai <?= format_datetime($loan['pickup_deadline']) ?>.</p>
						<?php elseif ($status === 'approved'): ?>
							<p class="mt-3 text-xs text-sky-800 bg-sky-50 border border-sky-200 rounded-lg px-3 py-2">Sudah disetujui. Ambil buku di toko paling lambat <?= format_datetime($loan['pickup_deadline']) ?>.</p>
						<?php elseif ($status === 'borrowed'): ?>
							<p class="mt-3 text-xs <?= $isOverdue ? 'text-rose-800 bg-rose-50 border-rose-200' : 'text-slate-700 bg-slate-50 border-slate-200' ?> border rounded-lg px-3 py-2">
								Diambil <?= format_datetime($loan['picked_up_at']) ?>.
								<?= $isOverdue ? 'Terlambat '.((int) $loan['late_days']).' hari.' : 'Harap kembalikan sesuai jatuh tempo.' ?>
							</p>
						<?php elseif ($status === 'rejected'): ?>
							<p class="mt-3 text-xs text-rose-800 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">Ditolak: <?= html_escape($loan['rejection_reason'] ?: 'Tidak ada alasan.') ?></p>
						<?php elseif ($status === 'expired'): ?>
							<p class="mt-3 text-xs text-orange-800 bg-orange-50 border border-orange-200 rounded-lg px-3 py-2">Reservasi kedaluwarsa karena melewati batas waktu. Stok sudah dikembalikan.</p>
						<?php endif; ?>
					</div>

					<div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-sm">
						<div class="flex justify-between">
							<span class="text-slate-500">Jumlah buku</span>
							<span class="font-semibold text-slate-800"><?= (int) $loan['item_count'] ?></span>
						</div>
						<?php if ((int) $loan['unpaid_fine'] > 0): ?>
							<div class="mt-3 rounded-lg bg-rose-100 border border-rose-200 px-3 py-2 text-rose-800">
								<p class="text-xs font-semibold">Denda belum dibayar</p>
								<p class="text-lg font-bold"><?= rupiah($loan['unpaid_fine']) ?></p>
								<p class="text-xs mt-1">Bayar di toko saat mengembalikan buku.</p>
							</div>
						<?php elseif ($status === 'returned'): ?>
							<p class="mt-3 text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">Peminjaman selesai. Terima kasih!</p>
						<?php endif; ?>

						<?php if ($canCancel): ?>
							<?= form_open('peminjaman/batal/'.(int) $loan['id'], array('class' => 'mt-3', 'onsubmit' => "return confirm('Batalkan reservasi ini? Stok akan dikembalikan.');")) ?>
								<button type="submit" class="w-full px-4 py-2 rounded-lg border border-rose-300 text-rose-700 text-xs font-semibold hover:bg-rose-50">Batalkan reservasi</button>
							<?= form_close() ?>
						<?php endif; ?>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
