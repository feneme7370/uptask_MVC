<div class="contenedor login">

<?php include __DIR__ ."/../templates/sitio-web.php" ;?>

<div class="contenedor-sm">
    <p class="descripcion-pagina">Iniciar sesion</p>
    
    <?php include __DIR__ ."/../templates/alertas.php" ;?>
    
        <form action="/" method="POST" class="formulario">
            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="email"
                    name="email" 
                    id="email"
                    placeholder="Tu email"
                    autofocus
                >
            </div>
            <div class="campo">
                <label for="password">Password</label>
                <input 
                    type="password"
                    name="password" 
                    id="password"
                    placeholder="Tu password"
                >
            </div>

            <input type="submit" class="boton" value="Iniciar sesion">
        </form>

        <div class="acciones">
            <a href="/crear">¿Aun no tienes una cuenta? obtener una</a>
            <a href="/olvide">¿Olvidaste tu password?</a>
        </div>
    </div> <!-- contenedor-sm -->

</div>