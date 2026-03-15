/* ════════════════════════════════════════════════════════════════
   updates.js

   RULES:
   1. All posts always visible.
   2. Normal layout: rows of 3.
   3. When a card is clicked it expands IN PLACE (stays in its row).
   4. That row becomes: [ expanded card (span 2) ] [ next post ]
   5. The post that was originally beside the expanded card in that
      row gets pushed down to the NEXT row, joining the cards below.
   6. "Next post" = index + 1, wraps to index 0 if last post.

   Example (6 posts, 2 rows):
   Normal:
     row1: [p1][p2][p3]
     row2: [p4][p5][p6]

   Click p1 (row1, idx0):  neighbour = p2
     row1: [p1 expanded][p2]       ← p3 pushed down
     row2: [p3][p4][p5]            ← p3 joins existing row2 cards
     row3: [p6]

   Click p4 (row2, idx3):  neighbour = p5
     row1: [p1][p2][p3]            ← row1 unchanged
     row2: [p4 expanded][p5]       ← p6 pushed down
     row3: [p6]

   Click p6 (row2, idx5):  neighbour = p1 (wrap)
     row1: [p2][p3][p4]            ← p1 moved out of row1
     row2: [p6 expanded][p1]       ← p5 pushed down
     row3: [p5]
   ════════════════════════════════════════════════════════════════ */
