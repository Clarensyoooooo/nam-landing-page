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
            if (target && target !== '#' && document.querySelector(target)) {
                e.preventDefault();
                document.querySelector(target).scrollIntoView({ behavior: 'smooth' });
            }
        });
    });


    /* ═══════════════════════════════════════════════
       3. SCROLL-REVEAL
    ═══════════════════════════════════════════════ */
    var revObs = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                revObs.unobserve(e.target);
            }
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
                    var n = 0;
                    var step = Math.ceil(target / 50);
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

    document.querySelectorAll('#services .service-modern-card').forEach(function (card) {
        card.addEventListener('click', function (e) {
            if (e.target.closest('.service-read-more')) {
                e.preventDefault();
            }
            openSvcModal(
                card.getAttribute('data-name'),
                card.getAttribute('data-desc'),
                JSON.parse(card.getAttribute('data-imgs') || '[]')
            );
        });
        card.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') card.click();
        });
    });

    function openSvcModal(name, desc, images) {
        titleEl.textContent = name;
        descEl.textContent  = desc;
        slidesWrap.innerHTML = '';
        dotsWrap.innerHTML   = '';
        cur = 0;
        tot = images.length;

        if (!tot) {
            slidesWrap.innerHTML = '<div class="svcm-no-img"><i class="fas fa-hard-hat"></i></div>';
        } else {
            images.forEach(function (src, i) {
                var s   = document.createElement('div');
                s.className = 'svcm-slide' + (i === 0 ? ' on' : '');
                var img = document.createElement('img');
                img.src = src;
                img.alt = name;
                s.appendChild(img);
                slidesWrap.appendChild(s);

                if (tot > 1) {
                    var d = document.createElement('button');
                    d.className = 'svcm-dot' + (i === 0 ? ' on' : '');
                    d.setAttribute('aria-label', 'Image ' + (i + 1));
                    (function (idx) {
                        d.addEventListener('click', function () { svcGoTo(idx); });
                    }(i));
                    dotsWrap.appendChild(d);
                }
            });
        }

        svcModal.classList.add('open');
        document.body.style.overflow = 'hidden';
        clearInterval(tmr);

        if (tot > 1) {
            tmr = setInterval(function () {
                svcGoTo((cur + 1) % tot);
            }, 3000);
        }
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

    // "Inquire Now" inside service modal → open contact modal
    document.getElementById('svcmQuoteBtn').addEventListener('click', function () {
        closeSvcModal();
        openContactModal();
    });

    svcModal.addEventListener('click', function (e) {
        if (e.target === svcModal) closeSvcModal();
    });


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

    // Nav button
    var navContactBtn = document.getElementById('navContactBtn');
    if (navContactBtn) navContactBtn.addEventListener('click', openContactModal);

    // Hero "Inquire Now" button
    var heroContactBtn = document.getElementById('heroContactBtn');
    if (heroContactBtn) heroContactBtn.addEventListener('click', openContactModal);

    // Footer contact link
    var footerContactLink = document.getElementById('footerContactLink');
    if (footerContactLink) {
        footerContactLink.addEventListener('click', function (e) {
            e.preventDefault();
            openContactModal();
        });
    }

    // Close button
    document.getElementById('contactModalCloseBtn').addEventListener('click', closeContactModal);

    // Click outside to close
    contactModal.addEventListener('click', function (e) {
        if (e.target === contactModal) closeContactModal();
    });


    /* ═══════════════════════════════════════════════
       VMO ACCORDION
    ═══════════════════════════════════════════════ */
    (function () {
        var triggers = document.querySelectorAll('.vmo-trigger');
        var panels   = document.querySelectorAll('.vmo-panel');
        if (!triggers.length || !panels.length) return;

        function openVmo(key) {
            triggers.forEach(function (t) {
                t.classList.toggle('active', t.getAttribute('data-vmo') === key);
            });
            panels.forEach(function (p) {
                p.classList.toggle('open', p.id === 'vmo-' + key);
            });
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
            var pasted = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/[^0-9]/g, '').slice(0, 6);
            pasted.split('').forEach(function (ch, j) {
                if (digits[j]) {
                    digits[j].value = ch;
                    progDots[j].classList.add('filled');
                    digits[j].classList.add('filled');
                }
            });
            digits[Math.min(pasted.length, 5)].focus();
            updateVerifyBtn();
        });
    });

    function getCode()         { return digits.map(function (d) { return d.value; }).join(''); }
    function updateVerifyBtn() { vmVerifyBtn.disabled = (getCode().length !== 6 || countdownSeconds <= 0); }

    function clearDigits() {
        digits.forEach(function (d, i) {
            d.value = '';
            d.classList.remove('filled', 'error');
            progDots[i].classList.remove('filled');
        });
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
            if (secs <= 0) {
                clearInterval(resendInterval);
                vmResendBtn.disabled = false;
                vmResendTimer.textContent = '';
            }
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
        if (e.key === 'Escape') {
            closeVerifyModal();
            closeSvcModal();
            closeContactModal();
        }
    });

    function sendOTP(email, onSuccess, onError) {
        var fd = new FormData();
        fd.append('email', email);
        fetch('backend/send_verification.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) { if (data.success) onSuccess(data); else onError(data.message); })
            .catch(function () { onError('Network error. Please try again.'); });
    }

    /* contact form submit → send OTP */
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

    /* resend OTP */
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

    /* ── verify button ── */
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
                    // 1. Close the verify modal
                    closeVerifyModal();

                    // 2. Reset the form
                    contactForm.reset();

                    // 3. Show success banner INSIDE the contact modal (keep it open)
                    var banner = document.getElementById('contactSuccessBanner');
                    var msgEl  = document.getElementById('contactSuccessMsg');
                    if (banner && msgEl) {
                        msgEl.textContent = data.message;
                        banner.classList.add('show');
                    }

                    // 4. Keep contact modal open — user will close it manually
                    openContactModal();

                } else {
                    shakeDigits();
                    showVmAlert(data.message, 'error');
                    vmVerifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify & Send Message';
                    vmVerifyBtn.disabled  = false;
                }
            })
            .catch(function () {
                shakeDigits();
                showVmAlert('Something went wrong. Please try again.', 'error');
                vmVerifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify & Send Message';
                vmVerifyBtn.disabled  = false;
            });
    });


    /* ═══════════════════════════════════════════════
       8. AUTO-DISMISS BOOTSTRAP ALERTS
    ═══════════════════════════════════════════════ */
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            var btn = alert.querySelector('.btn-close');
            if (btn) btn.click();
        }, 5000);
    });

}());