<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
    
    <form action="/crear-proyectos" method="POST" class="formulario">    

        <?php include_once __DIR__ . '/formulario-proyectos.php'; ?>

        <input type="submit" value="Cargar">
    </form>
</div>










<?php include_once __DIR__ . '/footer-dashboard.php'; ?>