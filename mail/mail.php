<?php

    require_once __DIR__ . '/../conexionPDO.php';

    $sql = "SELECT DISTINCT c.idclientes, c.nom_cliente, c.ape_cliente, c.email_cliente
        FROM clientes as c
        INNER JOIN electrodomesticos as e ON c.idclientes = e.idclientes
        INNER JOIN reparaciones as r ON r.idelectrodomesticos = e.idelectrodomesticos
        LEFT JOIN atencion_presupuesto as a ON a.id_reparacion = r.id_reparacion
        WHERE (r.estado_reparacion = 1 
        OR a.estado_presup IN ('Presupuesto a Enviar', 'Presupuesto enviado', 'Presupuesto confirmado'))";

    $stmt = $base->prepare($sql);
    $stmt->execute();
    $clientesActivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    require_once __DIR__ . '/../header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mail</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="./mail.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
    <style>
        /* Ocultar inputs inicialmente */

        #form-inputs {
            display: none;
            position: center;
        }
    </style>
    <script>
        function seleccionarPlantilla() {
            const seleccion = document.querySelector('input[name="tipo_correo"]:checked').value;
            const montoInput = document.getElementById("monto-container");
            const electrodomesticoInput = document.getElementById("electrodomestico-container");
            const nombreInput = document.getElementById("nombre-container");
            const mensajeInput = document.getElementById("mensaje-container");
            const emailSelect = document.getElementById("email-select-container");
            const emailInput = document.getElementById("email-input-container");
            const asunto = document.getElementById("asunto");
            const formInputs = document.getElementById("form-inputs");
            const descReInput = document.getElementById("msj-container");
            const materialesInput = document.getElementById("msj-box");
            const otherEmail =document.getElementById("other_email");

            formInputs.style.display = "block";
            asunto.value = ""; // Resetear asunto por defecto

            if (seleccion === "Presupuesto") {
                montoInput.style.display = "block";
                electrodomesticoInput.style.display = "block";
                nombreInput.style.display = "none";
                mensajeInput.style.display = "none";
                materialesInput.style.display = "block";
                descReInput.style.display = "block";
                emailSelect.style.display = "block";
                emailInput.style.display = "none";
                asunto.value = "Cotizacion de la Reparacion";
            } else if (seleccion === "Reparacion") {
                montoInput.style.display = "block";
                electrodomesticoInput.style.display = "block";
                nombreInput.style.display = "none";
                mensajeInput.style.display = "none";
                materialesInput.style.display = "none";
                descReInput.style.display = "none";
                emailSelect.style.display = "block";
                emailInput.style.display = "none";
                asunto.value = "Reparacion Finalizada";
            } else if (seleccion === "Otros") {
                montoInput.style.display = "none";
                electrodomesticoInput.style.display = "none";
                nombreInput.style.display = "block";
                mensajeInput.style.display = "block";
                materialesInput.style.display = "none";
                descReInput.style.display = "none";
                emailSelect.style.display = "none";
                emailInput.style.display = "block";
                otherEmail.setAttribute("required","required"); 
                asunto.readOnly = false;
            }
        }

        function actualizarCliente(selectElement) {
            const emailCliente = selectElement.value; 
            const idCliente = selectElement.options[selectElement.selectedIndex].getAttribute('data-id'); 
            const nomCliente = selectElement.options[selectElement.selectedIndex].getAttribute('data-nom');
            const emailHiddenInput = document.getElementById('email-hidden'); 
            const idHiddenInput = document.getElementById('id-cliente-hidden'); 
            const nombreHiddenInput = document.getElementById('nombre-hidden');
            // Actualizamos el valor de los inputs ocultos
            emailHiddenInput.value = emailCliente;
            idHiddenInput.value = idCliente;
            nombreHiddenInput.value = nomCliente;
            //console.log(emailCliente);

            // Opcional: Llamar a otra función si es necesario
            cargarElectrodomesticos(idCliente); 
        }
        function cargarElectrodomesticos(idCliente) {
            const electrodomesticoSelect = document.getElementById("electrodomestico-select");

            if (!idCliente) {
                electrodomesticoSelect.innerHTML = '<option value="">No hay datos para mostrar</option>';
                return;
            }

            console.log("ID Cliente:", idCliente);

            // Realizar la solicitud fetch
            fetch(`obtenerElectrodomesticos.php?idcliente=${idCliente}`)
                .then(response => response.text())
                .then(data => {
                    electrodomesticoSelect.innerHTML = data;
                    console.log(electrodomesticoSelect);
                })
                .catch(error => {
                    console.error("Error al cargar los electrodomésticos:", error);
                    electrodomesticoSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        }
    </script>
</head>
<body>
    <main>
        <h1>Enviar Mensaje</h1>
        <div>
            <form class="mail-form" action="./mail_inter.php" method="POST">
                <div class="radio-group">
                    <fieldset>
                        <legend>Selecciona el tipo de correo:</legend>
                        <label class="label">
                            <input type="radio" name="tipo_correo" value="Presupuesto" onchange="seleccionarPlantilla()"> Presupuesto / Cotización
                        </label>
                        <label class="label">
                            <input type="radio" name="tipo_correo" value="Reparacion" onchange="seleccionarPlantilla()"> Reparación Finalizada
                        </label>
                        <label class="label">
                            <input type="radio" name="tipo_correo" value="Otros" onchange="seleccionarPlantilla()"> Otros
                        </label>
                </fieldset>
                </div>

                <!-- Inputs ocultos inicialmente -->
                <div  class="form-group" id="form-inputs">
                    <div id="email-input-container" style="display: none;">
                        <label for="para" class="label">Para:</label>
                        <input class="input" id="other_email" name="other_email" type="email" tabindex="1" placeholder="Correo destinatario">
                    </div>
                    <label for="asunto" class="label">Asunto: </label><input id="asunto" class="input" name="asunto" type="text" placeholder="Asunto" tabindex="1" readonly>
                    <div id="nombre-container" style="display: none;">
                    <label for="departe" class="label">De: </label><input id="nombre" class="input" name="nombre" type="text" tabindex="2" value="<?php echo htmlspecialchars(ucwords($nombre)); ?>" readonly>
                    </div>
                    <!-- Select dinámico para Cotización/Reparación -->
                    <div id="email-select-container" style="display: none;">
                        <label for="para" class="label">Para: </label> 
                        <select id="email-select" name="email-select" class="input" tabindex="3" onchange="actualizarCliente(this)">
                            <option value="">Seleccione un cliente</option>
                            <?php 
                            foreach ($clientesActivos as $cliente): 
                            ?>
                                <option value="<?php echo htmlspecialchars($cliente['email_cliente']); ?>" data-id="<?php echo htmlspecialchars($cliente['idclientes']); ?>" data-nom ="<?php echo htmlspecialchars(ucwords($cliente['nom_cliente']));?>">
                                    <?php echo htmlspecialchars(ucwords($cliente['nom_cliente']) . " " . ucwords($cliente['ape_cliente']) . " - " . $cliente['email_cliente']); ?>
                                </option>
                            <?php 
                            endforeach; 
                            ?>
                        </select>
                        <!-- Input hidden para almacenar el ID del cliente -->
                        <input type="hidden" id="id-cliente-hidden" name="id_cliente" value="">
                        <!-- Campo oculto para el correo -->
                        <input type="hidden" id="email-hidden" name="email" value="">
                        <input type="hidden" id="nombre-hidden" name="nombre_cli" value="">

                        <!--id oculto reparacion-->

                    </div>
                    <br><hr>
                    <!-- Selección de electrodoméstico -->
                    <div id="electrodomestico-container" style="display: none;">
                        <hr><br>
                        <select id="electrodomestico-select" name="electrodomestico" class="input" tabindex="4">
                            <option value="">Seleccione un electrodoméstico</option>
                        </select>
                        <input type="hidden" id="electrodomestico-hidden" name="electrodomestico_full" value="">
                        <input type="hidden" id="id-repa" name="id_repa" value="">
                    </div>
                    <div id="monto-container" style="display: none;">
                        <label for="monto" class="label">Monto del Presupuesto: </label>
                        <input id="monto" class="input" name="monto" type="number" placeholder="$" tabindex="5">
                    </div>
                    <div id="mensaje-container" style="display: none;">
                        <label for="mensaje" class="label">Mensaje:</label><br><br>
                        <textarea id="mensaje" name="mensaje" placeholder="Escriba aquí" tabindex="6" name="desc" id="desc" cols="80" rows="15" style="border-radius: 0.5rem;"></textarea>
                    </div>
                    <div id="msj-box" style="display: none;">
                        <label for="materiales" class="label">Materiales de la Reparación:</label><br>
                        <textarea id="materiales" name="materiales" placeholder="Escriba aquí" tabindex="6" name="desc" id="desc" cols="80" rows="5" style="border-radius: 0.5rem;"></textarea>
                    </div>
                    <div id="msj-container" style="display: none;">
                        <label for="descRe" class="label">Detalle de la Reparación:</label><br>
                        <textarea id="descRe" name="descRe" placeholder="Escriba aquí" tabindex="6" name="desc" id="desc" cols="80" rows="10" style="border-radius: 0.5rem;"></textarea>
                    </div>
                    <br>
                    <input class="btn" name="enviar" type="submit" value="Enviar">
                    
                </div>


            </form>
        </div>
    </main>
    <?php require '../footer.php'; ?>

    <script>
        document.getElementById('email-hidden').addEventListener('change', function () {
            console.log('Valor seleccionado:', this.value);
        });
        document.addEventListener('DOMContentLoaded', function() {
            const electrodomesticoSelect = document.getElementById("electrodomestico-select");

            if (electrodomesticoSelect) {
                electrodomesticoSelect.addEventListener('change', function() {
                    actualizarElectrodomesticoHidden(this);
                });

                // Disparar manualmente el evento change si solo hay una opción
                if (electrodomesticoSelect.options.length === 2) { // 1 opción más el "Seleccione un electrodoméstico"
                    electrodomesticoSelect.selectedIndex = 1; // Selecciona la única opción disponible
                    electrodomesticoSelect.dispatchEvent(new Event('change'));
                }
            }
        });

        function actualizarElectrodomesticoHidden(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const marca = selectedOption.getAttribute('data-marca');
            const modelo = selectedOption.getAttribute('data-modelo');
            const tipo = selectedOption.getAttribute('data-tipo');
            const idRepa = selectedOption.getAttribute('data-repa');
            
            const electrodomesticoHidden = document.getElementById('electrodomestico-hidden');
            
             document.getElementById('id-repa').value = idRepa;

            electrodomesticoHidden.value = `${tipo} ${marca} - ${modelo.toUpperCase()}`;
            console.log("Todo junto que se pasa a php: ", electrodomesticoHidden.value);
        }
    </script>
</body>
</html>
