@extends('layouts/contentNavbarLayout')

@section('title', 'Search Movies')

@section('content')
<div class="row">
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Search Movies</h4>
      </div>
      <div class="card-body">
        <form action="{{ route('search.movies') }}" method="POST" class="row g-3" id="searchForm">
          @csrf
          <div class="col-12">
            <div class="input-group">
              <input type="text" class="form-control" name="title" placeholder="Enter movie keyword..." required value="{{ $keyword ?? '' }}">
              <button class="btn btn-primary" type="submit" id="searchButton">
                <i class="mdi mdi-magnify me-1"></i> Search
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Loading Spinner -->
  <div class="col-12 mb-4 d-none" id="loadingSpinner">
    <div class="card">
      <div class="card-body text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <h5 class="mt-2">Searching Movies...</h5>
        <p class="text-muted">This may take a few seconds</p>
      </div>
    </div>
  </div>

  <!-- Search Results -->
  <div id="searchResults" class="row">
    @if(isset($searchPerformed) && isset($movies))
      @if(count($movies) > 0)
        @foreach($movies as $movie)
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                  <div class="flex-shrink-0">
                    <img src="{{ $movie['Poster'] !== 'N/A' ? $movie['Poster'] : asset('assets/img/elements/1.jpg') }}" 
                         alt="{{ $movie['Title'] }}" 
                         class="rounded"
                         style="width: 100px; height: 150px; object-fit: cover;">
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="mb-1">{{ $movie['Title'] }}</h5>
                    <div class="mb-2">
                      <span class="badge bg-label-primary me-1">{{ $movie['Year'] }}</span>
                      <span class="badge bg-label-info">{{ $movie['Runtime'] }}</span>
                    </div>
                    <div class="mb-2">
                      <i class="mdi mdi-star text-warning"></i>
                      <span>{{ $movie['imdbRating'] }}/10</span>
                    </div>
                    <p class="text-muted mb-0">{{ Str::limit($movie['Plot'], 100) }}</p>
                  </div>
                </div>
                <div class="border-top pt-3">
                  <div class="row">
                    <div class="col-6">
                      <small class="text-muted mb-1 d-block">Director</small>
                      <span class="text-truncate d-block">{{ $movie['Director'] }}</span>
                    </div>
                    <div class="col-6">
                      <small class="text-muted mb-1 d-block">Genre</small>
                      <span class="text-truncate d-block">{{ $movie['Genre'] }}</span>
                    </div>
                  </div>
                </div>
                <div class="mt-3 text-center">
                  <div class="favorite-container" data-imdb-id="{{ $movie['imdbID'] }}">
                    @if($movie['isFavorite'])
                      <button type="button" class="btn btn-outline-primary me-2" disabled>
                        <i class="mdi mdi-heart text-danger"></i> In Favorites
                      </button>
                    @else
                      <button type="button" class="btn btn-primary me-2 add-favorite-btn" data-movie='@json($movie)'>
                        <i class="mdi mdi-heart-outline"></i> Add to Favorites
                      </button>
                    @endif
                  </div>
                  <a href="https://www.imdb.com/title/{{ $movie['imdbID'] }}" target="_blank" class="btn btn-outline-primary">
                    <i class="mdi mdi-web me-1"></i> View on IMDb
                  </a>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <div class="col-12">
          <div class="card">
            <div class="card-body text-center py-5">
              <i class="mdi mdi-movie-off mdi-48px text-secondary mb-2"></i>
              <h5>No Movies Found</h5>
              <p class="text-muted">Try searching with different keywords</p>
            </div>
          </div>
        </div>
      @endif
    @endif
  </div>
</div>

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchForm = document.getElementById('searchForm');
  const searchResults = document.getElementById('searchResults');
  const loadingSpinner = document.getElementById('loadingSpinner');

  // Handle search form submission
  searchForm.addEventListener('submit', function() {
    searchResults.style.opacity = '0.5';
    loadingSpinner.classList.remove('d-none');
    
    // Re-enable the form after 10 seconds in case of errors
    setTimeout(function() {
      searchResults.style.opacity = '1';
      loadingSpinner.classList.add('d-none');
    }, 10000);
  });

  // Handle adding to favorites
  document.addEventListener('click', function(e) {
    if (e.target.closest('.add-favorite-btn')) {
      const btn = e.target.closest('.add-favorite-btn');
      const movieData = btn.dataset.movie;
      const container = btn.closest('.favorite-container');
      
      // Show loading state
      btn.disabled = true;
      btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Adding...';

      // Send AJAX request
      fetch('{{ route("add.favorite") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          movie_data: movieData
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          // Update UI to show favorited state
          container.innerHTML = `
            <button type="button" class="btn btn-outline-primary me-2" disabled>
              <i class="mdi mdi-heart text-danger"></i> In Favorites
            </button>
          `;
          
          // Show success message
          showToast('success', data.message);
        } else {
          // Revert button state
          btn.disabled = false;
          btn.innerHTML = '<i class="mdi mdi-heart-outline"></i> Add to Favorites';
          
          // Show error message
          showToast('error', data.message);
        }
      })
      .catch(error => {
        // Revert button state on error
        btn.disabled = false;
        btn.innerHTML = '<i class="mdi mdi-heart-outline"></i> Add to Favorites';
        showToast('error', 'An error occurred. Please try again.');
      });
    }
  });

  // Helper function to show toast messages
  function showToast(type, message) {
    // You can implement this based on your toast notification system
    // For example, using the toast function from your layout
    if (typeof toast === 'function') {
      toast(type, message);
    }
  }
});
</script>
@endsection

@endsection
