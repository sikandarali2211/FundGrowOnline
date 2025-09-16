@extends('layouts.app')
@section('content')
<style>
    /* Glowing 3D Separator Line */
    .glow-separator {
        width: 1000px;
        height: 1px;
        margin: 40px auto;
        border-radius: 3px;
        background: linear-gradient(90deg, #3bd17a, #0ff, #3bd17a);
        box-shadow: 0 0 15px #3bd17a, 0 0 25px #3bd17a, 0 0 35px #0ff;
        animation: glowPulse 2s infinite alternate;
    }

    /* Glow animation */
    @keyframes glowPulse {
        0% {
            box-shadow: 0 0 4px #3bd17a, 0 0 6px #3bd17a, 0 0 10px #0ff;
        }

        40% {
            box-shadow: 0 0 6px #3bd17a, 0 0 8px #3bd17a, 0 0 12px #0ff;
        }
    }

    .services-section {
        font-family: 'Inter', sans-serif;
    }

    .service-card {
        background: rgba(59, 209, 122, 0.05);
        border: 1px solid rgba(59, 209, 122, 0.3);
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        transition: all 0.4s ease;
        backdrop-filter: blur(10px);
    }

    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 25px rgba(59, 209, 122, 0.4);
        border-color: #3bd17a;
    }

    .service-icon {
        font-size: 40px;
        color: #3bd17a;
        margin-bottom: 20px;
    }

    .service-title {
        color: #111;
        font-size: 1.25rem;
        margin-bottom: 15px;
    }

    .service-card p {
        color: #000000;
        font-size: 0.95rem;
    }

    @media (max-width: 767px) {
        .services-section {
            padding-top: 60px;
            padding-bottom: 60px;
        }
    }


    .cta-section {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .how-box {
        padding: 20px;
        text-align: center;
    }

    .icon-wrap {
        font-size: 48px;
        margin-bottom: 15px;
    }

    .glow-icon i {
        color: #12cc85;
        transition: transform 0.3s ease, text-shadow 0.3s ease;
    }

    .glow-icon i:hover {
        transform: scale(1.2);
        text-shadow: 0 0 15px rgba(7, 45, 66, 0.9),
            0 0 30px rgba(7, 45, 66, 0.7),
            0 0 45px rgba(7, 45, 66, 0.5);
    }

    .how-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #fff;
    }

    .how-text {
        font-size: 14px;
        color: #413e3e;
    }

    .section-subtitle {
        color: #12cc85;
        font-weight: 600;
    }

    .how-footer {
        font-size: 15px;
        font-weight: 600;
        color: #12cc85;
    }

    .custom-list-icons {
        list-style: none;
        padding-left: 0;
    }

    .custom-list-icons li {
        position: relative;
        padding-left: 28px;
        margin-bottom: 10px;
    }

    .custom-list-icons li::before {
        content: "✔";
        /* aap icon ya emoji change kar sakte ho */
        position: absolute;
        left: 0;
        color: #FDBB2D;
        /* golden color */
        font-weight: bold;
    }

    .img-fluid {
        height: 700px;
    }

    .hero-img {
        max-width: 720px;
        width: 100%;
        height: auto;
    }

    @media (max-width: 1326px) {
        .hero-img {
            margin-right: -170px;
        }
    }

    .hero-title {
        font-size: 42px;
        font-weight: 700;
        line-height: 1.3;
    }

    .hero-txt {
        font-size: 18px;
        margin: 20px 0;
    }

    .sponsor-thumb {
        padding: 15px;
    }

    .sponsor-logo {
        max-width: 180px;
        /* fix logo size */
        height: auto;
        /* keep aspect ratio */
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .sponsor-logo:hover {
        transform: scale(1.1);
        /* hover zoom effect */
    }

    .section-title {
        font-size: 22px;
        font-weight: 600;
        color: #12cc85;
        /* green accent */
    }

    .about-section {
        color: #030303;
        border-radius: 20px;
        padding: 80px 20px;
    }

    .about-section .section-title {
        font-size: 28px;
        font-weight: 700;
        color: #3bd17a;
    }

    .about-section .section-text {
        font-size: 16px;
        color: #000000;
        line-height: 1.8;
    }

    .about-section .custom-list-icons li {
        margin-bottom: 10px;
        position: relative;
        padding-left: 25px;
    }

    .about-section .custom-list-icons li::before {
        content: "✔";
        color: #22c55e;
        font-weight: bold;
        position: absolute;
        left: 0;
    }

    /* Modern About Company Section Styles */
    .about-company-section {
        background: linear-gradient(90deg, rgba(18, 45, 67, 1) 28%, rgba(34, 197, 94, 1) 100%);
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }

    .about-company-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23e2e8f0" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .about-tag {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #65D17A, #65D17A);
        color: white;
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);
    }

    .about-tag .arrow {
        margin-left: 8px;
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .about-tag:hover .arrow {
        transform: translateX(3px);
    }

    .about-main-heading {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 25px;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .about-description {
        font-size: 1.2rem;
        color: #4a5568;
        line-height: 1.7;
        margin-bottom: 40px;
        max-width: 600px;
    }

    .about-feature-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }

    .about-feature-card {
        background: white;
        padding: 35px 25px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .about-feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    }

    .about-feature-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        font-size: 24px;
        color: white;
    }

    .about-feature-icon.growth {
        background: linear-gradient(135deg, #122D43, #122D43);
    }

    .about-feature-icon.revenue {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .about-feature-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 15px;
    }

    .about-feature-text {
        color: #6b7280;
        line-height: 1.6;
        font-size: 1rem;
    }

    .about-learn-more-btn {
        background: #374151;
        color: white;
        padding: 15px 35px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(55, 65, 81, 0.3);
    }

    .about-learn-more-btn:hover {
        background: #1f2937;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(55, 65, 81, 0.4);
        color: white;
        text-decoration: none;
    }

    .about-learn-more-btn .arrow {
        margin-left: 8px;
        font-size: 14px;
        transition: transform 0.3s ease;
    }

    .about-learn-more-btn:hover .arrow {
        transform: translateX(3px);
    }

    .about-team-image {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .about-team-image img {
        width: 100%;
        height: 500px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .about-team-image:hover img {
        transform: scale(1.05);
    }

    .play-button {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #374151;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .play-button:hover {
        background: white;
        transform: translate(-50%, -50%) scale(1.1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 768px) {
        .about-main-heading {
            font-size: 2.5rem;
        }
 
        .about-description {
            font-size: 1.1rem;
        }
 
        .about-feature-cards {
            grid-template-columns: 1fr;
            gap: 20px;
        }
 
        .about-team-image img {
            height: 350px;
        }
 
        .play-button {
            width: 60px;
            height: 60px;
            font-size: 20px;
        }
    }

    /* Contact Section Styles */
    .contact-section {
        background: #f8fafc;
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }

    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        min-height: 600px;
    }

    .contact-left {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        padding: 80px 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position: relative;
        min-height: 600px;
    }

    .contact-illustration {
        position: relative;
        width: 100%;
        height: 400px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .mailbox {
        position: relative;
        width: 120px;
        height: 120px;
        background: #fbbf24;
        border-radius: 15px;
        z-index: 3;
        box-shadow: 0 10px 25px rgba(251, 191, 36, 0.3);
    }

    .mailbox::before {
        content: '';
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 8px;
        height: 40px;
        background: #1e40af;
        border-radius: 4px;
    }

    .mailbox::after {
        content: '';
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 20px;
        background: #1e40af;
        border-radius: 0 0 15px 15px;
    }

    .envelope {
        position: absolute;
        top: 20px;
        left: 20px;
        width: 80px;
        height: 50px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .envelope::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 48%, #e5e7eb 49%, #e5e7eb 51%, transparent 52%);
    }

    .paper-airplane {
        position: absolute;
        width: 30px;
        height: 20px;
        background: #1e40af;
        border-radius: 0 0 15px 15px;
        transform: rotate(-45deg);
        animation: fly 3s ease-in-out infinite;
    }

    .paper-airplane.left {
        top: 50px;
        left: 50px;
        animation-delay: 0s;
    }

    .paper-airplane.right {
        top: 80px;
        right: 50px;
        animation-delay: 1.5s;
    }

    .paper-airplane::before {
        content: '';
        position: absolute;
        top: -5px;
        left: 10px;
        width: 20px;
        height: 2px;
        background: #1e40af;
        border-radius: 1px;
        opacity: 0.6;
    }

    .person {
        position: absolute;
        width: 40px;
        height: 60px;
        background: #1e40af;
        border-radius: 20px 20px 10px 10px;
    }

    .person.left {
        bottom: 50px;
        left: 30px;
    }

    .person.center {
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        background: #fbbf24;
    }

    .person.right {
        bottom: 50px;
        right: 30px;
    }

    .person::before {
        content: '';
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 20px;
        height: 20px;
        background: #1e40af;
        border-radius: 50%;
    }

    .person.center::before {
        background: #1e40af;
    }

    .bushes {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 30px;
        background: #1e40af;
        border-radius: 15px 15px 0 0;
        opacity: 0.3;
    }

    @keyframes fly {
        0%, 100% { transform: rotate(-45deg) translateY(0px); }
        50% { transform: rotate(-45deg) translateY(-20px); }
    }


    .contact-right {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%), url('{{asset("assets/images/contact/image2.jpg")}}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        backdrop-filter: blur(20px);
        padding: 80px 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 600px;
        position: relative;
        border-left: 1px solid rgba(255, 255, 255, 0.2);
    }

    .contact-right::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(16, 185, 129, 0.2);
        z-index: 1;
    }

    .contact-content {
        position: relative;
        z-index: 2;
    }

    .contact-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 15px;
        letter-spacing: -0.01em;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .contact-subtitle {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 40px;
        line-height: 1.6;
        max-width: 450px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .contact-form {
        max-width: 500px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 8px;
        text-transform: none;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .form-input {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 8px;
        font-size: 1rem;
        color: #374151;
        background: rgba(255, 255, 255, 0.9);
        transition: all 0.3s ease;
        outline: none;
        font-family: inherit;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .form-input:focus {
        border-color: #ffffff;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
    }

    .form-input::placeholder {
        color: #6b7280;
        font-style: normal;
    }

    .form-textarea {
        min-height: 120px;
        resize: vertical;
        font-family: inherit;
    }

    .form-submit-btn {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 15px 40px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: none;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .form-submit-btn:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
    }

    .form-submit-btn:active {
        transform: translateY(0px);
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
    }


    @media (max-width: 992px) {
        .contact-container {
            margin: 0 20px;
        }
    }

    @media (max-width: 768px) {
        .contact-section {
            padding: 60px 0;
        }
        
        .contact-left,
        .contact-right {
            padding: 50px 30px;
            min-height: 500px;
        }
        
        .contact-title {
            font-size: 2.2rem;
        }
        
        .contact-subtitle {
            font-size: 1.1rem;
            margin-bottom: 40px;
        }
        
        .telephone-icon {
            width: 100px;
            height: 100px;
        }
        
        .form-input {
            padding: 15px 20px;
        }
        
        .form-submit-btn {
            padding: 15px 35px;
        }
    }

    @media (max-width: 576px) {
        .contact-container {
            margin: 0 10px;
            border-radius: 15px;
        }
        
        .contact-left,
        .contact-right {
            padding: 40px 20px;
        }
        
        .contact-title {
            font-size: 1.8rem;
        }
        
        .telephone-icon {
            width: 80px;
            height: 80px;
        }
    }
</style>

<!-- Hero Section -->

<section class="hero-section-10 gradient-2 overflow-hidden">
    <div class="bottom-shape d-none d-lg-block">
        <img src="assets/images/hero/hero-shape-10.png" alt="hero">
    </div>
    <div class="container">
        <div class="row align-items-center">

            <!-- Left Side Text -->
            <div class="col-lg-6 col-md-12 hero-content cl-white wow fadeInUp">
                <h1 class="hero-title">
                    <span class="d-md-block">Fund Grow Online </span>
                    The Smarter Way to Fund, Grow & Succeed
                </h1>
                <p class="hero-txt">
                    Join a global crowdfunding ecosystem designed for financial freedom, transparency, and growth.
                </p>
                <div class="hero-button-group">
                    <a href="{{route('register')}}" class="cmn--btn btn--white">Get Started</a>
                    <a href="{{asset('assets/pdf.pdf')}}" class="cmn--btn btn--white  ">Read Document</a>
                </div>
            </div>

            <!-- Right Side Image -->
            <div class="col-lg-6 col-md-12 text-center wow fadeInRight">
                <img src="assets/images/hero/hero10.png" alt="hero" class="hero-img img-fluid">
            </div>

        </div>
    </div>
</section>
<!-- Hero Section -->





<!-- Feature Section -->
<section class="feature-section pt-120 pb-60">
    <div class="container">
        <div class="feat-wrapper">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6 wow fadeInUp">
                    <div class="feat-item">
                        <div class="feat-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div class="feat-cont">
                            <h5 class="title">Easy Contribution</h5>
                            <p>
                                Start your journey with as little as $10 and join a transparent crowdfunding pool.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp">
                    <div class="feat-item active">
                        <div class="feat-icon">
                            <i class="las la-shield-alt"></i>
                        </div>
                        <div class="feat-cont">
                            <h5 class="title">Advanced Security</h5>
                            <p>
                                All transactions powered by BEP20 blockchain & smart contracts for speed, security &
                                trust.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp">
                    <div class="feat-item">
                        <div class="feat-icon">
                            <i class="las la-globe-africa"></i>
                        </div>
                        <div class="feat-cont">
                            <h5 class="title">Global Community</h5>
                            <p>
                                Be part of a worldwide ecosystem where your growth multiplies through community power.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Feature Section -->


<!-- Glowing Separator Line -->
<div class="glow-separator"></div>

<!-- About Section -->
<!-- <section class="about-section pt-60 pb-120">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10">
                <div class="about--content">

                    <div class="section-header mb-5">
                        <h2 class="section-title mb-3">Our Vision</h2>
                        <p class="section-text quote__txt">
                            Build a worldwide community-driven crowdfunding system for financial success.
                        </p>
                    </div>

                    <div class="section-header mb-5">
                        <h2 class="section-title mb-3">Our Mission</h2>
                        <p class="section-text quote__txt">
                            Empower people using technology, transparency, and collaboration.
                        </p>
                    </div>

                    <div class="section-header">
                        <h2 class="section-title mb-3">Why Choose Us</h2>
                        <ul class="section-text quote__txt custom-list-icons text-start d-inline-block">
                            <li>100% Transparent system</li>
                            <li>Blockchain-powered security</li>
                            <li>Fast & Easy withdrawals</li>
                            <li>Pools from $10 to $100,000</li>
                            <li>Global community growth</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section> -->
<!-- About Section -->

<!-- Modern About Company Section -->
<section class="about-company-section">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Side Content -->
            <div class="col-lg-6 col-md-12">
                <div class="about-content">
                    <!-- About Company Tag -->
                    <div class="about-tag">
                        About Company
                        <span class="arrow">→</span>
                    </div>

                    <!-- Main Heading -->
                    <h4 class="about-main-heading" style="color:white; ">
                        INTRODUCING FUND GROW ONLINE
                    </h4>

                    <!-- Description -->
                    <p class="about-description" style="color:white; ">
                        Fund Grow Online is a next-generation crowdfunding platform designed for
                        networkers and investors.
                    </p>

                    <!-- Feature Cards -->
                    <div class="about-feature-cards">
                        <!-- Growth Card -->
                        <div class="about-feature-card">
                            <div class="about-feature-icon growth">
                                <i class="fas fa-thumbs-up"></i>
                            </div>
                            <h4 class="about-feature-title">Our Vision</h4>
                            <p class="about-feature-text">
                                To build a global communitydriven crowdfunding ecosystem
                                where individuals and investors
                                can unlock opportunities for
                                financial growth, freedom, and
                                long-term success
                            </p>
                        </div>

                        <!-- Revenue Card -->
                        <div class="about-feature-card">
                            <div class="about-feature-icon revenue">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <h4 class="about-feature-title">Our Mission</h4>
                            <p class="about-feature-text">
                                At Fund Grow Online, our mission is to
                                empower people worldwide by
                                combining technology, transparency, and
                                collaboration. We provide a trusted
                                platform where everyone can fund, grow,
                                and succeed online — together.
                            </p>
                        </div>
                    </div>

                    <!-- Learn More Button -->
                    <!-- <a href="#services" class="about-learn-more-btn">
                        Learn More
                        <span class="arrow">→</span>
                    </a> -->
                </div>
            </div>

            <!-- Right Side Team Image -->
            <div class="col-lg-6 col-md-12">
                <div class="about-team-image">
                    <img src="{{asset('assets/images/about/about12.jpg')}}" alt="Team Collaboration" class="img-fluid">
                    <!-- <div class="play-button">
                        <i class="fas fa-play"></i>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Modern About Company Section -->



<!-- Glowing Separator Line -->
<div class="glow-separator"></div>



<!-- How It Works Section -->
<section class="how-it-works-section pt-120 pb-120" id="howitworks">
    <div class="container">
        <div class="section-header section-header-center wow fadeInUp">
            <h2 class="section-title fw--medium">HOW IT WORKS</h2>
            <p class="section-subtitle">YOUR PARTNER IN GROWTH</p>
        </div>

        <div class="row text-center justify-content-center mt-5">

            <!-- Step 1 -->
            <div class="col-md-2 col-6 mb-4 wow fadeInUp">
                <div class="how-box">
                    <div class="icon-wrap glow-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h5 class="how-title">JOIN</h5>
                    <p class="how-text">
                        Sign up and choose your starting pool (from just $10).
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col-md-2 col-6 mb-4 wow fadeInUp">
                <div class="how-box">
                    <div class="icon-wrap glow-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h5 class="how-title">FUND</h5>
                    <p class="how-text">
                        Contribute to the community-driven crowdfunding pool.
                    </p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col-md-2 col-6 mb-4 wow fadeInUp">
                <div class="how-box">
                    <div class="icon-wrap glow-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="how-title">GROW</h5>
                    <p class="how-text">
                        Your funds multiply with our 3.6x income plan.
                    </p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="col-md-2 col-6 mb-4 wow fadeInUp">
                <div class="how-box">
                    <div class="icon-wrap glow-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <h5 class="how-title">REFER</h5>
                    <p class="how-text">
                        Invite others and earn attractive referral bonuses.
                    </p>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="col-md-2 col-6 mb-4 wow fadeInUp">
                <div class="how-box">
                    <div class="icon-wrap glow-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h5 class="how-title">EARN</h5>
                    <p class="how-text">
                        Withdraw your income quickly and enjoy financial freedom.
                    </p>
                </div>
            </div>

        </div>

        <div class="text-center mt-4">
            <p class="how-footer">A SIMPLE, SCALABLE, AND TRANSPARENT JOURNEY TO SUCCESS.</p>
        </div>
    </div>
</section>
<!-- How It Works Section -->

<!-- Glowing Separator Line -->
<div class="glow-separator"></div>

<!-- Services Section -->
<section class="services-section pt-80 pb-80">
    <div class="container">

        <!-- Section Header -->
        <div class="text-center mb-5">
            <h2 class="section-title" style="color: #3bd17a; font-family: 'Montserrat', sans-serif;">Our Services</h2>
            <p style="color: #ccc; font-size: 1rem; max-width: 600px; margin: 0 auto;">
                Unlock financial growth with our Crowdfunding Plans designed for all levels.
            </p>
        </div>

        <!-- Service Cards -->
        <div class="row g-4">

            <!-- Card 1 -->
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h4 class="service-title">Flexible Plans</h4>
                    <p>Pools from <strong>$10 to $100,000</strong> with <strong>3.6x growth potential</strong>.</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4 class="service-title">Dual-phase Income</h4>
                    <p>Enjoy instant earnings and long-term multiplication with our dual-phase income system.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <h4 class="service-title">Referral Rewards</h4>
                    <p>Earn up to <strong>$56,836</strong> by referring others and growing your network.</p>
                </div>
            </div>



        </div>

    </div>
</section>

<!-- Services Section-->

<!-- CTA Section -->
<section class="cta-section" style="background-image: url('./assets/images/shapes-bg/cta.png'); height: 100vh;">
</section>

<!-- CTA Section -->

<!-- Faqs Section -->
<section class="faqs-section pt-120 pb-60" id="faq">
    <div class="container">
        <div class="section-header section-header-center wow fadeInUp">
            <h2 class="section-title fw--medium">FAQs</h2>
            <p>
                Find answers to the most common questions about Fund Grow Online.
            </p>
        </div>
        <div class="faq-wrapper-3 faq-wrapper faq--wrapper-dark wow fadeInUp">

            <div class="faq-item border active open">
                <div class="faq-title">
                    <h5 class="title">What is Fund Grow Online?</h5>
                    <span class="plus"></span>
                </div>
                <div class="faq-content">
                    <p>
                        Fund Grow Online is a global crowdfunding platform where members can contribute, grow, and earn
                        together. It combines blockchain security, smart contracts, and a transparent income system to
                        ensure fair growth opportunities for everyone.
                    </p>
                </div>
            </div>

            <div class="faq-item border">
                <div class="faq-title">
                    <h5 class="title">How much do I need to start?</h5>
                    <span class="plus"></span>
                </div>
                <div class="faq-content">
                    <p>
                        You can start with as little as <strong>$10</strong> by joining the Grower Pool. Higher pools
                        are also
                        available up to $100,000 for those aiming for bigger growth.
                    </p>
                </div>
            </div>

            <div class="faq-item border">
                <div class="faq-title">
                    <h5 class="title">How does the referral plan work?</h5>
                    <span class="plus"></span>
                </div>
                <div class="faq-content">
                    <p>
                        Our referral system rewards you every time you invite someone to join. You earn up to
                        <strong>30% referral bonus</strong> on contributions, plus unlimited earnings from the $10
                        direct pool.
                    </p>
                </div>
            </div>

            <div class="faq-item border">
                <div class="faq-title">
                    <h5 class="title">How can I withdraw my earnings?</h5>
                    <span class="plus"></span>
                </div>
                <div class="faq-content">
                    <p>
                        Withdrawals are quick and simple. 60% of your income goes directly to your cash wallet and
                        can be withdrawn anytime. The remaining 40% is reserved for sustainability and reinvestment.
                    </p>
                </div>
            </div>

            <div class="faq-item border">
                <div class="faq-title">
                    <h5 class="title">Is my investment secure?</h5>
                    <span class="plus"></span>
                </div>
                <div class="faq-content">
                    <p>
                        Yes. All transactions are powered by <strong>BEP20 blockchain</strong> and smart contracts.
                        This ensures transparency, automation, and fraud prevention at every step.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- Faqs Section -->


<!-- Glowing Separator Line -->
<div class="glow-separator"></div>

<!-- Sponsor Section -->
<div class="sponsor-section pt-60 pb-120">
    <div class="container">

        <!-- First Row -->
        <div class="section-header text-center mb-4">
            <h2 class="section-title">Our Technology Partners</h2>
        </div>
        <div class="row justify-content-center mb-5">
            <div class="col-md-4 col-6 text-center mb-3">
                <div class="sponsor-thumb">
                    <img src="assets/images/sponsor/chainLink.png" alt="sponsor" class="sponsor-logo">
                </div>
            </div>
            <div class="col-md-4 col-6 text-center mb-3">
                <div class="sponsor-thumb">
                    <img src="assets/images/sponsor/binance-Smart-Chain.png" alt="sponsor" class="sponsor-logo">
                </div>
            </div>
            <div class="col-md-4 col-6 text-center mb-3">
                <div class="sponsor-thumb">
                    <img src="assets/images/sponsor/polygon.png" alt="sponsor" class="sponsor-logo">
                </div>
            </div>
        </div>

        <!-- Second Row -->
        <div class="section-header text-center mb-4">
            <h2 class="section-title">Our Security Partners</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-4 col-6 text-center mb-3">
                <div class="sponsor-thumb">
                    <img src="assets/images/sponsor/metaMask.png" alt="sponsor" class="sponsor-logo">
                </div>
            </div>
            <div class="col-md-4 col-6 text-center mb-3">
                <div class="sponsor-thumb">
                    <img src="assets/images/sponsor/usdt.png" alt="sponsor" class="sponsor-logo">
                </div>
            </div>
            <div class="col-md-4 col-6 text-center mb-3">
                <div class="sponsor-thumb">
                    <img src="assets/images/sponsor/trustwallet.png" alt="sponsor" class="sponsor-logo">
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Sponsor Section -->

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-container">
            <div class="row g-0">
              

                <!-- Right Side - Contact Form -->
                <div class="col-lg-12 col-md-12">
                    <div class="contact-right">
                        <div class="contact-content">
                            <h2 class="contact-title">Get in touch</h2>
                            <p class="contact-subtitle">
                                Feel free to contact us and we will get back to you as soon as it possible
                            </p>

                            <form class="contact-form" action="#" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="full_name" class="form-label">Name</label>
                                    <input type="text" id="full_name" name="full_name" class="form-input" placeholder="Name" required>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" id="email" name="email" class="form-input" placeholder="E-mail" required>
                                </div>

                                <div class="form-group">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea id="message" name="message" class="form-input form-textarea" rows="5" placeholder="Message" required></textarea>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="form-submit-btn">
                                        Send
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Contact Section -->

@endsection