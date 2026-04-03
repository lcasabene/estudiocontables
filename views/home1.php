<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contador Ariel Casabene - Estudio Contable</title>
    <meta name="description" content="Estudio Contable Casabene. Contador Público Nacional. Servicios contables, impositivos y laborales en Argentina.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2a5298;
            --accent: #c8a951;
            --accent-light: #ddc477;
            --dark: #0f1c2e;
            --light: #f8f9fa;
            --gray: #6c757d;
            --white: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            overflow-x: hidden;
        }

        /* ── Navbar ── */
        .navbar-custom {
            background: rgba(15, 28, 46, 0.97);
            backdrop-filter: blur(12px);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }
        .navbar-custom.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.3);
        }
        .navbar-brand-text {
            color: var(--white) !important;
            font-weight: 700;
            font-size: 1.2rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .navbar-brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            font-size: 1rem;
            flex-shrink: 0;
        }
        .navbar-brand-text span { color: var(--accent); }
        .nav-link-custom {
            color: rgba(255,255,255,0.75) !important;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.5rem 1rem !important;
            transition: color 0.3s;
            position: relative;
        }
        .nav-link-custom::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1rem;
            right: 1rem;
            height: 2px;
            background: var(--accent);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .nav-link-custom:hover { color: var(--white) !important; }
        .nav-link-custom:hover::after { transform: scaleX(1); }
        .btn-ingreso {
            background: var(--accent);
            color: var(--dark) !important;
            font-weight: 600;
            padding: 0.5rem 1.5rem !important;
            border-radius: 50px;
            border: none;
            transition: all 0.3s;
        }
        .btn-ingreso:hover {
            background: var(--accent-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(200,169,81,0.4);
        }
        .btn-ingreso::after { display: none; }

        /* ── Hero ── */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 55%, var(--primary-light) 100%);
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }
        /* Grid pattern overlay */
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }
        .hero-glow {
            position: absolute;
            top: -30%;
            right: -10%;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(ellipse, rgba(200,169,81,0.12) 0%, transparent 65%);
            pointer-events: none;
        }
        .hero-fade-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 140px;
            background: linear-gradient(to top, var(--white), transparent);
            pointer-events: none;
            z-index: 1;
        }
        .hero-content { position: relative; z-index: 2; }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(200,169,81,0.12);
            color: var(--accent);
            padding: 0.4rem 1.2rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(200,169,81,0.25);
        }
        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 4.2rem);
            font-weight: 800;
            color: var(--white);
            line-height: 1.08;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }
        .hero h1 .accent { color: var(--accent); }
        .hero-subtitle {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.65);
            line-height: 1.8;
            max-width: 480px;
            margin-bottom: 2.5rem;
        }
        .btn-hero-primary {
            background: var(--accent);
            color: var(--dark);
            font-weight: 700;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            border: none;
            font-size: 0.95rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-hero-primary:hover {
            background: var(--accent-light);
            color: var(--dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(200,169,81,0.35);
        }
        .btn-hero-secondary {
            background: transparent;
            color: var(--white);
            font-weight: 600;
            padding: 1rem 2rem;
            border-radius: 50px;
            border: 2px solid rgba(255,255,255,0.25);
            font-size: 0.95rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-hero-secondary:hover {
            border-color: rgba(255,255,255,0.6);
            color: var(--white);
            background: rgba(255,255,255,0.08);
        }

        /* Hero stats */
        .hero-stats {
            margin-top: 3.5rem;
            display: flex;
            gap: 2.5rem;
            flex-wrap: wrap;
        }
        .hero-stat {
            position: relative;
            padding-left: 0;
        }
        .hero-stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent);
            line-height: 1;
            margin-bottom: 0.25rem;
        }
        .hero-stat-label {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .hero-stat-divider {
            width: 1px;
            background: rgba(255,255,255,0.12);
            align-self: stretch;
        }

        /* Hero visual cards */
        .hero-visual {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding-left: 2rem;
        }
        .hero-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .hero-card:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(200,169,81,0.3);
            transform: translateX(-4px);
        }
        .hero-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--dark);
            flex-shrink: 0;
        }
        .hero-card h6 {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.15rem;
        }
        .hero-card p {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.5);
            margin: 0;
        }
        .hero-card-featured {
            background: rgba(200,169,81,0.12);
            border-color: rgba(200,169,81,0.3);
        }

        /* ── Sections ── */
        section { padding: 6rem 0; }
        .section-label {
            display: inline-block;
            color: var(--accent);
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }
        .section-title {
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }
        .section-subtitle {
            font-size: 1.05rem;
            color: var(--gray);
            max-width: 580px;
            line-height: 1.7;
        }

        /* ── Services ── */
        .services-section { background: var(--white); }
        .service-card {
            background: var(--white);
            border-radius: 18px;
            padding: 2.5rem 2rem;
            height: 100%;
            border: 1px solid #e9ecef;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 48px rgba(0,0,0,0.08);
            border-color: transparent;
        }
        .service-card:hover::before { transform: scaleX(1); }
        .service-icon {
            width: 58px;
            height: 58px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
            color: var(--white);
        }
        .service-card h4 {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.6rem;
            color: var(--dark);
        }
        .service-card p {
            color: var(--gray);
            font-size: 0.9rem;
            line-height: 1.75;
            margin: 0;
        }

        /* ── Why us ── */
        .why-section { background: var(--light); }
        .why-step {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }
        .why-step-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--accent);
            opacity: 0.25;
            line-height: 1;
            min-width: 60px;
            font-variant-numeric: tabular-nums;
        }
        .why-step h5 {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 0.4rem;
        }
        .why-step p {
            font-size: 0.9rem;
            color: var(--gray);
            line-height: 1.7;
            margin: 0;
        }
        .why-divider {
            border: none;
            border-top: 1px dashed #dee2e6;
            margin: 1.75rem 0;
        }
        .why-highlight-box {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 20px;
            padding: 3rem;
            color: var(--white);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .why-highlight-box h3 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        .why-highlight-box p {
            opacity: 0.75;
            line-height: 1.8;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }
        .why-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 50px;
            padding: 0.35rem 1rem;
            font-size: 0.8rem;
            color: var(--accent);
            font-weight: 600;
            margin: 0.25rem;
        }

        /* ── Specialty ── */
        .specialty-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
            color: var(--white);
            position: relative;
            overflow: hidden;
        }
        .specialty-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(ellipse, rgba(200,169,81,0.07) 0%, transparent 65%);
            pointer-events: none;
        }
        .specialty-badge {
            display: inline-block;
            background: rgba(200,169,81,0.15);
            color: var(--accent);
            padding: 0.4rem 1.2rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(200,169,81,0.25);
        }
        .specialty-section h2 {
            font-size: clamp(1.8rem, 3vw, 2.3rem);
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }
        .specialty-section h2 span { color: var(--accent); }
        .specialty-section .lead-text {
            font-size: 1rem;
            opacity: 0.75;
            line-height: 1.85;
            margin-bottom: 2rem;
        }
        .specialty-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding: 1rem 1.25rem;
            background: rgba(255,255,255,0.04);
            border-radius: 12px;
            border-left: 3px solid var(--accent);
            transition: background 0.3s;
        }
        .specialty-item:hover { background: rgba(255,255,255,0.08); }
        .specialty-item i {
            font-size: 1.3rem;
            color: var(--accent);
            margin-top: 2px;
            min-width: 26px;
        }
        .specialty-item h6 {
            font-weight: 700;
            margin-bottom: 0.2rem;
            font-size: 0.95rem;
        }
        .specialty-item p {
            margin: 0;
            font-size: 0.875rem;
            opacity: 0.65;
            line-height: 1.65;
        }
        .specialty-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            height: 100%;
        }
        .specialty-icon-big {
            font-size: 4.5rem;
            color: var(--accent);
            opacity: 0.9;
            margin-bottom: 1.5rem;
            display: block;
        }
        .specialty-card h4 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .specialty-card p {
            opacity: 0.65;
            font-size: 0.9rem;
        }
        .specialty-card .blog-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--accent);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 1rem;
            transition: gap 0.3s;
        }
        .specialty-card .blog-link:hover { gap: 0.7rem; }

        /* ── About ── */
        .about-section { background: var(--white); }
        .about-feature {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .about-feature-icon {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.1rem;
        }
        .about-feature h5 {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: var(--dark);
        }
        .about-feature p {
            font-size: 0.875rem;
            color: var(--gray);
            margin: 0;
            line-height: 1.65;
        }
        .about-profile-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 20px;
            padding: 3rem 2.5rem;
            height: 100%;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }
        .about-profile-card::before {
            content: '';
            position: absolute;
            bottom: -40px;
            right: -40px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .about-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: rgba(200,169,81,0.2);
            border: 3px solid rgba(200,169,81,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--accent);
            margin-bottom: 1.5rem;
        }
        .about-profile-card h3 {
            font-weight: 800;
            font-size: 1.4rem;
            margin-bottom: 0.3rem;
        }
        .about-profile-card .title-line {
            font-size: 0.9rem;
            opacity: 0.7;
            margin-bottom: 1.5rem;
        }
        .about-profile-card .divider {
            width: 50px;
            height: 2px;
            background: var(--accent);
            opacity: 0.5;
            margin: 0 auto 1.5rem;
        }
        .about-profile-card .detail {
            font-size: 0.85rem;
            opacity: 0.65;
            line-height: 1.6;
            max-width: 280px;
        }
        .about-profile-card .accent-text {
            color: var(--accent);
            font-weight: 600;
        }

        /* ── Blog Preview ── */
        .blog-preview-section { background: var(--light); }
        .blog-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e9ecef;
            height: 100%;
            transition: all 0.35s ease;
            display: flex;
            flex-direction: column;
        }
        .blog-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-color: transparent;
        }
        .blog-card-body {
            padding: 1.75rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .blog-card-category {
            display: inline-block;
            background: rgba(30,58,95,0.08);
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }
        .blog-card h5 {
            font-weight: 700;
            font-size: 1rem;
            color: var(--dark);
            line-height: 1.5;
            margin-bottom: 0.6rem;
        }
        .blog-card p {
            font-size: 0.875rem;
            color: var(--gray);
            line-height: 1.7;
            flex: 1;
        }
        .blog-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.75rem;
            border-top: 1px solid #f0f2f5;
            font-size: 0.8rem;
            color: var(--gray);
        }
        .blog-card-footer a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: gap 0.3s, color 0.3s;
        }
        .blog-card-footer a:hover {
            color: var(--accent);
            gap: 0.6rem;
        }
        .blog-empty {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
        }

        /* ── CTA ── */
        .cta-section {
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            color: var(--white);
            text-align: center;
            padding: 5rem 0;
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .cta-section::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 60%;
            height: 200%;
            background: radial-gradient(ellipse, rgba(200,169,81,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .cta-inner { position: relative; z-index: 2; }
        .cta-section h2 {
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }
        .cta-section p {
            font-size: 1.05rem;
            opacity: 0.75;
            margin-bottom: 2rem;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
        }

        /* ── Contact ── */
        .contact-icon {
            width: 50px;
            height: 50px;
            min-width: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.2rem;
        }
        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .contact-item h6 {
            font-weight: 700;
            font-size: 0.78rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 0.2rem;
        }
        .contact-item p, .contact-item a {
            font-size: 0.95rem;
            color: var(--dark);
            margin: 0;
            font-weight: 500;
            text-decoration: none;
        }
        .contact-item a:hover { color: var(--accent); }
        .contact-form-wrapper {
            background: var(--white);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 4px 30px rgba(0,0,0,0.06);
            border: 1px solid #e9ecef;
        }
        .form-control, .form-select {
            border-radius: 10px !important;
            border-color: #e0e4ea !important;
            font-size: 0.95rem !important;
            padding: 0.65rem 1rem !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(30,58,95,0.1) !important;
        }
        .btn-submit {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            font-weight: 700;
            padding: 0.9rem 2.5rem;
            border-radius: 50px;
            border: none;
            font-size: 0.95rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30,58,95,0.3);
        }

        /* ── Footer ── */
        footer {
            background: var(--dark);
            color: rgba(255,255,255,0.45);
            padding: 3rem 0 1.5rem;
        }
        .footer-brand {
            color: var(--white);
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
        }
        .footer-brand span { color: var(--accent); }
        .footer-text {
            font-size: 0.875rem;
            line-height: 1.7;
            max-width: 280px;
        }
        .footer-heading {
            color: rgba(255,255,255,0.7);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-links li { margin-bottom: 0.5rem; }
        .footer-links a {
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s;
        }
        .footer-links a:hover { color: var(--accent); }
        .footer-divider {
            border-color: rgba(255,255,255,0.08);
            margin: 2rem 0 1.5rem;
        }
        .footer-bottom {
            font-size: 0.8rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .footer-bottom a { color: var(--accent); text-decoration: none; }
        .footer-bottom a:hover { color: var(--accent-light); }

        /* ── WhatsApp floating button ── */
        .wa-float {
            position: fixed;
            bottom: 1.75rem;
            right: 1.75rem;
            width: 56px;
            height: 56px;
            background: #25d366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: var(--white);
            box-shadow: 0 4px 20px rgba(37,211,102,0.45);
            text-decoration: none;
            z-index: 999;
            transition: all 0.3s;
        }
        .wa-float:hover {
            transform: scale(1.1);
            color: var(--white);
            box-shadow: 0 6px 25px rgba(37,211,102,0.6);
        }
        .wa-float::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: #25d366;
            animation: wa-pulse 2.5s ease-out infinite;
            z-index: -1;
        }
        @keyframes wa-pulse {
            0% { transform: scale(1); opacity: 0.6; }
            100% { transform: scale(1.8); opacity: 0; }
        }

        /* ── Animations ── */
        .fade-up {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.65s ease, transform 0.65s ease;
        }
        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .fade-up[data-delay="1"] { transition-delay: 0.1s; }
        .fade-up[data-delay="2"] { transition-delay: 0.2s; }
        .fade-up[data-delay="3"] { transition-delay: 0.3s; }
        .fade-up[data-delay="4"] { transition-delay: 0.4s; }
        .fade-up[data-delay="5"] { transition-delay: 0.5s; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            .hero-visual { display: none; }
        }
        @media (max-width: 768px) {
            .hero-stats { gap: 1.5rem; }
            .hero-stat-number { font-size: 1.6rem; }
            section { padding: 4rem 0; }
            .section-title { font-size: 1.75rem; }
            .hero-buttons { flex-direction: column; align-items: flex-start; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand navbar-brand-text" href="#">
                <div class="navbar-brand-icon"><i class="bi bi-building"></i></div>
                Estudio <span>Casabene</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="bi bi-list text-white fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#servicios">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#entidades-religiosas">Entidades Religiosas</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= base_url('blog') ?>">Blog</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#estudio">El Estudio</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#contacto">Contacto</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link btn-ingreso" href="<?= base_url('estudio/login') ?>">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero" id="inicio">
        <div class="hero-glow"></div>
        <div class="hero-fade-bottom"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <div class="hero-badge">
                        <i class="bi bi-patch-check-fill"></i>
                        Contador Público Nacional
                    </div>
                    <h1>Luis Ariel<br><span class="accent">Casabene</span></h1>
                    <p class="hero-subtitle">
                        Soluciones contables, impositivas y de asesoramiento integral
                        para empresas, profesionales y entidades en Argentina.
                    </p>
                    <div class="d-flex gap-3 flex-wrap hero-buttons">
                        <a href="#servicios" class="btn-hero-primary">
                            Conocer Servicios <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="https://wa.me/5492995743759" target="_blank" class="btn-hero-secondary">
                            <i class="bi bi-whatsapp"></i> Consultar gratis
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <div class="hero-stat-number">+15</div>
                            <div class="hero-stat-label">Años de experiencia</div>
                        </div>
                        <div class="hero-stat-divider"></div>
                        <div class="hero-stat">
                            <div class="hero-stat-number">100%</div>
                            <div class="hero-stat-label">Compromiso</div>
                        </div>
                        <div class="hero-stat-divider"></div>
                        <div class="hero-stat">
                            <div class="hero-stat-number">Pyme</div>
                            <div class="hero-stat-label">Especialidad</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-flex">
                    <div class="hero-visual w-100">
                        <div class="hero-card hero-card-featured">
                            <div class="hero-card-icon"><i class="bi bi-journal-bookmark"></i></div>
                            <div>
                                <h6>Contabilidad & Balances</h6>
                                <p>Estados contables y reportes financieros</p>
                            </div>
                        </div>
                        <div class="hero-card">
                            <div class="hero-card-icon"><i class="bi bi-receipt-cutoff"></i></div>
                            <div>
                                <h6>Impuestos Nacionales y Provinciales</h6>
                                <p>AFIP, IIBB, Ganancias, Bienes Personales</p>
                            </div>
                        </div>
                        <div class="hero-card">
                            <div class="hero-card-icon"><i class="bi bi-church"></i></div>
                            <div>
                                <h6>Entidades Religiosas Evangélicas</h6>
                                <p>Especialidad en iglesias y asociaciones</p>
                            </div>
                        </div>
                        <div class="hero-card">
                            <div class="hero-card-icon"><i class="bi bi-people"></i></div>
                            <div>
                                <h6>Sueldos y Cargas Sociales</h6>
                                <p>Liquidación de haberes y libro sueldo digital</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios -->
    <section id="servicios" class="services-section">
        <div class="container">
            <div class="text-center mb-5 fade-up">
                <div class="section-label">Qué ofrecemos</div>
                <h2 class="section-title">Servicios Profesionales</h2>
                <p class="section-subtitle mx-auto">
                    Un servicio integral y personalizado para cada cliente, con enfoque
                    en resultados concretos para su negocio.
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4 fade-up" data-delay="1">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-journal-bookmark"></i></div>
                        <h4>Contabilidad</h4>
                        <p>Registraciones contables, balances, estados de resultados y reportes financieros para la toma de decisiones.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up" data-delay="2">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-receipt-cutoff"></i></div>
                        <h4>Impuestos</h4>
                        <p>Liquidación de impuestos nacionales y provinciales. AFIP, IIBB, monotributo, ganancias y bienes personales.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up" data-delay="3">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-people"></i></div>
                        <h4>Sueldos y Jornales</h4>
                        <p>Liquidación de haberes, cargas sociales, altas y bajas de personal, libro sueldo digital.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up" data-delay="1">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-briefcase"></i></div>
                        <h4>Asesoramiento</h4>
                        <p>Asesoramiento integral en materia contable, impositiva, laboral y societaria para su negocio.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up" data-delay="2">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-building-gear"></i></div>
                        <h4>Sociedades</h4>
                        <p>Constitución de sociedades, actas, asambleas, inscripciones y trámites ante organismos de control.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up" data-delay="3">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-clipboard2-data"></i></div>
                        <h4>Auditorías</h4>
                        <p>Auditorías contables, certificaciones e informes especiales para entidades bancarias y organismos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Por qué elegirnos -->
    <section class="why-section">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 fade-up">
                    <div class="why-highlight-box">
                        <h3>Su empresa en manos de un profesional de confianza</h3>
                        <p>
                            Cada cliente es único. Por eso trabajamos con un enfoque
                            personalizado, brindando soluciones a medida y acompañamiento
                            continuo en cada etapa de su negocio.
                        </p>
                        <div>
                            <span class="why-chip"><i class="bi bi-patch-check me-1"></i> Matriculado CPCE Neuquén</span>
                            <span class="why-chip"><i class="bi bi-shield-check me-1"></i> Confidencialidad</span>
                            <span class="why-chip"><i class="bi bi-laptop me-1"></i> Portal digital</span>
                            <span class="why-chip"><i class="bi bi-geo-alt me-1"></i> Atención remota</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 fade-up" data-delay="2">
                    <div class="section-label">Por qué elegirnos</div>
                    <h2 class="section-title mb-4">Cómo trabajamos</h2>

                    <div class="why-step">
                        <div class="why-step-number">01</div>
                        <div>
                            <h5>Primera consulta sin cargo</h5>
                            <p>Analizamos su situación, le explicamos qué necesita y le brindamos una propuesta clara y sin compromisos.</p>
                        </div>
                    </div>
                    <hr class="why-divider">
                    <div class="why-step">
                        <div class="why-step-number">02</div>
                        <div>
                            <h5>Diagnóstico y planificación</h5>
                            <p>Definimos juntos los servicios adecuados, los plazos y los honorarios. Sin letra chica.</p>
                        </div>
                    </div>
                    <hr class="why-divider">
                    <div class="why-step">
                        <div class="why-step-number">03</div>
                        <div>
                            <h5>Gestión y seguimiento continuo</h5>
                            <p>Nos encargamos de todos los trámites, vencimientos e informes. Usted solo tiene que enfocarse en su negocio.</p>
                        </div>
                    </div>
                    <hr class="why-divider">
                    <div class="why-step">
                        <div class="why-step-number">04</div>
                        <div>
                            <h5>Portal del cliente 24/7</h5>
                            <p>Acceda a sus documentos, liquidaciones y novedades impositivas desde cualquier dispositivo, en cualquier momento.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Especialidad: Entidades Religiosas -->
    <section class="specialty-section" id="entidades-religiosas">
        <div class="container position-relative">
            <div class="row align-items-center g-5">
                <div class="col-lg-5 text-center fade-up">
                    <div class="specialty-card">
                        <i class="bi bi-church specialty-icon-big"></i>
                        <h4>Entidades Religiosas</h4>
                        <p>Evangélicas y confesiones inscriptas en el Registro Nacional de Cultos</p>
                        <a href="<?= base_url('blog') ?>" class="blog-link">
                            Ver artículos del blog <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-7 fade-up" data-delay="2">
                    <div class="specialty-badge">Especialidad</div>
                    <h2>Entidades <span>Religiosas Evangélicas</span></h2>
                    <p class="lead-text">
                        Asesoramiento integral y especializado para iglesias, asociaciones
                        y entidades religiosas evangélicas en todo el país. Acompañamos desde
                        la constitución hasta la gestión contable e impositiva permanente.
                    </p>
                    <div class="specialty-item">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        <div>
                            <h6>Contabilidad y Libros</h6>
                            <p>Registraciones contables, rúbrica de libros (Diario, Inventario y Balances, Actas) y elaboración de estados contables conforme normativa vigente.</p>
                        </div>
                    </div>
                    <div class="specialty-item">
                        <i class="bi bi-award"></i>
                        <div>
                            <h6>Exenciones Impositivas</h6>
                            <p>Tramitación y mantenimiento de exenciones en Ganancias, IVA, Ingresos Brutos y tasas municipales. Certificados de no retención y exclusión.</p>
                        </div>
                    </div>
                    <div class="specialty-item">
                        <i class="bi bi-bank"></i>
                        <div>
                            <h6>Trámites ante Organismos de Control</h6>
                            <p>Inscripciones y presentaciones ante el Registro Nacional de Cultos, IGJ, AFIP, Rentas provinciales y municipios.</p>
                        </div>
                    </div>
                    <div class="specialty-item">
                        <i class="bi bi-file-earmark-text"></i>
                        <div>
                            <h6>Constitución y Personería Jurídica</h6>
                            <p>Asistencia en la constitución de la entidad, redacción de estatutos, obtención de personería jurídica y filial eclesiástica.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sobre el Estudio -->
    <section class="about-section" id="estudio">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 fade-up">
                    <div class="section-label">Sobre el estudio</div>
                    <h2 class="section-title">Profesionalismo y confianza al servicio de su empresa</h2>
                    <p class="text-muted mb-4" style="line-height:1.8; font-size: 0.95rem;">
                        El Estudio Contable Casabene se dedica a brindar servicios profesionales
                        de excelencia, acompañando a empresas y emprendedores en cada etapa de su
                        crecimiento con soluciones a medida.
                    </p>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <h5>Confianza y confidencialidad</h5>
                            <p>Manejamos su información con los más altos estándares de seguridad y ética profesional.</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="bi bi-clock-history"></i></div>
                        <div>
                            <h5>Atención personalizada</h5>
                            <p>Cada cliente recibe un trato directo y dedicado, con respuestas ágiles y oportunas.</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        <div>
                            <h5>Tecnología al servicio</h5>
                            <p>Plataforma digital para que acceda a su información contable en cualquier momento y lugar.</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="bi bi-mortarboard"></i></div>
                        <div>
                            <h5>Actualización permanente</h5>
                            <p>Seguimiento constante de los cambios normativos impositivos y laborales para proteger su negocio.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 fade-up" data-delay="2">
                    <div class="about-profile-card">
                        <div class="about-avatar">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h3>Cr. Ariel Casabene</h3>
                        <div class="title-line">Contador Público Nacional</div>
                        <div class="divider"></div>
                        <p class="detail">
                            Matriculado en el <span class="accent-text">Consejo Profesional de Ciencias Económicas de Neuquén</span>
                        </p>
                        <p class="detail mt-2">
                            Más de 15 años de experiencia asesorando a empresas, pymes, profesionales
                            y entidades religiosas en toda la Argentina.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Preview -->
    <section class="blog-preview-section">
        <div class="container">
            <div class="row align-items-end mb-5">
                <div class="col fade-up">
                    <div class="section-label">Novedades</div>
                    <h2 class="section-title mb-0">Últimas publicaciones</h2>
                </div>
                <div class="col-auto fade-up" data-delay="2">
                    <a href="<?= base_url('blog') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                        Ver todo el blog <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <?php if (!empty($recent_posts)): ?>
            <div class="row g-4">
                <?php foreach (array_slice($recent_posts, 0, 3) as $i => $post): ?>
                <div class="col-md-4 fade-up" data-delay="<?= $i + 1 ?>">
                    <div class="blog-card">
                        <div class="blog-card-body">
                            <span class="blog-card-category">
                                <?= htmlspecialchars($post['category'] ?? 'Impositivo') ?>
                            </span>
                            <h5><?= htmlspecialchars($post['title']) ?></h5>
                            <p><?= htmlspecialchars(mb_strimwidth(strip_tags($post['excerpt'] ?? $post['content'] ?? ''), 0, 100, '...')) ?></p>
                        </div>
                        <div class="blog-card-footer">
                            <span><i class="bi bi-calendar3 me-1"></i>
                                <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                            </span>
                            <a href="<?= base_url('blog/' . ($post['slug'] ?? $post['id'])) ?>">
                                Leer más <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="blog-empty fade-up">
                <i class="bi bi-newspaper" style="font-size:2.5rem; opacity:0.2;"></i>
                <p class="mt-2 mb-3">Próximamente nuevas publicaciones.</p>
                <a href="<?= base_url('blog') ?>" class="btn btn-outline-secondary rounded-pill px-4">Ir al blog</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container cta-inner">
            <h2 class="fade-up">Acceda a su portal de cliente</h2>
            <p class="fade-up">Consulte su información contable, documentos e impuestos desde cualquier dispositivo, las 24 horas.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap fade-up">
                <a href="<?= base_url('estudio/login') ?>" class="btn-hero-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Ingresar al Sistema
                </a>
                <a href="#contacto" class="btn-hero-secondary">
                    <i class="bi bi-envelope"></i> Contactar
                </a>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contacto">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5 fade-up">
                    <div class="section-label">Contacto</div>
                    <h2 class="section-title">Hablemos sobre su negocio</h2>
                    <p class="text-muted mb-4" style="line-height:1.8; font-size:0.95rem;">
                        Estamos para ayudarlo. Comuníquese con nosotros y le brindaremos
                        una primera consulta sin cargo.
                    </p>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="bi bi-telephone"></i></div>
                        <div>
                            <h6>Teléfono</h6>
                            <p><a href="tel:+5492995743759">+54 9 299 574-3759</a></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="bi bi-envelope"></i></div>
                        <div>
                            <h6>Email</h6>
                            <p><a href="mailto:info@contadorarielcasabene.online">info@contadorarielcasabene.online</a></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="bi bi-whatsapp"></i></div>
                        <div>
                            <h6>WhatsApp</h6>
                            <p><a href="https://wa.me/5492995743759" target="_blank">Enviar mensaje directo</a></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <h6>Ubicación</h6>
                            <p>Neuquén, Argentina &mdash; Atención presencial y remota</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 fade-up" data-delay="2">
                    <div class="contact-form-wrapper">
                        <h5 class="fw-bold mb-1" style="color: var(--dark);">Envíenos un mensaje</h5>
                        <p class="text-muted small mb-4">Le responderemos a la brevedad.</p>
                        <form id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Nombre completo</label>
                                    <input type="text" class="form-control" placeholder="Su nombre" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Email</label>
                                    <input type="email" class="form-control" placeholder="su@email.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Teléfono / WhatsApp</label>
                                    <input type="tel" class="form-control" placeholder="299 000-0000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Asunto</label>
                                    <select class="form-select">
                                        <option selected>Seleccione...</option>
                                        <option>Consulta general</option>
                                        <option>Monotributo</option>
                                        <option>Impuestos</option>
                                        <option>Sueldos</option>
                                        <option>Sociedades</option>
                                        <option>Entidades Religiosas</option>
                                        <option>Otro</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Mensaje</label>
                                    <textarea class="form-control" rows="4" placeholder="Cuéntenos en qué podemos ayudarlo..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-submit">
                                        <i class="bi bi-send"></i> Enviar Consulta
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <i class="bi bi-building me-2"></i>Estudio <span>Casabene</span>
                    </div>
                    <p class="footer-text">
                        Contador Público Nacional matriculado en el CPCE Neuquén.
                        Servicios contables, impositivos y de asesoramiento integral para empresas y entidades.
                    </p>
                </div>
                <div class="col-6 col-lg-2 offset-lg-1">
                    <div class="footer-heading">Servicios</div>
                    <ul class="footer-links">
                        <li><a href="#servicios">Contabilidad</a></li>
                        <li><a href="#servicios">Impuestos</a></li>
                        <li><a href="#servicios">Sueldos</a></li>
                        <li><a href="#servicios">Sociedades</a></li>
                        <li><a href="#servicios">Auditorías</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="footer-heading">Especialidades</div>
                    <ul class="footer-links">
                        <li><a href="#entidades-religiosas">Entidades Religiosas</a></li>
                        <li><a href="#servicios">Pymes</a></li>
                        <li><a href="#servicios">Emprendedores</a></li>
                        <li><a href="<?= base_url('blog') ?>">Blog</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <div class="footer-heading">Contacto</div>
                    <ul class="footer-links">
                        <li><a href="tel:+5492995743759"><i class="bi bi-telephone me-2"></i>+54 9 299 574-3759</a></li>
                        <li><a href="mailto:info@contadorarielcasabene.online"><i class="bi bi-envelope me-2"></i>Enviar email</a></li>
                        <li><a href="https://wa.me/5492995743759" target="_blank"><i class="bi bi-whatsapp me-2"></i>WhatsApp</a></li>
                        <li><a href="<?= base_url('estudio/login') ?>"><i class="bi bi-box-arrow-in-right me-2"></i>Portal clientes</a></li>
                    </ul>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="footer-bottom">
                <span>&copy; <?= date('Y') ?> Estudio Contable Casabene &mdash; Todos los derechos reservados.</span>
                <a href="<?= base_url('estudio/login') ?>">Acceso al sistema</a>
            </div>
        </div>
    </footer>

    <!-- WhatsApp flotante -->
    <a href="https://wa.me/5492995743759" target="_blank" class="wa-float" title="Consultar por WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll
        window.addEventListener('scroll', () => {
            document.getElementById('mainNav')
                .classList.toggle('scrolled', window.scrollY > 50);
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    const navCollapse = document.getElementById('navMenu');
                    if (navCollapse.classList.contains('show')) {
                        bootstrap.Collapse.getInstance(navCollapse)?.hide();
                    }
                }
            });
        });

        // Fade-up con stagger
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

        // Formulario de contacto
        document.getElementById('contactForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="bi bi-check-circle"></i> ¡Mensaje enviado!';
            btn.style.background = '#198754';
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-send"></i> Enviar Consulta';
                btn.style.background = '';
                this.reset();
            }, 3000);
        });
    </script>
</body>
</html>
