export function initBracket(matchConnections) {
    positionBracketMatches();
    scheduleDraw(matchConnections);

    window.addEventListener('resize', () => {
        positionBracketMatches();
        scheduleDraw(matchConnections);
    });

    const container = document.querySelector('.bracket-lines')?.closest('.relative');
    if (container && 'ResizeObserver' in window) {
        const ro = new ResizeObserver(() => {
            positionBracketMatches();
            scheduleDraw(matchConnections);
        });
        ro.observe(container);
    }

    window.addEventListener('scroll', () => scheduleDraw(matchConnections), { passive: true });
}

function scheduleDraw(matchConnections) {
    requestAnimationFrame(() => {
        requestAnimationFrame(() => drawBracketLines(matchConnections));
    });
}

function positionBracketMatches() {
    // Base spacing to avoid overlap (keep original simple stacking)
    const rounds = document.querySelectorAll('.bracket-round');
    const MATCH_HEIGHT = 60;
    const MATCH_SPACING = 32;

    rounds.forEach((round, roundIndex) => {
        const matches = round.querySelectorAll('[data-match-id]');
        matches.forEach(m => {
            // Reset previous transforms each cycle
            m.style.transform = '';
        });
        if (roundIndex === 0) {
            // First round: keep original computed spacing
            matches.forEach((match, index) => {
                if (index === 0) {
                    match.style.marginTop = '0px';
                } else {
                    match.style.marginTop = `${MATCH_HEIGHT + MATCH_SPACING}px`;
                }
            });
        } else {
            // Provide a coarse baseline vertical spacing
            const baselineSpacing = Math.pow(2, roundIndex) * (MATCH_HEIGHT + MATCH_SPACING);
            matches.forEach((match, index) => {
                if (index === 0) {
                    match.style.marginTop = `${(baselineSpacing / 2) - (MATCH_HEIGHT / 2)}px`;
                } else {
                    match.style.marginTop = `${baselineSpacing - MATCH_SPACING}px`;
                }
            });
        }
    });

    // After baseline flow, center each match between its two parent matches
    centerMatchesBetweenParents();
}

function centerMatchesBetweenParents() {
    // Build parent map: next_match_id -> array of parent elements
    const allMatches = Array.from(document.querySelectorAll('[data-match-id]'));
    const parentMap = new Map();
    allMatches.forEach(m => {
        const nextId = m.getAttribute('data-next-match-id') || m.dataset.nextMatchId;
        if (nextId) {
            if (!parentMap.has(nextId)) parentMap.set(nextId, []);
            parentMap.get(nextId).push(m);
        }
    });

    // For each child match id with 2 parents, adjust its vertical position
    parentMap.forEach((parents, childId) => {
        if (parents.length !== 2) return;
        const child = document.querySelector(`[data-match-id="${childId}"]`);
        if (!child) return;

        const pRects = parents.map(p => p.getBoundingClientRect());
        const childRect = child.getBoundingClientRect();

        const targetCenterY = (pRects[0].top + pRects[0].height / 2 + pRects[1].top + pRects[1].height / 2) / 2;
        const currentCenterY = childRect.top + childRect.height / 2;

        const delta = targetCenterY - currentCenterY;
        // Translate vertically to center between parents
        child.style.transform = `translateY(${Math.round(delta)}px)`;
    });
}

function drawBracketLines(matchConnections) {
    const svg = document.querySelector('.bracket-lines');
    if (!svg) return;
    const container = svg.closest('.relative');
    if (!container) return;

    const containerRect = container.getBoundingClientRect();
    svg.setAttribute('width', containerRect.width);
    svg.setAttribute('height', containerRect.height);
    svg.innerHTML = '';

    matchConnections.forEach(connection => {
        if (!connection.next_match_id) return;

        const fromMatch = document.querySelector(`[data-match-id="${connection.id}"]`);
        const toMatch = document.querySelector(`[data-match-id="${connection.next_match_id}"]`);
        if (!fromMatch || !toMatch) return;

        const from = getRelativeCenters(fromMatch, containerRect);
        const to = getRelativeCenters(toMatch, containerRect);

        const x1 = from.right;
        const y1 = from.centerY;
        const x2 = to.left;
        const y2 = to.centerY;

        const midX = x1 + (x2 - x1) / 2;
        const radius = 8;

        let pathData = `M ${x1},${y1} L ${midX - radius},${y1} `;
        if (y2 > y1) {
            pathData += `Q ${midX},${y1} ${midX},${y1 + radius} L ${midX},${y2 - radius} Q ${midX},${y2} ${midX + radius},${y2} `;
        } else if (y2 < y1) {
            pathData += `Q ${midX},${y1} ${midX},${y1 - radius} L ${midX},${y2 + radius} Q ${midX},${y2} ${midX + radius},${y2} `;
        } else {
            pathData += `L ${x2},${y2}`;
        }
        if (y2 !== y1) {
            pathData += `L ${x2},${y2}`;
        }

        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('d', pathData);
        path.setAttribute('stroke', '#9CA3AF');
        path.setAttribute('stroke-width', '2');
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke-linecap', 'round');
        path.setAttribute('stroke-linejoin', 'round');
        svg.appendChild(path);
    });
}

function getRelativeCenters(el, containerRect) {
    const r = el.getBoundingClientRect();
    return {
        left: r.left - containerRect.left,
        right: r.right - containerRect.left,
        centerY: (r.top - containerRect.top) + r.height / 2
    };
}