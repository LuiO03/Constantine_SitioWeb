
<!--=============== FOOTER ===============-->

        <footer class="footer">
            <div class="footer__container">
                <!-- Columna Legales -->
                <div class="footer__column">
                <div class="subtitulo_cuerpo">INFORMACIÓN LEGAL</div>
                <ul>
                    <li><a href="<?php echo $url_base;?>/legal/politicas_privacidad/">Política de privacidad</a></li>
                    <li><a href="<?php echo $url_base;?>/legal/terminos_condiciones/">Términos y condiciones</a></li>
                    <li><a href="<?php echo $url_base;?>/legal/libro_reclamaciones/">Libro de reclamaciones</a></li>
                </ul>
                </div>
        
                <!-- Columna Catálogo de Productos -->
                <div class="footer__column">
                <div class="subtitulo_cuerpo">CATÁLOGO DE PRODUCTOS</div>
                <ul>
                    <li><a href="<?php echo $url_base;?>/productos/?publico=">Todos</a></li>
                    <li><a href="<?php echo $url_base;?>/productos/?publico=&categoria=Abrigos">Abrigos</a></li>
                    <li><a href="<?php echo $url_base;?>/productos/?publico=&categoria=Pantalones">Pantalones</a></li>
                    <li><a href="<?php echo $url_base;?>/productos/?publico=&categoria=Shorts">Shorts</a></li>
                    <li><a href="<?php echo $url_base;?>/productos/?publico=&categoria=Pijamas">Pijamas</a></li>
                </ul>
                </div>
        
                <!-- Columna Contacto -->
                <div class="footer__column">
                <div class="subtitulo_cuerpo">NAVEGACIÓN</div>
                <ul>
                    <li><a href="<?php echo $url_base;?>/nosotros/">Nosotros</a></li>
                    <li><a href="<?php echo $url_base;?>/servicios/">Servicios</a></li>
                    <li><a href="<?php echo $url_base;?>/blog/">Blog</a></li>
                    <li><a href="<?php echo $url_base;?>/contactos/">Contactos</a></li>
                </ul>
                </div>
        
                <!-- Columna Redes Sociales -->
                <div class="footer__column">
                <div class="subtitulo_cuerpo">CONTÁCTANOS</div>
                <ul>

                <?php foreach($contactos_negocio as $contacto) {?>
                    <li><a href="tel:+51900159745"><?php echo $contacto['telefono']; ?></a></li>
                    <li><a href="mailto:constantine@gmail.com"><?php echo $contacto['correo']; ?></a></li>
                    <li><a href="#"><?php echo $contacto['direccion']; ?></a></li>
                <?php } ?>
                </ul>

                <div class="subtitulo_cuerpo">REDES SOCIALES</div>
                    <ul class="social_icons">

                        <?php foreach ($redes_sociales as $red){ ?>
                            <li class="<?php echo $red['red']?>">
                                <a href="<?php echo $red['enlace']?>"><i class="<?php echo $red['icono']?> icon"></i></a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>

                <!-- Columna Logo de la Empresa -->
                <a href="<?php echo $url_base;?>" class="footer__column logo_column">
                    <img src="<?php echo $url_base;?>/images/logos/logo_solo_white.png" alt="Logo Constantine">
                    <div class="logo_texto logo_texto_footer">CONSTANTINE</div>
                    <div>"La elegancia de ser único."</div>
                </a>
            </div>

            <!-- Botón de Subir -->
            <button class="scroll_arriba" onclick="scrollToTop()">
                <i class="ri-arrow-up-s-fill"></i>
            </button>
        
            <!-- Parte inferior del footer -->
            <div class="footer__bottom">
                <p class="subtitulo_cuerpo">©Constantine 2024 - Todos los Derechos Reservados - Huancayo</p>
            </div>
        </footer>
     
        <!--=============== MAIN JS ===============-->
        <script src="<?php echo $url_base;?>/js/main.js"></script>

    </body>
</html>