/*=============== SHOW MENU AMBURGUESA ===============*/

const showMenu = (toggleId, navId) =>{
   const toggle = document.getElementById(toggleId),
         nav = document.getElementById(navId)

   toggle.addEventListener('click', () =>{
       // Add show-menu class to nav menu
       nav.classList.toggle('show-menu')

       // Add show-icon to show and hide the menu icon
       toggle.classList.toggle('show-icon')
   })
}

showMenu('nav-toggle','nav-menu')

/* =============== BARRA LATERAL DEL CARRITO =============== */

document.addEventListener("DOMContentLoaded", function () {
    const botonBolsa = document.querySelector(".carrito_boton");
    const sidebarCarrito = document.getElementById("sidebarCarrito");
    const cerrarSidebar = document.getElementById("cerrarSidebar");
    const overlay = document.getElementById("overlay");

    // Función para abrir la barra lateral
    function abrirSidebar() {
        sidebarCarrito.classList.add("active");
        overlay.classList.add("active");
    }

    // Función para cerrar la barra lateral
    function cerrarSidebarCarrito() {
        sidebarCarrito.classList.remove("active");
        overlay.classList.remove("active");
    }

    // Mostrar barra lateral y overlay
    botonBolsa.addEventListener("click", abrirSidebar);

    // Ocultar barra lateral y overlay al hacer clic en cerrar o en el overlay
    cerrarSidebar.addEventListener("click", cerrarSidebarCarrito);
    overlay.addEventListener("click", cerrarSidebarCarrito);
});

/*================= MENÚ EDITAR USUARIO =================*/

document.addEventListener('click', function (event) {
    const menu = document.getElementById('usuarioMenu'); // Contenedor del menú
    const toggle = document.getElementById('toggleProfileMenu'); // Checkbox que controla el menú

    // Si el clic ocurrió fuera del menú y del botón, desmarcar el checkbox
    if (!menu.contains(event.target)) {
        toggle.checked = false;
    }
});

/*=============== NAV PEGAJOSO ===============*/

let prevScrollPos = window.pageYOffset;
const header = document.querySelector('.header_nav');

window.onscroll = function() {
  // Verifica si el ancho de la ventana es mayor o igual a 1024px
  if (window.innerWidth >= 1023) {
    let currentScrollPos = window.pageYOffset;

    if (prevScrollPos > currentScrollPos) {
      // Muestra el header cuando haces scroll hacia arriba
      header.style.top = "0";
    } else {
      // Oculta el header cuando haces scroll hacia abajo
      header.style.top = "-80px"; // Ajusta según el tamaño del header
    }

    prevScrollPos = currentScrollPos;
  }
};

/*=============== PRECARGADOR ===============*/

window.addEventListener("load", function() {
    document.getElementById("preloader").style.display = "none";
});

window.addEventListener("load", function() {
    document.getElementById("preloader").style.display = "none";
    
    document.body.classList.add("loaded");
});

/*=============== ANIMACIÓN DEL IMAGOTIPO ===============*/

document.querySelectorAll(".logo_texto").forEach(link => {
    link.innerHTML = link.innerText.split('').map((letters, i) => {
        if (letters === ' ') {
            return ' ';
        } else {
            return `<span style="transition-delay:${i * 50}ms">${letters}</span>`
        }
    }).join('');
});

document.querySelectorAll(".logo_texto_footer").forEach(link => {
    link.innerHTML = link.innerText.split('').map((letters, i) => {
        if (letters === ' ') {
            return ' ';
        } else {
            return `<span style="transition-delay:${i * 50}ms">${letters}</span>`
        }
    }).join('');
});

/*=============== IR ARRIBA DE LA PAGINA ===============*/

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth' // Hace que el desplazamiento sea suave
    });
}

/*=============== JS DE PRODUCTO SELECCIONADO ===============*/

// Variables de carrusel del producto
let imagenActual = 0;
const imagenes = document.querySelectorAll('.carrusel_producto .imagen');

// Mostrar imagen anterior en el carrusel
function anterior() {
    imagenes[imagenActual].classList.remove('imagen-activa');
    imagenActual = (imagenActual > 0) ? imagenActual - 1 : imagenes.length - 1;
    imagenes[imagenActual].classList.add('imagen-activa');
}

// Mostrar imagen siguiente en el carrusel
function siguiente() {
    imagenes[imagenActual].classList.remove('imagen-activa');
    imagenActual = (imagenActual < imagenes.length - 1) ? imagenActual + 1 : 0;
    imagenes[imagenActual].classList.add('imagen-activa');
}

// Pestañas de producto
const pestanas = document.querySelectorAll('.lista-pestanas li');
const paneles = document.querySelectorAll('.contenido-pestanas > div');

