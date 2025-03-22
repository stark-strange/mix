@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Handle delete button clicks
  document.querySelectorAll('.delete-record').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const deleteUrl = this.getAttribute('data-url');
      document.getElementById('deleteForm').action = deleteUrl;
      new bootstrap.Modal(document.getElementById('deleteConfirmationModal')).show();
    });
  });
});
</script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>
@endsection

@section('content')
<div class="row">
  <div class="col-12 mb-1">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Favourite Movies</h4>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="table-responsive">
        <table class="table">
          <thead class="table-light">
            <tr>
              <th class="text-truncate">Movie</th>
              <th class="text-truncate">Genre</th>
              <th class="text-truncate">Rating</th>
              <th class="text-truncate">Year</th>
              <th class="text-truncate">Duration</th>
              <th class="text-truncate">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($favoriteMovies as $movie)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-3">
                    <img src="{{ $movie->poster }}" alt="Movie Poster" class="rounded">
                  </div>
                  <div>
                    <h6 class="mb-0 text-truncate">{{ $movie->title }}</h6>
                    <small class="text-truncate">{{ $movie->genre }}</small>
                  </div>
                </div>
              </td>
              <td class="text-truncate">
                @php
                  $firstGenre = explode(',', $movie->genre)[0];
                  $icon = match(trim($firstGenre)) {
                    'Action' => 'run',
                    'Drama' => 'drama-masks',
                    'Crime' => 'pistol',
                    'Sci-Fi' => 'rocket-launch',
                    default => 'movie'
                  };
                  $color = match(trim($firstGenre)) {
                    'Action' => 'info',
                    'Drama' => 'primary',
                    'Crime' => 'danger',
                    'Sci-Fi' => 'success',
                    default => 'secondary'
                  };
                @endphp
                <i class="mdi mdi-{{ $icon }} mdi-24px text-{{ $color }} me-1"></i> {{ $firstGenre }}
              </td>
              <td class="text-truncate"><i class="mdi mdi-star text-warning"></i> {{ $movie->rating }}</td>
              <td class="text-truncate">{{ $movie->year }}</td>
              <td class="text-truncate">{{ $movie->runtime }}</td>
              <td>
                <div class="d-flex align-items-center">
                  <button type="button" class="btn btn-link p-0 text-body delete-record" data-url="{{ route('remove.favorite', $movie->id) }}">
                    <i class="mdi mdi-delete-outline"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-4">
                <div class="text-center">
                  <i class="mdi mdi-movie-open mdi-48px text-secondary mb-2"></i>
                  <h6 class="mb-1">No favorite movies yet</h6>
                  <p class="mb-2">Start adding movies to your favorites list!</p>
                  <a href="{{ route('search.index') }}" class="btn btn-primary">
                    <i class="mdi mdi-magnify me-1"></i> Search Movies
                  </a>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this movie from your favorites?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteForm" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
