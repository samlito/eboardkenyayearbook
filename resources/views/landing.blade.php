<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TWWDA eBoard System</title>
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #C8102E;
            --accent-green: #7AC143;
            --dark-green: #4A7729;
            --button-green: #9CD273;
            --light-bg: #fafafa;
            --gradient-blue: linear-gradient(135deg, #C8102E, #BD212E);
            --gradient-green: linear-gradient(135deg, #7AC143, #9CD273);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: #333;
            overflow-x: hidden;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-blue);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #1b1b1b;
        }
        
        /* Navbar Styling */
        .navbar {
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
            padding: 12px 0;
            transition: all 0.3s ease;
        }
        
        .navbar-scrolled {
            padding: 8px 0;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-blue) !important;
        }
        
        .navbar-brand img {
            height: 50px;
            transition: all 0.3s ease;
        }
        
        .nav-link {
            position: relative;
            font-weight: 500;
            color: #333 !important;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem !important;
            margin: 0 0.2rem;
        }
        
        .nav-link:hover {
            color: var(--primary-blue) !important;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gradient-green);
            transition: width 0.3s ease;
            border-radius: 10px;
        }
        
        .nav-link:hover::after {
            width: 70%;
        }
        
        /* Button Styling */
        .btn-success {
            background: var(--gradient-green);
            border: none;
            color: #fff;
            transition: all 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(122, 193, 67, 0.3);
        }
        
        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(122, 193, 67, 0.4);
            color: #fff;
        }
        
        .btn-primary-custom {
            background: var(--gradient-blue);
            border: none;
            color: #fff;
            transition: all 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(200, 16, 46, 0.3);
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(200, 16, 46, 0.4);
            color: #fff;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(200, 16, 46, 0.85), rgba(200, 16, 46, 0.9)), url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1500&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 140px 0 100px;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        /* Features Section */
        .features-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            background: var(--gradient-blue);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            padding: 15px;
            border-radius: 50%;
            background-color: rgba(200, 16, 46, 0.1);
        }
        
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            height: 100%;
            background: #fff;
            overflow: hidden;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .feature-card:hover .features-icon {
            background: var(--gradient-green);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Stats Section */
        .stats-section {
            background: var(--gradient-blue);
            color: white;
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: var(--gradient-green);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Testimonials */
        .testimonial-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin: 15px;
            position: relative;
        }
        
        .testimonial-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--accent-green);
        }
        
        /* CTA Section */
        .cta-section {
            background: var(--gradient-blue);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }
        
        /* Footer */
        footer {
            background: #131313;
            color: #fff;
            padding: 60px 0 30px;
        }
        
        .footer-links h5 {
            font-weight: 600;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-links h5::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 3px;
            background: var(--accent-green);
            border-radius: 3px;
        }
        
        .footer-links ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.8rem;
        }
        
        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #fff;
            padding-left: 5px;
        }
        
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            background: var(--accent-green);
            transform: translateY(-3px);
        }
        
        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--gradient-green);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(122, 193, 67, 0.4);
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
            z-index: 1000;
        }
        
        .back-to-top.active {
            opacity: 1;
            visibility: visible;
            bottom: 30px;
        }
        
        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(122, 193, 67, 0.6);
        }
        
        /* Animations */
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
        }
        
        /* Carousel Controls */
        .carousel-control-prev, .carousel-control-next {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.8;
            transition: all 0.3s ease;
        }
        
        .carousel-control-prev:hover, .carousel-control-next:hover {
            opacity: 1;
            background: rgba(255, 255, 255, 0.3);
        }
        
        .carousel-control-prev {
            left: 20px;
        }
        
        .carousel-control-next {
            right: 20px;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero {
                padding: 100px 0 80px;
                background-attachment: scroll;
            }
            
            .display-4 {
                font-size: 2.2rem;
            }
            
            .stat-number {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Back to Top Button -->
    <a href="#" class="back-to-top">
        <i class="bi bi-arrow-up"></i>
    </a>

    <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top py-2" id="mainNav">
    <div class="container">
        <!-- Logo + Brand -->
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="https://tanawwda.go.ke/wp-content/uploads/2021/04/banner.png" alt="TWWDA Logo" height="50" class="me-2">
            <span class="d-none d-md-inline">eBoard System</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-3">
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="{{ url('/') }}">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="#">Schedule Meetings</a>
                </li>

                <li class="nav-item">
                    <a href="#" class="btn btn-success d-flex align-items-center gap-2 px-3 py-2 rounded-pill fw-semibold hover-lift">
                        <i class="bi bi-pencil-square"></i> Inquiry Form
                    </a>
                </li>

                @if (Route::has('login'))
                    @auth
                        <li class="nav-item">
                            <a href="{{ url('/dashboard') }}" class="nav-link fw-semibold">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ url('/login') }}"
                               class="btn btn-primary-custom d-flex align-items-center gap-2 px-3 py-2 rounded-pill fw-semibold hover-lift">
                                <i class="bi bi-box-arrow-in-right"></i>
                                <span>Log in to eBoard</span>
                            </a>
                        </li>
                    @endauth
                @endif
            </ul>
        </div>
    </div>
