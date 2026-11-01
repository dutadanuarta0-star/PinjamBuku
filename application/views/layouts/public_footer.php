</main>

<footer class="mt-12 border-t border-slate-200 bg-white">
	<div class="max-w-6xl mx-auto px-4 py-10 grid gap-8 md:grid-cols-3 text-sm text-slate-600">
		<div>
			<div class="flex items-center gap-2 mb-3">
				<span class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center font-bold">P</span>
				<span class="font-bold text-slate-900"><?= html_escape($settings['store_name']) ?></span>
			</div>
			<p class="leading-relaxed"><?= html_escape(isset($settings['store_description']) ? $settings['store_description'] : '') ?></p>
		</div>
		<div>
			<h2 class="font-semibold text-slate-900 mb-3">Kunjungi Kami</h2>
			<p><?= html_escape(isset($settings['store_address']) ? $settings['store_address'] : '-') ?></p>
			<p class="mt-1"><?= html_escape(isset($settings['store_hours']) ? $settings['store_hours'] : '-') ?></p>
		</div>
		<div>
			<h2 class="font-semibold text-slate-900 mb-3">Kontak</h2>
			<p><?= html_escape(isset($settings['store_phone']) ? $settings['store_phone'] : '-') ?></p>
			<p class="mt-1"><?= html_escape(isset($settings['store_email']) ? $settings['store_email'] : '-') ?></p>
			<div class="mt-3 flex gap-4">
				<a href="<?= site_url('katalog') ?>" class="text-sky-700 hover:underline">Katalog</a>
				<a href="<?= site_url('tentang') ?>" class="text-sky-700 hover:underline">Tentang</a>
			</div>
		</div>
	</div>
	<div class="border-t border-slate-100 py-4 text-center text-xs text-slate-400">
		&copy; <?= date('Y') ?> <?= html_escape($settings['store_name']) ?>. Semua hak dilindungi.
	</div>
</footer>

</body>
</html>
