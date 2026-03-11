/* ════════════════════════════════════════════════════════════════
   updates.js — public Updates section interactivity
   ════════════════════════════════════════════════════════════════ */

(function () {

    var updModal     = document.getElementById('updModal');
    var updModalImg  = document.getElementById('updModalImg');
    var updModalImgP = document.getElementById('updModalImgPlaceholder');
    var updModalDate = document.getElementById('updModalDate');
    var updModalTtl  = document.getElementById('updModalTitle');
    var updModalDesc = document.getElementById('updModalDesc');
    var closeBtn     = document.getElementById('updModalClose');
    var toggleBtn    = document.getElementById('updToggleBtn');
    var toggleLabel  = document.getElementById('updToggleLabel');
    var toggleIcon   = document.getElementById('updToggleIcon');
    var toggleWrap   = document.getElementById('updToggleWrap');
    var seeAllBtn    = document.getElementById('updSeeAllBtn');
    var grid         = document.getElementById('updGrid');

    if (!updModal) return;

    var hiddenCards = grid ? Array.from(grid.querySelectorAll('.upd-card-hidden')) : [];
    var expanded    = false;

    /* Show the toggle button only if there are hidden cards */
    if (hiddenCards.length > 0 && toggleWrap) {
        toggleWrap.style.display = 'flex';
    }

    /* ── Open modal ── */
    window.openUpdModal = function (card) {
        var img    = card.getAttribute('data-img');
        var title  = card.getAttribute('data-title');
        var desc   = card.getAttribute('data-desc');
        var date   = card.getAttribute('data-date');

        updModalDate.textContent = date  || '';
        updModalTtl.textContent  = title || '';
        updModalDesc.textContent = desc  || '';

        if (img) {
            updModalImg.src          = img;
            updModalImg.alt          = title;
            updModalImg.style.display = 'block';
            if (updModalImgP) updModalImgP.style.display = 'none';
        } else {
            updModalImg.style.display = 'none';
            if (updModalImgP) updModalImgP.style.display = 'flex';
        }

        updModal.classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    /* ── Close modal ── */
    window.closeUpdModal = function () {
        updModal.classList.remove('open');
        document.body.style.overflow = '';
    };

    if (closeBtn) closeBtn.addEventListener('click', closeUpdModal);

    updModal.addEventListener('click', function (e) {
        if (e.target === updModal) closeUpdModal();
    });

    /* ── Card click ── */
    if (grid) {
        grid.querySelectorAll('.upd-card').forEach(function (card) {
            card.addEventListener('click', function () {
                openUpdModal(card);
            });
            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openUpdModal(card);
                }
            });
            card.setAttribute('tabindex', '0');
            card.setAttribute('role', 'button');
        });
    }

    /* ── Show more / less toggle ── */
    window.toggleUpdates = function () {
        expanded = !expanded;

        hiddenCards.forEach(function (card, i) {
            if (expanded) {
                card.classList.add('upd-show');
                /* stagger reveal */
                setTimeout(function () {
                    card.classList.add('visible');
                }, i * 80);
            } else {
                card.classList.remove('upd-show', 'visible');
            }
        });

        if (toggleLabel) {
            toggleLabel.textContent = expanded
                ? 'Show Less'
                : 'Show All ' + (hiddenCards.length + 3) + ' Posts';
        }

        if (toggleIcon) {
            toggleIcon.className = expanded
                ? 'fas fa-chevron-up'
                : 'fas fa-chevron-down';
        }

        /* Scroll back to grid top when collapsing */
        if (!expanded && grid) {
            grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    /* ── "See All" header button → same as toggle ── */
    if (seeAllBtn) {
        seeAllBtn.addEventListener('click', function () {
            if (!expanded) toggleUpdates();
            if (grid) setTimeout(function () {
                grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 50);
        });
    }

    /* ── Close on ESC ── */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && updModal.classList.contains('open')) {
            closeUpdModal();
        }
    });

}());