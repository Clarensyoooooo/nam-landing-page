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

// ── Supplies data ──
$sup_cat_result = $conn->query("
    SELECT sc.id, sc.category_name, sc.description, sc.is_active, sc.sort_order,
           COUNT(s.id) AS supply_count
    FROM supply_categories sc
    LEFT JOIN supplies s ON s.category_id = sc.id AND s.is_active = 1
    WHERE sc.is_active = 1
    GROUP BY sc.id
    ORDER BY sc.sort_order ASC
");
$supply_categories = $sup_cat_result ? $sup_cat_result->fetch_all(MYSQLI_ASSOC) : [];

$all_supplies_result = $conn->query("
    SELECT s.*, sc.category_name
    FROM supplies s
    LEFT JOIN supply_categories sc ON s.category_id = sc.id
    WHERE s.is_active = 1 AND sc.is_active = 1
    ORDER BY sc.sort_order ASC, s.sort_order ASC
");
$all_supplies = $all_supplies_result ? $all_supplies_result->fetch_all(MYSQLI_ASSOC) : [];

// ── Updates / Posts ──
$updates_result = $conn->query("
    SELECT * FROM updates
    WHERE is_active = 1
    ORDER BY sort_order ASC, created_at DESC
");
$all_updates = $updates_result ? $updates_result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAM Builders and Supply Corp</title>
    <meta name="description" content="Complete construction and industrial solutions for residential, commercial, and industrial projects.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/updates.css">
</head>
<body>

    <!-- ── Header ── -->
    <header id="mainHeader">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-lg">
                <a class="navbar-brand" href="#home">
                    <img src="css/assets/logo.png" alt="NAM Builders" onerror="this.style.display='none'">
                    <span style="color:var(--primary-dark);">NAM Builders and Supply Corp.</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item"><a class="nav-link" href="#home"     data-section="home">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about"    data-section="about">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="#services" data-section="services">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="#supplies" data-section="supplies">Supplies</a></li>
                        <li class="nav-item"><a class="nav-link" href="#updates"  data-section="updates">Updates</a></li>
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
        <video autoplay muted loop playsinline
            style="position:absolute; inset:0; width:100%; height:100%;
                    object-fit:cover; z-index:0; pointer-events:none;">
            <source src="css/assets/hero-bg.mp4" type="video/mp4">
        </video>
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
                                <h4><i class="fas fa-chart-line"></i> Objectives</h4>
                                <p>Our primary goal is to consistently attain sustainable, long-term growth in cash flow, aimed at maximizing returns for our valued investors. As we pursue this financial success, we are deeply committed to upholding stringent standards of environmental responsibility, safety, and health compliance throughout all our operations.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="stats-bar" id="stats" style="border-radius: 12px; margin: 3rem 0 2rem;">
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
                            <span class="stat-number"><span class="counter" data-target="6">0</span></span>
                            <span class="stat-label">Service Categories</span>
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

    <!-- ══════════════════════════════════════════════
         SERVICES — Horizontal scroll hijack
    ══════════════════════════════════════════════ -->
    <section id="services">

        <!--
            .svc-sticky uses top:72px so the section's "What We Do / Our Services"
            header is the first thing that hits the top of the screen — freeze starts
            exactly there, not mid-page.
        -->
        <div class="svc-sticky" id="svcSticky">

            <!-- Section header -->
            <div class="svc-header">
                <div>
                    <p class="svc-eyebrow">What We Do</p>
                    <h2 class="svc-title">Our Services</h2>
                    <p class="svc-subtitle">Comprehensive solutions tailored to your needs. Scroll to explore.</p>
                </div>
                <div class="svc-meta">
                    <span class="svc-counter" id="svcCounter">01 / <?php echo str_pad(count($services) ?: 1, 2, '0', STR_PAD_LEFT); ?></span>
                    <div class="svc-progress-bar">
                        <div class="svc-progress-fill" id="svcProgressFill"></div>
                    </div>
                    <!-- Arrow navigation buttons -->
                    <div class="svc-arrows">
                        <button class="svc-arrow-btn" id="svcBtnPrev" aria-label="Previous service" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="svc-arrow-btn" id="svcBtnNext" aria-label="Next service">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Scrolling cards track -->
            <div class="svc-viewport">
                <div class="svc-track" id="svcTrack">

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
                            $num = str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
                        ?>
                        <div class="svc-card"
                             data-index="<?php echo $idx; ?>"
                             data-name="<?php echo htmlspecialchars($sv['service_name']); ?>"
                             data-desc="<?php echo htmlspecialchars(strip_tags($sv['description'])); ?>"
                             data-imgs='<?php echo json_encode($all_images); ?>'>

                            <span class="svc-card-badge"><?php echo $num; ?></span>

                            <div class="svc-card-img">
                                <?php if ($first_img): ?>
                                    <img src="<?php echo $first_img; ?>"
                                         alt="<?php echo htmlspecialchars($sv['service_name']); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="svc-card-placeholder">
                                        <i class="fas fa-hard-hat"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="svc-card-label">
                                <span class="svc-card-tag">Service</span>
                                <h3 class="svc-card-name"><?php echo htmlspecialchars($sv['service_name']); ?></h3>
                                <span class="svc-card-cta">Learn more <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php
                        $placeholders = [
                            ['General Construction','fa-hard-hat'],
                            ['Custom Building','fa-building'],
                            ['Outdoor Design','fa-tree'],
                            ['Interior Remodeling','fa-couch'],
                            ['Roofing Services','fa-tools'],
                        ];
                        foreach ($placeholders as $pi => $ph):
                            $num = str_pad($pi+1,2,'0',STR_PAD_LEFT);
                        ?>
                        <div class="svc-card" data-index="<?php echo $pi; ?>">
                            <span class="svc-card-badge"><?php echo $num; ?></span>
                            <div class="svc-card-img">
                                <div class="svc-card-placeholder">
                                    <i class="fas <?php echo $ph[1]; ?>"></i>
                                </div>
                            </div>
                            <div class="svc-card-label">
                                <span class="svc-card-tag">Service</span>
                                <h3 class="svc-card-name"><?php echo $ph[0]; ?></h3>
                                <span class="svc-card-cta">Learn more <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Navigation dots -->
            <div class="svc-dots" id="svcDots"></div>

            <!-- Scroll hint (fades once user starts scrolling) -->
            <div class="svc-scroll-hint" id="svcScrollHint">
                <span>Scroll to explore</span>
                <div class="svc-hint-icon"><i class="fas fa-arrow-right"></i></div>
            </div>

        </div><!-- /svc-sticky -->
    </section><!-- /services -->

    <!-- ── Service Detail Modal ── -->
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

    <!-- ── Supplies Section ── -->
    <section id="supplies">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">What We Supply</span>
                <h2>Our Supply Catalog</h2>
                <p>Sourcing quality construction, electrical, safety, and office supplies for every project need.</p>
            </div>

            <?php if (!empty($supply_categories)): ?>
            <div class="sup-filter-bar reveal">
                <button class="sup-filter-pill active" data-filter="all">
                    <i class="fas fa-layer-group"></i>
                    <span>All</span>
                    <em><?php echo count($all_supplies); ?></em>
                </button>
                <?php foreach ($supply_categories as $scat): ?>
                    <?php if ($scat['supply_count'] > 0): ?>
                    <button class="sup-filter-pill" data-filter="<?php echo $scat['id']; ?>"
                            style="--cat-color:#1565C0;">
                        <i class="fas fa-tag"></i>
                        <span><?php echo htmlspecialchars($scat['category_name']); ?></span>
                        <em><?php echo $scat['supply_count']; ?></em>
                    </button>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="sup-cards-grid" id="supCardsGrid">
                <?php if (!empty($all_supplies)): ?>
                    <?php foreach ($all_supplies as $i => $sup):
                        $imgSrc = !empty($sup['image_path']) ? UPLOADS_URL . htmlspecialchars($sup['image_path']) : '';
                        $delay  = ($i % 4) * 0.07;
                    ?>
                    <div class="sup-item reveal"
                        data-cat="<?php echo $sup['category_id']; ?>"
                        style="animation-delay:<?php echo $delay; ?>s;">
                        <div class="sup-item-inner">
                            <div class="sup-item-img" style="--cat-bg:#1565C0;">
                                <?php if ($imgSrc): ?>
                                    <img src="<?php echo $imgSrc; ?>"
                                        alt="<?php echo htmlspecialchars($sup['supply_name']); ?>"
                                        loading="lazy"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="sup-item-icon-placeholder" style="display:none;">
                                        <i class="fas fa-box"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="sup-item-icon-placeholder">
                                        <i class="fas fa-box"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="sup-item-cat-badge" style="background:#1565C0;">
                                    <i class="fas fa-tag"></i>
                                    <?php echo htmlspecialchars($sup['category_name'] ?? ''); ?>
                                </span>
                            </div>
                            <div class="sup-item-body">
                                <h4 class="sup-item-name"><?php echo htmlspecialchars($sup['supply_name']); ?></h4>
                                <?php if (!empty($sup['description'])): ?>
                                    <p class="sup-item-desc"><?php echo htmlspecialchars($sup['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="sup-item-hover">
                                <button class="sup-inquire-btn" onclick="openSupplyInquiry('<?php echo htmlspecialchars(addslashes($sup['supply_name'])); ?>')">
                                    <i class="fas fa-paper-plane"></i> Inquire
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="sup-empty-state">
                        <i class="fas fa-boxes"></i>
                        <p>Supply catalog coming soon.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div id="supNoResultsPublic" style="display:none; text-align:center; color:var(--text-light); padding:3rem 1rem;">
                <i class="fas fa-search" style="font-size:2.5rem; margin-bottom:.75rem; display:block; opacity:.4;"></i>
                No supplies found in this category.
            </div>
            <?php else: ?>
                <div style="text-align:center; color:var(--text-light); padding:2rem;">
                    Supply catalog is being updated. Please check back soon.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── Clients ── -->
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

    <!-- ════════════════════════════════════════════════════════
         UPDATES / POSTS
    ════════════════════════════════════════════════════════ -->
    <section id="updates">
        <div class="container-lg">

            <div class="upd-section-header reveal">
                <div>
                    <span class="section-tag">Latest News</span>
                    <h2 class="upd-section-title">Updates &amp; <span class="upd-title-accent">Posts</span></h2>
                    <p class="upd-section-sub">Stay informed on our latest projects, announcements, and milestones.</p>
                </div>
                <?php if (count($all_updates) > 3): ?>
                <button class="upd-see-all-btn" id="updSeeAllBtn">
                    View All <i class="fas fa-arrow-right"></i>
                </button>
                <?php endif; ?>
            </div>

            <?php if (!empty($all_updates)): ?>
            <div class="upd-grid" id="updGrid">
                <?php foreach ($all_updates as $ui => $upd):
                    $imgSrc = !empty($upd['image_path']) ? UPLOADS_URL . htmlspecialchars($upd['image_path']) : '';
                    $date   = date('M j, Y', strtotime($upd['created_at']));
                    $hidden = $ui >= 3 ? ' upd-card-hidden' : '';
                ?>
                <article class="upd-card reveal reveal-delay-<?php echo ($ui % 3) + 1; ?><?php echo $hidden; ?>"
                         data-id="<?php echo $upd['id']; ?>"
                         data-title="<?php echo htmlspecialchars($upd['title']); ?>"
                         data-desc="<?php echo htmlspecialchars($upd['description']); ?>"
                         data-img="<?php echo $imgSrc; ?>"
                         data-date="<?php echo $date; ?>">
                    <div class="upd-card-inner">
                        <div class="upd-card-img-wrap">
                            <?php if ($imgSrc): ?>
                                <img src="<?php echo $imgSrc; ?>"
                                     alt="<?php echo htmlspecialchars($upd['title']); ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="upd-img-placeholder">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                            <?php endif; ?>
                            <div class="upd-date-badge">
                                <i class="fas fa-calendar-alt"></i>
                                <?php echo $date; ?>
                            </div>
                        </div>
                        <div class="upd-card-body">
                            <h3 class="upd-card-title"><?php echo htmlspecialchars($upd['title']); ?></h3>
                            <p class="upd-card-desc"><?php echo htmlspecialchars($upd['description']); ?></p>
                            <button class="upd-read-btn">Read More <i class="fas fa-arrow-right"></i></button>
                        </div>
                        <div class="upd-card-accent"></div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php if (count($all_updates) > 3): ?>
            <div class="upd-toggle-wrap" id="updToggleWrap" style="display:none;">
                <button class="upd-toggle-btn" id="updToggleBtn" onclick="toggleUpdates()">
                    <span id="updToggleLabel">Show All <?php echo count($all_updates); ?> Posts</span>
                    <i class="fas fa-chevron-down" id="updToggleIcon"></i>
                </button>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <div style="text-align:center; color:var(--text-light); padding:3rem 1rem;">
                <i class="fas fa-newspaper" style="font-size:3rem; margin-bottom:1rem; display:block; opacity:.3;"></i>
                No updates yet. Check back soon!
            </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- Updates Detail Modal -->
    <div id="updModal" role="dialog" aria-modal="true" aria-labelledby="updModalTitle">
        <div class="updm-box">
            <button class="updm-close" id="updModalClose">&times;</button>
            <div class="updm-img-wrap" id="updModalImgWrap">
                <img id="updModalImg" src="" alt="" style="display:none;">
                <div class="updm-img-placeholder" id="updModalImgPlaceholder"><i class="fas fa-newspaper"></i></div>
            </div>
            <div class="updm-body">
                <div class="updm-meta"><i class="fas fa-calendar-alt"></i> <span id="updModalDate"></span></div>
                <h2 class="updm-title" id="updModalTitle"></h2>
                <div class="updm-divider"></div>
                <p class="updm-desc" id="updModalDesc"></p>

            </div>
        </div>
    </div>

    <!-- ── Founder / CEO ── -->
    <section id="founder">
        <div class="container-lg">
            <div class="founder-wrap">
                <div class="founder-text-col founder-reveal-text">
                    <span class="section-tag">Leadership</span>
                    <div class="founder-card">
                        <div class="founder-quote-icon">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <blockquote class="founder-quote">
                            "We don't just build structures — we build trust, relationships, and futures. Every project we take on is a reflection of our unwavering commitment to excellence, safety, and the people we serve."
                        </blockquote>
                        <div class="founder-rule"></div>
                        <div class="founder-identity">
                            <div class="founder-initials">N</div>
                            <div>
                                <h3 class="founder-name">Alberto Molinyawe Jr</h3>
                                <span class="founder-title">Founder &amp; Chief Executive Officer</span>
                                <div class="founder-socials">
                                    <a href="#" class="founder-social" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                    <a href="#" class="founder-social" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="founder-photo-col founder-reveal-photo">
                    <div class="founder-photo-frame">
                        <div class="founder-photo-bg-accent"></div>
                        <div class="founder-photo-wrap">
                            <img src="css/assets/hero-bg.jpg"
                                alt="NAM Builders Founder & CEO"
                                onerror="this.src='https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&q=80'">
                        </div>
                        <div class="founder-badge-float">
                            <i class="fas fa-award"></i>
                            <span>Founder &amp; CEO</span>
                        </div>
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
                            <span>RNA Building Brgy. Santiago Malvar, Batangas</span>
                        </div>
                        <div class="cm-info-item">
                            <div class="cm-info-icon"><i class="fas fa-phone"></i></div>
                            <span>09230209877 / 09385314311 / 09568365775 / 09461704399</span>
                        </div>
                        <div class="cm-info-item">
                            <div class="cm-info-icon"><i class="fas fa-envelope"></i></div>
                            <span>nam.nswt@myahoo.com</span>
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
                    <div class="contact-info"><i class="fas fa-map-marker-alt"></i><span>RNA Building Brgy. Santiago Malvar, Batangas</span></div>
                    <div class="contact-info"><i class="fas fa-phone"></i><span>09230209877 / 09385314311 / 09568365775 / 09461704399</span></div>
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
    <script src="js/updates.js"></script>

</body>
</html>