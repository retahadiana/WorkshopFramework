<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Purple Admin</title>
    @include('layout.head')
    @stack('page-styles')
  </head>
  <body>
    @include('layout.navbar')

    <div class="container-fluid page-body-wrapper">
      @include('layout.sidebar')

      <div class="main-panel">
        <div class="content-wrapper">
          @yield('content')
        </div>

        @include('layout.footer')
      </div>
    </div>

    @include('layout.scripts')
    @stack('page-scripts')
  </body>
</html>