<div class="contenedor restablecer">

<?php include __DIR__ ."/../templates/sitio-web.php" ;?>

<div class="contenedor-sm">
    <p class="descripcion-pagina">Reestablecer tu cuenta en UpTask</p>
    
    <?php include __DIR__ ."/../templates/alertas.php" ;?>

    <?php if($mostrar) { ?>
        <form method="POST" class="formulario">
            <div class="campo">
                <label for="password">Password</label>
                <input 
                    type="password"
                    name="password" 
                    id="password"
                    placeholder="Tu password"
                >
            </div>
            <input type="submit" class="boton" value="Guardar password">
        </form>
    <?php } ?>
        <div class="acciones">
            <a href="/">¿ya tienes cuenta? iniciar sesion</a>
            <a href="/crear">¿Aun no tienes una cuenta? obtener una</a>
        </div>
    </div> <!-- contenedor-sm -->

</div>