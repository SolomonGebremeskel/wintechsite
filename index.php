<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');


// ======================================================================
// Wintech Site — index.php  (renamed from index.html)
// Requires PHP to: generate CSRF token, show status banners,
// output dynamic copyright year, and set security headers.
// ======================================================================

session_start();

// Generate CSRF token once per session (regenerated after each use)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Map redirect status codes from contact.php to user-facing messages
$status_messages = [
    'success'          => ['type' => 'success', 'text' => 'Message sent! We will get back to you shortly.'],
    'error'            => ['type' => 'error',   'text' => 'Server error. Please try again or email us directly.'],
    'validation_error' => ['type' => 'error',   'text' => 'Please complete all required fields correctly.'],
    'csrf_error'       => ['type' => 'error',   'text' => 'Session expired. Please refresh the page and try again.'],
    'rate_limited'     => ['type' => 'error',   'text' => 'Too many submissions. Please wait 15 minutes before trying again.'],
];
$status_key = $_GET['status'] ?? '';
$banner = $status_messages[$status_key] ?? null;

// Security headers (supplement .htaccess — belt-and-suspenders)
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wintech Site</title>
    <meta name="description"
          content="Wintech Site provides web development, database solutions, business automation, network infrastructure and professional IT support services.">

    <!--
        SECURITY: Content-Security-Policy
        Restricts what resources the browser is allowed to load.
        Prevents XSS by blocking inline scripts and unauthorized origins.
    -->
    <meta http-equiv="Content-Security-Policy"
          content="
            default-src 'self';
            script-src  'self';
            style-src   'self' https://cdnjs.cloudflare.com;
            img-src     'self' data: https:;
            font-src    'self' https://cdnjs.cloudflare.com;
            connect-src 'self';
            frame-ancestors 'none';
          ">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/png" sizes="16*16" href="logo-16.png">
    <link rel="icon" type="image/png" sizes="32*32" href="logo-32.png">
    <link rel="icon" type="image/png" sizes="48*48" href="logo-48.png">
    <link rel="apple-touch-icon" sizes="180*180" href="apple-touch-icon.png">



    <!--
        PERFORMANCE: Preconnect — tells the browser to open a TCP/TLS
        connection to cdnjs BEFORE it encounters the Font Awesome link tag.
        Saves ~150-300 ms on first load.
    -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    <!--
        PERFORMANCE: Preload hero background (LCP element).
        The browser discovers CSS background images late during paint;
        preloading moves it to the highest-priority fetch queue.
    -->
    <link rel="preload" as="image" href="hero.webp">

    <!--
        Font Awesome loaded as a normal render-blocking stylesheet.
        The previous preload+onload trick relied on an inline event
        handler, which the page's CSP (script-src 'self') silently
        blocks — that prevented the stylesheet from ever activating
        and broke every icon on the site.
    -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    <!-- Main stylesheet -->
    <link rel="stylesheet" href="style.css">
	<script src="script.js" defer></script>
