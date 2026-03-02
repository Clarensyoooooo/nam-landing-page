<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$services = getAllServices($conn, true);
$clients  = getAllClients($conn, true);

foreach ($services as &$service) {
    $sid = intval($service['id']);
    $img_result = $conn->query("SELECT * FROM service_images WHERE service_id = $sid ORDER BY sort_order ASC");
    $service['images'] = $img_result ? $img_result->fetch_all(MYSQLI_ASSOC) : [];
    if (empty($service['images']) && !empty($service['image_path'])) {
        $service['images'] = [['image_path' => $service['image_path']]];
    }
}
unset($service);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAM Builders and Supply Corp - Building Excellence, Delivering Quality</title>
    <meta name="description" content="Complete construction and industrial solutions for residential, commercial, and industrial projects.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- ── Header ── -->
    <header id="mainHeader">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-lg">
                <a class="navbar-brand" href="#home">
                    <img src="uploads/nam-logo.png" alt="NAM Builders" onerror="this.style.display='none'">
                    <span>NAM Builders <span style="color:var(--primary-color);">&amp; Supply Corp.</span></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item"><a class="nav-link" href="#home"     data-section="home">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about"    data-section="about">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="#services" data-section="services">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="#clients"  data-section="clients">Clients</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact"  data-section="contact">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- ── Hero ── -->
    <section class="hero" id="home">
        <div class="hero-content">
            <span class="hero-eyebrow">
                <i class="fas fa-hard-hat"></i>
                Trusted Construction Partner
            </span>
            <h1>Built for Business,<br><span class="highlight">Powered by Supply</span></h1>
            <p>Trusted one-stop shop for a wide array of builder and supply needs.</p>
            <div class="hero-buttons">
                <a href="#services" class="btn-primary-main">
                    <i class="fas fa-cogs"></i> Our Services
                </a>
                <a href="#contact" class="btn-secondary-main">
                    <i class="fas fa-paper-plane"></i> Get a Quote
                </a>
            </div>
        </div>

    </section>

    <!-- ── Stats Bar ── -->
    <div class="stats-bar" id="stats">
        <div class="container-lg">
            <div class="stats-grid">
                <div class="stat-item reveal">
                    <span class="stat-number"><span class="counter" data-target="150">0</span>+</span>
                    <span class="stat-label">Projects Completed</span>
                </div>
                <div class="stat-item reveal reveal-delay-1">
                    <span class="stat-number"><span class="counter" data-target="50">0</span>+</span>
                    <span class="stat-label">Happy Clients</span>
                </div>
                <div class="stat-item reveal reveal-delay-2">
                    <span class="stat-number"><span class="counter" data-target="15">0</span>+</span>
                    <span class="stat-label">Years Experience</span>
                </div>
                <div class="stat-item reveal reveal-delay-3">
                    <span class="stat-number"><span class="counter" data-target="8">0</span></span>
                    <span class="stat-label">Service Categories</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── About ── -->
    <section class="light-bg" id="about">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">Who We Are</span>
                <h2>About Us</h2>
                <p>Over a decade of building trust, one project at a time.</p>
            </div>
            <div class="about-content reveal">
                <p style="text-align:center;font-size:1.08rem;color:var(--text-light);max-width:680px;margin:0 auto;">
                    Trusted one-stop shop for a wide array of builder and supply needs. With a strong commitment to high-quality workmanship and efficient progress, we take pride in being your reliable contractor.
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card reveal reveal-delay-1">
                    <div class="feature-icon"><i class="fas fa-medal"></i></div>
                    <h3>Quality Workmanship</h3>
                    <p>Expert craftsmanship and strict quality control in every project we undertake.</p>
                </div>
                <div class="feature-card reveal reveal-delay-2">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Safety First</h3>
                    <p>Committed to maintaining the highest safety standards on every job site.</p>
                </div>
                <div class="feature-card reveal reveal-delay-3">
                    <div class="feature-icon"><i class="fas fa-handshake"></i></div>
                    <h3>Client Satisfaction</h3>
                    <p>Dedicated to exceeding expectations and building lasting partnerships.</p>
                </div>
                <div class="feature-card reveal reveal-delay-4">
                    <div class="feature-icon"><i class="fas fa-rocket"></i></div>
                    <h3>On-Time Delivery</h3>
                    <p>Reliable and efficient project delivery from planning to completion.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Services ── -->
    <section id="services">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">What We Do</span>
                <h2>Our Services</h2>
                <p>Comprehensive solutions tailored to your needs. Click any service to learn more.</p>
            </div>
            <div class="services-grid">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $idx => $sv):
                        $imgs_array = array_map(fn($i) => UPLOADS_URL . $i['image_path'], $sv['images']);
                        $data_imgs  = htmlspecialchars(json_encode($imgs_array), ENT_QUOTES, 'UTF-8');
                        $data_name  = htmlspecialchars($sv['service_name'], ENT_QUOTES, 'UTF-8');
                        $data_desc  = htmlspecialchars($sv['description'],  ENT_QUOTES, 'UTF-8');
                        $first_img  = !empty($sv['images']) ? UPLOADS_URL . $sv['images'][0]['image_path'] : '';
                        $delay      = ($idx % 4);
                    ?>
                    <div class="service-card reveal reveal-delay-<?php echo $delay; ?>"
                         role="button" tabindex="0"
                         data-name="<?php echo $data_name; ?>"
                         data-desc="<?php echo $data_desc; ?>"
                         data-imgs="<?php echo $data_imgs; ?>">
                        <div class="service-image">
                            <?php if ($first_img): ?>
                                <img src="<?php echo $first_img; ?>" alt="<?php echo $data_name; ?>" loading="lazy">
                            <?php else: ?>
                                <div class="svc-img-placeholder"><i class="fas fa-hard-hat"></i></div>
                            <?php endif; ?>
                            <div class="svc-overlay"><i class="fas fa-search-plus"></i></div>
                        </div>
                        <div class="svc-name-bar"><?php echo htmlspecialchars($sv['service_name']); ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="grid-column:1/-1;text-align:center;color:var(--text-light);">No services available.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ── Service Modal ── -->
    <div id="svcModal" role="dialog" aria-modal="true" aria-labelledby="svcmTitle">
        <div class="svcm-box">
            <div class="svcm-left">
                <button class="svcm-close" id="svcmCloseBtn">&times;</button>
                <div class="svcm-slides" id="svcmSlides"></div>
                <div class="svcm-dots"   id="svcmDots"></div>
            </div>
            <div class="svcm-right">
                <h2 class="svcm-title" id="svcmTitle"></h2>
                <div class="svcm-bar"></div>
                <p  class="svcm-desc"  id="svcmDesc"></p>
                <div class="svcm-cta">
                    <a href="#contact" id="svcmQuoteBtn">
                        <i class="fas fa-paper-plane"></i> Get a Quote
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Clients ── -->
    <section class="clients-section" id="clients">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">Who We Work With</span>
                <h2>Our Trusted Clients</h2>
                <p>Partnering with industry leaders to deliver excellence.</p>
            </div>
        </div>

        <!-- Full-width marquee strip (intentionally outside container for edge-to-edge) -->
        <div class="clients-marquee-wrap">
            <div class="clients-marquee-track">
                <?php
                // Render the list TWICE — translateX(-50%) lands exactly back at start
                $loop = array_merge($clients, $clients);
                foreach ($loop as $client):
                ?>
                    <div class="clients-marquee-item">
                        <?php if (!empty($client['image_path'])): ?>
                            <img src="<?php echo UPLOADS_URL . htmlspecialchars($client['image_path']); ?>"
                                 alt="<?php echo sanitize($client['client_name']); ?>"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="clients-marquee-placeholder">
                                <i class="fas fa-building"></i>
                                <span><?php echo sanitize($client['client_name']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ── Contact ── -->
    <section id="contact">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">Reach Out</span>
                <h2>Get In Touch</h2>
                <p>Ready to start your project? Contact us today for a free consultation.</p>
            </div>
            <div class="contact-form reveal">

                <div id="contactSuccessBanner">
                    <i class="fas fa-check-circle"></i>
                    <span id="contactSuccessMsg"></span>
                </div>

                <form id="contactForm" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Full Name</label>
                                <input type="text" name="full_name" id="cf_name" class="form-control" placeholder="Juan dela Cruz" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" id="cf_email" class="form-control" placeholder="juan@example.com" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Phone</label>
                                <input type="tel" name="phone" id="cf_phone" class="form-control" placeholder="+63 9XX XXX XXXX">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-cogs"></i> Service Needed</label>
                                <select name="service_needed" id="cf_service" class="form-control">
                                    <option value="">Select a service</option>
                                    <?php foreach ($services as $sv): ?>
                                        <option value="<?php echo htmlspecialchars($sv['service_name']); ?>">
                                            <?php echo htmlspecialchars($sv['service_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-comment-dots"></i> Message</label>
                        <textarea name="message" id="cf_message" class="form-control" placeholder="Tell us about your project..." required></textarea>
                    </div>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- ── Verification Modal ── -->
    <div id="verifyModal" role="dialog" aria-modal="true" aria-labelledby="vmTitle">
        <div class="vm-box">
            <div class="vm-header">
                <button class="vm-close-btn" id="vmCloseBtn" title="Cancel">&times;</button>
                <div class="vm-icon"><i class="fas fa-shield-alt"></i></div>
                <h3 id="vmTitle">Verify Your Email</h3>
                <p>We sent a 6-digit code to your email address.</p>
            </div>
            <div class="vm-body">
                <div class="vm-alert" id="vmAlert"></div>
                <div class="vm-email-display">
                    <i class="fas fa-envelope"></i>
                    <span id="vmEmailDisplay">—</span>
                </div>
                <span class="vm-code-label">Enter 6-digit code</span>
                <div class="vm-digit-row" id="vmDigitRow">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" id="vd0">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" id="vd1">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" id="vd2">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" id="vd3">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" id="vd4">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" id="vd5">
                </div>
                <div class="vm-progress" id="vmProgress">
                    <div class="vm-prog-dot" id="vp0"></div>
                    <div class="vm-prog-dot" id="vp1"></div>
                    <div class="vm-prog-dot" id="vp2"></div>
                    <div class="vm-prog-dot" id="vp3"></div>
                    <div class="vm-prog-dot" id="vp4"></div>
                    <div class="vm-prog-dot" id="vp5"></div>
                </div>
                <div class="vm-timer" id="vmTimer">
                    Code expires in <strong id="vmCountdown">10:00</strong>
                </div>
                <button class="vm-submit-btn" id="vmVerifyBtn" disabled>
                    <i class="fas fa-check-circle"></i> Verify &amp; Send Message
                </button>
                <div class="vm-resend">
                    Didn't receive the code?
                    <button id="vmResendBtn" disabled>Resend Code</button>
                    <span id="vmResendTimer"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Footer ── -->
    <footer>
        <div class="container-lg">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-building"></i> NAM Builders</h3>
                    <p>Complete construction and industrial solutions with a focus on quality, safety, and client satisfaction.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#home"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="#about"><i class="fas fa-chevron-right"></i> About</a></li>
                        <li><a href="#services"><i class="fas fa-chevron-right"></i> Services</a></li>
                        <li><a href="#clients"><i class="fas fa-chevron-right"></i> Clients</a></li>
                        <li><a href="#contact"><i class="fas fa-chevron-right"></i> Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <div class="contact-info"><i class="fas fa-map-marker-alt"></i><span>Your Address Here, Philippines</span></div>
                    <div class="contact-info"><i class="fas fa-phone"></i><span>+63 9XX XXX XXXX</span></div>
                    <div class="contact-info"><i class="fas fa-envelope"></i><span>info@nambuilders.com</span></div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> NAM Builders and Supply Corp. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/carousel.js"></script>

</body>
</html>