//planes js

const btnMensual = document.getElementById("btn-mensual");
const btnEntradas = document.getElementById("btn-entradas");
const navToggle = document.getElementById("nav-toggle");
const navMenu = document.getElementById("nav-menu");

const planesMensuales = document.getElementById("planes-mensuales");
const planesEntradas = document.getElementById("planes-entradas");

if (btnMensual && btnEntradas && planesMensuales && planesEntradas) {
    btnMensual.addEventListener("click", () => {
        btnMensual.classList.add("activo");
        btnEntradas.classList.remove("activo");

        planesMensuales.classList.remove("oculto");
        planesEntradas.classList.add("oculto");
    });

    btnEntradas.addEventListener("click", () => {
        btnEntradas.classList.add("activo");
        btnMensual.classList.remove("activo");

        planesEntradas.classList.remove("oculto");
        planesMensuales.classList.add("oculto");
    });
}

function closeMobileMenu() {
    if (!navToggle || !navMenu) {
        return;
    }

    navToggle.classList.remove("is-open");
    navMenu.classList.remove("is-open");
    navToggle.setAttribute("aria-expanded", "false");
}

if (navToggle && navMenu) {
    navToggle.addEventListener("click", () => {
        const isOpen = navMenu.classList.toggle("is-open");
        navToggle.classList.toggle("is-open", isOpen);
        navToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });

    navMenu.querySelectorAll("a").forEach((link) => {
        link.addEventListener("click", () => {
            closeMobileMenu();
        });
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
    });

    document.addEventListener("click", (event) => {
        if (!navMenu.classList.contains("is-open")) {
            return;
        }

        if (event.target instanceof Node && !navMenu.contains(event.target) && !navToggle.contains(event.target)) {
            closeMobileMenu();
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            closeMobileMenu();
        }
    });
}

// scroll suave navbar
const navbarAnchors = document.querySelectorAll('.nav-center a[href^="#"], .logo-link[href^="#"]');

function easeInOutCubic(t) {
    return t < 0.5
        ? 4 * t * t * t
        : 1 - Math.pow(-2 * t + 2, 3) / 2;
}

function smoothScrollTo(targetY, duration) {
    const startY = window.scrollY;
    const distance = targetY - startY;
    const startTime = performance.now();

    function step(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easedProgress = easeInOutCubic(progress);

        window.scrollTo(0, startY + (distance * easedProgress));

        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    }

    window.requestAnimationFrame(step);
}

navbarAnchors.forEach((anchor) => {
    anchor.addEventListener("click", (event) => {
        const targetId = anchor.getAttribute("href");
        if (!targetId || targetId === "#") {
            return;
        }

        const target = document.querySelector(targetId);
        if (!target) {
            return;
        }

        event.preventDefault();

        const navbar = document.querySelector(".navbar");
        const navbarHeight = navbar ? navbar.offsetHeight : 0;
        const targetY = target.getBoundingClientRect().top + window.scrollY - navbarHeight - 14;

        closeMobileMenu();
        smoothScrollTo(Math.max(targetY, 0), 750);
        window.history.replaceState(null, "", targetId);
    });
});


//formulario js

// carrusel js
document.addEventListener("DOMContentLoaded", function () {

    const slides = document.querySelectorAll(".carrusel1");
    let indiceActual = 0;

    function cambiarCarrusel() {
        slides[indiceActual].classList.remove("activo");
        indiceActual++;
        if (indiceActual >= slides.length) {
            indiceActual = 0;
        }
        slides[indiceActual].classList.add("activo");
    }

    setInterval(cambiarCarrusel, 3000);

});