(function () {

    var outerGrid = document.getElementById('updGrid');
    if (!outerGrid) return;

    var allCards    = [];   /* original ordered list, never changes */
    var activeCard  = null;
    var curSlide    = 0;
    var totalSlides = 0;
    var slideEls    = [];
    var dotEls      = [];

    /* ── INIT ── */
    function init() {
        allCards = Array.from(outerGrid.querySelectorAll(':scope > .upd-card'));
        allCards.forEach(function (c) {
            c.classList.remove('upd-card-hidden', 'upd-show');
            c.style.display = '';
            ensurePanels(c);
            c.addEventListener('click', function () { toggleCard(c); });
            c.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleCard(c); }
            });
        });
        var tw = document.getElementById('updToggleWrap');
        var sa = document.getElementById('updSeeAllBtn');
        if (tw) tw.style.display = 'none';
        if (sa) sa.style.display = 'none';
        renderGrid();
    }

    /* ════════════════════════════════════════════════════════════
       RENDER GRID
    ════════════════════════════════════════════════════════════ */
    function renderGrid() {
        outerGrid.innerHTML = '';

        /* ── No active card: plain rows of 3 ── */
        if (!activeCard) {
            chunkIntoRows(allCards, 3).forEach(function (chunk) {
                var row = makeRow('upd-row');
                chunk.forEach(function (c) { row.appendChild(c); });
                outerGrid.appendChild(row);
            });
            return;
        }

        var n         = allCards.length;
        var activeIdx = allCards.indexOf(activeCard);

        /* Next post (neighbour) — wraps around */
        var neighbourIdx = (activeIdx + 1) % n;
        var neighbour    = allCards[neighbourIdx];

        /* Which row (0-based) does the active card normally live in? */
        var activeRow = Math.floor(activeIdx / 3);

        /* Build a flat ordered list WITHOUT active and neighbour */
        var others = allCards.filter(function (c) {
            return c !== activeCard && c !== neighbour;
        });

        /* Split others into: those that belong BEFORE active's row,
           and those that belong IN or AFTER active's row.
           "Before" = their original row index < activeRow            */
        var before = [];
        var after  = [];
        others.forEach(function (c) {
            var origIdx = allCards.indexOf(c);
            var origRow = Math.floor(origIdx / 3);
            if (origRow < activeRow) {
                before.push(c);
            } else {
                after.push(c);
            }
        });

        /* ── Rows BEFORE active row: unchanged 3-col ── */
        chunkIntoRows(before, 3).forEach(function (chunk) {
            var row = makeRow('upd-row');
            chunk.forEach(function (c) { row.appendChild(c); });
            outerGrid.appendChild(row);
        });

        /* ── Active row: expanded card + neighbour ── */
        var activeRowEl = makeRow('upd-row upd-row-has-active');
        activeRowEl.appendChild(activeCard);
        activeRowEl.appendChild(neighbour);
        outerGrid.appendChild(activeRowEl);

        /* ── Rows AFTER active row: remaining cards in 3-col ── */
        chunkIntoRows(after, 3).forEach(function (chunk) {
            var row = makeRow('upd-row');
            chunk.forEach(function (c) { row.appendChild(c); });
            outerGrid.appendChild(row);
        });
    }

    function chunkIntoRows(arr, size) {
        var rows = [];
        for (var i = 0; i < arr.length; i += size) {
            rows.push(arr.slice(i, i + size));
        }
        return rows;
    }

    function makeRow(cls) {
        var el = document.createElement('div');
        el.className = cls;
        return el;
    }

    /* ════════════════════════════════════════════════════════════
       TOGGLE / EXPAND / COLLAPSE
    ════════════════════════════════════════════════════════════ */
    function toggleCard(card) {
        if (activeCard === card) {
            collapseCard();
        } else {
            if (activeCard) collapseCard(true);
            expandCard(card);
        }
    }

    function expandCard(card) {
        activeCard = card;
        card.classList.add('upd-active');
        injectExpandContent(card);
        renderGrid();
        setTimeout(function () {
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 80);
    }

    function collapseCard(silent) {
        if (!activeCard) return;
        var card = activeCard;

        var imgSide  = card.querySelector('.updx-img-side');
        var textSide = card.querySelector('.updx-text-side');
        if (imgSide)  imgSide.innerHTML  = '';
        if (textSide) textSide.innerHTML = '';

        card.classList.remove('upd-active');
        activeCard  = null;
        slideEls    = []; dotEls = [];
        curSlide    = 0;  totalSlides = 0;

        if (!silent) renderGrid();
    }

    /* ════════════════════════════════════════════════════════════
       INJECT EXPANDED CONTENT
    ════════════════════════════════════════════════════════════ */
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

        /* Slides */
        var slidesWrap = document.createElement('div');
        slidesWrap.className = 'updx-slides';
        imgs.forEach(function (src, i) {
            var slide = document.createElement('div');
            slide.className = 'updx-slide' + (i === 0 ? ' active' : '');
            if (src) {
                var img = document.createElement('img');
                img.src = src; img.alt = title;
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

        var db = document.createElement('div');
        db.className = 'updx-date-badge';
        db.innerHTML = '<i class="fas fa-calendar-alt"></i>' + date;
        imgSide.appendChild(db);

        if (imgs.length > 1) {
            ['prev', 'next'].forEach(function (dir) {
                var btn = document.createElement('button');
                btn.className = 'updx-arrow updx-arrow-' + dir;
                btn.setAttribute('aria-label', dir === 'prev' ? 'Previous' : 'Next');
                btn.innerHTML = '<i class="fas fa-chevron-' + (dir === 'prev' ? 'left' : 'right') + '"></i>';
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    goSlide(dir === 'prev' ? curSlide - 1 : curSlide + 1);
                });
                imgSide.appendChild(btn);
            });

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

        var titleEl = document.createElement('h2');
        titleEl.className = 'updx-title';
        titleEl.textContent = title;

        var descEl = document.createElement('p');
        descEl.className = 'updx-desc';
        descEl.textContent = desc;

        var closeBtn = document.createElement('button');
        closeBtn.className = 'updx-close-btn';
        closeBtn.innerHTML = '<i class="fas fa-times"></i> Close';
        closeBtn.addEventListener('click', function (e) { e.stopPropagation(); collapseCard(); });

        textSide.appendChild(titleEl);
        textSide.appendChild(descEl);
        textSide.appendChild(closeBtn);
    }

    function goSlide(idx) {
        if (!slideEls.length) return;
        slideEls[curSlide].classList.remove('active');
        if (dotEls[curSlide]) dotEls[curSlide].classList.remove('active');
        curSlide = ((idx % totalSlides) + totalSlides) % totalSlides;
        slideEls[curSlide].classList.add('active');
        if (dotEls[curSlide]) dotEls[curSlide].classList.add('active');
    }

    function ensurePanels(card) {
        if (!card.querySelector('.updx-img-side')) {
            var imgSide  = document.createElement('div');
            imgSide.className  = 'updx-img-side';
            var textSide = document.createElement('div');
            textSide.className = 'updx-text-side';
            card.appendChild(imgSide);
            card.appendChild(textSide);
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && activeCard) collapseCard();
        if (activeCard) {
            if (e.key === 'ArrowLeft')  goSlide(curSlide - 1);
            if (e.key === 'ArrowRight') goSlide(curSlide + 1);
        }
    });

    init();

}());