</head>
<body>

    <?php if ($banner): ?>
    <div class="status-banner status-<?= htmlspecialchars($banner['type'], ENT_QUOTES, 'UTF-8') ?>"
         role="alert">
        <?= htmlspecialchars($banner['text'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <header class="navbar-header">
        <div class="nav-container">
            <div class="logo-area">

                <!-- Logo -->
                <img src="logo-512-transparent.png" srcset="logo-256-transparent.png 256w, logo-512-transparent.png 512w, logo-1024-transparent.png 1024w" 
				sizes="(max-width: 600px) 128px, 200px" alt="Wintech Site Logo" width="48" height="48"> 
                
                <div class="brand-text-wrapper">
                    <span class="brand-title">WINTECH SITE</span>
                    <span class="brand-slogan">Reliable I.T. Support, Innovative Development</span>
                </div>
            </div>

            <!-- ACCESSIBILITY: aria-label identifies navigation landmark -->
            <nav class="navigation-menu" aria-label="Main navigation">
                <a href="#home"><i class="fa-solid fa-house" aria-hidden="true"></i> <span>Home</span></a>
                <a href="#about"><i class="fa-solid fa-user" aria-hidden="true"></i> <span>About</span></a>
                <a href="#services"><i class="fa-solid fa-layer-group" aria-hidden="true"></i> <span>Service</span></a>
                <a href="#contact"><i class="fa-solid fa-envelope" aria-hidden="true"></i> <span>Contact</span></a>
            </nav>
        </div>
    </header>

    <main class="main-content-wrapper">

        <section id="home" class="hero-section" aria-label="Hero">
            <div class="hero-inner-overlay">
                <h1 class="hero-headline">
                    Innovative Technology Solutions for Modern Businesses
                </h1>
                <p class="hero-subline-slogan">
                    We design software applications, websites, database systems, networking
                    and IT infrastructure that help businesses automate operations, improve
                    productivity, and achieve sustainable growth.
                </p>
                <div class="hero-buttons">
                    <a href="#services" class="cta-button">Start Your Project</a>
                    <a href="#contact"  class="secondary-button">Free Consultation</a>
                </div>
            </div>
        </section>

        <section id="about" class="about-section">
            <div class="section-container">
                <h2 class="section-heading">ABOUT US</h2>
                <div class="about-grid-layout">

                    <div class="about-text-column">
                        <p class="paragraph-text">
                            Wintech Site is a forward-thinking technology company dedicated to
                            delivering innovative, reliable, and cost-effective digital solutions.
                            We empower businesses and individuals by transforming complex ideas into
                            scalable technology systems that drive sustainable growth.
                        </p>
                    </div>

                    <div class="about-media-column">
                        <div class="team-showcase-wrapper">
                            <!--
                                PERFORMANCE: loading="lazy" defers this below-fold image.
                                decoding="async" lets the browser decode off the main thread.
                                Explicit dimensions prevent CLS.
                            -->
                            <img src="about.webp"
                                 alt="Wintech Site Team Collaborating on a Project"
                                 class="team-project-image"
                                 loading="lazy"
                                 decoding="async">
                            <div class="image-caption-tag">
                                <i class="fa-solid fa-people-group" aria-hidden="true"></i>
                                Wintech Site Team Collaboration
                            </div>
                        </div>
                    </div>

                </div>

                <div class="pillars-container-fullwide">
                    <div class="pillar-wide-row">
                        <div class="pillar-header">
                            <i class="fa-solid fa-book-open icon-story" aria-hidden="true"></i>
                            <h4>Our Story</h4>
                        </div>
                        <p>Founded with a passionate drive for technology and innovation, Wintech Site
                           began as a small IT service provider. Through dedication to our clients, we have
                           grown into a trusted partner offering comprehensive software development, modern
                           web solutions, robust networking, and reliable IT support services.</p>
                    </div>

                    <div class="pillar-wide-row">
                        <div class="pillar-header">
                            <i class="fa-solid fa-eye icon-vision" aria-hidden="true"></i>
                            <h4>Our Vision</h4>
                        </div>
                        <p>To be the foundational digital backbone for growing businesses worldwide, enabling
                           them to operate seamlessly, securely, and without technological boundaries or
                           infrastructural downtime.</p>
                    </div>

                    <div class="pillar-wide-row">
                        <div class="pillar-header">
                            <i class="fa-solid fa-bullseye icon-mission" aria-hidden="true"></i>
                            <h4>Our Mission</h4>
                        </div>
                        <p>To eliminate operational chaos by delivering high-performance software, bulletproof
                           network infrastructure, and intelligent automation—backed by 24/7 proactive support
                           that guarantees total peace of mind.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="service-section section-container">
            <h2 class="section-heading">Our Professional Services</h2>
            <p class="section-subtitle">
                Comprehensive technology solutions designed to help businesses grow, automate operations,
                improve efficiency, and achieve long-term success.
            </p>
            <div class="services-grid-layout">
                 <div class="service-box">
                    <div class="service-icon"><i class="fa-solid fa-laptop-code" aria-hidden="true"></i></div>
                    <h3 class="service-box-title">Software Applications</h3>
                    <p class="service-description">Secure and scalable business applications tailored to your operational requirements.</p>
                </div>
                    <div class="service-box">
                    <div class="service-icon"><i class="fa-solid fa-globe" aria-hidden="true"></i></div>
                    <h3 class="service-box-title">Website Development</h3>
                    <p class="service-description">Professional, responsive and SEO-friendly websites designed to strengthen your online presence.</p>
                </div>
                <div class="service-box">
                    <div class="service-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></div>
                    <h3 class="service-box-title">Database Solutions</h3>
                    <p class="service-description">Reliable database architecture for secure storage, fast performance and seamless integration.</p>
                </div>
                <div class="service-box">
                    <div class="service-icon"><i class="fa-solid fa-gears" aria-hidden="true"></i></div>
                    <h3 class="service-box-title">Business Automation</h3>
                    <p class="service-description">Automate repetitive tasks and improve productivity through intelligent workflows.</p>
                </div>
                <div class="service-box">
                    <div class="service-icon"><i class="fa-solid fa-network-wired" aria-hidden="true"></i></div>
                    <h3 class="service-box-title">Network Installation</h3>
                    <p class="service-description">Secure networking solutions for offices, institutions and growing businesses.</p>
                </div>
                <div class="service-box">
                    <div class="service-icon"><i class="fa-solid fa-headset" aria-hidden="true"></i></div>
                    <h3 class="service-box-title">IT Technical Support</h3>
                    <p class="service-description">Fast, reliable support services that keep your systems running efficiently.</p>
                </div>
            </div>
        </section>

       
		<!-- WHY CHOOSE WINTECH SITE -->
      <section class="why-choose">
      <div class="container">
        <h2>WHY CHOOSE <span>WINTECH SITE</span></h2>

        <div class="value-card" id="valueCard">

            <div class="icon" id="icon">✔</div>

            <h3 id="title">Professional Support</h3>

            <p id="description">
                Our experienced team is always ready to provide fast and reliable technical support.
            </p>

        </div>

        <div class="dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>

    </div>
</section>
		
		

        <section id="contact" class="contact-portal-section">
            <div class="section-container">
                <div class="footer-flex-split">

                    <div class="footer-info-column">
                        <h2 class="footer-heading">CONTACT</h2>
                        <ul class="contact-details-list">
                            <li><i class="fa-solid fa-phone" aria-hidden="true"></i>
                                <span>+256748513443/+256781747432</span></li>
                            <li><i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                <span>info@wintechsite.com</span></li>
                            <li><i class="fa-solid fa-globe" aria-hidden="true"></i>
                                <span>www.wintechsite.com</span></li>
                        </ul>
                    </div>

                    <div class="footer-form-column">
                        <form class="contact-inline-form" action="contact.php" method="POST" novalidate>

                            <!-- SECURITY: CSRF synchronizer token -->
                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

                            <!--
                                SECURITY: Honeypot field — visually hidden from real users,
                                bots that auto-fill forms will populate it and get silently rejected.
                                Uses position:absolute offscreen instead of display:none so some
                                bots don't detect and skip it.
                            -->
                            <div class="hp-field" aria-hidden="true">
                                <label for="hp_website">Leave this field blank</label>
                                <input type="text" id="hp_website" name="website"
                                       tabindex="-1" autocomplete="off" value="">
                            </div>

                            <div class="input-field-group">
                                <input type="text"
                                       name="name"
                                       placeholder="Name"
                                       required
                                       maxlength="100"
                                       autocomplete="name"
                                       class="form-text-input">
                            </div>
                            <div class="input-field-group">
                                <input type="email"
                                       name="email"
                                       placeholder="Email"
                                       required
                                       maxlength="254"
                                       autocomplete="email"
                                       class="form-text-input">
                            </div>
                            <div class="input-field-group">
                                <input type="tel"
                                       name="phone"
                                       placeholder="Phone Number"
                                       maxlength="20"
                                       autocomplete="tel"
                                       class="form-text-input">
                            </div>
                            <div class="input-field-group">
                                <textarea name="message"
                                          placeholder="Message"
                                          rows="4"
                                          required
                                          maxlength="3000"
                                          class="form-textarea-input"></textarea>
                            </div>
                            <button type="submit" class="form-submit-btn">Send Message</button>
                        </form>
                    </div>

                </div>
            </div>
        </section>

    </main>

    <a href="https://wa.me/256748513443"
       class="floating-whatsapp-shortcut"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Chat with Wintech Site on WhatsApp">
        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
    </a>

    <footer class="site-legal-footer">
        <div class="footer-container">
            <!-- PERFORMANCE: Dynamic year via PHP — no JS needed -->
            <p>&copy; <?= date('Y') ?> Wintech Site. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>
