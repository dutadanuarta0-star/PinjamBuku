<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$minDays = (int) $settings['min_loan_days'];
$maxDays = (int) $settings['max_loan_days'];
$hasProblem = FALSE;

foreach ($items as $item)
{
	if ( ! $item['book_active'] OR ! $item['variant_active'] OR (int) $item['stock_available'] < 1)
	{
		$hasProblem = TRUE;
		break;
	}
}
?>
<div class="flex flex-wrap items-end justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-slate-900">Keranjang</h1>
		<p class="text-sm text-slate-500 mt-1"><?= count($items) ?> buku siap diajukan sebagai reservasi.</p>
	</div>
	<a href="<?= site_url('katalog') ?>" class="text-sm font-semibold text-sky-700 hover:underline">Tambah buku lain</a>
</div>

<?php if (empty($items)): ?>
	<div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
		<p class="font-semibold text-slate-800">Keranjangmu masih kosong.</p>
		<p class="text-sm text-slate-500 mt-1">Cari buku favoritmu di katalog dan tambahkan ke keranjang.</p>
		<a href="<?= site_url('katalog') ?>" class="inline-flex mt-4 px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">Buka katalog</a>
	</div>
<?php else: ?>
	<div class="grid gap-6 lg:grid-cols-[1fr_320px] items-start">
		<div class="space-y-3">
			<?php foreach ($items as $item): ?>
				<?php
				$cover = ! empty($item['cover_url']) ? $item['cover_url'] : base_url('assets/img/placeholder.svg');
				$unavailable = ! $item['book_active'] OR ! $item['variant_active'] OR (int) $item['stock_available'] < 1;
				?>
				<div class="rounded-2xl border <?= $unavailable ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-white' ?> p-4 sm:p-5">
					<div class="flex gap-4">
						<a href="<?= site_url('buku/'.$item['book_slug']) ?>" class="w-16 sm:w-20 shrink-0 aspect-[3/4] rounded-lg overflow-hidden bg-slate-100 border border-slate-200">
							<img src="<?= html_escape($cover) ?>" alt="Cover <?= html_escape($item['title']) ?>" class="w-full h-full object-cover" loading="lazy" onerror="this.onerror=null;this.src='<?= base_url('assets/img/placeholder.svg') ?>';">
						</a>

						<div class="flex-1 min-w-0">
							<p class="text-xs text-sky-700 font-medium"><?= html_escape($item['category_name']) ?></p>
							<h2 class="font-semibold text-slate-900 leading-snug">
								<a href="<?= site_url('buku/'.$item['book_slug']) ?>" class="hover:text-sky-700"><?= html_escape($item['title']) ?></a>
							</h2>
							<p class="text-sm text-slate-500 mt-0.5">Varian: <span class="font-medium text-slate-700"><?= html_escape($item['variant_name']) ?></span></p>

							<?php if ($unavailable): ?>
								<p class="mt-2 text-xs font-semibold text-rose-700">Buku/varian ini sudah tidak tersedia. Hapus dari keranjang untuk melanjutkan.</p>
							<?php endif; ?>

							<div class="mt-3 flex flex-wrap items-center gap-2">
								<?= form_open('keranjang/ubah', array('class' => 'flex items-center gap-2')) ?>
									<input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
									<label for="duration_<?= (int) $item['id'] ?>" class="text-xs text-slate-500">Durasi</label>
									<select name="duration_days" id="duration_<?= (int) $item['id'] ?>" class="rounded-lg border border-slate-300 px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
										<?php for ($day = $minDays; $day <= $maxDays; $day++): ?>
											<option value="<?= $day ?>" <?= (int) $item['duration_days'] === $day ? 'selected' : '' ?>><?= $day ?> hari</option>
										<?php endfor; ?>
									</select>
									<button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Simpan</button>
								<?= form_close() ?>

								<?= form_open('keranjang/hapus', array('class' => 'ml-auto')) ?>
									<input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
									<button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-700 hover:bg-rose-50">Hapus</button>
								<?= form_close() ?>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="rounded-2xl border border-slate-200 bg-white p-5 lg:sticky lg:top-20">
			<h2 class="font-semibold text-slate-900">Ringkasan reservasi</h2>
			<dl class="mt-4 space-y-2 text-sm">
				<div class="flex justify-between">
					<dt class="text-slate-500">Jumlah buku</dt>
					<dd class="font-medium text-slate-800"><?= count($items) ?></dd>
				</div>
				<div class="flex justify-between">
					<dt class="text-slate-500">Durasi pinjam</dt>
					<dd class="font-medium text-slate-800"><?= $minDays ?>&ndash;<?= $maxDays ?> hari / buku</dd>
				</div>
				<div class="flex justify-between">
					<dt class="text-slate-500">Denda terlambat</dt>
					<dd class="font-medium text-slate-800"><?= rupiah($settings['fine_per_day']) ?>/hari/buku</dd>
				</div>
			</dl>

			<p class="mt-4 text-xs text-slate-500 leading-relaxed">Stok akan langsung ditahan setelah reservasi diajukan. Ambil buku setelah admin mengonfirmasi, maksimal <?= (int) $settings['pickup_deadline_days'] ?> hari.</p>

			<?php if ($hasProblem): ?>
				<p class="mt-4 text-xs font-semibold text-rose-700">Hapus buku yang tidak tersedia sebelum melanjutkan.</p>
				<button type="button" disabled class="mt-3 w-full px-5 py-2.5 rounded-xl bg-slate-200 text-slate-500 font-semibold cursor-not-allowed">Ajukan reservasi</button>
			<?php else: ?>
				<?= form_open('keranjang/reservasi', array('class' => 'mt-3')) ?>
					<button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700">Ajukan reservasi</button>
				<?= form_close() ?>
			<?php endif; ?>

			<a href="<?= site_url('katalog') ?>" class="mt-2 block text-center px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">Lanjut cari buku</a>
		</div>
	</div>
<?php endif; ?>
