<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrgController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResearcherController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

$role = fn (string $r) => RoleMiddleware::class . ':' . $r;

// Public
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/leaderboard', [PublicController::class, 'leaderboard'])->name('leaderboard');
Route::get('/hall-of-fame', [PublicController::class, 'hallOfFame'])->name('hall');
Route::get('/programs', [PublicController::class, 'programs'])->name('programs');
Route::get('/programs/{program}', [PublicController::class, 'program'])->name('programs.show');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register/researcher', [AuthController::class, 'showResearcherRegister'])->name('register.researcher');
    Route::post('/register/researcher', [AuthController::class, 'registerResearcher']);
    Route::get('/register/organization', [AuthController::class, 'showOrgRegister'])->name('register.org');
    Route::post('/register/organization', [AuthController::class, 'registerOrg']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () use ($role) {
    // Shared report pages (authorization is checked in the controller)
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::post('/reports/{report}/comments', [ReportController::class, 'comment'])->name('reports.comment');
    Route::patch('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');

    // Researcher
    Route::middleware($role('researcher'))->prefix('researcher')->name('researcher.')->group(function () {
        Route::get('/', [ResearcherController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [ResearcherController::class, 'reports'])->name('reports');
        Route::get('/programs/{program}/report', [ResearcherController::class, 'createReport'])->name('report.create');
        Route::post('/programs/{program}/report', [ResearcherController::class, 'storeReport'])->name('report.store');
        Route::get('/wallet', [ResearcherController::class, 'wallet'])->name('wallet');
        Route::post('/withdraw', [ResearcherController::class, 'withdraw'])->name('withdraw');
    });

    // Organization (waiting-for-approval page is reachable before approval)
    Route::get('/org/pending', [OrgController::class, 'pending'])->middleware($role('organization'))->name('org.pending');
    Route::middleware($role('org'))->prefix('org')->name('org.')->group(function () {
        Route::get('/', [OrgController::class, 'dashboard'])->name('dashboard');
        Route::get('/programs/create', [OrgController::class, 'createProgram'])->name('programs.create');
        Route::post('/programs', [OrgController::class, 'storeProgram'])->name('programs.store');
        Route::get('/reports', [OrgController::class, 'reports'])->name('reports');
    });

    // Admin
    Route::middleware($role('admin'))->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
        Route::get('/organizations', [AdminController::class, 'orgs'])->name('orgs');
        Route::post('/organizations/{org}/approve', [AdminController::class, 'approveOrg'])->name('orgs.approve');
        Route::post('/organizations/{org}/reject', [AdminController::class, 'rejectOrg'])->name('orgs.reject');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
        Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('withdrawals');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/audit', [AdminController::class, 'audit'])->name('audit');
    });
});
