<!DOCTYPE html>
<html lang="zxx">

<head>
    @include('partials.head')
</head>

<body>

    @include('partials.sidebar')

    @include('partials.navbar')

    <main class="nxl-container">
        @yield('content')

        <!-- [ Footer ] start -->
        @include('partials.footer')
        <!-- [ Footer ] end -->
    </main>

    <!--! BEGIN: Vendors JS !-->
    @include('partials.scripts')
    <!--! END: Theme Customizer !-->
</body>

</html>