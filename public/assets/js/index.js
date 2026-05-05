//planes js

const btnMensual = document.getElementById("btn-mensual");
const btnEntradas = document.getElementById("btn-entradas");

const planesMensuales = document.getElementById("planes-mensuales");
const planesEntradas = document.getElementById("planes-entradas");

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


//formulario js

const formulario = document.getElementById("formulario-contacto");

formulario.addEventListener("submit", function (e) {
    e.preventDefault(); 
    
    formulario.reset();

  
});


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



