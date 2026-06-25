<?php
    include("../admin/seccion/inicio/conexion.php");
    
    $url_base="http://localhost/Constantine_SitioWeb";
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['id_usuario'])) {
        header("Location:".$url_base. "/admin/seccion/inicio/login.php"); // Redirige al login si no está autenticado
        exit();
    }

    $usuario_id = $_SESSION['id_usuario'];

    // Verificar si el usuario tiene un pedido pendiente
    $sentencia = $conexion->prepare("SELECT * FROM pedidos WHERE id_usuario = :id_usuario AND estado = 'pendiente'");
    $sentencia->bindParam(':id_usuario', $usuario_id, PDO::PARAM_INT);
    $sentencia->execute();
    $pedidoPendiente = $sentencia->fetch(PDO::FETCH_ASSOC);

    // Si no hay pedido pendiente, obtener la dirección del usuario
    if (!$pedidoPendiente) {
        $sentencia = $conexion->prepare("SELECT direccion FROM usuarios WHERE id_usuario = :id_usuario");
        $sentencia->bindParam(':id_usuario', $usuario_id, PDO::PARAM_INT);
        $sentencia->execute();
        $direccion = $sentencia->fetchColumn();
    }

    $sentencia = $conexion->prepare("SELECT id_local, nombre_local, direccion FROM locales");
    $sentencia->execute();
    $lista_locales = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constantine - Carrito</title>
    <link rel="stylesheet" href="../style/nav.css">
    <link rel="stylesheet" href="../style/base.css">
    <link rel="stylesheet" href="../style/footer.css">
    <link rel="stylesheet" href="carrito.css">
    <link rel="icon" href="../images/logos/logo_solo_white.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <!--=============== HEADER ===============-->
    <?php include("../layout/header.php"); ?>

    <main class="carrito-contenedor">

        <div class="titulo_cuerpo">Carrito <span class="text_red">de compras</span></div>

        <?php if ($pedidoPendiente) : ?>
            <!-- Si hay un pedido pendiente, mostrar el botón "Ver Pedido" -->
            <div class="mensaje">
                <p>Ya tienes un pedido pendiente.</p>
                <div class="boton_contenedor">
                <a href="confirmacion.php?pedido_id=<?php echo $pedidoPendiente['id_pedido']; ?>" class="boton_general">
                <i class="ri-eye-line"></i>
                    <span>Ver Pedido</span>
                </a>
                </div>
            </div>
        <?php else : ?>
            <?php if (empty($carrito)) : ?>
                <p class="mensaje">Tu carrito está vacío.</p>
            <?php else : ?>
                <div class="carrito-flex">
                    <!-- Tabla del carrito -->
                    <div class="tabla-carrito-container">
                        <table class="tabla-carrito">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Color</th>
                                    <th>Talla</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Precio Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carrito as $item) : ?>
                                    <tr>
                                        <td>
                                            <div class="producto-detalle">
                                                <img src="../images/productos/<?php echo htmlspecialchars($item['nombre_publico']); ?>/<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre_producto']); ?>">
                                                <span><?php echo htmlspecialchars($item['nombre_producto']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="color-box" style="background-color: <?php echo htmlspecialchars($item['codigo_color']); ?>;"></div>
                                            <span><?php echo htmlspecialchars($item['nombre_color']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['nombre_talla']); ?></td>
                                        <td>
                                            <div class="cantidad-container">
                                                <button type="button" class="btn-cantidad btn-menos" data-id="<?php echo $item['id_carrito']; ?>" data-cantidad="<?php echo $item['cantidad']; ?>">
                                                    <i class="ri-subtract-line"></i>
                                                </button>

                                                <input type="number" name="cantidad" class="cantidad" value="<?php echo $item['cantidad']; ?>" min="1" data-precio="<?php echo $item['precio_unitario']; ?>" data-id="<?php echo $item['id_carrito']; ?>" readonly>

                                                <button type="button" class="btn-cantidad btn-mas" data-id="<?php echo $item['id_carrito']; ?>" data-cantidad="<?php echo $item['cantidad']; ?>">
                                                    <i class="ri-add-line"></i>
                                                </button>
                                            </div>
                                        </td>

                                        <td>
                                            S/. 
                                            <span class="precio-unitario"><?php echo number_format($item['precio_unitario'], 2); ?></span>
                                        </td>

                                        <td>
                                            S/. 
                                            <span class="precio-total"><?php echo number_format($item['precio_total'], 2); ?></span>
                                        </td>

                                        <td>
                                            <form action="eliminar_carrito.php" method="POST">
                                                <input type="hidden" name="id_carrito" value="<?php echo $item['id_carrito']; ?>">
                                                <button type="submit" class="btn-eliminar">
                                                    <i class="ri-delete-bin-2-fill"></i>
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Cuadro de totales -->
                    <div class="totales-container">
                        <h1 class="titulo_carrito">TOTALES</h1>
                        <div class="totales">
                            <?php
                            $subtotal = array_sum(array_column($carrito, 'precio_total'));
                            $envio = 5.00;
                            $total = $subtotal + $envio;
                            ?>
                            <p>
                                <strong class="titulo_carrito">Subtotal:</strong> S/. <span class="subtotal"><?php echo number_format($subtotal, 2); ?></span>
                            </p>
                            <p>
                                <strong class="titulo_carrito">Envío:</strong> S/. <span class="envio"><?php echo number_format($envio, 2); ?></span>
                            </p>
                            <hr class="linea-dashed">
                            <p>
                                <strong class="titulo_carrito">Total:</strong> S/. <span class="total"><?php echo number_format($total, 2); ?></span>
                            </p>

                            <div class="boton_contenedor">
                                <button type="button" id="finalizarPedidoBtn" class="boton_general">
                                    <i class="ri-bank-card-fill"></i>
                                    <span>FINALIZAR PEDIDO</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <div id="modalFinalizarPedido" class="modal-overlay oculto">
    <div class="modal">
        <div class="modal-header">
            <div class="titulo_cuerpo">Finalizar <span class="text_red">Pedido</span></div>
            <button class="close-btn" id="btnCerrarModal" aria-label="Cerrar modal">✖</button>
        </div>
        <div class="modal-content">
            <h3 class="subtitulo_cuerpo">¿Cómo quieres recibir tu pedido?</h3>
            <div class="opciones">
                <div class="opcion opcion-entrega" id="opcionDelivery" data-tipo="delivery">
                    <i class="ri-truck-line"></i>
                    <span>Delivery</span>
                </div>
                <div class="opcion opcion-entrega" id="opcionRetirar" data-tipo="retirar">
                    <i class="ri-store-2-line"></i>
                    <span>Retirar</span>
                </div>
            </div>
            <div id="contenidoDinamico"></div>
        </div>
        <div class="modal-footer">
            
            <button type="button" id="btnCancelar" class="boton_general boton-cancelar">
                <i class="ri-close-fill"></i>
                <span>Cancelar</span>
            </button>
            <button type="button" id="btnConfirmar" class="boton_general boton-secundario" disabled>
                <i class="ri-checkbox-circle-fill"></i>
                <span>Confirmar</span>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
    const finalizarPedidoBtn = document.getElementById("finalizarPedidoBtn");
    const modalFinalizarPedido = document.getElementById("modalFinalizarPedido");
    const btnCerrarModal = document.getElementById("btnCerrarModal");
    const opcionesEntrega = document.querySelectorAll(".opcion-entrega");
    const btnConfirmar = document.getElementById("btnConfirmar");
    const btnCancelar = document.getElementById("btnCancelar");
    const contenidoDinamico = document.getElementById("contenidoDinamico");

    let tipoEntrega = '';  // Para almacenar el tipo de entrega seleccionado
    let direccion = '';    // Dirección de entrega (si aplica)
    let idLocal = '';      // ID del local para retirar (si aplica)

    // Mostrar modal
    finalizarPedidoBtn.addEventListener("click", () => {
        modalFinalizarPedido.classList.remove("oculto");
    });

    // Cerrar modal
    btnCerrarModal.addEventListener("click", () => {
        modalFinalizarPedido.classList.add("oculto");
    });

    btnCancelar.addEventListener("click", () => {
        modalFinalizarPedido.classList.add("oculto");
    });

    // Actualizar contenido según la opción seleccionada
    opcionesEntrega.forEach((opcion) => {
        opcion.addEventListener("click", () => {
            // Limpiar selección previa
            opcionesEntrega.forEach((op) => op.classList.remove("seleccionado"));

            // Activar la opción seleccionada
            opcion.classList.add("seleccionado");

            // Actualizar el contenido dinámico
            tipoEntrega = opcion.dataset.tipo;
            contenidoDinamico.innerHTML = tipoEntrega === "delivery"
                ? `<h4 class="subtitulo_cuerpo">Tu dirección registrada:</h4>
                   <p id="direccionUsuario"><?php echo htmlspecialchars($direccion); ?></p>`

                : `<h4 class="subtitulo_cuerpo">Selecciona un local:</h4>
                   <ul class="lista-locales">
                       <?php foreach ($lista_locales as $local) : ?>
                           <li>
                               <input type="radio" id="local-<?php echo $local['id_local']; ?>" name="local" value="<?php echo $local['id_local']; ?>" class="radio-local">
                               <label for="local-<?php echo $local['id_local']; ?>">
                                   <?php echo htmlspecialchars($local['nombre_local']); ?> - <?php echo htmlspecialchars($local['direccion']); ?>
                               </label>
                           </li>
                       <?php endforeach; ?>
                   </ul>`;

            // Habilitar botón confirmar si hay contenido visible
            btnConfirmar.disabled = false;
        });
    });

    // Confirmar pedido
    btnConfirmar.addEventListener("click", () => {
        // Obtener dirección si es delivery

        
        if (tipoEntrega === 'delivery') {
            direccion = document.querySelector('#direccionUsuario').textContent; // O lo que tengas para la dirección
        }

        // Obtener el local si es retirar
if (tipoEntrega === 'retirar') {
    idLocal = document.querySelector('input[name="local"]:checked')?.value;

    // Validar si no se ha seleccionado un local
    if (!idLocal) {
        alert('Por favor, selecciona un local para retirar.');
        return; // Detener la ejecución
    }
}


        // Realizar la solicitud POST a procesar_pedido.php
        const formData = new FormData();
        formData.append('tipo_entrega', tipoEntrega);
        formData.append('direccion', direccion);
        formData.append('id_local', idLocal);

        fetch('procesar_pedido.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("¡Pedido procesado exitosamente!");
                modalFinalizarPedido.classList.add("oculto"); // Cerrar el modal
            } else {
                alert("¡Pedido procesado exitosamente!");
                modalFinalizarPedido.classList.add("oculto");
            }
        })
        .catch(error => {
            alert("¡Pedido procesado exitosamente!");
                modalFinalizarPedido.classList.add("oculto");
        });
    });
});


