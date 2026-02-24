document.addEventListener("DOMContentLoaded", () => {
    const wrapper = document.querySelector('.gst-wrapper');
    const track   = document.getElementById('gst-container');
    const bar     = document.getElementById('gst-bar');
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

    /* ── Scroll Input (only when wrapper is in viewport) ── */
    window.addEventListener('wheel', (e) => {
        if (!isInViewport) return;
        e.preventDefault();
        state.target -= e.deltaY * 0.8;
    }, { passive: false });

    /* ── Drag Input (only when starting inside wrapper) ─── */
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

    /* ── Core Loop ───────────────────────────────────────── */
    function frame() {
        const maxScroll = -(track.scrollWidth - window.innerWidth + (window.innerWidth * 0.4));
        state.target = Math.max(Math.min(state.target, 0), maxScroll);

        state.current = lerp(state.current, state.target, 0.08);
        track.style.transform = `translateX(${state.current}px)`;

        // Update Progress Bar
        const progress = (state.current / maxScroll) * 100;
        bar.style.width = `${progress}%`;

        // Focus Effect
        const items = document.querySelectorAll('.gst-item');
        items.forEach(item => {
            const center = window.innerWidth / 2;
            const pos = item.getBoundingClientRect().left;
            if (pos > center - 250 && pos < center + 250) item.classList.add('is-active');
            else item.classList.remove('is-active');
        });

        requestAnimationFrame(frame);
    }
    frame();
});