// js/scripts.js

// Función para filtrar productos en tiempo real
function buscarProductos() {
    let input = document.getElementById('buscador');
    let filter = input.value.toLowerCase();
    let productos = document.getElementsByClassName('producto-card');

    for (let i = 0; i < productos.length; i++) {
        let nombre = productos[i].getElementsByTagName('h3')[0].innerText.toLowerCase();
        let descripcion = productos[i].getElementsByTagName('p')[0].innerText.toLowerCase();
        if (nombre.includes(filter) || descripcion.includes(filter)) {
            productos[i].style.display = "";
        } else {
            productos[i].style.display = "none";
        }
    }
}

// Función para filtrar por categoría
document.addEventListener('DOMContentLoaded', function() {
    let botonesCategoria = document.querySelectorAll('.categoria-btn');
    botonesCategoria.forEach(function(btn) {
        btn.addEventListener('click', function() {
            let categoria = this.innerText.toLowerCase();
            let productos = document.getElementsByClassName('producto-card');

            for (let i = 0; i < productos.length; i++) {
                let productoCategoria = productos[i].getElementsByTagName('p')[1].innerText.toLowerCase();
                if (productoCategoria.includes(categoria)) {
                    productos[i].style.display = "";
                } else {
                    productos[i].style.display = "none";
                }
            }
        });
    });

    // Agregar al carrito con AJAX (opcional)
    let botonesAgregar = document.querySelectorAll('.btn-agregar');
    botonesAgregar.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            let id = this.getAttribute('data-id');

            // Enviar solicitud AJAX para agregar al carrito
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'agregar_carrito.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.status == 200) {
                    alert('Producto agregado al carrito.');
                    // Actualizar el contador del carrito si es necesario
                }
            };
            xhr.send('id=' + id);
        });
    });
});

form.addEventListener('submit', (event) => {
    const mensajeSeleccion = document.getElementById('mensaje-seleccion');
    if (!inputVariante.value || !inputPrecio.value) {
        event.preventDefault(); // Detener el envío del formulario

        // Mostrar el mensaje
        mensajeSeleccion.classList.add('mensaje-visible');
        mensajeSeleccion.classList.remove('mensaje-oculto');

        // Ocultar el mensaje después de 3 segundos
        setTimeout(() => {
            mensajeSeleccion.classList.add('mensaje-oculto');
            mensajeSeleccion.classList.remove('mensaje-visible');
        }, 3000);
    }
});