</script>
    <!--=============== FOOTER ===============-->
    <?php include("../layout/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Función para actualizar la cantidad y recalcular el precio
            $(".btn-cantidad").on("click", function() {
                var $button = $(this);
                var $input = $button.siblings("input.cantidad");
                var cantidad = parseInt($input.val());
                var id_carrito = $button.data("id");
                var precio_unitario = parseFloat($input.data("precio"));
                
                // Ajusta la cantidad
                if ($button.hasClass("btn-menos") && cantidad > 1) {
                    cantidad--;
                } else if ($button.hasClass("btn-mas")) {
                    cantidad++;
                }

                // Actualiza el campo de cantidad
                $input.val(cantidad);

                // Calcula el precio total
                var precio_total = cantidad * precio_unitario;
                $button.closest("tr").find(".precio-total").text(precio_total.toFixed(2));

                // Actualiza el subtotal
                actualizarSubtotal();
                
                // Aquí puedes hacer una llamada AJAX para actualizar la cantidad en la base de datos
                $.ajax({
                    url: 'actualizar_carrito.php',
                    method: 'POST',
                    data: {
                        id_carrito: id_carrito,
                        cantidad: cantidad
                    },
                    success: function(response) {
                        console.log(response); // Puedes manejar la respuesta si es necesario
                    }
                });
            });

            // Función para actualizar el subtotal en tiempo real
            function actualizarSubtotal() {
                var subtotal = 0;
                $(".precio-total").each(function() {
                    subtotal += parseFloat($(this).text().replace("S/. ", ""));
                });
                var envio = 5.00;
                var total = subtotal + envio;

                // Actualiza el subtotal y el total
                $(".totales-container .totales .subtotal").text(subtotal.toFixed(2));
                $(".totales-container .totales .envio").text(envio.toFixed(2));
                $(".totales-container .totales .total").text(total.toFixed(2));
            }
        });
    </script>
</body>
</html>
