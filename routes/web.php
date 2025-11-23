<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\BracketController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MatchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Main navigation routes
Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/teams', [TeamController::class, 'index'])->name('teams');
Route::get('/users', [UserController::class, 'index'])->name('users');

// Authentication routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Profile routes (authenticated users only)
Route::get('/profile', [ProfileController::class, 'show'])->name('profile')->middleware('auth');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');

// Admin routes (admin users only)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'adminIndex'])->name('admin.users');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // Tournament approval
    Route::get('/tournaments', [TournamentController::class, 'adminIndex'])->name('admin.tournaments');
    Route::post('/tournaments/{id}/approve', [TournamentController::class, 'approve'])->name('admin.tournaments.approve');
    Route::post('/tournaments/{id}/reject', [TournamentController::class, 'reject'])->name('admin.tournaments.reject');
});

// Team routes (create must be before {id} to avoid conflicts)
Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create')->middleware('auth');
Route::post('/teams', [TeamController::class, 'store'])->name('teams.store')->middleware('auth');

Route::get('/teams/{id}', [TeamController::class, 'show'])->name('teams.team.show');
Route::get('/teams/{id}/edit', [TeamController::class, 'edit'])->name('teams.team-edit')->middleware('auth');
Route::put('/teams/{id}', [TeamController::class, 'update'])->name('teams.update')->middleware('auth');
Route::delete('/teams/{id}', [TeamController::class, 'destroy'])->name('teams.destroy')->middleware('auth');
Route::post('/teams/{id}/players', [TeamController::class, 'addPlayer'])->name('teams.addPlayer')->middleware('auth');
Route::delete('/teams/{id}/players/{userId}', [TeamController::class, 'removePlayer'])->name('teams.removePlayer')->middleware('auth');
Route::post('/teams/{id}/leave', [TeamController::class, 'leave'])->name('teams.leave')->middleware('auth');

// User profile routes 
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.user.show');

// Tournament routes (create must be before {id} to avoid conflicts)
Route::get('/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create')->middleware('auth');
Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store')->middleware('auth');

Route::get('/tournaments/{id}', [TournamentController::class, 'show'])->name('tournaments.show');
Route::get('/tournaments/{id}/edit', [TournamentController::class, 'edit'])->name('tournaments.edit')->middleware('auth');
Route::put('/tournaments/{id}', [TournamentController::class, 'update'])->name('tournaments.update')->middleware('auth');
Route::delete('/tournaments/{id}', [TournamentController::class, 'destroy'])->name('tournaments.destroy')->middleware('auth');

// Tournament registration
Route::post('/tournaments/{id}/register', [TournamentController::class, 'register'])->name('tournaments.register')->middleware('auth');
Route::post('/tournaments/{id}/register-team', [TournamentController::class, 'registerTeam'])->name('tournaments.registerTeam')->middleware('auth');
Route::delete('/tournaments/{id}/unregister', [TournamentController::class, 'unregister'])->name('tournaments.unregister')->middleware('auth');
Route::delete('/tournaments/{id}/unregister-team/{teamId}', [TournamentController::class, 'unregisterTeam'])->name('tournaments.unregisterTeam')->middleware('auth');

// Organizer contestant management
Route::get('/tournaments/{id}/contestants', [TournamentController::class, 'manageContestants'])->name('tournaments.manageContestants')->middleware('auth');
Route::post('/tournaments/{id}/contestants/{contestantId}/approve', [TournamentController::class, 'approveContestant'])->name('tournaments.approveContestant')->middleware('auth');
Route::post('/tournaments/{id}/contestants/{contestantId}/reject', [TournamentController::class, 'rejectContestant'])->name('tournaments.rejectContestant')->middleware('auth');
Route::delete('/tournaments/{id}/contestants/{contestantId}/remove', [TournamentController::class, 'removeContestant'])->name('tournaments.removeContestant')->middleware('auth');

// Bracket management routes
Route::post('/tournaments/{id}/shuffle-contestants', [BracketController::class, 'shuffle'])->name('tournaments.shuffle')->middleware('auth');
Route::post('/tournaments/{id}/reorder-contestants', [BracketController::class, 'reorder'])->name('tournaments.reorder')->middleware('auth');
Route::post('/tournaments/{tournament}/generate-bracket', [BracketController::class, 'generate'])
    ->name('tournaments.generateBracket');

// OPTIONAL: allow GET (remove if not desired)
Route::get('/tournaments/{tournament}/generate-bracket', function() {
    return redirect()->back();
});

Route::post('/tournaments/{tournament}/clear-bracket', [BracketController::class, 'clear'])
    ->name('tournaments.clearBracket');
Route::delete('/tournaments/{id}/teams/{teamId}', [TournamentController::class, 'removeTeam'])
    ->name('tournaments.removeTeam')
    ->middleware('auth');

// Match routes
Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
Route::get('/matches/{id}', [MatchController::class, 'show'])->name('matches.show');
Route::get('/matches/{id}/edit', [MatchController::class, 'edit'])->name('matches.edit')->middleware('auth');
Route::put('/matches/{id}', [MatchController::class, 'update'])->name('matches.update')->middleware('auth');
Route::delete('/matches/{id}', [MatchController::class, 'destroy'])->name('matches.destroy')->middleware('auth');
Route::post('/matches/{id}/result', [MatchController::class, 'setResult'])->name('matches.setResult')->middleware('auth');

// Prize management routes (for organizers)
Route::middleware('auth')->group(function () {
    Route::get('/tournaments/{id}/prizes', [TournamentController::class, 'managePrizes'])->name('tournaments.managePrizes');
    Route::post('/tournaments/{id}/prizes', [TournamentController::class, 'storePrize'])->name('tournaments.storePrize');
    Route::put('/tournaments/{id}/prizes/{prizeIndex}', [TournamentController::class, 'updatePrize'])->name('tournaments.updatePrize');
    Route::delete('/tournaments/{id}/prizes/{prizeIndex}', [TournamentController::class, 'destroyPrize'])->name('tournaments.destroyPrize');
});