</nav>


   <!-- Hero Section -->
<section class="hero text-center" id="home">
    <div class="container hero-content">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <h1 class="display-3 fw-bold mb-4" data-aos="fade-down" data-aos-delay="100">
                        Digital Governance Made Seamless
                    </h1>
                    <p class="lead mb-4 fs-5" data-aos="fade-down" data-aos-delay="200">
                        Manage board meetings, papers, approvals, and documentation — all in one secure platform.
                    </p>
                    <a href="{{ url('/login') }}"
                       class="btn btn-success btn-lg px-5 py-3 rounded-pill fw-semibold hover-lift"
                       data-aos="fade-up" data-aos-delay="300">
                       Login to eBoard
                    </a>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-item">
                    <h1 class="display-3 fw-bold mb-4">
                        Efficient Meeting Coordination
                    </h1>
                    <p class="lead mb-4 fs-5">
                        Streamline how meetings are scheduled and managed with our advanced tools.
                    </p>
                    <a href="{{ url('/login') }}"
                       class="btn btn-success btn-lg px-5 py-3 rounded-pill fw-semibold hover-lift">
                       Explore Features
                    </a>
                </div>

                <!-- Slide 3 -->
                <div class="carousel-item">
                    <h1 class="display-3 fw-bold mb-4">
                        Centralized Document Access
                    </h1>
                    <p class="lead mb-4 fs-5">
                        All board documentation available from a single, secure location.
                    </p>
                    <a href="{{ url('/login') }}"
                       class="btn btn-success btn-lg px-5 py-3 rounded-pill fw-semibold hover-lift">
                       Access Documents
                    </a>
                </div>
            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</section>


<!-- Fonts & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Style -->
<style>
  body {
    font-family: 'Poppins', sans-serif;
  }

  #features {
    background: #fbfbfb;
    padding: 70px 0;
  }

  #features h2 {
    color: #232323;
    font-size: 2.6rem;
    font-weight: 700;
  }

  #features .lead {
    color: #6c757d;
    max-width: 700px;
    margin: 0 auto 40px;
  }

  .feature-card {
    background: #fff;
    border-radius: 14px;
    padding: 35px 30px;
    box-shadow: 0 6px 25px rgba(200, 16, 46, 0.08);
    transition: 0.3s ease;
    border-top: 4px solid #C8102E;
    height: 100%;
    text-align: center;
  }

  .feature-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 40px rgba(200, 16, 46, 0.12);
    border-top-color: #0ff05e;
  }

  .features-icon {
    font-size: 2.2rem;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #1a1a1a, #0ff05e);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 0 auto 20px;
    transition: background 0.3s ease;
  }

  .feature-card:hover .features-icon {
    background: linear-gradient(135deg, #0ff05e, #1a1a1a);
  }

  .feature-card h5 {
    font-size: 1.15rem;
    font-weight: 600;
    margin-bottom: 12px;
    color: #212529;
  }

  .feature-card p {
    font-size: 0.95rem;
    color: #6c757d;
  }

  @media (max-width: 768px) {
    #features h2 {
      font-size: 2rem;
    }

    .feature-card {
      padding: 25px 20px;
    }
  }
</style>

