@extends('layouts/commonMaster' )

@php
/* Display elements */
$contentNavbar = true;
$containerNav = ($containerNav ?? 'container-xxl');
$isNavbar = ($isNavbar ?? true);
$isMenu = ($isMenu ?? true);
$isFlex = ($isFlex ?? false);
$isFooter = ($isFooter ?? true);

/* HTML Classes */
$navbarDetached = 'navbar-detached';

/* Content classes */
$container = ($container ?? 'container-xxl');

@endphp

@section('layoutContent')
  <!-- Toaster -->
  @if(session('success') || session('error'))
  <div class="bs-toast toast toast-placement-ex m-2 fade bg-{{ session('success') ? 'success' : 'danger' }} top-0 end-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
    <div class="toast-header">
      <i class="mdi mdi-{{ session('success') ? 'check-circle' : 'alert-circle' }} me-2"></i>
      <div class="me-auto fw-semibold">{{ session('success') ? 'Success' : 'Error' }}</div>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      {{ session('success') ?? session('error') }}
    </div>
  </div>
  @endif

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Remove from Favorites</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-4">
            <i class="mdi mdi-alert-circle-outline mdi-48px text-warning mb-2"></i>
            <h6>Are you sure you want to remove this movie from favorites?</h6>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <form id="deleteForm" action="" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Remove</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
    <div class="layout-container">

      @if ($isMenu)
      @include('layouts/sections/menu/verticalMenu')
      @endif


      <!-- Layout page -->
      <div class="layout-page">
        <!-- BEGIN: Navbar-->
        @if ($isNavbar)
        @include('layouts/sections/navbar/navbar')
        @endif
        <!-- END: Navbar-->


        <!-- Content wrapper -->
        <div class="content-wrapper">

          <!-- Content -->
          @if ($isFlex)
          <div class="{{$container}} d-flex align-items-stretch flex-grow-1 p-0">
            @else
            <div class="{{$container}} flex-grow-1 container-p-y">
              @endif

              @yield('content')

            </div>
            <!-- / Content -->

            <!-- Footer -->
            @if ($isFooter)
            @include('layouts/sections/footer/footer')
            @endif
            <!-- / Footer -->
            <div class="content-backdrop fade"></div>
          </div>
          <!--/ Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      @if ($isMenu)
      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
      @endif
      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->
  @endsection
