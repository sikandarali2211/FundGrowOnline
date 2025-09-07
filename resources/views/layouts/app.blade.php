<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Kaniz - Bitcoin & Cryptocurrency ICO Landing Page HTML5 Template</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.min.css">
    <link rel="stylesheet" href="assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/owl.min.css">
    <link rel="stylesheet" href="assets/css/odometer.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <link rel="stylesheet" href="assets/css/main.css">

    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
</head>

<body>


    <!-- ==========Preloader Starts Here========== -->

    <div class="overlayer" id="page-loader">
        <div class="candles-loader">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <h4 class="loader-text">FundGrow-Online</h4>
    </div>



    <div class="toTopBtn"><i class="fas fa-angle-up"></i></div>
    <div class="overlay"></div>
    <!-- ==========Preloader Ends Here========== -->


    <!-- Header Section -->
    <header class="header-section">
        <div class="container">
            <div class="header-area">
                <div class="logo me-auto">
                    <a href="/"><img src="assets/images/logo/FundGrow-logo.png" alt="logo"></a>
                </div>
                <ul class="menu">
                    <li>
                        <a href="/">home</a>
                    </li>
                    <li>
                        <a href="/">About</a>
                    </li>
                    <li>
                        <a href="/">Services</a>
                    </li>
                    <li>
                        <a href="/">Contact</a>
                    </li>
                    <li>
                        <div class="header-buttons d-flex flex-wrap justify-content-center d-md-none">
                            <a href="{{ route('register') }}" class="cmn--btn btn--white m-3 btn-pill">sign up</a>
                        </div>
                    </li>
                </ul>
                @guest
                    <div class="header-buttons d-none d-md-flex">
                        <a href="{{ route('login') }}" class="cmn--btn btn--white btn-pill">sign in</a>
                        <a href="{{ route('register') }}" class="cmn--btn btn--white btn-pill">sign up</a>
                    </div>
                @else
                    <div class="header-buttons d-none d-md-flex">
                        <a href="{{ Auth::user()->utype === 'ADM' ? route('admin.index') : route('user.index') }}"
                            class="cmn--btn btn--white btn-pill"><span>{{ Auth::user()->name }}</span></a>
                        {{-- Logout Form --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="cmn--btn btn--white btn-pill"
                                onclick="return confirm('Are you sure you want to logout?')">
                                Logout
                            </button>
                        </form>
                    </div>
                @endguest

                <div class="header-bar d-lg-none ms-3">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>
    <!-- Header Section -->

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
                                <a href="index-10.html#0">
                                    <img src="assets/images/logo/FundGrow-logo.png" alt="footer">
                                </a>
                            </div>
                            <ul class="footer__links">
                                <li>
                                    <a href="Mailto:info@yourdomain.com">info@yourdomain.com</a>
                                </li>
                                <li>
                                    <h5 class="m-0 fw--semibold">
                                        <a href="Tel:+0015481592491" class="text-white">+001 548 159 2491</a>
                                    </h5>
                                </li>
                            </ul>
                            <ul class="social-icons-2 mt-3">
                                <li>
                                    <a href="index-10.html#0">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="index-10.html#0">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="index-10.html#0">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="index-10.html#0">
                                        <i class="fab fa-pinterest"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="text-start footer-widget social-border-color-1">
                            <h6 class="footer__title text-white">About Company</h6>
                            <ul class="footer__links">
                                <li>
                                    <a href="index-10.html#0">Web Design</a>
                                </li>
                                <li>
                                    <a href="index-10.html#0">Development</a>
                                </li>
                                <li>
                                    <a href="index-10.html#0">Graphic Design</a>
                                </li>
                                <li>
                                    <a href="index-10.html#0">Branding</a>
                                </li>
                                <li>
                                    <a href="index-10.html#0">Creative Solution</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="text-start footer-widget social-border-color-1">
                            <h6 class="footer__title text-white">Our Resorces </h6>
                            <ul class="footer__links">
                                <li>
                                    <a href="index-10.html#0">Documentation</a>
                                </li>
                                <li>
                                    <a href="index-10.html#0">
                                        IOS & Android Apps</a>
                                </li>
                                <li>
                                    <a href="index-10.html#0">
                                        Privacy Policy</a>
                                </li>
                                <li>
                                    <a href="index-10.html#0">
                                        Support Forum</a>
                                </li>
                                <li>
                                    <a href="index-10.html#0">
                                        Terms Conditions</a>
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
                            Copyright &copy; 2024 <a href="javascript:void(0)"
                                class="text-white fw--semibold text-decoration-none">Kaniz</a> all right resurved.
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <ul class="quick-links justify-content-center justify-content-lg-end">
                            <li>
                                <a href="index-10.html#0">Home</a>
                            </li>
                            <li>
                                <a href="index-10.html#0">Buy or Sell</a>
                            </li>
                            <li>
                                <a href="index-10.html#0">Privacy Policy</a>
                            </li>
                            <li>
                                <a href="index-10.html#0">Faq</a>
                            </li>
                            <li>
                                <a href="index-10.html#0">Contact</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section -->


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
    <script>
        // Hide loader after 3 seconds
        window.addEventListener("load", () => {
            setTimeout(() => {
                document.getElementById("page-loader").style.opacity = "0";
                document.getElementById("page-loader").style.pointerEvents = "none";
            }, 3000); // 3 sec
        });
    </script>

</body>

</html>
