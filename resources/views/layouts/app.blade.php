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

        @media (min-width: 992px) {
            .floating-nav {
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(7, 45, 66, 0.85);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(59, 209, 122, 0.3);
                border-radius: 50px;
                height: 70px;
                /* 👈 reduced from 95px */
                width: 95%;
                max-width: 900px;
                padding: 0 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
                z-index: 1000;
                transition: transform 0.3s ease, opacity 0.3s ease;
            }

            .floating-nav.hidden-nav {
                transform: translateX(-50%) translateY(-120%);
                opacity: 0;
            }

            .navbar-brand img {
                height: 50px;
                /* 👈 scaled down to match new nav height */
                display: block;
            }

            .menu {
                display: flex;
                align-items: center;
                gap: 25px;
                margin: 0 !important;
                padding: 0;
                list-style: none;
                height: 100%;
            }

            .menu .nav-item {
                display: flex;
                align-items: center;
                height: 100%;
            }

            .menu .nav-link {
                font-size: 15px;
                font-weight: 600;
                color: #3bd17a !important;
                padding: 0;
                line-height: 1;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .menu .nav-link:hover {
                color: #fff !important;
            }

            .cmn--btn {
                background: #3bd17a;
                color: #072d42;
                border: none;
                padding: 6px 16px;
                /* 👈 slightly smaller button to match nav height */
                font-size: 14px;
                border-radius: 25px;
                font-weight: 600;
                transition: all 0.3s;
                text-decoration: none;
            }
        }


        /* Mobile Navbar */
        @media (max-width: 991px) {
            .navbar {
                background: rgba(7, 45, 66, 0.6);
                /* Transparent background with slight opacity */
                backdrop-filter: blur(10px);
                /* Blurring the background */
                position: fixed;
                /* Keeps the navbar fixed at the top */
                width: 100%;
                /* Ensure navbar spans the entire width */
                z-index: 999;
                /* Keeps navbar above other content */
                top: 0;
                /* Aligns navbar to the top */
                left: 0;
                /* Aligns navbar to the left */
                padding: 2px 10px;
                /* Optional padding */
            }

            .navbar .nav-link {
                color: #fff !important;
                font-weight: 500;
                text-align: center;
            }

            .navbar .nav-link:hover {
                color: #3bd17a !important;
            }

            .cmn--btn {
                background: #3bd17a;
                color: #072d42;
                border-radius: 25px;
                margin: 5px auto;
                padding: 6px 16px;
                display: block;
                width: 80%;
                text-align: center;
            }
        }

        /* Ensure the collapse class works */
        .collapse {
            display: none !important;
        }

        /* When the "show" class is added, the navbar content should be visible */
        .collapse.show {
            display: block !important;
        }
    </style>
</head>

<body>


    <!-- Desktop Floating Navbar -->
    <header id="floatingNav" class="floating-nav d-none d-lg-block">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid justify-content-between">
                <a class="navbar-brand text-white" href="/">
                    <img src="{{ asset('assets/images/favicon.png') }}" alt="logo" height="50">
                </a>
                <ul class="navbar-nav menu mx-auto">
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="/services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                </ul>
                <div class="d-flex align-items-center" style="gap: 0.75rem;">
                    @guest
                        <a href="{{ route('login') }}" class="cmn--btn">Sign In</a>
                        <a href="{{ route('register') }}" class="cmn--btn">Sign Up</a>
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
                                <button class="cmn--btn " type="submit">Subscribe</button>
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
