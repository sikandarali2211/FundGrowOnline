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

    <style>
        html,
        body {
            overflow-x: hidden;
            /* Prevent horizontal scroll */
        }

        /* Loader Fullscreen */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #072d42;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.6s ease;
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
                height: 80px;
                width: 95%;
                max-width: 900px;
                padding: 0 30px;
                display: flex;
                align-items: center;
                /* 👈 all items vertically center */
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
                height: 60px;
                /* 👈 logo resize to fit height */
                display: block;
            }

            .menu {
                display: flex;
                align-items: center;
                gap: 25px;
                margin: 0 !important;
                padding: 0;
                list-style: none;
            }

            .menu .nav-link {
                font-size: 15px;
                font-weight: 600;
                color: #3bd17a !important;
                transition: color 0.2s;
                line-height: normal;
                /* 👈 remove extra baseline */
                padding: 0;
            }

            .menu .nav-link:hover {
                color: #fff !important;
            }

            .cmn--btn {
                background: #3bd17a;
                color: #072d42;
                border: none;
                padding: 8px 18px;
                font-size: 15px;
                border-radius: 25px;
                font-weight: 600;
                transition: all 0.3s;
                text-decoration: none;
                
            }
        }


        /* Mobile Navbar */
        @media (max-width: 991px) {
            .navbar {
                background: #072d42 !important;
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
    </style>
</head>

<body>
    <!-- Loader -->
    <div id="page-loader">
        <div class="candles-loader">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <h4 class="loader-text text-white">FundGrow-Online</h4>
    </div>

    <!-- Desktop Floating Navbar -->
    <header id="floatingNav" class="floating-nav d-none d-lg-block">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid justify-content-between">
                <a class="navbar-brand text-white" href="/">
                    <img src="{{ asset('assets/images/favicon.png') }}" alt="logo" height="70">
                </a>
                <ul class="navbar-nav menu mx-auto">
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="/services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                </ul>
                <div>
                    @guest
                        <a href="{{ route('login') }}" class="cmn--btn me-2">Sign In</a>
                        <a href="{{ route('register') }}" class="cmn--btn">Sign Up</a>
                    @else
                        <a href="{{ Auth::user()->utype === 'ADM' ? route('admin.index') : route('user.index') }}"
                            class="cmn--btn me-2">{{ Auth::user()->name }}</a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
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
                <img src="{{ asset('assets/images/favicon.png') }}" alt="logo" height="40">
            </a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
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
                            <ul class="footer__links">
                                <!-- <li>
                                    <a href="Mailto:info@yourdomain.com">info@yourdomain.com</a>
                                </li>
                                <li>
                                    <h5 class="m-0 fw--semibold">
                                        <a href="Tel:+0015481592491" class="text-white">+001 548 159 2491</a>
                                    </h5>
                                </li> -->
                            </ul>
                            <ul class="social-icons-2 mt-3">
                                <li>
                                    <a href="#">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fab fa-pinterest"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <!-- <div class="text-start footer-widget social-border-color-1">
                            <h6 class="footer__title text-white">About Company</h6>
                            <ul class="footer__links">
                                <li>
                                    <a href="#">Web Design</a>
                                </li>
                                <li>
                                    <a href="#">Development</a>
                                </li>
                                <li>
                                    <a href="#">Graphic Design</a>
                                </li>
                                <li>
                                    <a href="#">Branding</a>
                                </li>
                                <li>
                                    <a href="#">Creative Solution</a>
                                </li>
                            </ul>
                        </div> -->
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
                                <!-- <li>
                                    <a href="#">
                                        Terms Conditions</a>
                                </li> -->
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
                                <button class="cmn--btn btn--transparent" type="submit">Subscribe</button>
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
                                class="text-white fw--semibold text-decoration-none">Fund Grow Online</a> all right resurved.
                        </div>
                    </div>
                    <!-- <div class="col-lg-6">
                        <ul class="quick-links justify-content-center justify-content-lg-end">
                            <li>
                                <a href="#">Home</a>
                            </li>
                            <li>
                                <a href="#">Buy or Sell</a>
                            </li>
                            <li>
                                <a href="#">Privacy Policy</a>
                            </li>
                            <li>
                                <a href="#">Faq</a>
                            </li>
                            <li>
                                <a href="#">Contact</a>
                            </li>
                        </ul>
                    </div> -->
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section -->


    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Loader hide after 3s
        window.addEventListener("load", () => {
            setTimeout(() => {
                const loader = document.getElementById("page-loader");
                loader.style.opacity = "0";
                loader.style.pointerEvents = "none";
                setTimeout(() => loader.style.display = "none", 600);
            }, 3000);
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
