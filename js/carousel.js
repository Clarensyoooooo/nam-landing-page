(function () {

    /* ═══════════════════════════════════════════════
       1. NAVBAR — scroll shadow + active link
    ═══════════════════════════════════════════════ */
    var header   = document.getElementById('mainHeader');
    var sections = document.querySelectorAll('section[id]');
    var navLinks = document.querySelectorAll('.navbar-nav .nav-link[data-section]');

    function updateNav() {
        header.classList.toggle('scrolled', window.scrollY > 40);
        var current = '';
        sections.forEach(function (s) {
            if (window.scrollY >= s.offsetTop - 110) current = s.id;
        });
        navLinks.forEach(function (l) {
            l.classList.toggle('active-link', l.getAttribute('data-section') === current);
        });
    }
    window.addEventListener('scroll', updateNav, { passive: true });
    updateNav();


    /* ═══════════════════════════════════════════════
       2. SMOOTH ANCHOR SCROLLING
    ═══════════════════════════════════════════════ */
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var target = this.getAttribute('href');
            if (!target || target === '#' || !document.querySelector(target)) return;
            e.preventDefault();
            var targetEl  = document.querySelector(target);
            var navHeight = 72;
            window.scrollTo({ top: Math.max(0, targetEl.offsetTop - navHeight), behavior: 'smooth' });
        });
    });


    /* ═══════════════════════════════════════════════
       3. SCROLL-REVEAL
    ═══════════════════════════════════════════════ */
    var revObs = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { e.target.classList.add('visible'); revObs.unobserve(e.target); }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(function (el) { revObs.observe(el); });


    /* ═══════════════════════════════════════════════
       4. ANIMATED COUNTERS
    ═══════════════════════════════════════════════ */
    var counted = false;
    var statsEl = document.getElementById('stats');
    if (statsEl) {
        new IntersectionObserver(function (e) {
            if (e[0].isIntersecting && !counted) {
                counted = true;
                document.querySelectorAll('.counter').forEach(function (c) {
                    var target = parseInt(c.getAttribute('data-target'));
                    var n = 0, step = Math.ceil(target / 50);
                    var t = setInterval(function () {
                        n += step;
                        if (n >= target) { n = target; clearInterval(t); }
                        c.textContent = n;
                    }, 28);
                });
            }
        }, { threshold: 0.4 }).observe(statsEl);
    }


    /* ═══════════════════════════════════════════════
       5. SERVICE MODAL
    ═══════════════════════════════════════════════ */
    var svcModal   = document.getElementById('svcModal');
    var slidesWrap = document.getElementById('svcmSlides');
    var dotsWrap   = document.getElementById('svcmDots');
    var titleEl    = document.getElementById('svcmTitle');
    var descEl     = document.getElementById('svcmDesc');
    var cur = 0, tot = 0, tmr = null;

    window.openSvcModalExternal = openSvcModal;

    document.addEventListener('svcCardClick', function (e) {
        openSvcModal(e.detail.name, e.detail.desc, e.detail.images);
    });

    document.querySelectorAll('#services .service-modern-card').forEach(function (card) {
        card.addEventListener('click', function (e) {
            if (e.target.closest('.service-read-more')) e.preventDefault();
            openSvcModal(
                card.getAttribute('data-name'),
                card.getAttribute('data-desc'),
                JSON.parse(card.getAttribute('data-imgs') || '[]')
            );
        });
    });

    function openSvcModal(name, desc, images) {
        titleEl.textContent = name  || '';
        descEl.textContent  = desc  || '';
        slidesWrap.innerHTML = '';
        dotsWrap.innerHTML   = '';
        cur = 0;
        tot = images ? images.length : 0;

        if (!tot) {
            slidesWrap.innerHTML = '<div class="svcm-no-img"><i class="fas fa-hard-hat"></i></div>';
        } else {
            images.forEach(function (src, i) {
                var s   = document.createElement('div');
                s.className = 'svcm-slide' + (i === 0 ? ' on' : '');
                var img = document.createElement('img');
                img.src = src; img.alt = name;
                s.appendChild(img);
                slidesWrap.appendChild(s);

                if (tot > 1) {
                    var d = document.createElement('button');
                    d.className = 'svcm-dot' + (i === 0 ? ' on' : '');
                    d.setAttribute('aria-label', 'Image ' + (i + 1));
                    (function (idx) { d.addEventListener('click', function () { svcGoTo(idx); }); }(i));
                    dotsWrap.appendChild(d);
                }
            });
        }

        svcModal.classList.add('open');
        document.body.style.overflow = 'hidden';
        clearInterval(tmr);
        if (tot > 1) tmr = setInterval(function () { svcGoTo((cur + 1) % tot); }, 3000);
    }

    function svcGoTo(idx) {
        var ss = slidesWrap.querySelectorAll('.svcm-slide');
        var ds = dotsWrap.querySelectorAll('.svcm-dot');
        if (!ss.length) return;
        ss[cur].classList.remove('on');
        if (ds[cur]) ds[cur].classList.remove('on');
        cur = idx;
        ss[cur].classList.add('on');
        if (ds[cur]) ds[cur].classList.add('on');
    }

    function closeSvcModal() {
        svcModal.classList.remove('open');
        document.body.style.overflow = '';
        clearInterval(tmr);
    }

    document.getElementById('svcmCloseBtn').addEventListener('click', closeSvcModal);
    document.getElementById('svcmQuoteBtn').addEventListener('click', function () {
        closeSvcModal();
        openContactModal();
    });
    svcModal.addEventListener('click', function (e) { if (e.target === svcModal) closeSvcModal(); });


    /* ═══════════════════════════════════════════════
       6. CONTACT MODAL
    ═══════════════════════════════════════════════ */
    var contactModal = document.getElementById('contactModal');

    function openContactModal() {
        contactModal.classList.add('open');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(function () {
            var firstInput = contactModal.querySelector('input');
            if (firstInput) firstInput.focus();
        });
    }

    function closeContactModal() {
        contactModal.classList.remove('open');
        document.body.style.overflow = '';
    }

    var navContactBtn = document.getElementById('navContactBtn');
    if (navContactBtn) navContactBtn.addEventListener('click', openContactModal);

    var heroContactBtn = document.getElementById('heroContactBtn');
    if (heroContactBtn) heroContactBtn.addEventListener('click', openContactModal);

    var footerContactLink = document.getElementById('footerContactLink');
    if (footerContactLink) {
        footerContactLink.addEventListener('click', function (e) { e.preventDefault(); openContactModal(); });
    }

    document.getElementById('contactModalCloseBtn').addEventListener('click', closeContactModal);
    contactModal.addEventListener('click', function (e) { if (e.target === contactModal) closeContactModal(); });


    /* ═══════════════════════════════════════════════
       VMO ACCORDION
    ═══════════════════════════════════════════════ */
    (function () {
        var triggers = document.querySelectorAll('.vmo-trigger');
        var panels   = document.querySelectorAll('.vmo-panel');
        if (!triggers.length || !panels.length) return;

        function openVmo(key) {
            triggers.forEach(function (t) { t.classList.toggle('active', t.getAttribute('data-vmo') === key); });
            panels.forEach(function (p) { p.classList.toggle('open', p.id === 'vmo-' + key); });
        }

        triggers.forEach(function (t) {
            t.addEventListener('click', function () { openVmo(t.getAttribute('data-vmo')); });
        });
        openVmo('vision');
    }());


    /* ═══════════════════════════════════════════════
       7. VERIFICATION MODAL
    ═══════════════════════════════════════════════ */
    var verifyModal    = document.getElementById('verifyModal');
    var vmEmailDisplay = document.getElementById('vmEmailDisplay');
    var vmAlert        = document.getElementById('vmAlert');
    var vmVerifyBtn    = document.getElementById('vmVerifyBtn');
    var vmResendBtn    = document.getElementById('vmResendBtn');
    var vmResendTimer  = document.getElementById('vmResendTimer');
    var vmCountdown    = document.getElementById('vmCountdown');
    var vmTimerEl      = document.getElementById('vmTimer');
    var digits         = [0,1,2,3,4,5].map(function (i) { return document.getElementById('vd' + i); });
    var progDots       = [0,1,2,3,4,5].map(function (i) { return document.getElementById('vp' + i); });

    var savedFormData     = null;
    var countdownInterval = null;
    var resendInterval    = null;
    var countdownSeconds  = 0;

    digits.forEach(function (inp, i) {
        inp.addEventListener('input', function () {
            inp.value = inp.value.replace(/[^0-9]/g, '').slice(-1);
            progDots[i].classList.toggle('filled', inp.value !== '');
            inp.classList.toggle('filled', inp.value !== '');
            inp.classList.remove('error');
            if (inp.value && i < 5) digits[i + 1].focus();
            updateVerifyBtn();
        });
        inp.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !inp.value && i > 0) {
                digits[i - 1].value = '';
                progDots[i - 1].classList.remove('filled');
                digits[i - 1].classList.remove('filled');
                digits[i - 1].focus();
                updateVerifyBtn();
            }
        });
        inp.addEventListener('paste', function (e) {
            e.preventDefault();
            var pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);
            pasted.split('').forEach(function (ch, j) {
                if (digits[j]) { digits[j].value = ch; progDots[j].classList.add('filled'); digits[j].classList.add('filled'); }
            });
            digits[Math.min(pasted.length, 5)].focus();
            updateVerifyBtn();
        });
    });

    function getCode()         { return digits.map(function (d) { return d.value; }).join(''); }
    function updateVerifyBtn() { vmVerifyBtn.disabled = (getCode().length !== 6 || countdownSeconds <= 0); }

    function clearDigits() {
        digits.forEach(function (d, i) { d.value = ''; d.classList.remove('filled', 'error'); progDots[i].classList.remove('filled'); });
        vmVerifyBtn.disabled = true;
    }

    function shakeDigits() {
        digits.forEach(function (d) { d.classList.add('error'); });
        setTimeout(function () { digits.forEach(function (d) { d.classList.remove('error'); }); }, 500);
    }

    function showVmAlert(msg, type) {
        var icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
        vmAlert.className = 'vm-alert show ' + type;
        vmAlert.innerHTML = '<i class="fas fa-' + icon + '"></i> ' + msg;
    }
    function hideVmAlert() { vmAlert.className = 'vm-alert'; vmAlert.innerHTML = ''; }

    function startCountdown(seconds) {
        clearInterval(countdownInterval);
        countdownSeconds = seconds;
        updateVerifyBtn();
        countdownInterval = setInterval(function () {
            countdownSeconds--;
            var m = Math.floor(countdownSeconds / 60);
            var s = countdownSeconds % 60;
            vmCountdown.textContent = m + ':' + (s < 10 ? '0' : '') + s;
            if (countdownSeconds <= 0) {
                clearInterval(countdownInterval);
                vmTimerEl.classList.add('expired');
                vmCountdown.textContent = 'Expired';
                vmVerifyBtn.disabled = true;
                showVmAlert('Code expired. Please request a new one.', 'error');
            }
        }, 1000);
    }

    function startResendCooldown() {
        vmResendBtn.disabled = true;
        var secs = 60;
        vmResendTimer.textContent = ' (' + secs + 's)';
        resendInterval = setInterval(function () {
            secs--;
            vmResendTimer.textContent = ' (' + secs + 's)';
            if (secs <= 0) { clearInterval(resendInterval); vmResendBtn.disabled = false; vmResendTimer.textContent = ''; }
        }, 1000);
    }

    function openVerifyModal(email) {
        vmEmailDisplay.textContent = email;
        clearDigits();
        hideVmAlert();
        vmTimerEl.classList.remove('expired');
        vmCountdown.textContent = '10:00';
        startCountdown(600);
        startResendCooldown();
        verifyModal.classList.add('open');
        document.body.style.overflow = 'hidden';
        setTimeout(function () { digits[0].focus(); }, 300);
    }

    function closeVerifyModal() {
        verifyModal.classList.remove('open');
        document.body.style.overflow = '';
        clearInterval(countdownInterval);
        clearInterval(resendInterval);
    }

    document.getElementById('vmCloseBtn').addEventListener('click', closeVerifyModal);
    verifyModal.addEventListener('click', function (e) { if (e.target === verifyModal) closeVerifyModal(); });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeVerifyModal(); closeSvcModal(); closeContactModal(); }
    });

    function sendOTP(email, onSuccess, onError) {
        var fd = new FormData();
        fd.append('email', email);
        fetch('backend/send_verification.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) { if (data.success) onSuccess(data); else onError(data.message); })
            .catch(function () { onError('Network error. Please try again.'); });
    }

    /* Contact form submit → send OTP */
    var contactForm = document.getElementById('contactForm');
    var submitBtn   = document.getElementById('submitBtn');

    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var name    = document.getElementById('cf_name').value.trim();
        var email   = document.getElementById('cf_email').value.trim();
        var message = document.getElementById('cf_message').value.trim();

        if (!name)    { document.getElementById('cf_name').focus(); return; }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { document.getElementById('cf_email').focus(); return; }
        if (!message) { document.getElementById('cf_message').focus(); return; }

        savedFormData = new FormData(contactForm);
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending code…';
        submitBtn.disabled  = true;

        sendOTP(email,
            function () {
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
                submitBtn.disabled  = false;
                openVerifyModal(email);
            },
            function (errMsg) {
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
                submitBtn.disabled  = false;
                var el = document.getElementById('otpSendError');
                if (!el) {
                    el = document.createElement('div');
                    el.id = 'otpSendError';
                    el.className = 'alert alert-danger';
                    el.style.marginBottom = '1rem';
                    contactForm.insertBefore(el, contactForm.firstChild);
                }
                el.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + errMsg;
                setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 6000);
            }
        );
    });

    /* Resend OTP */
    vmResendBtn.addEventListener('click', function () {
        var email = document.getElementById('cf_email').value.trim();
        hideVmAlert();
        clearDigits();
        vmTimerEl.classList.remove('expired');
        vmCountdown.textContent = '10:00';
        showVmAlert('Sending a new code…', 'info');
        sendOTP(email,
            function () {
                showVmAlert('New code sent! Check your inbox.', 'success');
                startCountdown(600);
                startResendCooldown();
                setTimeout(hideVmAlert, 4000);
                digits[0].focus();
            },
            function (msg) { showVmAlert(msg, 'error'); }
        );
    });

    /* Verify button */
    vmVerifyBtn.addEventListener('click', function () {
        var code = getCode();
        if (code.length !== 6) return;

        vmVerifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying…';
        vmVerifyBtn.disabled  = true;

        if (!savedFormData) {
            showVmAlert('Form data lost. Please close and re-submit the form.', 'error');
            vmVerifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify & Send Message';
            return;
        }

        var fd = new FormData();
        for (var pair of savedFormData.entries()) { fd.append(pair[0], pair[1]); }
        fd.append('otp_code', code);

        fetch('backend/submit_contact.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    closeVerifyModal();
                    closeContactModal();
                    contactForm.reset();
                    if (typeof showFrontToast === 'function') {
                        showFrontToast(data.message, 'success');
                    }
                } else {
                    shakeDigits();
                    showVmAlert(data.message, 'error');
                    vmVerifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify &amp; Send Message';
                    vmVerifyBtn.disabled  = false;
                }
            })
            .catch(function () {
                shakeDigits();
                showVmAlert('Something went wrong. Please try again.', 'error');
                vmVerifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify &amp; Send Message';
                vmVerifyBtn.disabled  = false;
            });
    });


    /* ═══════════════════════════════════════════════
       8. SUPPLIES SECTION FILTER (legacy pill filter)
    ═══════════════════════════════════════════════ */
    (function () {
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

        window.openSupplyInquiry = function (supplyName) {
            var serviceSelect = document.getElementById('cf_service');
            if (serviceSelect) {
                var selected = false;
                for (var i = 0; i < serviceSelect.options.length; i++) {
                    if (serviceSelect.options[i].text.toLowerCase().includes('supply')) {
                        serviceSelect.value = serviceSelect.options[i].value;
                        selected = true; break;
                    }
                }
                if (!selected) {
                    var tempOpt = document.createElement('option');
                    tempOpt.value = tempOpt.text = 'Supply Services';
                    serviceSelect.appendChild(tempOpt);
                    serviceSelect.value = 'Supply Services';
                }
            }
            var msgField = document.getElementById('cf_message');
            if (msgField && !msgField.value.trim()) {
                msgField.value = 'I am interested in: ' + supplyName + '\n\nPlease send me availability and pricing information.';
            }
            openContactModal();
        };
    }());


    /* ═══════════════════════════════════════════════
       9. FOUNDER SECTION SCROLL REVEAL
    ═══════════════════════════════════════════════ */
    (function () {
        var founderEls = document.querySelectorAll('.founder-reveal-text, .founder-reveal-photo');
        if (!founderEls.length) return;
        var founderObs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) { entry.target.classList.add('founder-in'); founderObs.unobserve(entry.target); }
            });
        }, { threshold: 0.15 });
        founderEls.forEach(function (el) { founderObs.observe(el); });
    }());


    /* ═══════════════════════════════════════════════
       10. AUTO-DISMISS BOOTSTRAP ALERTS
    ═══════════════════════════════════════════════ */
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () { var btn = alert.querySelector('.btn-close'); if (btn) btn.click(); }, 5000);
    });


    /* ═══════════════════════════════════════════════
       11. SERVICES — prev/next arrow carousel
    ═══════════════════════════════════════════════ */
    (function () {
        var svcTrack    = document.getElementById('svcTrack');
        var svcCounter  = document.getElementById('svcCounter');
        var svcFill     = document.getElementById('svcProgressFill');
        var svcDotsWrap = document.getElementById('svcDots');
        var btnPrev     = document.getElementById('svcBtnPrev');
        var btnNext     = document.getElementById('svcBtnNext');

        if (!svcTrack) return;

        var svcCards = Array.from(svcTrack.querySelectorAll('.svc-card'));
        var N = svcCards.length;
        if (N === 0) return;

        var CARD_W   = 280;
        var CARD_GAP = 18;
        var activeIdx = 0;
        var prevIdx   = -1;
        var animRaf   = null;
        var currentTx = 0;
        var targetTx  = 0;

        var svcDots = [];
        if (svcDotsWrap) {
            svcCards.forEach(function (_, i) {
                var d = document.createElement('button');
                d.className = 'svc-dot';
                d.setAttribute('aria-label', 'Service ' + (i + 1));
                svcDotsWrap.appendChild(d);
                svcDots.push(d);
                d.addEventListener('click', function () { goToCard(i); });
            });
        }

        function recalc() {
            var vw = window.innerWidth;
            CARD_W = vw <= 480 ? 185 : vw <= 768 ? 220 : 280;
            var CARD_H = vw <= 480 ? 260 : vw <= 768 ? 300 : 370;
            svcCards.forEach(function (c) {
                c.style.width = c.style.minWidth = c.style.maxWidth = CARD_W + 'px';
                c.style.height = CARD_H + 'px';
                c.style.flexShrink = '0';
            });
            svcTrack.style.flexWrap = 'nowrap';
            svcTrack.style.width    = 'max-content';
            targetTx  = getTxForCard(activeIdx);
            currentTx = targetTx;
            svcTrack.style.transform = 'translateX(' + currentTx + 'px)';
        }

        var svcViewport = document.querySelector('.svc-viewport');
        function getViewportW() { return svcViewport ? svcViewport.clientWidth : window.innerWidth; }
        function getTxForCard(idx) { return (getViewportW() * 0.5 - CARD_W * 0.5) - idx * (CARD_W + CARD_GAP); }

        function animateTick() {
            var diff = targetTx - currentTx;
            if (Math.abs(diff) < 0.5) { currentTx = targetTx; svcTrack.style.transform = 'translateX(' + currentTx + 'px)'; animRaf = null; return; }
            currentTx += diff * 0.14;
            svcTrack.style.transform = 'translateX(' + currentTx + 'px)';
            animRaf = requestAnimationFrame(animateTick);
        }
        function startAnim() { if (!animRaf) animRaf = requestAnimationFrame(animateTick); }
        function pad(n) { return n < 10 ? '0' + n : '' + n; }

        function goToCard(idx) {
            idx = Math.max(0, Math.min(N - 1, idx));
            activeIdx = idx;
            targetTx  = getTxForCard(idx);
            updateUI();
            startAnim();
        }

        function updateUI() {
            if (activeIdx !== prevIdx) {
                prevIdx = activeIdx;
                if (svcCounter) svcCounter.textContent = pad(activeIdx + 1) + ' / ' + pad(N);
                svcDots.forEach(function (d, i) { d.classList.toggle('svc-dot-active', i === activeIdx); });
            }
            if (svcFill) svcFill.style.transform = 'scaleX(' + (N > 1 ? activeIdx / (N - 1) : 1) + ')';
            if (btnPrev) btnPrev.disabled = (activeIdx === 0);
            if (btnNext) btnNext.disabled = (activeIdx === N - 1);
            svcCards.forEach(function (card, i) {
                card.classList.remove('svc-active', 'svc-near', 'svc-far', 'svc-right',
                                      'svc-d1', 'svc-d2', 'svc-d3', 'svc-d4');
                var diff    = i - activeIdx;
                var absDiff = Math.abs(diff);
                var isRight = diff > 0;

                if (diff === 0) {
                    card.classList.add('svc-active');
                } else {
                    // distance class for bridge arc depth
                    var dClass = absDiff >= 4 ? 'svc-d4' :
                                 absDiff === 3 ? 'svc-d3' :
                                 absDiff === 2 ? 'svc-d2' : 'svc-d1';
                    card.classList.add(dClass);
                    if (isRight) card.classList.add('svc-right');
                    // keep legacy near/far for any external code
                    if (absDiff === 1) card.classList.add('svc-near');
                    else               card.classList.add('svc-far');
                }
            });
        }

        if (btnPrev) btnPrev.addEventListener('click', function (e) { e.stopPropagation(); goToCard(activeIdx - 1); });
        if (btnNext) btnNext.addEventListener('click', function (e) { e.stopPropagation(); goToCard(activeIdx + 1); });

        /* ── Touch / swipe support (full carousel wrap, not just track) ── */
        var svcWrap     = document.querySelector('.svc-carousel-wrap');
        var touchStartX = 0;
        var touchStartY = 0;
        var isSwiping   = false;

        function onTouchStart(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            isSwiping   = false;
        }
        function onTouchMove(e) {
            var dx = Math.abs(e.touches[0].clientX - touchStartX);
            var dy = Math.abs(e.touches[0].clientY - touchStartY);
            if (dx > dy && dx > 8) { isSwiping = true; e.preventDefault(); }
        }
        function onTouchEnd(e) {
            var dx = touchStartX - e.changedTouches[0].clientX;
            if (isSwiping && Math.abs(dx) > 35) {
                goToCard(dx > 0 ? activeIdx + 1 : activeIdx - 1);
            }
            isSwiping = false;
        }

        /* Attach to the full carousel wrap so swipe works anywhere on the strip */
        if (svcWrap) {
            svcWrap.addEventListener('touchstart', onTouchStart, { passive: true });
            svcWrap.addEventListener('touchmove',  onTouchMove,  { passive: false });
            svcWrap.addEventListener('touchend',   onTouchEnd,   { passive: true });
        }

        /* ── Card click: non-active → center first; active → open modal ── */
        svcCards.forEach(function (card) {
            card.addEventListener('click', function (e) {
                e.stopPropagation();
                var idx = parseInt(card.getAttribute('data-index'));

                /* If this card is NOT the active/center one, just slide it to center */
                if (idx !== activeIdx) {
                    goToCard(idx);
                    return;   /* do NOT open modal yet */
                }

                /* Card is already centered — open the modal */
                var name   = card.getAttribute('data-name');
                var desc   = card.getAttribute('data-desc');
                var images = [];
                try { images = JSON.parse(card.getAttribute('data-imgs') || '[]'); } catch (_) {}
                openSvcModal(name, desc, images);
            });
        });

        function init() {
            recalc();
            goToCard(0);
            window.addEventListener('resize', function () { recalc(); updateUI(); });
        }

        if (document.readyState === 'complete') { init(); }
        else { window.addEventListener('load', init); }
    }());


    /* ═══════════════════════════════════════════════
       12. SUPPLIES — category panel + paginated grid
    ═══════════════════════════════════════════════ */
    (function () {
        var PER_PAGE = 12;

        function getCatData()    { return window.supCatData    || {}; }
        function getUploadsUrl() { return window.supUploadsUrl || ''; }

        var infoName = document.getElementById('supInfoName');
        var infoDesc = document.getElementById('supInfoDesc');
        var grid     = document.getElementById('supImgGrid');
        var pagInfo  = document.getElementById('supPagInfo');
        var btnPrev  = document.getElementById('supPagPrev');
        var btnNext  = document.getElementById('supPagNext');
        var catBtns  = document.querySelectorAll('.sup-cat-btn');

        if (!grid || !catBtns.length) return;

        var activeCat  = null;
        var curPage    = 1;
        var totalPages = 1;

        function getItems() { return (getCatData()[activeCat]) || []; }

        function renderGrid() {
            var items = getItems();
            totalPages = Math.max(1, Math.ceil(items.length / PER_PAGE));
            if (curPage > totalPages) curPage = totalPages;
            var start = (curPage - 1) * PER_PAGE;
            var slice = items.slice(start, start + PER_PAGE);

            grid.innerHTML = '';

            slice.forEach(function (sup) {
                var cell = document.createElement('div');
                cell.className = 'sup-img-cell';
                cell.setAttribute('title', sup.name);

                if (sup.image) {
                    var img = document.createElement('img');
                    img.src     = getUploadsUrl() + sup.image;
                    img.alt     = sup.name;
                    img.loading = 'lazy';
                    img.onerror = function () {
                        this.style.display = 'none';
                        cell.classList.add('sup-img-cell--empty');
                    };
                    cell.appendChild(img);
                } else {
                    cell.classList.add('sup-img-cell--empty');
                }

                var overlay = document.createElement('div');
                overlay.className = 'sup-img-overlay';
                var label = document.createElement('span');
                label.className = 'sup-img-label';
                label.textContent = sup.name;
                overlay.appendChild(label);
                cell.appendChild(overlay);

                cell.addEventListener('click', function () {
                    var isActive = cell.classList.contains('sup-active');
                    document.querySelectorAll('.sup-img-cell.sup-active').forEach(function (c) {
                        c.classList.remove('sup-active');
                    });
                    if (!isActive) cell.classList.add('sup-active');
                });

                grid.appendChild(cell);
            });

            if (!slice.length) {
                var empty = document.createElement('p');
                empty.style.cssText = 'grid-column:1/-1;text-align:center;color:var(--text-light);padding:2rem;';
                empty.textContent = 'No supplies in this category yet.';
                grid.appendChild(empty);
            }

            pagInfo.textContent = 'Page ' + curPage + ' of ' + totalPages;
            btnPrev.disabled    = curPage === 1;
            btnNext.disabled    = curPage === totalPages;
        }

        function switchCat(btn) {
            catBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');

            var newCat = parseInt(btn.getAttribute('data-cat-id'));
            if (newCat === activeCat) return;

            activeCat = newCat;
            curPage   = 1;

            [infoName, infoDesc, grid].forEach(function (el) { el.classList.add('sup-fade'); });

            setTimeout(function () {
                infoName.textContent = btn.getAttribute('data-cat-name');
                infoDesc.textContent = btn.getAttribute('data-cat-desc');
                renderGrid();
                [infoName, infoDesc, grid].forEach(function (el) { el.classList.remove('sup-fade'); });
            }, 220);
        }

        function fadePage() {
            grid.classList.add('sup-fade');
            setTimeout(function () {
                renderGrid();
                grid.classList.remove('sup-fade');
            }, 180);
        }

        function init() {
            var firstBtn = document.querySelector('.sup-cat-btn.active');
            if (!firstBtn) firstBtn = catBtns[0];
            if (firstBtn) {
                activeCat = parseInt(firstBtn.getAttribute('data-cat-id'));
                infoName.textContent = firstBtn.getAttribute('data-cat-name');
                infoDesc.textContent = firstBtn.getAttribute('data-cat-desc');
                renderGrid();
            }

            var inquireBtn = document.getElementById('supInquireBtn');
            if (inquireBtn) {
                inquireBtn.addEventListener('click', function () {
                    var serviceSelect = document.getElementById('cf_service');
                    if (serviceSelect) {
                        var matched = false;
                        for (var i = 0; i < serviceSelect.options.length; i++) {
                            if (serviceSelect.options[i].text.toLowerCase().includes('supply')) {
                                serviceSelect.value = serviceSelect.options[i].value;
                                matched = true;
                                break;
                            }
                        }
                        if (!matched) {
                            var opt = document.createElement('option');
                            opt.value = opt.text = 'Supply Services';
                            opt.id = 'supTempOpt';
                            serviceSelect.appendChild(opt);
                            serviceSelect.value = 'Supply Services';
                        }
                    }
                    var catName  = (infoName && infoName.textContent.trim()) ? infoName.textContent.trim() : 'Supplies';
                    var msgField = document.getElementById('cf_message');
                    if (msgField && !msgField.value.trim()) {
                        msgField.value = 'I am interested in: ' + catName + '\n\nPlease send me availability and pricing information.';
                    }
                    var contactModal = document.getElementById('contactModal');
                    if (contactModal) {
                        contactModal.classList.add('open');
                        document.body.style.overflow = 'hidden';
                    }
                });
            }
        }

        catBtns.forEach(function (btn) {
            btn.addEventListener('click', function () { switchCat(btn); });
        });

        btnPrev.addEventListener('click', function () {
            if (curPage > 1) { curPage--; fadePage(); }
        });
        btnNext.addEventListener('click', function () {
            if (curPage < totalPages) { curPage++; fadePage(); }
        });

        if (document.readyState === 'complete') { init(); }
        else { window.addEventListener('load', init); }
    }());

}());