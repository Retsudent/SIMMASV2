<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Web\\AuthPage::login');
$routes->get('/login', 'Web\\AuthPage::login');
$routes->get('/logout', 'Web\\AuthPage::logout');
$routes->get('/dashboard/admin', 'Web\\Dashboard::admin');
$routes->get('/dashboard/guru', 'Web\\Dashboard::guru');
$routes->get('/dashboard/siswa', 'Web\\Dashboard::siswa');

// Debug routes
$routes->get('/debug/db', 'DebugController::checkDatabase');
$routes->get('/debug/internships', 'DebugController::testInternships');

// Auth endpoints
$routes->post('api/auth/login', 'AuthController::login');
$routes->get('api/auth/me', 'AuthController::me', ['filter' => 'jwt']);

// Public school info endpoint
$routes->get('api/school-info', 'Admin\\SettingsController::getSchoolInfo');

// Protected examples per role
$routes->group('api/admin', ['filter' => 'jwt:admin'], static function($routes) {
    $routes->get('ping', 'Home::index');
    $routes->get('dudi', 'Admin\\DudiController::index');
    $routes->get('dudi/(:num)', 'Admin\\DudiController::show/$1');
    $routes->get('dudi/(:num)/students', 'Admin\\DudiController::students/$1');
    $routes->post('dudi', 'Admin\\DudiController::create');
    $routes->put('dudi/(:num)', 'Admin\\DudiController::update/$1');
    $routes->delete('dudi/(:num)', 'Admin\\DudiController::delete/$1');
    // users
    $routes->get('users', 'Admin\\UserController::index');
    $routes->post('users', 'Admin\\UserController::create');
    $routes->put('users/(:num)', 'Admin\\UserController::update/$1');
    $routes->delete('users/(:num)', 'Admin\\UserController::delete/$1');
        // settings
        $routes->get('settings', 'Admin\\SettingsController::index');
        $routes->put('settings', 'Admin\\SettingsController::update');
        $routes->post('settings/upload-logo', 'Admin\\SettingsController::uploadLogo');
    // stats
    $routes->get('stats', 'Admin\\StatsController::overview');
});

$routes->group('api/guru', ['filter' => 'jwt:guru'], static function($routes) {
    $routes->get('ping', 'Home::index');
    $routes->get('logbook', 'Guru\\LogbookController::index');
    $routes->get('logbook/stats', 'Guru\\LogbookController::stats');
    $routes->put('logbook/(:num)/verify', 'Guru\\LogbookController::verify/$1');
    $routes->get('stats', 'Guru\\StatsController::overview');
    // Global stats (same as admin dashboard) but accessible to guru
    $routes->get('stats/global', 'Admin\\StatsController::overview');
    // DUDI read-only for guru (filtered by mentees)
    $routes->get('dudi', 'Guru\\DudiController::index');
    $routes->get('dudi/all', 'Guru\\DudiController::all');
    $routes->get('dudi/stats', 'Guru\\DudiController::stats');
    // Internship management (read + limited actions)
    $routes->get('internships', 'Guru\\InternshipController::index');
    $routes->get('internships/stats', 'Guru\\InternshipController::stats');
    $routes->get('internships/debug', 'Guru\\InternshipController::debugAll');
    $routes->get('internships/(:num)/debug', 'Guru\\InternshipController::debug/$1');
    $routes->put('internships/(:num)/activate', 'Guru\\InternshipController::activate/$1');
    $routes->put('internships/(:num)/grade', 'Guru\\InternshipController::grade/$1');
    $routes->put('internships/(:num)', 'Guru\\InternshipController::update/$1');
    $routes->delete('internships/(:num)', 'Guru\\InternshipController::delete/$1');
    $routes->post('internships', 'Guru\\InternshipController::create');
    $routes->get('students', 'Guru\\InternshipController::students');
});

$routes->group('api/siswa', ['filter' => 'jwt:siswa'], static function($routes) {
    $routes->get('ping', 'Home::index');
    $routes->get('logbook', 'Siswa\\LogbookController::index');
    $routes->get('logbook/(:num)', 'Siswa\\LogbookController::show/$1');
    $routes->post('logbook', 'Siswa\\LogbookController::create');
    $routes->put('logbook/(:num)', 'Siswa\\LogbookController::update/$1');
    $routes->delete('logbook/(:num)', 'Siswa\\LogbookController::delete/$1');
    $routes->get('my/internship', 'Siswa\\MyController::internship');
    // siswa mendaftar magang (status default: pending)
    $routes->post('internships', 'Siswa\\InternshipController::apply');
    // daftar pengajuan/penempatan saya (pending/aktif)
    $routes->get('internships', 'Siswa\\InternshipController::myList');
    // daftar DUDI aktif untuk siswa
    $routes->get('dudi', 'Siswa\\DudiController::all');
});

// File download route (public)
$routes->get('uploads/(:any)', 'FileController::download/$1');
