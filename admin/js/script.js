/*================= VARIABLES =================*/
const logo = document.getElementById("logo");
const barraLateral = document.querySelector(".barra_lateral");
const spans = document.querySelectorAll("span");
const palanca = document.querySelector(".switch");
const circulo = document.querySelector(".circulo");
const menu = document.querySelector(".menu_hamburguesa");
const main = document.querySelector("main");
const iconoModo = document.getElementById("icono-modo");






/*================= MENÚ EDITAR USUARIO =================*/

document.addEventListener('click', function (event) {
    const menu = document.getElementById('usuarioMenu'); // Contenedor del menú
    const toggle = document.getElementById('toggleProfileMenu'); // Checkbox que controla el menú

    // Si el clic ocurrió fuera del menú y del botón, desmarcar el checkbox
    if (!menu.contains(event.target)) {
        toggle.checked = false;
    }
});
// Seleccionar el input de la imagen y el botón de eliminar
const fotoInput = document.getElementById('foto');
const preview = document.getElementById('preview');
const btnEliminar = document.getElementById('btnEliminar');

// Evento para mostrar la previsualización de la imagen seleccionada
fotoInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Evento para eliminar la imagen seleccionada
btnEliminar.addEventListener('click', () => {
    // Restaurar la imagen a la predeterminada
    preview.src = '../../images/usuarios/usuario_default.png';
    // Limpiar el input de la foto para que no suba ningún archivo
    fotoInput.value = '';
});

/*================= AUMENTO DE TAMAÑO DE LAS IMÁGENES =================*/
function toggleImageSize() {
    const preview = document.getElementById('preview');
    if (preview.classList.contains('w-25')) {
        preview.classList.remove('w-25');
        preview.classList.add('w-50');
    } else {
        preview.classList.remove('w-50');
        preview.classList.add('w-25');
    }
}

/*================= MODO OSCURO =================*/
// Función para aplicar el modo oscuro
function aplicarModoOscuro() {
    let body = document.body;
    body.classList.add("modo_oscuro");
    circulo.classList.add("prendido");

    // Cambiar todas las tablas a 'table-dark'
    let tablas = document.querySelectorAll("table");
    tablas.forEach((tabla) => {
        tabla.classList.remove("table-light");
        tabla.classList.add("table-dark");
    });
}

// Función para quitar el modo oscuro
function quitarModoOscuro() {
    let body = document.body;
    body.classList.remove("modo_oscuro");
    circulo.classList.remove("prendido");

    // Cambiar todas las tablas a 'table-light'
    let tablas = document.querySelectorAll("table");
    tablas.forEach((tabla) => {
        tabla.classList.remove("table-dark");
        tabla.classList.add("table-light");
    });
}

// Al cargar la página, verificar si el modo oscuro está activado
if (localStorage.getItem("modoOscuro") === "true") {
    aplicarModoOscuro();
} else {
    quitarModoOscuro();
}

// Evento para alternar entre modo oscuro y claro
palanca.addEventListener("click", () => {
    let body = document.body;
    body.classList.toggle("modo_oscuro");

    if (body.classList.contains("modo_oscuro")) {
        aplicarModoOscuro();
        localStorage.setItem("modoOscuro", "true"); // Guardar en localStorage
    } else {
        quitarModoOscuro();
        localStorage.setItem("modoOscuro", "false"); // Guardar en localStorage
    }
});

/*================= MINIBARRA LATERAL =================*/
// Función para aplicar el estado de la barra lateral minimizada
function aplicarMiniBarraLateral() {
    barraLateral.classList.add("mini-barra_lateral");
    main.classList.add("min-main");
    spans.forEach((span) => {
        span.classList.add("oculto");
    });
}

// Función para quitar el estado de la barra lateral minimizada
function quitarMiniBarraLateral() {
    barraLateral.classList.remove("mini-barra_lateral");
    main.classList.remove("min-main");
    spans.forEach((span) => {
        span.classList.remove("oculto");
    });
}

// Al cargar la página, verificar si la barra lateral minimizada está activada
if (localStorage.getItem("barraLateralMinimizada") === "true") {
    aplicarMiniBarraLateral();
} else {
    quitarMiniBarraLateral();
}

