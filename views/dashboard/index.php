<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<?php if(count($proyectos) === 0){ ?>
        <p class="no-proyectos">No hay proyectos creados</p>
        <p class="no-proyectos"><a href="/crear-proyectos">Crea uno</a></p>
<?php }else{ ?>
        <ul class="listado-proyectos">

            <?php foreach($proyectos as $protecto){ ?>
                <li class="proyecto">
                    <a href="/proyecto?id=<?php echo $protecto->url; ?>">
                        <?php echo $protecto->proyecto; ?>
                    </a>
                </li>
            <?php } ?>

        </ul>
<?php } ?>










<?php include_once __DIR__ . '/footer-dashboard.php'; ?>