pestanas.forEach((pestana, index) => {
    pestana.addEventListener('click', () => {
        document.querySelector('.pestana-activa').classList.remove('pestana-activa');
        pestana.classList.add('pestana-activa');
        document.querySelector('.contenido-pestanas .panel-descripcion').style.display = 'none';
        paneles.forEach(panel => panel.style.display = 'none');
        paneles[index].style.display = 'block';
    });
});


/*=============== SLIDER DEL BANNER PRINCIPAL ===============*/

const slides = document.querySelectorAll('.slide');
const indicators = document.querySelectorAll('.dot');
const carousel = document.querySelector('.carousel_banner');

let currentIndex = 0;
let autoSlideInterval;

// Función para cambiar de diapositiva
function changeSlide(index) {
    currentIndex = (index + slides.length) % slides.length;  // Asegurar que el índice esté en el rango correcto

    // Mover el carrusel
    const offset = -currentIndex * 100;
    carousel.style.transform = `translateX(${offset}%)`;

    // Actualizar indicadores activos
    indicators.forEach(dot => dot.classList.remove('active'));
    indicators[currentIndex].classList.add('active');

    // Reiniciar animaciones (opcional)
    slides.forEach(slide => {
        slide.querySelectorAll('.titulo_banner, .enfasis_banner, .descripcion_banner, .botones_banner').forEach(el => {
            el.style.animation = 'none';
            el.offsetHeight; // Forzar reflow
            el.style.animation = ''; // Reactivar la animación
        });
    });
}

// Función para avanzar a la siguiente diapositiva
function nextSlide() {
    changeSlide(currentIndex + 1);
}

// Función para retroceder a la diapositiva anterior
function prevSlide() {
    changeSlide(currentIndex - 1);
}

// Reiniciar el desplazamiento automático (cuando se usa manualmente la navegación)
function resetAutoSlide() {
    stopAutoSlide();  // Detener el desplazamiento automático
    startAutoSlide(); // Reiniciar el desplazamiento automático
}

// Botones de navegación manual
document.querySelector('.btn_banner_next').addEventListener('click', () => {
    nextSlide();
    resetAutoSlide();  // Reiniciar el temporizador después de avanzar manualmente
});

document.querySelector('.btn_banner_prev').addEventListener('click', () => {
    prevSlide();
    resetAutoSlide();  // Reiniciar el temporizador después de retroceder manualmente
});

// Configurar desplazamiento automático
function startAutoSlide() {
    autoSlideInterval = setInterval(nextSlide, 5000); // Cambia de diapositiva cada 5 segundos
}

// Detener el desplazamiento automático
function stopAutoSlide() {
    clearInterval(autoSlideInterval);
}

// Iniciar el carrusel automáticamente
startAutoSlide();
changeSlide(currentIndex);

// Pausar el auto-slide solo cuando el mouse está sobre la sección del banner, no sobre los botones
document.querySelector('.banner').addEventListener('mouseover', (event) => {
    // Verifica si el puntero está sobre un botón de navegación, si no, detén el auto-slide
    if (!event.target.closest('.btn_banner_next') && !event.target.closest('.btn_banner_prev')) {
        stopAutoSlide();
    }
});

// Reanudar el auto-slide cuando el mouse sale del área completa del banner
document.querySelector('.banner').addEventListener('mouseout', (event) => {
    if (!event.target.closest('.btn_banner_next') && !event.target.closest('.btn_banner_prev')) {
        startAutoSlide();
    }
});

/*=============== SLIDER DE PRODUCTOS DAMAS/CABALLEROS/NIÑOS ===============*/

$(document).ready(function(){
    $(".carousel").owlCarousel({
        margin: 20,
        nav: false,
        loop: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        smartSpeed: 600,
        slideTransition : 'ease',
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',

        responsive: {
            0: { items: 1, nav: false },
            600: { items: 2, nav: false },
            1024: { items: 3, nav: false },
            1200: { items: 4, nav: false }
        }
    });

    // Eventos de navegación por sección
    $("#button-prev-caballeros").click(function(){
        $("#caballeros .carousel").trigger('prev.owl.carousel');
    });
    $("#button-next-caballeros").click(function(){
        $("#caballeros .carousel").trigger('next.owl.carousel');
    });

    $("#button-prev-damas").click(function(){
        $("#damas .carousel").trigger('prev.owl.carousel');
    });
    $("#button-next-damas").click(function(){
        $("#damas .carousel").trigger('next.owl.carousel');
    });

    $("#button-prev-ninos").click(function(){
        $("#ninos .carousel").trigger('prev.owl.carousel');
    });
    $("#button-next-ninos").click(function(){
        $("#ninos .carousel").trigger('next.owl.carousel');
    });

    $("#button-prev-ninas").click(function(){
        $("#ninas .carousel").trigger('prev.owl.carousel');
    });
    $("#button-next-ninas").click(function(){
        $("#ninas .carousel").trigger('next.owl.carousel');
    });
});