<!-- Features Section HTML -->
<section id="features">
  <div class="container">
    <div class="text-center mb-5">
      <h2 data-aos="fade-up">What You Can Do</h2>
      <p class="lead" data-aos="fade-up" data-aos-delay="100">
        Empowering board members and executives with tools for effective governance.
      </p>
    </div>

    <div class="row g-4">
      <!-- Card 1 -->
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
        <div class="feature-card">
          <div class="features-icon"><i class="bi bi-calendar3"></i></div>
          <h5>Meeting Management</h5>
          <p>Schedule, manage, and notify board members with automated invites and agenda syncing.</p>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="feature-card">
          <div class="features-icon"><i class="bi bi-file-earmark-text"></i></div>
          <h5>Board Documents</h5>
          <p>Upload, annotate, and version key board papers with approval workflows and access control.</p>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="250">
        <div class="feature-card">
          <div class="features-icon"><i class="bi bi-check2-circle"></i></div>
          <h5>Approvals & Tracking</h5>
          <p>Enable structured approvals for resolutions and actions, with full audit trails and status updates.</p>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
        <div class="feature-card">
          <div class="features-icon"><i class="bi bi-lock-fill"></i></div>
          <h5>Secure Governance</h5>
          <p>Control access to meetings, minutes, and strategic docs with roles, encryption, and logging.</p>
        </div>
      </div>

      <!-- Card 5 -->
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="350">
        <div class="feature-card">
          <div class="features-icon"><i class="bi bi-chat-square-text"></i></div>
          <h5>Collaborate & Discuss</h5>
          <p>Comment and collaborate on board matters with threaded chats, notes, and document discussions.</p>
        </div>
      </div>

      <!-- Card 6 -->
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
        <div class="feature-card">
          <div class="features-icon"><i class="bi bi-graph-up-arrow"></i></div>
          <h5>Smart Reporting</h5>
          <p>Visualize attendance, document flow, and action progress with interactive dashboards and reports.</p>
        </div>
      </div>
    </div>
  </div>
</section>



<!-- Google Fonts + Bootstrap Icons (optional) -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Styles -->
<style>
  body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
  }

  .board-hierarchy {
    background: #f7f7f7;
    position: relative;
    padding: 100px 20px 70px;
    overflow: hidden;
  }

  /* SVG Separator */
  .section-divider {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    line-height: 0;
    transform: translateY(-1px);
  }

  .section-divider svg {
    display: block;
    width: 100%;
    height: 60px;
  }

  /* Title */
  .board-hierarchy h2 {
    text-align: center;
    font-weight: 700;
    font-size: 2.8rem;
    color: #232323;
    margin-bottom: 10px;
    position: relative;
  }

  .board-hierarchy h2::after {
    content: '';
    width: 80px;
    height: 4px;
    background: #1a1a1a;
    display: block;
    margin: 15px auto 0;
    border-radius: 6px;
    animation: expandLine 1s ease forwards;
  }

  @keyframes expandLine {
    0% { width: 0; opacity: 0; }
    100% { width: 80px; opacity: 1; }
  }

  .board-hierarchy p.subtitle {
    text-align: center;
    color: #6c757d;
    font-size: 1.1rem;
    margin-bottom: 50px;
  }

  .board-level {
    margin-bottom: 60px;
  }

  .board-level-title {
    text-align: center;
    font-size: 1.25rem;
    font-weight: 600;
    color: #232323;
    margin-bottom: 25px;
    border-bottom: 2px dashed #C8102E;
    display: inline-block;
    padding-bottom: 6px;
  }

  .board-cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 24px;
  }

  .board-card {
    background: #fff;
    border-radius: 12px;
    padding: 30px 24px;
    box-shadow: 0 8px 24px rgba(200, 16, 46, 0.07);
    text-align: center;
    transition: all 0.3s ease;
    width: 300px;
    border-top: 5px solid #C8102E;
    position: relative;
  }

  .board-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(200, 16, 46, 0.12);
    border-top-color: #0ff05e;
  }

  .board-card .name {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 8px;
    color: #212529;
  }

  .board-card .role {
    font-size: 0.95rem;
    color: #6c757d;
    line-height: 1.5;
  }

  @media (max-width: 768px) {
    .board-card {
      width: 100%;
    }
  }
</style>

