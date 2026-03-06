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
    <title>NAM Builders and Supply Corp</title>
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
        <!-- Video Background -->
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

            <!-- ── Stats Bar ── -->
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
                            <span class="stat-number"><span class="counter" data-target="8">0</span></span>
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

    <!-- ════════════════════════════════════════════════════════
        SUPPLIES SECTION  —  paste into index.php
        Place this block AFTER the #clients section and BEFORE #founder
        ════════════════════════════════════════════════════════ -->

    <!-- ══ PHP data-fetching block — place at top of index.php with other queries ══
    <?php
    // Fetch supply categories with their supplies
    $sup_cat_result = $conn->query("
        SELECT sc.*,
            COUNT(s.id) AS supply_count
        FROM supply_categories sc
        LEFT JOIN supplies s ON s.category_id = sc.id AND s.is_active = 1
        WHERE sc.is_active = 1
        GROUP BY sc.id
        ORDER BY sc.sort_order ASC
    ");
    $supply_categories = $sup_cat_result ? $sup_cat_result->fetch_all(MYSQLI_ASSOC) : [];

    $all_supplies_result = $conn->query("
        SELECT s.*, sc.category_name, sc.color_hex, sc.icon_class
        FROM supplies s
        LEFT JOIN supply_categories sc ON s.category_id = sc.id
        WHERE s.is_active = 1 AND sc.is_active = 1
        ORDER BY sc.sort_order ASC, s.sort_order ASC
    ");
    $all_supplies = $all_supplies_result ? $all_supplies_result->fetch_all(MYSQLI_ASSOC) : [];
    ?>
    ══ end PHP block ══ -->

    <section id="supplies">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">What We Supply</span>
                <h2>Our Supply Catalog</h2>
                <p>Sourcing quality construction, electrical, safety, and office supplies for every project need.</p>
            </div>

            <?php if (!empty($supply_categories)): ?>

            <!-- ── Category filter pills ── -->
            <div class="sup-filter-bar reveal">
                <button class="sup-filter-pill active" data-filter="all">
                    <i class="fas fa-layer-group"></i>
                    <span>All</span>
                    <em><?php echo count($all_supplies); ?></em>
                </button>
                <?php foreach ($supply_categories as $scat): ?>
                    <?php if ($scat['supply_count'] > 0): ?>
                    <button class="sup-filter-pill" data-filter="<?php echo $scat['id']; ?>"
                            style="--cat-color:<?php echo htmlspecialchars($scat['color_hex']); ?>;">
                        <i class="<?php echo htmlspecialchars($scat['icon_class']); ?>"></i>
                        <span><?php echo htmlspecialchars($scat['category_name']); ?></span>
                        <em><?php echo $scat['supply_count']; ?></em>
                    </button>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- ── Supply cards grid ── -->
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
                            <!-- image / icon area -->
                            <div class="sup-item-img" style="--cat-bg:<?php echo htmlspecialchars($sup['color_hex'] ?? '#1565C0'); ?>;">
                                <?php if ($imgSrc): ?>
                                    <img src="<?php echo $imgSrc; ?>"
                                        alt="<?php echo htmlspecialchars($sup['supply_name']); ?>"
                                        loading="lazy">
                                <?php else: ?>
                                    <div class="sup-item-icon-placeholder">
                                        <i class="<?php echo htmlspecialchars($sup['icon_class'] ?? 'fas fa-box'); ?>"></i>
                                    </div>
                                <?php endif; ?>
                                <!-- category badge -->
                                <span class="sup-item-cat-badge"
                                    style="background:<?php echo htmlspecialchars($sup['color_hex'] ?? '#1565C0'); ?>;">
                                    <i class="<?php echo htmlspecialchars($sup['icon_class'] ?? 'fas fa-box'); ?>"></i>
                                    <?php echo htmlspecialchars($sup['category_name'] ?? ''); ?>
                                </span>
                            </div>
                            <!-- text -->
                            <div class="sup-item-body">
                                <h4 class="sup-item-name"><?php echo htmlspecialchars($sup['supply_name']); ?></h4>
                                <?php if (!empty($sup['unit'])): ?>
                                    <span class="sup-item-unit">
                                        <i class="fas fa-ruler-combined"></i>
                                        per <?php echo htmlspecialchars($sup['unit']); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($sup['description'])): ?>
                                    <p class="sup-item-desc"><?php echo htmlspecialchars($sup['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <!-- hover overlay cta -->
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

            <!-- No-results message -->
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

    <!-- ════════════════════════════════════════════════════════
        CSS — add to css/style.css
        ════════════════════════════════════════════════════════ -->
    <style id="supplies-section-styles">
    /* ══════════════════════════════════════════════════
    SUPPLIES SECTION
    ══════════════════════════════════════════════════ */
    #supplies {
        background: #fff;
        padding: 90px 0;
    }

    /* ── Filter pill bar ── */
    .sup-filter-bar {
        display: flex;
        gap: .55rem;
        flex-wrap: wrap;
        justify-content: center;
        margin-bottom: 2.8rem;
    }

    .sup-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .5rem 1.1rem;
        border-radius: 50px;
        border: 1.5px solid var(--border-color);
        background: #fff;
        color: var(--text-light);
        font-size: .82rem;
        font-weight: 700;
        font-family: 'Barlow', sans-serif;
        cursor: pointer;
        transition: all .22s ease;
        letter-spacing: .02em;
        line-height: 1;
    }

    .sup-filter-pill em {
        font-style: normal;
        background: var(--light-bg);
        color: var(--text-light);
        border-radius: 50px;
        padding: .1rem .42rem;
        font-size: .7rem;
        font-weight: 800;
        transition: background .22s, color .22s;
    }

    .sup-filter-pill:hover,
    .sup-filter-pill.active {
        border-color: var(--cat-color, var(--primary-color));
        background: var(--cat-color, var(--primary-color));
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,.15);
    }

    .sup-filter-pill:hover em,
    .sup-filter-pill.active em {
        background: rgba(255,255,255,.22);
        color: #fff;
    }

    /* ── Cards grid ── */
    .sup-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.4rem;
    }

    /* ── Individual supply card ── */
    .sup-item {
        /* used by JS filter */
    }

    .sup-item-inner {
        background: #fff;
        border: 1.5px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
        position: relative;
        transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        cursor: pointer;
    }

    .sup-item-inner:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 42px rgba(0,0,0,.12);
        border-color: var(--primary-color);
    }

    /* image / icon placeholder */
    .sup-item-img {
        height: 160px;
        background: linear-gradient(135deg,
            color-mix(in srgb, var(--cat-bg, #1565C0) 10%, #f4f7fb),
            color-mix(in srgb, var(--cat-bg, #1565C0) 5%, #fff));
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sup-item-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }

    .sup-item-inner:hover .sup-item-img img {
        transform: scale(1.06);
    }

    .sup-item-icon-placeholder {
        font-size: 2.8rem;
        color: var(--cat-bg, #1565C0);
        opacity: .25;
        transition: opacity .3s, transform .3s;
    }

    .sup-item-inner:hover .sup-item-icon-placeholder {
        opacity: .38;
        transform: scale(1.1);
    }

    /* category badge on the image */
    .sup-item-cat-badge {
        position: absolute;
        bottom: 10px;
        left: 10px;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #fff;
        padding: .22rem .6rem;
        border-radius: 50px;
        white-space: nowrap;
        max-width: calc(100% - 20px);
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* text body */
    .sup-item-body {
        padding: 1rem 1rem .9rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: .35rem;
    }

    .sup-item-name {
        font-size: .9rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.35;
    }

    .sup-item-unit {
        font-size: .72rem;
        font-weight: 700;
        color: var(--text-light);
        display: inline-flex;
        align-items: center;
        gap: .28rem;
    }

    .sup-item-desc {
        font-size: .78rem;
        color: var(--text-light);
        line-height: 1.6;
        margin: 0;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* hover overlay with Inquire button */
    .sup-item-hover {
        position: absolute;
        inset: 0;
        background: rgba(21, 101, 192, 0.0);
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding-bottom: 1.1rem;
        opacity: 0;
        transition: opacity .28s ease, background .28s ease;
        pointer-events: none;
    }

    .sup-item-inner:hover .sup-item-hover {
        opacity: 1;
        background: rgba(21, 101, 192, 0.06);
        pointer-events: all;
    }

    .sup-inquire-btn {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        background: var(--primary-color);
        color: #fff;
        border: none;
        padding: .6rem 1.4rem;
        border-radius: 50px;
        font-size: .82rem;
        font-weight: 800;
        font-family: 'Barlow', sans-serif;
        letter-spacing: .04em;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(21, 101, 192, 0.4);
        transform: translateY(6px);
        transition: background .2s, transform .25s ease, box-shadow .2s;
    }

    .sup-item-inner:hover .sup-inquire-btn {
        transform: translateY(0);
    }

    .sup-inquire-btn:hover {
        background: var(--primary-dark);
        box-shadow: 0 10px 26px rgba(21, 101, 192, 0.5);
    }

    /* hidden card during filter */
    .sup-item.sup-hidden {
        display: none;
    }

    /* empty state */
    .sup-empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 1rem;
        color: var(--text-light);
    }
    .sup-empty-state i {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        display: block;
        opacity: .3;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .sup-cards-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }
        .sup-item-img { height: 130px; }
        .sup-filter-bar { gap: .4rem; }
        .sup-filter-pill { font-size: .76rem; padding: .42rem .85rem; }
    }

    @media (max-width: 480px) {
        .sup-cards-grid {
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
        }
        .sup-item-img { height: 110px; }
        .sup-item-name { font-size: .82rem; }
        .sup-item-body { padding: .75rem .75rem .7rem; }
    }
    </style>

    <!-- ════════════════════════════════════════════════════════
        JS — add inside the IIFE in js/carousel.js
        ════════════════════════════════════════════════════════ -->
    <script>
    (function () {
        /* ── Supply section filter ── */
        var supPills = document.querySelectorAll('.sup-filter-pill');
        var supItems = document.querySelectorAll('#supCardsGrid .sup-item');
        var supNoRes = document.getElementById('supNoResultsPublic');

        if (!supPills.length) return;

        supPills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                supPills.forEach(function (p) { p.classList.remove('active'); });
                pill.classList.add('active');

                var filter = pill.getAttribute('data-filter');
                var visible = 0;

                supItems.forEach(function (item) {
                    var show = filter === 'all' || item.getAttribute('data-cat') === filter;
                    item.classList.toggle('sup-hidden', !show);
                    if (show) visible++;
                });

                if (supNoRes) supNoRes.style.display = visible === 0 ? '' : 'none';
            });
        });

        /* ── Inquire button pre-fills contact modal ── */
        window.openSupplyInquiry = function (supplyName) {
            var serviceSelect = document.getElementById('cf_service');
            if (serviceSelect) {
                // Try to match exact option, or just set as text in a hidden note
                var matched = false;
                for (var i = 0; i < serviceSelect.options.length; i++) {
                    if (serviceSelect.options[i].text.toLowerCase().includes('supply') ||
                        serviceSelect.options[i].value.toLowerCase().includes('supply')) {
                        serviceSelect.value = serviceSelect.options[i].value;
                        matched = true;
                        break;
                    }
                }
            }
            var msgField = document.getElementById('cf_message');
            if (msgField && !msgField.value.trim()) {
                msgField.value = 'I am interested in: ' + supplyName + '\n\nPlease send me availability and pricing information.';
            }
            // Open the contact modal
            var contactModal = document.getElementById('contactModal');
            if (contactModal) {
                contactModal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        };
    }());
    </script>
    
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
    <!-- ── Founder / CEO ── -->
    <section id="founder">
        <div class="container-lg">
            <div class="founder-wrap">
                <!-- Text card (appears first) -->
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
                                <h3 class="founder-name">Aberto Molinyawe Jr</h3>
                                <span class="founder-title">Founder &amp; Chief Executive Officer</span>
                                <div class="founder-socials">
                                    <a href="#" class="founder-social" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                    <a href="#" class="founder-social" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- CEO photo (slides in from right) -->
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