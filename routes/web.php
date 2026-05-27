<?php

use App\Http\Controllers\Challenges\AggregationController;
use App\Http\Controllers\Challenges\IndexingController;
use App\Http\Controllers\Challenges\LeftJoinController;
use App\Http\Controllers\Challenges\NPlusOneController;
use App\Http\Controllers\Challenges\PaginationController;
use App\Http\Controllers\Challenges\SelectController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('index');

Route::get('/challenges/01-select', SelectController::class)->name('challenges.select');
Route::get('/challenges/02-nplus1', NPlusOneController::class)->name('challenges.nplus1');
Route::get('/challenges/03-left-join', LeftJoinController::class)->name('challenges.left-join');
Route::get('/challenges/04-aggregation', AggregationController::class)->name('challenges.aggregation');
Route::get('/challenges/05-indexing', IndexingController::class)->name('challenges.indexing');
Route::get('/challenges/06-pagination', PaginationController::class)->name('challenges.pagination');
