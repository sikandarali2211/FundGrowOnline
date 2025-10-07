<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FundGrow-Online</title>

    <!-- Bootstrap & Fonts -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">



    <style>
        html,
        body {
            overflow-x: hidden;
            /* Prevent horizontal scroll */
        }

        /* Desktop Floating Navbar */
        @media (min-width: 992px) {
            :root {
                --nav-h: 64px;
            }

            .floating-nav {
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                width: 95%;
                max-width: 900px;

                height: var(--nav-h);
                padding-inline: 16px;
                /* equal left/right */
                padding-block: 0;
                /* we center via flex, not padding */

                background: rgba(7, 45, 66, 0.85);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(59, 209, 122, 0.3);
                border-radius: 50px;

                display: flex;
                align-items: center;
                /* ✨ vertically centers everything */
                justify-content: center;
                /* middle container will handle spacing */
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
                z-index: 1000;
                transition: transform .3s ease, opacity .3s ease;
            }

            .floating-nav.hidden-nav {
                transform: translateX(-50%) translateY(-120%);
                opacity: 0;
            }

            .floating-nav .navbar {
                height: 100%;
                width: 100%;
            }

            .floating-nav .container-fluid {
                height: 100%;
                display: flex;
                align-items: center;
                /* center brand + menu + auth */
                justify-content: space-between;
                gap: 16px;
            }

            .navbar-brand {
                display: flex;
                align-items: center;
                height: 100%;
            }

            .navbar-brand img {
                height: calc(var(--nav-h) - 10px);
                /* nicely fits inside pill */
                display: block;
            }

            .menu {
                display: flex;
                align-items: center;
                /* ✨ nav items perfectly centered */
                gap: 28px;
                height: 100%;
                margin: 0 !important;
                padding: 0;
                list-style: none;
            }

            .menu .nav-item {
                height: 100%;
                display: flex;
                align-items: center;
            }

            .menu .nav-link {
                height: 100%;
                display: flex;
                align-items: center;
                /* ✨ true vertical center */
                justify-content: center;
                padding-inline: 10px;
                /* equal left/right on links */
                line-height: 1;
                font-size: 15px;
                font-weight: 600;
                color: #3bd17a !important;
            }

            .menu .nav-link:hover {
                color: #ffffff !important;
            }

            /* Right-side auth buttons wrapper */
            .auth {
                height: 100%;
                display: flex;
                align-items: center;
                /* ✨ centers Sign In / Sign Up vertically */
                gap: 10px;
            }

            /* Buttons: compact padding as requested */
            .cmn--btn {
                background: #3bd17a;
                color: #072d42;
                border: none;
                padding: 8px 18px;
                /* 👈 smaller than before */
                font-size: 14px;
                border-radius: 24px;
                font-weight: 600;
                line-height: 1;
                /* keeps height tight */
                transition: all 0.3s;
                text-decoration: none;
            }

            .floating-nav .cmn--btn1 {
                padding: 8px 18px;
                font-size: 15px;
                border-radius: 26px;
                background: #3bd17a;
                color: #072d42;
                border: none;
                font-weight: 600;
                line-height: 1;
                transition: all 0.3s;
                text-decoration: none;
            }

            .floating-nav .auth {
                gap: 12px;
            }

            .cmn--btn1 {
                background: #3bd17a;
                color: #072d42;
                border: none;
                padding: 10px 18px;
                font-size: 14px;
                border-radius: 24px;
                font-weight: 600;
                line-height: 1;
                transition: all 0.3s;
                text-decoration: none;
            }


        }


        /* ---- MOBILE COLLAPSE CLEANUP (<=991px) ---- */
        @media (max-width: 991px) {
            :root {
                --mnav-h: 56px;
            }

            /* slim fixed bar */
            .navbar.d-lg-none {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: var(--mnav-h);
                padding: 0 12px;
                background: rgba(7, 45, 66, .9);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(59, 209, 122, .15);
                z-index: 1000;
            }

            .navbar.d-lg-none .container-fluid {
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding: 0;
            }

            .navbar.d-lg-none .navbar-brand img {
                height: 40px !important;
                width: auto;
            }

            .navbar.d-lg-none .navbar-toggler {
                color: #fff;
                border: 0;
                padding: 6px;
                line-height: 1;
            }

            .navbar.d-lg-none .navbar-toggler:focus {
                box-shadow: none;
            }


            .navbar.d-lg-none .collapse {
                display: none;
            }

            .navbar.d-lg-none .collapse.show {
                display: block;
            }


            .navbar.d-lg-none .navbar-collapse {
                position: absolute;
                top: var(--mnav-h);
                left: 0;
                right: 0;
                background: rgba(7, 45, 66, .98);
                backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(59, 209, 122, .2);
                box-shadow: 0 12px 30px rgba(0, 0, 0, .35);
                padding: 12px;
                max-height: calc(100vh - var(--mnav-h));
                overflow-y: auto;
                transform-origin: top;
                transform: scaleY(0.96);
                opacity: 0;
                transition: transform .18s ease, opacity .18s ease;
            }

            .navbar.d-lg-none .navbar-collapse.show {
                transform: scaleY(1);
                opacity: 1;
            }


            .navbar.d-lg-none .navbar-nav {
                display: grid;
                gap: 6px;
                margin: 0;
                padding: 0;
            }

            .navbar.d-lg-none .nav-link {
                display: block;
                width: 100%;
                padding: 12px 14px;
                border-radius: 10px;
                color: #e9fff4 !important;
                font-weight: 600;
                text-align: left;
                background: rgba(255, 255, 255, .02);
                border: 1px solid rgba(59, 209, 122, .12);
            }

            .navbar.d-lg-none .nav-link:hover {
                background: rgba(59, 209, 122, .12);
                color: #3bd17a !important;
            }


            .navbar.d-lg-none .text-center {
                text-align: left !important;
            }

            .navbar.d-lg-none .cmn--btn {
                display: block;
                width: 100%;
                margin: 8px 0 0 0;
                padding: 10px 16px;
                border-radius: 12px;
                background: #3bd17a;
                color: #072d42;
                font-weight: 700;
                text-align: center;
            }

            .cmn--btn {
                background: #3bd17a;
                color: #072d42;
                border: none;
                padding: 8px 18px;
                font-size: 14px;
                border-radius: 24px;
                font-weight: 600;
                line-height: 1;
                transition: all 0.3s;
                text-decoration: none;
            }

            .cmn--btn1 {
                background: #3bd17a;
                color: #072d42;
                border: none;
                padding: 10px 18px;
                font-size: 14px;
                border-radius: 24px;
                font-weight: 600;
                line-height: 1;
                transition: all 0.3s;
                text-decoration: none;
            }
        }
    </style>
