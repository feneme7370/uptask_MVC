<div class="contenedor olvide">

<?php include __DIR__ ."/../templates/sitio-web.php" ;?>

<div class="contenedor-sm">
    <p class="descripcion-pagina">Recupera tu cuenta en UpTask</p>
    
    <?php include __DIR__ ."/../templates/alertas.php" ;?>

        <form action="/olvide" method="POST" class="formulario">
            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="text"
                    name="email" 
                    id="email"
                    placeholder="Tu email"
                >
            </div>
            <input type="submit" class="boton" value="Crear cuenta">
        </form>

        <div class="acciones">
            <a href="/">¿ya tienes cuenta? iniciar sesion</a>
            <a href="/crear">¿Aun no tienes una cuenta? obtener una</a>
        </div>
    </div> <!-- contenedor-sm -->

</div>