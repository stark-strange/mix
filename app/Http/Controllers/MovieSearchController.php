<?php

namespace App\Http\Controllers;

use App\Models\FavoriteMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class MovieSearchController extends Controller
{
    public function index()
    {
        return view('content.movie-search.search');
    }

    public function search(Request $request)
    {
        $keyword = $request->input('title');
        
        // First, search for movies matching the keyword
        $searchResponse = Http::get('https://www.omdbapi.com/', [
            's' => $keyword,
            'apikey' => 'af123112'
        ]);

        $searchResults = $searchResponse->json();
        
        if (isset($searchResults['Error'])) {
            return back()->with('error', 'No movies found. Please try a different keyword.');
        }

        // Get detailed information for each movie
        $movies = [];
        foreach (array_slice($searchResults['Search'], 0, 8) as $result) {
            $detailResponse = Http::get('https://www.omdbapi.com/', [
                'i' => $result['imdbID'],
                'apikey' => 'af123112'
            ]);
            
            $movieDetail = $detailResponse->json();
            if (!isset($movieDetail['Error'])) {
                // Check if movie is in favorites
                $movieDetail['isFavorite'] = FavoriteMovie::where('user_id', Auth::id())
                    ->where('imdb_id', $movieDetail['imdbID'])
                    ->exists();
                    
                $movies[] = $movieDetail;
            }
        }
        
        if (empty($movies)) {
            return back()->with('error', 'No detailed movie information found.');
        }
        
        return view('content.movie-search.search', [
            'movies' => $movies,
            'searchPerformed' => true,
            'keyword' => $keyword
        ])->with('success', count($movies) . ' movies found for "' . $keyword . '"');
    }

    public function addToFavorites(Request $request)
    {
        $movieData = json_decode($request->input('movie_data'), true);
        
        // Check if movie already exists in favorites
        $exists = FavoriteMovie::where('user_id', Auth::id())
            ->where('imdb_id', $movieData['imdbID'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Movie is already in your favorites!'
            ]);
        }

        // Create new favorite movie
        FavoriteMovie::create([
            'user_id' => Auth::id(),
            'title' => $movieData['Title'],
            'year' => $movieData['Year'],
            'poster' => $movieData['Poster'] !== 'N/A' ? $movieData['Poster'] : asset('assets/img/elements/1.jpg'),
            'genre' => $movieData['Genre'],
            'rating' => $movieData['imdbRating'],
            'runtime' => $movieData['Runtime'],
            'imdb_id' => $movieData['imdbID']
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $movieData['Title'] . ' has been added to your favorites!'
        ]);
    }

    public function removeFavorite(FavoriteMovie $movie)
    {
        // Check if the movie belongs to the authenticated user
        if ($movie->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to remove this movie.');
        }

        $title = $movie->title;
        $movie->delete();
        
        return redirect()->back()->with('success', $title . ' has been removed from your favorites.');
    }
}
