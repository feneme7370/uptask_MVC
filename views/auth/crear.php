<div class="contenedor crear">

<?php include __DIR__ . "/../templates/sitio-web.php" ;?>

<div class="contenedor-sm">
    <p class="descripcion-pagina">Crea tu cuenta en UpTask</p>
    
    <?php include __DIR__ . "/../templates/alertas.php" ;?>

        <form action="/crear" method="POST" class="formulario">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input 
                    type="text"
                    id="nombre"
                    placeholder="Tu Nombre"
                    name="nombre"
                    value="<?php echo $usuario->nombre; ?>"
                    autofocus
                    >
                </div>
                <div class="campo">
                    <label for="email">Email</label>
                    <input 
                    type="email"
                    name="email" 
                    id="email"
                    placeholder="Tu email"
                    value="<?php echo $usuario->email; ?>"
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
            <div class="campo">
                <label for="password2">Repetir Password</label>
                <input 
                    type="password"
                    name="password2" 
                    id="password2"
                    placeholder="Repetir tu password"
                >
            </div>

            <input type="submit" class="boton" value="Iniciar sesion">
        </form>

        <div class="acciones">
            <a href="/">¿ya tienes cuenta? iniciar sesion</a>
            <a href="/olvide">¿Olvidaste tu password?</a>
        </div>
    </div> <!-- contenedor-sm -->

</div>