<!-- HTML -->
<section class="board-hierarchy">
  <!-- SVG Section Top Divider -->
  <div class="section-divider">
    <svg viewBox="0 0 1440 50" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" fill-opacity="1" d="M0,0 C480,50 960,0 1440,50 L1440,0 L0,0 Z"></path></svg>
  </div>

  <div class="container">
    <h2>Board of Directors</h2>
    <p class="subtitle">Meet the leadership steering our agency forward.</p>

    <!-- Chairman -->
    <div class="board-level">
      <div class="text-center board-level-title">Chairman</div>
      <div class="board-cards">
        <div class="board-card">
          <div class="name">Mr. Teobald Mukundi Wambugu</div>
          <div class="role">Board Chairman</div>
        </div>
      </div>
    </div>

    <!-- Ministry Representatives -->
    <div class="board-level">
      <div class="text-center board-level-title">Ministry Representatives</div>
      <div class="board-cards">
        <div class="board-card">
          <div class="name">Eng. Mary Wamaitha</div>
          <div class="role">Rep. PS, Ministry of Water, Sanitation & Irrigation</div>
        </div>
        <div class="board-card">
          <div class="name">CPA. Edward Wamweya</div>
          <div class="role">Rep. CS, National Treasury & Planning</div>
        </div>
        <div class="board-card">
          <div class="name">Ms. Winfred Njoroge</div>
          <div class="role">Rep. IG, State Corporations</div>
        </div>
      </div>
    </div>

    <!-- Board Members -->
    <div class="board-level">
      <div class="text-center board-level-title">Board Members</div>
      <div class="board-cards">
        <div class="board-card">
          <div class="name">Dr. Geoffrey Rukunja</div>
          <div class="role">Board Member</div>
        </div>
        <div class="board-card">
          <div class="name">Ms. Cate Wanjirah Mbogoh</div>
          <div class="role">Board Member</div>
        </div>
        <div class="board-card">
          <div class="name">Mr. Anthony Mwangi</div>
          <div class="role">Board Member</div>
        </div>
        <div class="board-card">
          <div class="name">Ms. Irene Njoki Maina</div>
          <div class="role">Board Member</div>
        </div>
      </div>
    </div>

    <!-- CEO/Secretary -->
    <div class="board-level">
      <div class="text-center board-level-title">Chief Executive</div>
      <div class="board-cards">
        <div class="board-card">
          <div class="name">Eng. Philip Gichuki, MBS</div>
          <div class="role">Chief Executive Officer / Board Secretary</div>
        </div>
      </div>
    </div>
  </div>
</section>




<!-- Call to Action -->
<section id="cta" class="text-center" style="background: linear-gradient(135deg, #f2f2f2 0%, #d4edda 100%); padding: 40px 0; border-top: 4px solid #C8102E;">
  <div class="container">
    <h2 class="fw-bold mb-2 display-6" data-aos="fade-up" style="color: #232323; font-size: 2rem;">
      <i class="bi bi-speedometer2 me-2"></i>Streamline Your Board Processes
    </h2>
    <p class="mb-4 lead" data-aos="fade-up" data-aos-delay="100" style="color: #444; font-size: 1.1rem; max-width: 700px; margin: 0 auto;">
      Join TWWDA’s digital transformation journey and experience governance like never before.
    </p>

    <a href="{{ url('/login') }}"
       class="btn btn-outline-primary btn-lg px-5 py-3 rounded-pill fw-semibold shadow-sm"
       data-aos="fade-up"
       data-aos-delay="200"
       style="
         transition: all 0.3s ease;
         font-size: 1rem;
         background-color: #ffffff;
         color: #232323;
         border: 2px solid #C8102E;
       "
       onmouseover="this.style.backgroundColor='#232323'; this.style.color='#ffffff';"
       onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#232323';">
      <i class="bi bi-box-arrow-in-right me-2"></i>Access eBoard Now
    </a>

    <div class="mt-4" style="font-size: 0.9rem; color: #6c757d;">
      Secure, Reliable & Built for Public Sector Boards
    </div>
  </div>
</section>



    <!-- Google Fonts + Bootstrap Icons -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Footer Divider -->
<div class="footer-divider"></div>

