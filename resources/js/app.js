import { Livewire, Alpine } from "../../vendor/livewire/livewire/dist/livewire.esm";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import Chart from "chart.js/auto";

window.Chart = Chart;

gsap.registerPlugin(ScrollTrigger);

/* ── Realtime (optional) ─────────────────────────────────────
   Pusher/Echo are intentionally not bundled. If you later enable
   broadcasting, load Echo + a driver here. Livewire still works
   fully without realtime — status pages just refresh on navigation. */

/* ── Motion system ───────────────────────────────────────────── */
const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
if (reducedMotion) document.documentElement.classList.add("reduced-motion");

function initMotion(scope = document) {
    if (reducedMotion) return;

    // Hero load sequence: elements tagged data-hero animate in order
    const hero = [...scope.querySelectorAll("[data-hero]")].filter((el) => !el.dataset.heroInit);
    hero.forEach((el) => (el.dataset.heroInit = "1"));
    if (hero.length) {
        gsap.fromTo(hero,
            { y: 32, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.9, stagger: 0.12, ease: "power3.out", clearProps: "transform" }
        );
    }

    // 3D tilt cards: <div data-tilt> ... <span class="tilt-glare"></span></div>
    scope.querySelectorAll("[data-tilt]").forEach((el) => {
        if (el.dataset.tiltInit) return;
        el.dataset.tiltInit = "1";
        const strength = parseFloat(el.dataset.tilt) || 10;
        el.addEventListener("mousemove", (e) => {
            const r = el.getBoundingClientRect();
            const px = (e.clientX - r.left) / r.width;
            const py = (e.clientY - r.top) / r.height;
            el.style.setProperty("--glare-x", `${px * 100}%`);
            el.style.setProperty("--glare-y", `${py * 100}%`);
            gsap.to(el, {
                rotateY: (px - 0.5) * strength,
                rotateX: (0.5 - py) * strength,
                transformPerspective: 900,
                duration: 0.4, ease: "power2.out",
            });
        });
        el.addEventListener("mouseleave", () => {
            gsap.to(el, { rotateX: 0, rotateY: 0, duration: 0.7, ease: "elastic.out(1, 0.5)" });
        });
    });

    // Magnetic buttons: <a data-magnetic>
    scope.querySelectorAll("[data-magnetic]").forEach((el) => {
        if (el.dataset.magInit) return;
        el.dataset.magInit = "1";
        el.addEventListener("mousemove", (e) => {
            const r = el.getBoundingClientRect();
            gsap.to(el, { x: (e.clientX - r.left - r.width / 2) * 0.25, y: (e.clientY - r.top - r.height / 2) * 0.25, duration: 0.3 });
        });
        el.addEventListener("mouseleave", () => gsap.to(el, { x: 0, y: 0, duration: 0.5, ease: "elastic.out(1, 0.4)" }));
    });

    // Scroll reveals — gsap.from keeps the element visible in markup, so a
    // Livewire morph can never leave the page blank.
    scope.querySelectorAll("[data-reveal]").forEach((el) => {
        if (el.dataset.revealed) return;
        el.dataset.revealed = "1";
        gsap.from(el, {
            opacity: 0, y: 28, duration: 0.8, ease: "power3.out", clearProps: "all",
            scrollTrigger: { trigger: el, start: "top 88%", once: true },
        });
    });

    // Counters: <span data-counter="12400" data-prefix="₹">
    scope.querySelectorAll("[data-counter]").forEach((el) => {
        const target = parseFloat(el.dataset.counter);
        const prefix = el.dataset.prefix ?? "";
        const suffix = el.dataset.suffix ?? "";
        ScrollTrigger.create({
            trigger: el, start: "top 90%", once: true,
            onEnter: () => {
                const obj = { v: 0 };
                gsap.to(obj, {
                    v: target, duration: 1.6, ease: "power2.out",
                    onUpdate: () => (el.textContent = prefix + Math.round(obj.v).toLocaleString("en-IN") + suffix),
                });
            },
        });
    });

    // Status cycler used by the hero "live order" card
    scope.querySelectorAll("[data-status-cycle]").forEach((card) => {
        const steps = [...card.querySelectorAll("[data-step]")];
        if (!steps.length) return;
        let i = 0;
        const tick = () => {
            steps.forEach((s, n) => {
                s.classList.toggle("opacity-100", n <= i);
                s.classList.toggle("opacity-35", n > i);
                s.querySelector(".timeline-dot")?.classList.toggle("done", n <= i);
                s.querySelector(".timeline-dot")?.classList.toggle("active", n === i);
            });
            gsap.fromTo(steps[i], { x: 6 }, { x: 0, duration: 0.4 });
            i = (i + 1) % steps.length;
        };
        tick();
        setInterval(tick, 2200);
    });
}

// livewire:navigated fires on the first load AND on every wire:navigate —
// one init path, and only stale triggers (elements gone from the DOM) are killed.
document.addEventListener("livewire:navigated", () => {
    ScrollTrigger.getAll().forEach((t) => {
        if (t.trigger && !document.contains(t.trigger)) t.kill();
    });
    initMotion();
    ScrollTrigger.refresh();
});

/* Flash rows updated over Pusher */
window.addEventListener("rt-flash", (e) => {
    const el = document.querySelector(e.detail?.selector ?? "");
    if (!el) return;
    el.classList.remove("rt-updated");
    void el.offsetWidth;
    el.classList.add("rt-updated");
});

/* ── Theme toggle (dark / light) ─────────────────────────────── */
Alpine.store("theme", {
    // Single light theme — dark mode disabled by design.
    dark: false,
    toggle() {
        // no-op: the product ships with one light theme
    },
    init() { document.documentElement.classList.remove("dark"); localStorage.removeItem("theme"); },
});

window.Alpine = Alpine;
Livewire.start();

/* ── PWA service worker ──────────────────────────────────────── */
if ("serviceWorker" in navigator) {
    navigator.serviceWorker.register("/sw.js").catch(() => {});
}
