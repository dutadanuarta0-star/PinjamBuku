<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="grid gap-8 lg:grid-cols-[1.2fr_1fr]">
	<div>
		<span class="inline-flex items-center rounded-full bg-sky-50 border border-sky-200 px-3 py-1 text-xs font-semibold text-sky-700">TENTANG KAMI</span>
		<h1 class="mt-3 text-3xl font-bold text-slate-900"><?= html_escape($settings['store_name']) ?></h1>
		<p class="mt-3 text-lg text-slate-600"><?= html_escape($settings['store_tagline']) ?></p>
		<p class="mt-4 text-slate-600 leading-relaxed"><?= nl2br(html_escape($settings['store_description'])) ?></p>

		<div class="mt-8 grid gap-4 sm:grid-cols-3">
			<div class="rounded-2xl border border-slate-200 bg-white p-5">
				<div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">1</div>
				<h2 class="mt-3 font-semibold text-slate-900">Pesan online</h2>
				<p class="mt-1 text-sm text-slate-500">Pilih buku dan varian, lalu tentukan durasi pinjam.</p>
			</div>
			<div class="rounded-2xl border border-slate-200 bg-white p-5">
				<div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">2</div>
				<h2 class="mt-3 font-semibold text-slate-900">Konfirmasi admin</h2>
				<p class="mt-1 text-sm text-slate-500">Reservasi dicek, stok ditahan sampai kamu mengambilnya.</p>
			</div>
			<div class="rounded-2xl border border-slate-200 bg-white p-5">
				<div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">3</div>
				<h2 class="mt-3 font-semibold text-slate-900">Ambil & baca</h2>
				<p class="mt-1 text-sm text-slate-500">Kembalikan tepat waktu agar tidak kena denda.</p>
			</div>
		</div>
	</div>

	<div class="space-y-4">
		<div class="rounded-2xl border border-slate-200 bg-white p-5">
			<h2 class="font-semibold text-slate-900">Kunjungi toko</h2>
			<dl class="mt-3 space-y-3 text-sm">
				<div>
					<dt class="text-slate-500">Alamat</dt>
					<dd class="font-medium text-slate-800"><?= html_escape($settings['store_address']) ?></dd>
				</div>
				<div>
					<dt class="text-slate-500">Jam buka</dt>
					<dd class="font-medium text-slate-800"><?= html_escape($settings['store_hours']) ?></dd>
				</div>
				<div>
					<dt class="text-slate-500">Telepon</dt>
					<dd class="font-medium text-slate-800"><?= html_escape($settings['store_phone']) ?></dd>
				</div>
				<div>
					<dt class="text-slate-500">Email</dt>
					<dd class="font-medium text-slate-800"><?= html_escape($settings['store_email']) ?></dd>
				</div>
			</dl>
		</div>

		<div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
			<h2 class="font-semibold">Aturan peminjaman</h2>
			<ul class="mt-2 space-y-1 list-disc list-inside">
				<li>Durasi pinjam <?= (int) $settings['min_loan_days'] ?>&ndash;<?= (int) $settings['max_loan_days'] ?> hari per buku.</li>
				<li>Reservasi harus diambil maksimal <?= (int) $settings['pickup_deadline_days'] ?> hari setelah disetujui.</li>
				<li>Denda keterlambatan <?= rupiah($settings['fine_per_day']) ?> per hari per buku.</li>
			</ul>
		</div>

		<a href="<?= site_url('katalog') ?>" class="flex items-center justify-center px-5 py-3 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700">Jelajahi katalog</a>
	</div>
</div>
