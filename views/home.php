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
            background: rgba(15, 28, 46, 0.95);
            backdrop-filter: blur(10px);
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
            font-size: 1.25rem;
            letter-spacing: -0.5px;
        }
        .navbar-brand-text span {
            color: var(--accent);
        }
        .nav-link-custom {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1rem !important;
            transition: color 0.3s;
        }
        .nav-link-custom:hover { color: var(--accent) !important; }
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

        /* ── Hero ── */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 200%;
            background: radial-gradient(ellipse, rgba(200,169,81,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 120px;
            background: linear-gradient(to top, var(--white), transparent);
            pointer-events: none;
        }
        .hero-content { position: relative; z-index: 2; }
        .hero-badge {
            display: inline-block;
            background: rgba(200,169,81,0.15);
            color: var(--accent);
            padding: 0.4rem 1.2rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(200,169,81,0.3);
        }
        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            color: var(--white);
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }
        .hero h1 .accent { color: var(--accent); }
        .hero-subtitle {
            font-size: 1.15rem;
            color: rgba(255,255,255,0.7);
            line-height: 1.7;
            max-width: 500px;
            margin-bottom: 2.5rem;
        }
        .btn-hero-primary {
            background: var(--accent);
            color: var(--dark);
            font-weight: 700;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            border: none;
            font-size: 1rem;
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
            box-shadow: 0 8px 25px rgba(200,169,81,0.35);
        }
        .btn-hero-secondary {
            background: transparent;
            color: var(--white);
            font-weight: 600;
            padding: 1rem 2rem;
            border-radius: 50px;
            border: 2px solid rgba(255,255,255,0.3);
            font-size: 1rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-hero-secondary:hover {
            border-color: var(--white);
            color: var(--white);
            background: rgba(255,255,255,0.1);
        }
        .hero-stats {
            margin-top: 4rem;
            display: flex;
            gap: 3rem;
        }
        .hero-stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent);
        }
        .hero-stat-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── Sections ── */
        section { padding: 6rem 0; }
        .section-label {
            display: inline-block;
            color: var(--accent);
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }
        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        .section-subtitle {
            font-size: 1.1rem;
            color: var(--gray);
            max-width: 600px;
        }

        /* ── Services ── */
        .service-card {
            background: var(--white);
            border-radius: 16px;
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
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-color: transparent;
        }
        .service-card:hover::before { transform: scaleX(1); }
        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color: var(--white);
        }
        .service-card h4 {
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 0.75rem;
            color: var(--dark);
        }
        .service-card p {
            color: var(--gray);
            font-size: 0.95rem;
            line-height: 1.7;
            margin: 0;
        }

        /* ── About ── */
        .about-section { background: var(--light); }
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
            font-size: 0.9rem;
            color: var(--gray);
            margin: 0;
            line-height: 1.6;
        }
        .about-image-wrapper {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            padding: 3rem;
            height: 100%;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--white);
        }
        .about-image-wrapper i {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            opacity: 0.9;
        }
        .about-image-wrapper h3 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .about-image-wrapper p {
            opacity: 0.8;
            font-size: 1rem;
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
            top: -50%;
            left: -20%;
            width: 60%;
            height: 200%;
            background: radial-gradient(ellipse, rgba(200,169,81,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .cta-section p {
            font-size: 1.1rem;
            opacity: 0.8;
            margin-bottom: 2rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ── Contact ── */
        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
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
        .contact-item h6 {
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.2rem;
        }
        .contact-item p {
            font-size: 1rem;
            color: var(--dark);
            margin: 0;
            font-weight: 500;
        }
        .contact-item a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .contact-item a:hover { color: var(--accent); }

        /* ── Footer ── */
        footer {
            background: var(--dark);
            color: rgba(255,255,255,0.5);
            padding: 2rem 0;
            text-align: center;
            font-size: 0.9rem;
        }
        footer a { color: var(--accent); text-decoration: none; }
        footer a:hover { color: var(--accent-light); }

        /* ── Animations ── */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .hero-stats { gap: 1.5rem; flex-wrap: wrap; }
            .hero-stat-number { font-size: 1.5rem; }
            section { padding: 4rem 0; }
            .section-title { font-size: 2rem; }
            .hero-buttons { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand navbar-brand-text" href="#">
                <i class="bi bi-building me-2"></i>Estudio <span>Casabene</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="bi bi-list text-white fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#servicios">Servicios</a></li>
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
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 hero-content">
                    <div class="hero-badge">Contador Publico Nacional</div>
                    <h1>Ariel <span class="accent">Casabene</span></h1>
                    <p class="hero-subtitle">
                        Soluciones contables, impositivas y de asesoramiento integral 
                        para empresas y profesionales independientes en Argentina.
                    </p>
                    <div class="d-flex gap-3 flex-wrap hero-buttons">
                        <a href="#servicios" class="btn-hero-primary">
                            Conocer Servicios <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="#contacto" class="btn-hero-secondary">
                            <i class="bi bi-whatsapp"></i> Contactar
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div>
                            <div class="hero-stat-number">+15</div>
                            <div class="hero-stat-label">Anios de experiencia</div>
                        </div>
                        <div>
                            <div class="hero-stat-number">+200</div>
                            <div class="hero-stat-label">Clientes activos</div>
                        </div>
                        <div>
                            <div class="hero-stat-number">100%</div>
                            <div class="hero-stat-label">Compromiso</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios -->
    <section id="servicios">
        <div class="container">
            <div class="text-center mb-5 fade-up">
                <div class="section-label">Que ofrecemos</div>
                <h2 class="section-title">Servicios Profesionales</h2>
                <p class="section-subtitle mx-auto">
                    Brindamos un servicio integral adaptado a las necesidades de cada cliente, 
                    con un enfoque personalizado y profesional.
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4 fade-up">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-journal-bookmark"></i></div>
                        <h4>Contabilidad</h4>
                        <p>Registraciones contables, balances, estados de resultados y reportes financieros para la toma de decisiones.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-receipt-cutoff"></i></div>
                        <h4>Impuestos</h4>
                        <p>Liquidacion de impuestos nacionales y provinciales. AFIP, IIBB, monotributo, ganancias y bienes personales.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-people"></i></div>
                        <h4>Sueldos y Jornales</h4>
                        <p>Liquidacion de haberes, cargas sociales, altas y bajas de personal, libro sueldo digital.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-briefcase"></i></div>
                        <h4>Asesoramiento</h4>
                        <p>Asesoramiento integral en materia contable, impositiva, laboral y societaria para su negocio.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-building-gear"></i></div>
                        <h4>Sociedades</h4>
                        <p>Constitucion de sociedades, actas, asambleas, inscripciones y tramites ante organismos de control.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-up">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-clipboard2-data"></i></div>
                        <h4>Auditorias</h4>
                        <p>Auditorias contables, certificaciones e informes especiales para entidades bancarias y organismos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About -->
    <section class="about-section" id="estudio">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 fade-up">
                    <div class="section-label">Sobre el estudio</div>
                    <h2 class="section-title">Profesionalismo y confianza al servicio de su empresa</h2>
                    <p class="text-muted mb-4" style="line-height:1.8">
                        El Estudio Contable Casabene se dedica a brindar servicios profesionales 
                        de excelencia, acompaniando a empresas y emprendedores en cada etapa de su 
                        crecimiento con soluciones a medida.
                    </p>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <h5>Confianza y confidencialidad</h5>
                            <p>Manejamos su informacion con los mas altos estandares de seguridad y etica profesional.</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="bi bi-clock-history"></i></div>
                        <div>
                            <h5>Atencion personalizada</h5>
                            <p>Cada cliente recibe un trato directo y dedicado, con respuestas agiles y oportunas.</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        <div>
                            <h5>Tecnologia al servicio</h5>
                            <p>Plataforma digital para que acceda a su informacion contable en cualquier momento.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 fade-up">
                    <div class="about-image-wrapper">
                        <i class="bi bi-person-badge"></i>
                        <h3>Cr. Ariel Casabene</h3>
                        <p>Contador Publico Nacional</p>
                        <hr style="border-color: rgba(255,255,255,0.2); width: 60px; margin: 1.5rem auto;">
                        <p style="font-size:0.9rem; opacity: 0.7; max-width: 300px;">
                            Matriculado en el Consejo Profesional de Ciencias Economicas
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container position-relative">
            <h2 class="fade-up">Acceda a su portal de cliente</h2>
            <p class="fade-up">Ingrese al sistema para consultar su informacion contable, documentos e impuestos.</p>
            <a href="<?= base_url('estudio/login') ?>" class="btn-hero-primary fade-up">
                <i class="bi bi-box-arrow-in-right"></i> Ingresar al Sistema
            </a>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contacto">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5 fade-up">
                    <div class="section-label">Contacto</div>
                    <h2 class="section-title">Hablemos sobre su negocio</h2>
                    <p class="text-muted mb-4" style="line-height:1.8">
                        Estamos para ayudarlo. Comuniquese con nosotros y le brindaremos 
                        una primera consulta sin cargo.
                    </p>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="bi bi-telephone"></i></div>
                        <div>
                            <h6>Telefono</h6>
                            <p><a href="tel:+5491100000000">+54 9 11 0000-0000</a></p>
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
                            <p><a href="https://wa.me/5491100000000" target="_blank">Enviar mensaje</a></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <h6>Ubicacion</h6>
                            <p>Buenos Aires, Argentina</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 fade-up">
                    <div class="bg-light rounded-4 p-4 p-lg-5">
                        <h5 class="fw-bold mb-4">Envienos un mensaje</h5>
                        <form id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Nombre completo</label>
                                    <input type="text" class="form-control form-control-lg" placeholder="Su nombre" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Email</label>
                                    <input type="email" class="form-control form-control-lg" placeholder="su@email.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Telefono</label>
                                    <input type="tel" class="form-control form-control-lg" placeholder="11 0000-0000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Asunto</label>
                                    <select class="form-select form-select-lg">
                                        <option selected>Seleccione...</option>
                                        <option>Consulta general</option>
                                        <option>Monotributo</option>
                                        <option>Impuestos</option>
                                        <option>Sueldos</option>
                                        <option>Sociedades</option>
                                        <option>Otro</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Mensaje</label>
                                    <textarea class="form-control form-control-lg" rows="4" placeholder="Cuentenos en que podemos ayudarlo..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-lg px-5 text-white fw-semibold" style="background: var(--primary); border-radius: 50px;">
                                        <i class="bi bi-send me-2"></i>Enviar Consulta
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
            <p class="mb-1">
                <i class="bi bi-building me-1"></i> 
                <strong>Estudio Contable Casabene</strong> &mdash; Contador Publico Nacional
            </p>
            <p class="mb-0 small">
                &copy; <?= date('Y') ?> Todos los derechos reservados. | 
                <a href="<?= base_url('estudio/login') ?>">Acceso al sistema</a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            document.getElementById('mainNav')
                .classList.toggle('scrolled', window.scrollY > 50);
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Close mobile menu
                    const navCollapse = document.getElementById('navMenu');
                    if (navCollapse.classList.contains('show')) {
                        bootstrap.Collapse.getInstance(navCollapse)?.hide();
                    }
                }
            });
        });

        // Fade-up animation on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

        // Contact form (prevent default, show alert)
        document.getElementById('contactForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Gracias por su consulta. Nos pondremos en contacto a la brevedad.');
            this.reset();
        });
    </script>
</body>
</html>
