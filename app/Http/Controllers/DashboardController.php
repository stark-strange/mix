<?php

namespace App\Http\Controllers;

use App\Models\FavoriteMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $favoriteMovies = FavoriteMovie::where('user_id', Auth::id())->get();
        
        return view('content.dashboard.dashboard', [
            'favoriteMovies' => $favoriteMovies
        ]);
    }
}