</head>

<body>


    <!-- Desktop Floating Navbar -->
    <header id="floatingNav" class="floating-nav d-none d-lg-block">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand text-white" href="/">
                    <img src="{{ asset('assets/images/favicon.png') }}" alt="logo">
                </a>

                <ul class="navbar-nav menu mx-auto">
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="/services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                </ul>

                <div class="auth">
                    @guest
                        <a href="{{ route('login') }}" class="cmn--btn1">Sign In</a>
                        <a href="{{ route('register') }}" class="cmn--btn1">Sign Up</a>
                    @else
                        <a href="{{ Auth::user()->utype === 'ADM' ? route('admin.index') : route('user.index') }}"
                            class="cmn--btn">{{ Auth::user()->name }}</a>
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="cmn--btn">Logout</button>
                        </form>
                    @endguest
                </div>
            </div>
        </nav>
    </header>


    <!-- Mobile Navbar -->
    <nav class="navbar navbar-expand-lg d-lg-none">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="/">
                <img src="{{ asset('assets/images/favicon.png') }}" alt="logo" height="87">
            </a>
            <!-- Hamburger Menu Button -->
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Collapsible Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="/services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                </ul>
                <div class="text-center mt-3">
                    @guest
                        <a href="{{ route('login') }}" class="cmn--btn">Sign In</a>
                        <a href="{{ route('register') }}" class="cmn--btn">Sign Up</a>
                    @else
                        <a href="{{ Auth::user()->utype === 'ADM' ? route('admin.index') : route('user.index') }}"
                            class="cmn--btn">{{ Auth::user()->name }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="cmn--btn">Logout</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>


    <!-- Main Content -->
    @yield('content')

    <!-- Footer Section -->
    <footer class="footer-section-2 gradient-2 position-relative overflow-hidden">
        <div class="circle-1"></div>
        <div class="circle-2"></div>
        <div class="container">
            <div class="pt-120 pb-120">
                <div class="row gy-5">
                    <div class="col-lg-3 col-sm-6">
                        <div class="text-start footer-about">
                            <div class="footer-logo mb-25 ms-0">
                                <a href="#">
                                    <img src="assets/images/logo/FundGrow-logo.png" alt="footer">
                                </a>
                            </div>

                            <ul class="social-icons-2 mt-3">
                                <li>
                                    <a href=" https://www.facebook.com/share/1FQpXZA1nf/?mibextid=wwXIfr">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://x.com/FundGrowOnline">
                                        <i class="fa-brands fa-x-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="https://www.instagram.com/fundgrowonline?igsh=MTF2YzBndWp3bGluYg%3D%3D&utm_source=qr">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.tiktok.com/@fundgrowonline?is_from_webapp=1&sender_device=pc">
                                        <i class="fab fa-tiktok"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href=" https://www.youtube.com/channel/UCEVvRqXZksgc0cOSwNj1yJg">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">

                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="text-start footer-widget social-border-color-1">
                            <h6 class="footer__title text-white">Our Resorces </h6>
                            <ul class="footer__links">
                                <li>
                                    <a href="#">Home</a>
                                </li>
                                <li>
                                    <a href="#">
                                        About Us</a>
                                </li>
                                <li>
                                    <a href="#">
                                        Services</a>
                                </li>
                                <li>
                                    <a href="#">
                                        Contact Us</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="text-start footer-widget text-light">
                            <h6 class="footer__title text-white">Subscribe Newsletter</h6>
                            <p>
                                By subscribing to our mailing list you will always be updated
                            </p>
                            <form class="subscribe-form-2 mt-3">
                                <div class="form-group">
                                    <input type="text" class="form-control form--control" name="email"
                                        placeholder="Enter Your Email">
                                </div>
                                <button class="cmn--btn1 " type="submit">Subscribe</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom text-white">
            <div class="container">
                <div class="row g-2">
                    <div class="col-lg-6">
                        <div class="copyright text-center text-lg-start">
                            Copyright &copy; 2025 <a href="javascript:void(0)"
                                class="text-white fw--semibold text-decoration-none">Fund Grow Online</a> all right
                            resurved.
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section -->


    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.pkgd.min.js"></script>
    <script src="assets/js/magnific-popup.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/viewport.jquery.js"></script>
    <script src="assets/js/odometer.min.js"></script>
    <script src="assets/js/nice-select.js"></script>
    <script src="assets/js/owl.min.js"></script>
    <script src="assets/js/countdown.min.js"></script>
    <script src="assets/js/main.js"></script>
    <!-- Bootstrap 5 Bundle (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        // Toggle the collapse behavior manually
        const toggler = document.querySelector('.navbar-toggler');
        const collapseMenu = document.querySelector('#navbarMenu');

        // Toggle menu on hamburger click
        toggler.addEventListener('click', function() {
            // Toggle the "show" class, which controls the visibility of the navbar content
            collapseMenu.classList.toggle('show');

            // Update aria-expanded attribute
            const isExpanded = collapseMenu.classList.contains('show');
            toggler.setAttribute('aria-expanded', isExpanded);
        });

        // Close the menu when clicking outside of the navbar
        document.addEventListener('click', function(event) {
            if (!collapseMenu.contains(event.target) && !toggler.contains(event.target)) {
                collapseMenu.classList.remove('show');
                toggler.setAttribute('aria-expanded', 'false');
            }
        });


        // Floating navbar hide on scroll down, show on scroll up
        document.addEventListener("DOMContentLoaded", function() {
            const navbar = document.getElementById("floatingNav");
            let lastScrollY = window.scrollY;

            window.addEventListener("scroll", () => {
                if (window.scrollY > lastScrollY) {
                    navbar.classList.add("hidden-nav");
                } else {
                    navbar.classList.remove("hidden-nav");
                }
                lastScrollY = window.scrollY;
            });
        });
    </script>
</body>

</html>