// Evento para alternar entre barra lateral normal y minimizada
logo.addEventListener("click", () => {
    barraLateral.classList.toggle("mini-barra_lateral");
    main.classList.toggle("min-main");
    spans.forEach((span) => {
        span.classList.toggle("oculto");
    });

    // Guardar el estado de la barra lateral en localStorage
    if (barraLateral.classList.contains("mini-barra_lateral")) {
        localStorage.setItem("barraLateralMinimizada", "true");
    } else {
        localStorage.setItem("barraLateralMinimizada", "false");
    }
});

// Evento para el menú de la barra lateral
menu.addEventListener("click", () => {
    barraLateral.classList.toggle("max-barra_lateral");

    // Alternar clases para animación
    if (barraLateral.classList.contains("max-barra_lateral")) {
        menu.children[0].classList.add("ocultar");
        menu.children[0].classList.remove("mostrar");
        menu.children[1].classList.add("mostrar");
        menu.children[1].classList.remove("ocultar");
    } else {
        menu.children[0].classList.add("mostrar");
        menu.children[0].classList.remove("ocultar");
        menu.children[1].classList.add("ocultar");
        menu.children[1].classList.remove("mostrar");
    }

    if (window.innerWidth <= 320) {
        aplicarMiniBarraLateral();
    }
});

/*================= MENUS Y SUBMENÚS DE LA BARRA LATERAL =================*/

document.querySelectorAll('.submenu-toggle').forEach(toggle => {
    toggle.addEventListener('click', function(event) {
        event.preventDefault(); // Evitar el comportamiento por defecto del enlace
        const submenu = this.nextElementSibling; // Seleccionar el submenú siguiente
        
        // Alternar la visibilidad del submenú
        if (submenu.classList.contains('show')) {
            submenu.classList.remove('show'); // Ocultar el submenú
            this.classList.remove('active'); // Remover clase active
        } else {
            submenu.classList.add('show'); // Mostrar el submenú
            this.classList.add('active'); // Agregar clase active
        }
    });
});

/*================= CONFIRMAR ELIMINACIÓN DE REGISTROS =================*/

function confirmDelete(id) {
    // Modificar el href del botón "Eliminar" en la modal
    document.getElementById('confirmDeleteButton').href = "index.php?txtID=" + id;

    // Mostrar la modal
    var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    myModal.show();
}

/*================= VISTA PREVIA DE LAS IMÁGENES =================*/
function previewImage(event) {
    const reader = new FileReader();
    const preview = document.getElementById('preview');

    reader.onload = function(){
        preview.src = reader.result;
        preview.classList.remove('d-none'); // Mostrar la imagen cuando se seleccione un archivo
    }

    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    } else {
        preview.classList.add('d-none'); // Ocultar la imagen si no hay archivo seleccionado
    }
}


/*

const logo = document.getElementById("logo");
const barraLateral = document.querySelector(".barra_lateral");
const spans = document.querySelectorAll("span");
const palanca = document.querySelector(".switch");
const circulo = document.querySelector(".circulo");
const menu = document.querySelector(".menu_hamburguesa");
const main = document.querySelector("main");

menu.addEventListener("click",()=>{
    barraLateral.classList.toggle("max-barra_lateral");
    if(barraLateral.classList.contains("max-barra_lateral")){
        menu.children[0].style.display = "none";
        menu.children[1].style.display = "block";
    }
    else{
        menu.children[0].style.display = "block";
        menu.children[1].style.display = "none";
    }
    if(window.innerWidth<=320){
        barraLateral.classList.add("mini-barra_lateral");
        main.classList.add("min_main");
        spans.forEach((span)=>{
            span.classList.add("oculto");
        })
    }
});

palanca.addEventListener("click", () => {

    let body = document.body;
    body.classList.toggle("modo_oscuro");
    let tablas = document.querySelectorAll("table");

    tablas.forEach((tabla) => {
        if (body.classList.contains("modo_oscuro")) {
            tabla.classList.remove("table-light");
            tabla.classList.add("table-dark");
        } else {
            tabla.classList.remove("table-dark");
            tabla.classList.add("table-light");
        }
    });
    circulo.classList.toggle("prendido");
});

logo.addEventListener("click",()=>{
    barraLateral.classList.toggle("mini-barra_lateral");
    main.classList.toggle("min-main");
    spans.forEach((span)=>{
        span.classList.toggle("oculto");
    });
});

*/



