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
                    <img src="css/assets/logo.png" alt="NAM Builders" onerror="this.style.display='none'">
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
                        <li class="nav-item"><a class="nav-link" href="#team"  data-section="team">Team</a></li>
                        <li class="nav-item ms-lg-2">
                            <button class="btn-contact-nav" id="navContactBtn" type="button">
                                <i class="fas fa-paper-plane"></i> Contact Us
                            </button>
                        </li>
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
            <p>Complete construction and industrial solutions for residential, commercial, and industrial projects.</p>
            <div class="hero-buttons">
                <a href="#services" class="btn-primary-main">
                    <i class="fas fa-cogs"></i> Our Services
                </a>
                <button type="button" class="btn-secondary-main" id="heroContactBtn">
                    <i class="fas fa-paper-plane"></i> Inquire Now
                </button>
            </div>
        </div>
        <div class="hero-scroll-hint">
            <small>Scroll</small>
            <span><i class="fas fa-chevron-down"></i></span>
        </div>
    </section>

    <section class="clients-section" id="clients">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">Who We Work With</span>
                <h2>Our Trusted Clients</h2>
                <p>Partnering with industry leaders to deliver excellence.</p>
            </div>
        </div>
        <div class="clients-marquee-wrap">
            <div class="clients-marquee-track">
                <?php
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

    <!-- ── About ── -->
    <section class="light-bg" id="about">
        <div class="container-lg">
            <div class="about-two-col">
                <div class="about-img-col reveal">
                    <div class="about-img-wrap">
                        <img src="css/assets/about-bg.jpg"
                             alt="NAM Builders team"
                             onerror="this.src='https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=700&q=80'">
                        <div class="about-badge">
                            <span class="about-badge-num">15<sup>+</sup></span>
                            <span class="about-badge-label">Years of<br>Excellence</span>
                        </div>
                    </div>
                </div>
                <div class="about-text-col reveal reveal-delay-1">
                    <span class="section-tag">Who We Are</span>
                    <h2 class="about-heading">About Us</h2>
                    <div class="about-title-rule"></div>
                    <p class="about-intro">
                        NAM Builders and Supply Corp is a leading construction and industrial services company providing complete solutions for residential, commercial, and industrial projects. We specialize in general construction, renovation, electrical systems, fire protection, steel fabrication, office fit-outs, and building maintenance.
                    </p>
                    <div class="about-rule"></div>
                    <div class="vmo-triggers">
                        <button class="vmo-trigger active" data-vmo="vision">
                            <div class="vmo-trigger-icon"><i class="fas fa-eye"></i></div>
                            <span>Vision</span>
                        </button>
                        <div class="vmo-trigger-sep"></div>
                        <button class="vmo-trigger" data-vmo="mission">
                            <div class="vmo-trigger-icon"><i class="fas fa-bullseye"></i></div>
                            <span>Mission</span>
                        </button>
                        <div class="vmo-trigger-sep"></div>
                        <button class="vmo-trigger" data-vmo="objectives">
                            <div class="vmo-trigger-icon"><i class="fas fa-chart-line"></i></div>
                            <span>Objectives</span>
                        </button>
                    </div>
                    <div class="vmo-accordion">
                        <div class="vmo-panel vmo-vision open" id="vmo-vision">
                            <div class="vmo-panel-inner">
                                <h4><i class="fas fa-eye"></i> Vision</h4>
                                <p>We envision a future where the property maintenance industry is synonymous with positive change and relentless innovation. This vision drives us to redefine the standard of service quality and consistency that clients can rightfully expect from a company. Our unwavering commitment to honesty, integrity, and transparency serves as the cornerstone of trust as we work towards this vision.</p>
                            </div>
                        </div>
                        <div class="vmo-panel vmo-mission" id="vmo-mission">
                            <div class="vmo-panel-inner">
                                <h4><i class="fas fa-bullseye"></i> Mission</h4>
                                <p>Our mission is to cultivate enduring relationships with our valued customers. This mission complements our vision and objectives, emphasizing the paramount importance of customer satisfaction and stringent quality control. Every day, we strive to not only meet but exceed your expectations, ensuring your trust and peace of mind in our journey towards a transformed property maintenance industry.</p>
                            </div>
                        </div>
                        <div class="vmo-panel vmo-objectives" id="vmo-objectives">
                            <div class="vmo-panel-inner">
                                <h4><i class="fas fa-chart-line"></i> Business Objectives</h4>
                                <p>Our primary goal is to consistently attain sustainable, long-term growth in cash flow, aimed at maximizing returns for our valued investors. As we pursue this financial success, we are deeply committed to upholding stringent standards of environmental responsibility, safety, and health compliance throughout all our operations.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-title reveal">
                <h2>Our Values</h2>
                <p>At NAM Builders and Supply Corp., our commitment is rooted in a set of core values that drive our business.</p>
            </div>
            <div class="values-orbit">
                <div class="val-item val-left-1 reveal reveal-delay-1">
                    <div class="val-bubble">
                        <div class="val-icon"><i class="fas fa-graduation-cap"></i></div>
                        <h4>Professional Development &amp; Personal Skills</h4>
                        <p>We are committed to advancing our talents and skills to their fullest potential, whether as individuals, professionals, or managers.</p>
                    </div>
                </div>
                <div class="val-item val-top reveal reveal-delay-2">
                    <div class="val-bubble">
                        <div class="val-icon"><i class="fas fa-star"></i></div>
                        <h4>Quality</h4>
                        <p>We uphold the highest standards of professional excellence, ensuring the quality of our work aligns with the project's objectives.</p>
                    </div>
                </div>
                <div class="val-item val-right-1 reveal reveal-delay-1">
                    <div class="val-bubble">
                        <div class="val-icon"><i class="fas fa-smile"></i></div>
                        <h4>Customer Satisfaction</h4>
                        <p>We go above and beyond to exceed the expectations of our customers, both internally and externally, by proactively anticipating, understanding, and responding to their needs.</p>
                    </div>
                </div>
                <div class="val-item val-left-2 reveal reveal-delay-1">
                    <div class="val-bubble">
                        <div class="val-icon"><i class="fas fa-lightbulb"></i></div>
                        <h4>Entrepreneurial</h4>
                        <p>We encourage creativity, flexibility, and innovative thinking in our approach to challenges and opportunities.</p>
                    </div>
                </div>
                <div class="val-center reveal reveal-delay-2">
                    <div class="val-center-ring">
                        <div class="val-center-inner">
                            <img src="css/assets/logo.png"
                                 alt="NAM Builders and Supply Corp."
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="val-logo-placeholder" style="display:none;">
                                <i class="fas fa-building"></i>
                                <span>NAM</span>
                                <small>Builders &amp; Supply Corp.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="val-item val-right-2 reveal reveal-delay-1">
                    <div class="val-bubble">
                        <div class="val-icon"><i class="fas fa-comments"></i></div>
                        <h4>Communication</h4>
                        <p>We believe in transparent and honest communication, providing information openly and candidly.</p>
                    </div>
                </div>
                <div class="val-item val-left-3 reveal reveal-delay-1">
                    <div class="val-bubble">
                        <div class="val-icon"><i class="fas fa-sun"></i></div>
                        <h4>Attitude</h4>
                        <p>We approach our work with a positive and enthusiastic spirit, bringing vibrancy to every task.</p>
                    </div>
                </div>
                <div class="val-item val-bottom reveal reveal-delay-2">
                    <div class="val-bubble">
                        <div class="val-icon"><i class="fas fa-users"></i></div>
                        <h4>Teamwork</h4>
                        <p>We foster a collaborative environment where each team member focuses on a common goal, working together to achieve success.</p>
                    </div>
                </div>
                <div class="val-item val-right-3 reveal reveal-delay-1">
                    <div class="val-bubble">
                        <div class="val-icon"><i class="fas fa-hands"></i></div>
                        <h4>Respect</h4>
                        <p>We demonstrate respect for others through our actions, treating everyone with consideration and professionalism.</p>
                    </div>
                </div>
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

    <!-- ── Services ── -->
    <section id="services">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">What We Do</span>
                <h2>Our Services</h2>
                <p>Comprehensive solutions tailored to your needs. Click any service to learn more.</p>
            </div>
            <div class="services-modern-grid">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $idx => $sv):
                        $first_img = !empty($sv['images']) 
                            ? UPLOADS_URL . $sv['images'][0]['image_path'] 
                            : '';
                        $all_images = [];
                        if (!empty($sv['images'])) {
                            foreach ($sv['images'] as $img) {
                                $all_images[] = UPLOADS_URL . $img['image_path'];
                            }
                        }
                        $delay = ($idx % 3);
                    ?>
                    <div class="service-modern-card reveal reveal-delay-<?php echo $delay; ?>"
                        data-name="<?php echo htmlspecialchars($sv['service_name']); ?>"
                        data-desc="<?php echo htmlspecialchars(strip_tags($sv['description'])); ?>"
                        data-imgs='<?php echo json_encode($all_images); ?>'>
                        <div class="service-modern-image">
                            <?php if ($first_img): ?>
                                <img src="<?php echo $first_img; ?>" 
                                    alt="<?php echo htmlspecialchars($sv['service_name']); ?>" 
                                    loading="lazy">
                            <?php else: ?>
                                <div class="service-img-placeholder">
                                    <i class="fas fa-hard-hat"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="service-modern-content">
                            <h4><?php echo htmlspecialchars($sv['service_name']); ?></h4>
                            <a href="javascript:void(0);" class="service-read-more">
                                READ MORE <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
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
                    <button type="button" id="svcmQuoteBtn" class="btn-primary-main" style="border:none; cursor:pointer;">
                        <i class="fas fa-paper-plane"></i> Inquire Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Team ── -->
    <section id="team">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">The People Behind the Work</span>
                <h2>Our Team</h2>
                <p>Dedicated professionals committed to delivering excellence on every project.</p>
            </div>
            <div class="team-grid">
                <div class="team-card reveal reveal-delay-1">
                    <div class="team-photo">
                        <img src="css/assets/hero-bg.jpg" alt="Team Member">
                        <div class="team-hover-overlay">
                            <div class="team-socials">
                                <a href="https://www.facebook.com/k8.kit" class="team-social-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="team-social-btn" title="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                                <a href="#" class="team-social-btn" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                            <p class="team-hover-quote">"Excellence is not a skill, it's an attitude."</p>
                        </div>
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">NAM</h4>
                        <span class="team-role">Project Manager</span>
                    </div>
                </div>
                <div class="team-card reveal reveal-delay-2">
                    <div class="team-photo">
                        <img src="css/assets/hero-bg.jpg" alt="Team Member">
                        <div class="team-hover-overlay">
                            <div class="team-socials">
                                <a href="#" class="team-social-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="team-social-btn" title="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                                <a href="#" class="team-social-btn" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                            <p class="team-hover-quote">"Building trust, one project at a time."</p>
                        </div>
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">NAM</h4>
                        <span class="team-role">Founder, CEO</span>
                    </div>
                </div>
                <div class="team-card reveal reveal-delay-3">
                    <div class="team-photo">
                        <img src="css/assets/hero-bg.jpg" alt="Team Member">
                        <div class="team-hover-overlay">
                            <div class="team-socials">
                                <a href="#" class="team-social-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="team-social-btn" title="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                                <a href="#" class="team-social-btn" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                            <p class="team-hover-quote">"Quality is never an accident."</p>
                        </div>
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">NAM</h4>
                        <span class="team-role">Lead Engineer</span>
                    </div>
                </div>
                <div class="team-card reveal reveal-delay-4">
                    <div class="team-photo">
                        <img src="css/assets/hero-bg.jpg" alt="Team Member">
                        <div class="team-hover-overlay">
                            <div class="team-socials">
                                <a href="#" class="team-social-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="team-social-btn" title="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                                <a href="#" class="team-social-btn" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                            <p class="team-hover-quote">"Safety first, quality always."</p>
                        </div>
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">NAM</h4>
                        <span class="team-role">Safety Officer</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Contact Modal ── -->
    <div id="contactModal" role="dialog" aria-modal="true" aria-labelledby="contactModalTitle">
        <div class="cm-box">
            <div class="cm-left">
                <div class="cm-left-inner">
                    <div class="cm-left-logo">
                        <img src="css/assets/logo.png" alt="NAM Builders"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="cm-logo-fallback" style="display:none;">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    <h3>Get In Touch</h3>
                    <p>Ready to start your project? Send us a message and we'll get back to you soon.</p>
                    <div class="cm-info-list">
                        <div class="cm-info-item">
                            <div class="cm-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <span>Your Address Here, Philippines</span>
                        </div>
                        <div class="cm-info-item">
                            <div class="cm-info-icon"><i class="fas fa-phone"></i></div>
                            <span>+63 9XX XXX XXXX</span>
                        </div>
                        <div class="cm-info-item">
                            <div class="cm-info-icon"><i class="fas fa-envelope"></i></div>
                            <span>info@nambuilders.com</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cm-right">
                <button class="cm-close" id="contactModalCloseBtn" title="Close">&times;</button>
                <div class="cm-right-inner">
                    <span class="section-tag">Reach Out</span>
                    <h2 id="contactModalTitle">Send Us a Message</h2>
                    <div class="cm-title-rule"></div>
                    <div id="contactSuccessBanner">
                        <i class="fas fa-check-circle"></i>
                        <span id="contactSuccessMsg"></span>
                    </div>
                    <form id="contactForm" novalidate>
                        <div class="cm-row">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Full Name</label>
                                <input type="text" name="full_name" id="cf_name" class="form-control" placeholder="Juan dela Cruz" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" id="cf_email" class="form-control" placeholder="juan@example.com" required>
                            </div>
                        </div>
                        <div class="cm-row">
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Phone</label>
                                <input type="tel" name="phone" id="cf_phone" class="form-control" placeholder="+63 9XX XXX XXXX">
                            </div>
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
        </div>
    </div>

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
                        <li><a href="javascript:void(0);" id="footerContactLink"><i class="fas fa-chevron-right"></i> Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <div class="contact-info"><i class="fas fa-map-marker-alt"></i><span>RNA Building Brgy. Santiago Malvar, Batangas</span><span>Poblacion Brgy. 4, Tanauan City, Batangas</span></div>
                    <div class="contact-info"><i class="fas fa-phone"></i><span>09230209877</span><span>/ 09385314311</span><span>/ 09568365775</span><span>/ 09461704399</span></div>
                    <div class="contact-info"><i class="fas fa-envelope"></i><span>nam.nswt@myahoo.com</span></div>
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