<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') {{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="sources/logo/logo" href="/sources/logo/skullOnly.ico">


    <!-- Font Awesome cdnjs link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Styles -->
    <link href="{{ url('css/header.css') }}" rel="stylesheet">
    <link href="{{ url('css/footer.css') }}" rel="stylesheet">
    <link href="{{ url('css/extraPages.css') }}" rel="stylesheet">
    <link href="{{ url('css/home.css') }}" rel="stylesheet">
    <link href="{{ url('css/notification.css') }}" rel="stylesheet">
    <link href="{{ url('css/products.css') }}" rel="stylesheet">
    <link href="{{ url('css/about.css') }}" rel="stylesheet">
    <link href="{{ url('css/profile.css') }}" rel="stylesheet">
    <link href="{{ url('css/profileEdit.css') }}" rel="stylesheet">
    <link href="{{ url('css/profileEditPassword.css') }}" rel="stylesheet">
    <link href="{{ url('css/profileDelete.css') }}" rel="stylesheet">
    <link href="{{ url('css/faqs.css') }}" rel="stylesheet">
    <link href="{{ url('css/productDetail.css') }}" rel="stylesheet">
    <link href="{{ url('css/wishlist.css') }}" rel="stylesheet">
    <link href="{{ url('css/cart.css') }}" rel="stylesheet">
    <link href="{{ url('css/checkout.css') }}" rel="stylesheet">
    <link href="{{ url('css/orderDetails.css') }}" rel="stylesheet">
    <link href="{{ url('css/rating.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://js.pusher.com/7.0/pusher.min.js" defer></script>
    <script type="text/javascript" src={{ url('js/app.js') }} defer></script>
    <script type="text/javascript" src={{ url('js/slider.js') }} defer></script>
    <script type="text/javascript" src={{ url('js/tables.js') }} defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="{{ asset('js/promo.js') }}" defer></script>

    <!-- User Identifier -->
    <user user_id="{{ Auth::id() }}"></user>

</head>

<body>
    <main>
        <header>
            <section class="headerBar">

                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('sources/logo/logo_lbaw_side.png') }}" alt="RedHot" width="140">
                </a>

                <nav class="navbar">
                    <a href="{{ url('/products') }}">Produtos</a>
                    <a href="{{ url('/cart') }}">Carrinho</a>
                    <a href="{{ route('notifications', ['user_id' => Auth::id()]) }}">
                        Notificações
                        @if ($numberOfUnreadNotifications() > 0)
                            <span class='number-notifications'>{{ $numberOfUnreadNotifications() }}</span>
                        @endif
                    </a>
                    <a href="{{ url('/users/' . Auth::id()) }}">Perfil</a>
                    <a href="{{ url('/logout') }}">Logout</a>
                </nav>

                <div id="menu-bars" class="fas fa-bars"></div>

            </section>
        </header>

        @yield('content')

        <div class="wrapper"> </div>

        <footer>
            <section class="footer">

                <div class="box-conteiner">

                    <div class="box">
                        <h3>Links Rápidos</h3>
                        <a href="{{ url('/about') }}"> <i class="fas fa-angle-right"></i> Sobre Nós</a>
                        <a href="{{ url('/products') }}"> <i class="fas fa-angle-right"></i> Produtos</a>
                        <a href="{{ url('/cart') }}"> <i class="fas fa-angle-right"></i> Carrinho</a>
                        <a href="{{ url('/users/' . Auth::id()) }}"> <i class="fas fa-angle-right"></i> Perfil</a>
                        <a href="{{ url('/logout') }}"> <i class="fas fa-angle-right"></i> Logout</a>
                    </div>

                    <div class="box">
                        <h3>Links Extra</h3>
                        <a href="{{ url('/faqs') }}"> <i class="fas fa-angle-right"></i> FAQ</a>
                        <a href="{{ url('/pps') }}"> <i class="fas fa-angle-right"></i> Politicas de Privacidade</a>
                        <a href="{{ url('/tous') }}"> <i class="fas fa-angle-right"></i> Termos de Uso</a>
                    </div>

                    <div class="box">
                        <h3>Contactos</h3>
                        <a href="#"> <i class="fas fa-phone"></i> +351 220 000 000</a>
                        <a href="#"> <i class="fas fa-envelope"></i> redhotteam@gmail.com </a>
                        <a href="#"> <i class="fas fa-map"></i> Porto, Portugal - 4200-465</a>
                    </div>

                    <div class="box">
                        <h3>Social</h3>
                        <a href="#"> <i class="fab fa-facebook-f"></i> RedHot</a>
                        <a href="#"> <i class="fab fa-twitter"></i> @RedHot</a>
                        <a href="#"> <i class="fab fa-instagram"></i> @RedHot</a>
                        <a href="#"> <i class="fab fa-youtube"></i> RedHot</a>
                        <a href="#"> <i class="fab fa-linkedin"></i> RedHot</a>
                    </div>

                    <div class="box">
                        <img src="{{ asset('sources/logo/logo_lbaw.png') }}" width="150">
                    </div>

                </div>
            </section>

        </footer>
    </main>
</body>

</html>
