<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Halaman publik
$route['tentang'] = 'home/about';
$route['katalog'] = 'catalog/index';
$route['buku/(:any)'] = 'catalog/detail/$1';

// Akun customer
$route['daftar'] = 'auth/register';
$route['masuk'] = 'auth/login';
$route['keluar'] = 'auth/logout';

// Keranjang & reservasi
$route['keranjang'] = 'cart/index';
$route['keranjang/tambah'] = 'cart/addItem';
$route['keranjang/ubah'] = 'cart/updateItem';
$route['keranjang/hapus'] = 'cart/removeItem';
$route['keranjang/reservasi'] = 'cart/checkout';

// Peminjaman customer
$route['peminjaman'] = 'loans/index';
$route['peminjaman/batal/(:num)'] = 'loans/cancel/$1';

// Admin
$route['admin'] = 'adminDashboard/index';
$route['admin/masuk'] = 'adminAuth/login';
$route['admin/keluar'] = 'adminAuth/logout';

$route['admin/kategori'] = 'adminCategories/index';
$route['admin/kategori/tambah'] = 'adminCategories/create';
$route['admin/kategori/(:num)/ubah'] = 'adminCategories/edit/$1';
$route['admin/kategori/(:num)/hapus'] = 'adminCategories/delete/$1';

$route['admin/buku'] = 'adminBooks/index';
$route['admin/buku/tambah'] = 'adminBooks/create';
$route['admin/buku/(:num)/ubah'] = 'adminBooks/edit/$1';
$route['admin/buku/(:num)/hapus'] = 'adminBooks/delete/$1';

$route['admin/peminjaman'] = 'adminLoans/index';
$route['admin/peminjaman/(:num)/setujui'] = 'adminLoans/approve/$1';
$route['admin/peminjaman/(:num)/tolak'] = 'adminLoans/reject/$1';
$route['admin/peminjaman/(:num)/ambil'] = 'adminLoans/pickup/$1';
$route['admin/peminjaman/(:num)/kembali'] = 'adminLoans/returnBooks/$1';
$route['admin/peminjaman/(:num)/denda-lunas'] = 'adminLoans/markFinePaid/$1';
$route['admin/peminjaman/(:num)'] = 'adminLoans/detail/$1';

$route['admin/pengaturan'] = 'adminSettings/index';
