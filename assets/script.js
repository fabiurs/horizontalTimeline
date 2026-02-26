document.addEventListener("DOMContentLoaded", () => {
        // Accordion toggle button logic (only hide/show text)
        const toggleBtn = document.querySelector('.horizontal-timeline-toggle-all');
        let accordionOpen = false;
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                accordionOpen = !accordionOpen;
                toggleBtn.classList.toggle('toggled', accordionOpen);
                document.querySelectorAll('.horizontal-timeline-text').forEach(el => {
                    if (accordionOpen) {
                        el.classList.add('horizontal-timeline-text-visible');
                    } else {
                        el.classList.remove('horizontal-timeline-text-visible');
                    }
                });
            });
        }
    const wrapper = document.querySelector('.horizontal-timeline-wrapper');
    const track   = document.getElementById('horizontal-timeline-container');
    if (!track || !wrapper) return;

    let state = {
        target: 0,
        current: 0,
        isDragging: false,
        startX: 0,
        prevTarget: 0
    };

    /* ── Viewport detection ──────────────────────────────── */
    let isInViewport = false;

    const observer = new IntersectionObserver(
        ([entry]) => { isInViewport = entry.isIntersecting; },
        { threshold: 0.1 }
    );
    observer.observe(wrapper);

    const lerp = (start, end, amt) => (1 - amt) * start + amt * end;


    /* ── Drag Input (mouse & touch) ─── */
    // Mouse
    wrapper.addEventListener('mousedown', (e) => {
        state.isDragging = true;
        state.startX = e.clientX;
        state.prevTarget = state.target;
    });
    window.addEventListener('mousemove', (e) => {
        if (!state.isDragging) return;
        const delta = e.clientX - state.startX;
        state.target = state.prevTarget + delta * 2;
    });
    window.addEventListener('mouseup', () => state.isDragging = false);

    // Touch
    wrapper.addEventListener('touchstart', (e) => {
        if (e.touches.length !== 1) return;
        state.isDragging = true;
        state.startX = e.touches[0].clientX;
        state.prevTarget = state.target;
    });
    window.addEventListener('touchmove', (e) => {
        if (!state.isDragging || e.touches.length !== 1) return;
        const delta = e.touches[0].clientX - state.startX;
        state.target = state.prevTarget + delta * 2;
    });
    window.addEventListener('touchend', () => state.isDragging = false);

    // Prevent dragging of images inside the timeline
    const images = document.querySelectorAll('.horizontal-timeline-image img');
    images.forEach(img => {
        img.addEventListener('dragstart', (e) => e.preventDefault());
    });

    /* ── Core Loop ───────────────────────────────────────── */
    function frame() {
        const maxScroll = -(track.scrollWidth - window.innerWidth + (window.innerWidth * 0.4));
        state.target = Math.max(Math.min(state.target, 0), maxScroll);

        state.current = lerp(state.current, state.target, 0.08);
        track.style.transform = `translateX(${state.current}px)`;

        // Focus Effect
        const items = document.querySelectorAll('.horizontal-timeline-item');
        items.forEach(item => {
            const center = window.innerWidth / 2;
            const pos = item.getBoundingClientRect().left;
            if (pos > center - 250 && pos < center + 250) item.classList.add('is-active');
            else item.classList.remove('is-active');
        });

        requestAnimationFrame(frame);
    }
    frame();

    // Mouse wheel scroll: move timeline horizontally when in viewport
    wrapper.addEventListener('wheel', (e) => {
        if (!isInViewport) return;
        // Only scroll horizontally
        if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
            e.preventDefault();
            state.target -= e.deltaY * 1.2; // Adjust multiplier for sensitivity
        } else if (e.deltaX !== 0) {
            e.preventDefault();
            state.target -= e.deltaX * 1.2;
        }
    }, { passive: false });
});