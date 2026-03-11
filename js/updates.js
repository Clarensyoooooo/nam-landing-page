/* ════════════════════════════════════════════════════════════════
   updates.js  —  row-group expand strategy
   Cards are grouped into rows of 3 inside .upd-row wrappers.
   Clicking a card:
     1. Collapses any open card.
     2. Expands that card to 2-col, moves it FIRST in its row-wrapper.
     3. One sibling sits in the 2nd slot; the other is hidden.
     4. Row switches to 2-col grid (2fr 1fr).
   Collapsing restores original 3-col order.
   ════════════════════════════════════════════════════════════════ */
(function () {

    var outerGrid  = document.getElementById('updGrid');
    var toggleLabel = document.getElementById('updToggleLabel');
    var toggleIcon  = document.getElementById('updToggleIcon');
    var toggleWrap  = document.getElementById('updToggleWrap');
    var seeAllBtn   = document.getElementById('updSeeAllBtn');

    if (!outerGrid) return;

    /* ── State ── */
    var activeCard   = null;
    var activeRow    = null;
    var rowOrigOrder = [];   // original card order before expand
    var curSlide     = 0;
    var totalSlides  = 0;
    var slideEls     = [];
    var dotEls       = [];
    var showingAll   = false;
    var hiddenCards  = [];   // cards initially hidden

    /* ────────────────────────────────────────────────
       Build .upd-row wrappers from the flat card list
    ──────────────────────────────────────────────── */
    function buildRows() {
        /* collect all direct card children */
        var cards = Array.from(outerGrid.querySelectorAll(':scope > .upd-card'));
        hiddenCards = cards.filter(function (c) { return c.classList.contains('upd-card-hidden'); });

        /* clear outer grid */
        outerGrid.innerHTML = '';

        /* chunk into groups of 3 */
        var visible = cards.filter(function (c) { return !c.classList.contains('upd-card-hidden'); });
        var hidden  = cards.filter(function (c) { return  c.classList.contains('upd-card-hidden'); });

        function makeRows(list) {
            for (var i = 0; i < list.length; i += 3) {
                var row = document.createElement('div');
                row.className = 'upd-row';
                var chunk = list.slice(i, i + 3);
                chunk.forEach(function (c) { row.appendChild(c); });
                outerGrid.appendChild(row);
            }
        }

        makeRows(visible);

        /* append hidden cards in a hidden-holding row (won't display) */
        if (hidden.length) {
            hidden.forEach(function (c) { outerGrid.appendChild(c); });
        }

        /* wire up all visible cards */
        visible.forEach(bindCard);

        if (hidden.length > 0 && toggleWrap) toggleWrap.style.display = 'flex';
    }

    /* ────────────────────────────────────────────────
       Slide helpers
    ──────────────────────────────────────────────── */
    function goSlide(idx) {
        if (!slideEls.length) return;
        slideEls[curSlide].classList.remove('active');
        if (dotEls[curSlide]) dotEls[curSlide].classList.remove('active');
        curSlide = ((idx % totalSlides) + totalSlides) % totalSlides;
        slideEls[curSlide].classList.add('active');
        if (dotEls[curSlide]) dotEls[curSlide].classList.add('active');
    }

    /* ────────────────────────────────────────────────
       Build expanded content inside card
    ──────────────────────────────────────────────── */
    function injectExpandContent(card) {
        var title = card.getAttribute('data-title') || '';
        var desc  = card.getAttribute('data-desc')  || '';
        var date  = card.getAttribute('data-date')  || '';
        var imgs  = [];
        try { imgs = JSON.parse(card.getAttribute('data-imgs') || '[]'); } catch (e) {}
        if (!imgs.length) imgs = [''];

        curSlide = 0; totalSlides = imgs.length;
        slideEls = []; dotEls = [];

        var imgSide  = card.querySelector('.updx-img-side');
        var textSide = card.querySelector('.updx-text-side');
        imgSide.innerHTML  = '';
        textSide.innerHTML = '';

        /* slides */
        var slidesWrap = document.createElement('div');
        slidesWrap.className = 'updx-slides';
        imgs.forEach(function (src, i) {
            var slide = document.createElement('div');
            slide.className = 'updx-slide' + (i === 0 ? ' active' : '');
            if (src) {
                var img = document.createElement('img'); img.src = src; img.alt = title;
                slide.appendChild(img);
            } else {
                var ph = document.createElement('div');
                ph.className = 'updx-slide-placeholder';
                ph.innerHTML = '<i class="fas fa-newspaper"></i>';
                slide.appendChild(ph);
            }
            slidesWrap.appendChild(slide);
            slideEls.push(slide);
        });
        imgSide.appendChild(slidesWrap);

        /* date badge */
        var db = document.createElement('div');
        db.className = 'updx-date-badge';
        db.innerHTML = '<i class="fas fa-calendar-alt"></i>' + date;
        imgSide.appendChild(db);

        /* arrows + dots (multi-image only) */
        if (imgs.length > 1) {
            var prev = document.createElement('button');
            prev.className = 'updx-arrow updx-arrow-prev';
            prev.setAttribute('aria-label', 'Previous');
            prev.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prev.addEventListener('click', function (e) { e.stopPropagation(); goSlide(curSlide - 1); });

            var nxt = document.createElement('button');
            nxt.className = 'updx-arrow updx-arrow-next';
            nxt.setAttribute('aria-label', 'Next');
            nxt.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nxt.addEventListener('click', function (e) { e.stopPropagation(); goSlide(curSlide + 1); });

            imgSide.appendChild(prev);
            imgSide.appendChild(nxt);

            var dotsWrap = document.createElement('div');
            dotsWrap.className = 'updx-img-dots';
            imgs.forEach(function (_, i) {
                var dot = document.createElement('button');
                dot.className = 'updx-img-dot' + (i === 0 ? ' active' : '');
                dot.setAttribute('aria-label', 'Photo ' + (i + 1));
                (function (idx) {
                    dot.addEventListener('click', function (e) { e.stopPropagation(); goSlide(idx); });
                }(i));
                dotsWrap.appendChild(dot);
                dotEls.push(dot);
            });
            imgSide.appendChild(dotsWrap);
        }

        /* title */
        var titleEl = document.createElement('h2');
        titleEl.className = 'updx-title'; titleEl.textContent = title;

        /* desc */
        var descEl = document.createElement('p');
        descEl.className = 'updx-desc'; descEl.textContent = desc;

        /* close btn */
        var closeBtn = document.createElement('button');
        closeBtn.className = 'updx-close-btn';
        closeBtn.innerHTML = '<i class="fas fa-times"></i> Close';
        closeBtn.addEventListener('click', function (e) { e.stopPropagation(); collapseCard(); });

        textSide.appendChild(titleEl);
        textSide.appendChild(descEl);
        textSide.appendChild(closeBtn);
    }

    /* ────────────────────────────────────────────────
       Expand
    ──────────────────────────────────────────────── */
    function expandCard(card) {
        if (activeCard === card) { collapseCard(); return; }
        if (activeCard) collapseCard();

        var row = card.parentElement;
        if (!row || !row.classList.contains('upd-row')) return;

        /* remember original DOM order */
        rowOrigOrder = Array.from(row.children);

        /* the siblings (cards in the same row that are NOT the active card) */
        var siblings = rowOrigOrder.filter(function (c) { return c !== card; });

        /* reorder: active card first, then first sibling, hide the rest */
        row.innerHTML = '';
        row.appendChild(card);
        if (siblings[0]) row.appendChild(siblings[0]);
        /* hide extra siblings (shouldn't happen in 3-col row since we only have 2 siblings,
           but we show only 1 alongside the expanded card) */
        if (siblings[1]) {
            siblings[1].style.display = 'none';
        }

        injectExpandContent(card);

        activeCard = card;
        activeRow  = row;
        card.classList.add('upd-active');
        row.classList.add('upd-row-expanded');

        setTimeout(function () {
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 80);
    }

    /* ────────────────────────────────────────────────
       Collapse
    ──────────────────────────────────────────────── */
    function collapseCard() {
        if (!activeCard || !activeRow) return;

        activeCard.classList.remove('upd-active');
        activeRow.classList.remove('upd-row-expanded');

        /* restore original DOM order */
        activeRow.innerHTML = '';
        rowOrigOrder.forEach(function (c) {
            c.style.display = '';
            activeRow.appendChild(c);
        });

        /* clear expanded content */
        var imgSide  = activeCard.querySelector('.updx-img-side');
        var textSide = activeCard.querySelector('.updx-text-side');
        if (imgSide)  imgSide.innerHTML  = '';
        if (textSide) textSide.innerHTML = '';

        activeCard  = null;
        activeRow   = null;
        rowOrigOrder = [];
        slideEls = []; dotEls = []; curSlide = 0; totalSlides = 0;
    }

    /* ────────────────────────────────────────────────
       Bind a card
    ──────────────────────────────────────────────── */
    function bindCard(card) {
        /* inject empty expanded panels if not already there */
        if (!card.querySelector('.updx-img-side')) {
            var imgSide  = document.createElement('div'); imgSide.className  = 'updx-img-side';
            var textSide = document.createElement('div'); textSide.className = 'updx-text-side';
            card.appendChild(imgSide);
            card.appendChild(textSide);
        }
        card.addEventListener('click', function () { expandCard(card); });
        card.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); expandCard(card); }
        });
    }

    /* ────────────────────────────────────────────────
       Show more / less
    ──────────────────────────────────────────────── */
    window.toggleUpdates = function () {
        showingAll = !showingAll;
        collapseCard();

        hiddenCards.forEach(function (card) {
            if (showingAll) {
                card.classList.remove('upd-card-hidden');
                card.classList.add('upd-show');
            } else {
                card.classList.add('upd-card-hidden');
                card.classList.remove('upd-show');
            }
        });

        /* rebuild rows from scratch */
        buildRows();

        if (toggleLabel) {
            toggleLabel.textContent = showingAll
                ? 'Show Less'
                : 'Show All ' + (hiddenCards.length + 3) + ' Posts';
        }
        if (toggleIcon) {
            toggleIcon.className = showingAll ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
        }
        if (!showingAll && outerGrid) {
            outerGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    if (seeAllBtn) {
        seeAllBtn.addEventListener('click', function () {
            if (!showingAll) toggleUpdates();
            setTimeout(function () {
                outerGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 50);
        });
    }

    /* ESC / arrow keys */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && activeCard) collapseCard();
        if (activeCard) {
            if (e.key === 'ArrowLeft')  goSlide(curSlide - 1);
            if (e.key === 'ArrowRight') goSlide(curSlide + 1);
        }
    });

    /* ── INIT ── */
    buildRows();

}());