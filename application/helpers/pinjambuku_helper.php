<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('rupiah'))
{
	/**
	 * Format angka menjadi Rupiah tanpa desimal.
	 */
	function rupiah($amount)
	{
		return 'Rp'.number_format((float) $amount, 0, ',', '.');
	}
}

if ( ! function_exists('loan_status_label'))
{
	function loan_status_label($status)
	{
		$labels = array(
			'pending'  => 'Menunggu konfirmasi',
			'approved' => 'Siap diambil',
			'borrowed' => 'Sedang dipinjam',
			'returned' => 'Selesai',
			'rejected' => 'Ditolak',
			'cancelled'=> 'Dibatalkan',
			'expired'  => 'Kedaluwarsa',
		);

		return isset($labels[$status]) ? $labels[$status] : ucfirst($status);
	}
}

if ( ! function_exists('loan_status_class'))
{
	/**
	 * Kelas Tailwind untuk badge status peminjaman.
	 */
	function loan_status_class($status)
	{
		$classes = array(
			'pending'  => 'bg-amber-100 text-amber-800 border-amber-200',
			'approved' => 'bg-sky-100 text-sky-800 border-sky-200',
			'borrowed' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
			'returned' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
			'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
			'cancelled'=> 'bg-gray-100 text-gray-700 border-gray-200',
			'expired'  => 'bg-orange-100 text-orange-800 border-orange-200',
		);

		return isset($classes[$status]) ? $classes[$status] : 'bg-gray-100 text-gray-700 border-gray-200';
	}
}

if ( ! function_exists('format_datetime'))
{
	function format_datetime($datetime, $with_time = TRUE)
	{
		if (empty($datetime))
		{
			return '-';
		}

		$time = strtotime($datetime);

		return $with_time ? date('d/m/Y H:i', $time) : date('d/m/Y', $time);
	}
}

if ( ! function_exists('days_late'))
{
	/**
	 * Jumlah hari keterlambatan kalender. 0 jika belum lewat jatuh tempo.
	 */
	function days_late($due_date, $until = NULL)
	{
		if (empty($due_date))
		{
			return 0;
		}

		$due = new DateTime(date('Y-m-d', strtotime($due_date)));
		$end = new DateTime($until ? date('Y-m-d', strtotime($until)) : date('Y-m-d'));

		if ($end <= $due)
		{
			return 0;
		}

		return (int) $due->diff($end)->days;
	}
}

if ( ! function_exists('is_valid_cover_url'))
{
	/**
	 * URL cover harus kosong atau URL http(s) lengkap.
	 */
	function is_valid_cover_url($url)
	{
		$url = trim((string) $url);

		if ($url === '')
		{
			return TRUE;
		}

		if ( ! filter_var($url, FILTER_VALIDATE_URL))
		{
			return FALSE;
		}

		$scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

		return in_array($scheme, array('http', 'https'), TRUE);
	}
}

if ( ! function_exists('pagination_config'))
{
	/**
	 * Konfigurasi pagination CI3 dengan gaya Tailwind dan query string.
	 */
	function pagination_config($baseUrl, $totalRows, $perPage)
	{
		return array(
			'base_url'            => $baseUrl,
			'total_rows'          => (int) $totalRows,
			'per_page'            => (int) $perPage,
			'page_query_string'   => TRUE,
			'query_string_segment'=> 'page',
			'reuse_query_string'  => TRUE,
			'use_page_numbers'    => TRUE,
			'first_link'          => '&laquo;',
			'last_link'           => '&raquo;',
			'prev_link'           => '&lsaquo;',
			'next_link'           => '&rsaquo;',
			'full_tag_open'       => '<nav class="flex flex-wrap items-center gap-1 mt-8" aria-label="Pagination">',
			'full_tag_close'      => '</nav>',
			'cur_tag_open'        => '<span class="inline-flex items-center justify-center min-w-9 h-9 px-3 rounded-lg bg-sky-600 text-white text-sm font-semibold">',
			'cur_tag_close'       => '</span>',
			'num_tag_open'        => '<span class="inline-flex">',
			'num_tag_close'       => '</span>',
			'attributes'          => array('class' => 'inline-flex items-center justify-center min-w-9 h-9 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-700 hover:bg-gray-50'),
		);
	}
}