<!-- FOOTER -->
<footer class="text-light pt-5 pb-4">
  <div class="container">
    <div class="row gy-5">
      <!-- 1. System Info -->
      <div class="col-md-4 col-lg-3">
        <h5><i class="bi bi-laptop me-2"></i>System</h5>
        <strong class="d-block mb-2">TWWDA eBoard System</strong>
        <p class="mb-3">Digital board governance made seamless.</p>
        <div class="social-icons d-flex gap-2">
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        </div>
      </div>

      <!-- 2. Quick Links -->
      <div class="col-md-4 col-lg-2">
        <h5><i class="bi bi-link-45deg me-2"></i>Quick Links</h5>
        <ul class="list-unstyled footer-links">
          <li><a href="#"><i class="bi bi-chevron-right me-1"></i> Home</a></li>
          <li><a href="#"><i class="bi bi-chevron-right me-1"></i> About Us</a></li>
          <li><a href="#"><i class="bi bi-chevron-right me-1"></i> Services</a></li>
          <li><a href="#"><i class="bi bi-chevron-right me-1"></i> Contact</a></li>
        </ul>
      </div>

      <!-- 3. Resources -->
      <div class="col-md-4 col-lg-2">
        <h5><i class="bi bi-journal-code me-2"></i>Resources</h5>
        <ul class="list-unstyled footer-links">
          <li><a href="#"><i class="bi bi-chevron-right me-1"></i> Help Center</a></li>
          <li><a href="#"><i class="bi bi-chevron-right me-1"></i> Documentation</a></li>
          <li><a href="#"><i class="bi bi-chevron-right me-1"></i> FAQs</a></li>
          <li><a href="#"><i class="bi bi-chevron-right me-1"></i> Privacy Policy</a></li>
        </ul>
      </div>

      <!-- 4. Contacts -->
      <div class="col-md-6 col-lg-5">
        <h5><i class="bi bi-geo-alt me-2"></i>Our Contacts</h5>
        <ul class="list-unstyled footer-links">
          <li><i class="bi bi-geo-alt-fill me-2"></i> MAJI HOUSE, BADEN POWELL ROAD</li>
          <li><i class="bi bi-mailbox me-2"></i> P.O. BOX 1292 – 10100</li>
          <li><i class="bi bi-telephone-fill me-2"></i> +254 724 259 891</li>
          <li><i class="bi bi-telephone-fill me-2"></i> +254 61-2032282</li>
          <li><i class="bi bi-envelope-fill me-2"></i> info@tanawwda.go.ke</li>
          <li><i class="bi bi-globe me-2"></i>
            <a href="https://www.tanawwda.go.ke" target="_blank">www.tanawwda.go.ke</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="row mt-5 pt-4 border-top footer-bottom">
      <div class="col-md-6">
        <p class="mb-0">&copy; 2025 TANA Water Works Development Agency. All rights reserved.</p>
      </div>
      <div class="col-md-6 text-md-end mt-2 mt-md-0">
        <a href="https://www.tanawwda.go.ke" class="text-light" target="_blank">www.tanawwda.go.ke</a>
      </div>
    </div>
  </div>
</footer>

<!-- FOOTER STYLES -->
<style>
  footer {
    background: linear-gradient(135deg, #1a1a1a 0%, #9CD273 100%);
    font-family: 'Poppins', sans-serif;
    color: #ffffff;
  }

  footer h5 {
    font-weight: 700;
    margin-bottom: 20px;
  }

  .footer-links li {
    margin-bottom: 10px;
    font-size: 0.95rem;
  }

  .footer-links li a {
    color: #ffffff;
    text-decoration: none;
    transition: 0.3s ease;
  }

  .footer-links li a:hover {
    color: #f1f1f1;
    text-decoration: underline;
  }

  .social-icons a {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    font-size: 16px;
    color: #ffffff;
    transition: all 0.3s ease;
  }

  .social-icons a:hover {
    background: #ffffff;
    color: #232323;
    transform: scale(1.05);
  }

  .footer-divider {
    height: 6px;
    background: repeating-linear-gradient(
      90deg,
      #1a1a1a,
      #1a1a1a 20px,
      transparent 20px,
      transparent 40px
    );
  }

  .footer-bottom {
    font-size: 14px;
    color: #ffffff;
  }

  .footer-bottom a {
    color: #ffffff;
    font-weight: 500;
    text-decoration: none;
  }

  .footer-bottom a:hover {
    text-decoration: underline;
    color: #f8f9fa;
  }

  @media (max-width: 768px) {
    .footer-bottom {
      text-align: center;
    }

    .footer-bottom .text-md-end {
      text-align: center !important;
      margin-top: 10px;
    }
  }
</style>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
        
        // Back to top button
        const backToTopButton = document.querySelector('.back-to-top');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('active');
            } else {
                backToTopButton.classList.remove('active');
            }
        });
        
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({top: 0, behavior: 'smooth'});
        });
        
        // Animated counter for stats
        const counters = document.querySelectorAll('.stat-number');
        const speed = 200;
        
        function animateCounters() {
            counters.forEach(counter => {
                const target = +counter.getAttribute('data-count');
                const count = +counter.innerText;
                
                const inc = target / speed;
                
                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(() => animateCounters(), 1);
                } else {
                    counter.innerText = target;
                }
            });
        }
        
        // Start counter animation when stats section is in view
        const statsSection = document.querySelector('.stats-section');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(statsSection);
    </script>
</body>
</html>