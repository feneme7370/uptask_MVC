//meter variables para que no se mezclen con otros archivos IIFE
(function(){
    const direccion = 'http://127.0.0.1:3000';

    //diccionario para estados
    const estados = {
        0: 'Pendiente',
        1: 'Completa'
    }

    let tareas = [];//se llena con obtenerTareas
    let filtradas = [];//se llena al hacer click en el radio

    obtenerTareas();

    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', function(){
        mostrarFormulario()
    });

    /* ========================================== FILTRAR POR RADIO ========================================== */
    const filtros = document.querySelectorAll('#filtros input[type="radio"]');
    filtros.forEach( radio => {
        radio.addEventListener('input', filtrarTareas);
    });

    function filtrarTareas(e){
        const filtro = e.target.value;

        if(filtro !== ''){
            //filtrar array de tareas por las que tienen el estado correcto
            filtradas = tareas.filter( tarea => tarea.estado === filtro);
        }else{
            filtradas = [];
            
        }

        mostrarTarea();
    }

    
    /* ========================================== OBTENER TAREAS ========================================== */
    //consulta a la DB con API
    async function obtenerTareas(){
        try {
            //obtengo url
            const id = obtenerProyecto();
            const url = `/API/tareas?id=${id}`;
            //paso al fetch la url y que quede en resultado como json
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            //extrar resultados en una variable
            tareas = resultado.tareas;
            mostrarTarea();
        } catch (error) {
            console.log(error);
        }
    }

/* ========================================== MOSTRAR TAREAS ========================================== */    
    function mostrarTarea(){
        limpiarTareas();
        totalPendientes();
        totalCompletas();

        //filtros
        const arrayTareas = filtradas.length ? filtradas : tareas; 

        //mostrar tareas
        if(arrayTareas.length === 0){
            //en caso de que no haya ninguna creo elemento para mostrar
            const contenedorTareas = document.querySelector('#listado-tareas');
            const textoNoTareas = document.createElement('LI');
            textoNoTareas.textContent = 'No hay tareas';
            textoNoTareas.classList.add('no-tareas');

            contenedorTareas.appendChild(textoNoTareas);
            return;
        }

        arrayTareas.forEach((tarea) => {
            //crear los li con los id
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id;
            contenedorTarea.classList.add('tarea');

            //crear los p con los nombres
            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre;
            nombreTarea.ondblclick = function(){
                mostrarFormulario(true, {...tarea});
            }
            
            //crear un div para btn acciones
            const divTarea = document.createElement('DIV');
            divTarea.classList.add('opciones');

            //crear botones
            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            //agrega clase dinamica y en minusculas
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);
            btnEstadoTarea.textContent = estados[tarea.estado];//usa el diccionario de arriba
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;
            btnEstadoTarea.onclick = function(){
                cambiarEstadoTarea({...tarea});
            }
            
            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.textContent = 'Eliminar';
            btnEliminarTarea.onclick = function(){
                confirmarEliminarTarea({...tarea});
            }

            divTarea.appendChild(btnEstadoTarea);
            divTarea.appendChild(btnEliminarTarea);

            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(divTarea);

            const listadoTareas = document.querySelector('#listado-tareas');
            listadoTareas.appendChild(contenedorTarea);
            
        });
    }

    function totalPendientes(){
        const totalPendientes = tareas.filter(tarea => tarea.estado === "0");
        const pendientesRadio = document.querySelector('#pendientes');
        if(totalPendientes.length === 0){
            pendientesRadio.disabled = true;
        }else{
            pendientesRadio.disabled = false;
        }
    }
    function totalCompletas(){
        const totalCompletas = tareas.filter(tarea => tarea.estado === "1");
        const completadasRadio = document.querySelector('#completadas');
        if(totalCompletas.length === 0){
            completadasRadio.disabled = true;
        }else{
            completadasRadio.disabled = false;
        }
    }
    /* ========================================== MOSTRAR FORMULARIO MODAL ========================================== */

    function mostrarFormulario(editar = false, tarea = {}){
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario nueva-tarea">
            <legend>${editar ? 'Editar tarea' : 'Agrega una nueva tarea'}</legend>
                <div class="alertaDiv"></div>
                <div class="campo">
                    <label for="">Tarea</label>
                    <input 
                        type="text"
                        name="tarea"
                        id="tarea"
                        placeholder="${tarea.nombre ? 'Edita la tarea' : 'Agregar tarea al proyecto'}"
                        value="${tarea.nombre ? tarea.nombre : ''}"
                        autofocus
                        >
                </div>           
                <div class="opciones">
                    <input type="submit" class=" boton submit-nueva-tarea" value="${tarea.nombre ? 'Editar tarea' : 'Agregar tarea'}">
                    <button type="button" class="boton cerrar-modal">Cancelar</button>
                </div> 
            </form> 
        `;

        //JS en su primera recorrida no conoce los setTimeout, pinta primero lo de arriba, y en la otra vuelta ejecute el codigo, sino daria error de que no existe
        setTimeout(() => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');
        }, 10);

        modal.addEventListener('click', function(e){
            e.preventDefault();
            if(e.target.classList.contains('cerrar-modal')){
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout(() => {
                    modal.remove();
                }, 500);
            }
            if(e.target.classList.contains('submit-nueva-tarea')){
                //agarra el contenido del input, trim elimina espacios inciales y finales
                const nombreTarea = document.querySelector('#tarea').value.trim();

                if(nombreTarea === ''){
                    //alerta de error
                    mostrarAlerta('error', 'el nombre de la tarea es obligatorio', document.querySelector('.alertaDiv'));
                    return;
                }

                if(editar){
                    //se cambia el nombre al actual en el objeto tarea
                    tarea.nombre = nombreTarea;
                    actualizarTarea(tarea);
                }else{
                    agregarTarea(nombreTarea);
                }
            }
        });

        document.querySelector('.dashboard').appendChild(modal);
    }
    


    /* ========================================== MOSTRAR ALERTAS ========================================== */

    //MOSTRAR ALERTAS
    function mostrarAlerta(tipo, mensaje, referencia){
        const alertaPrevia = document.querySelector('.alerta');
        if(alertaPrevia){
            alertaPrevia.remove();
        }

        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;
        referencia.appendChild(alerta);

        //otra forma de poner un elemento entre un elemento padre y un hijo, y no quedaria dentro de legend
        //referencia.parentElement.insertBefore(alerta, referencia);

        //referencia.nextSibling();

        //eliminar alerta
        setTimeout(() => {
            alerta.remove();
        }, 5000);
    }

    /* ========================================== AGREGAR TAREAS ========================================== */

    //consultar el servidor
    async function agregarTarea(tarea){
        //construir la peticion
        const datos = new FormData();

        datos.append('nombre', tarea); 
        datos.append('proyectoId', obtenerProyecto());//es la url

        try {
            //generar peticion con los datos
            const url = direccion + '/API/tarea';
            const respuesta = await fetch(url, {
                method : 'POST',
                body : datos
            });

            const resultado = await respuesta.json();


            mostrarAlerta(resultado.tipo, resultado.mensaje, document.querySelector('.alertaDiv'));

            if(resultado.tipo === 'exito'){
                const modal = document.querySelector('.modal');
                setTimeout(() => {
                    modal.remove();
                }, 3000);

                //agregar el objeto de tarea al global de tareas
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea,
                    estado: 0,
                    proyectoId: resultado.proyectoId
                }

                //tomo la copia de las tareas y le paso nuevo objeto
                tareas = [...tareas, tareaObj];
                mostrarTarea();
            }
            

        } catch (error) {
            console.log(error);
            
        }
    }

    /* ========================================== CAMBIAR ESTADO DE TAREA ========================================== */

    //CAMBIAR ESTADO A 1 O 0
    function cambiarEstadoTarea(tarea){
        const nuevoEstado = tarea.estado === '1' ? '0' : '1';
        tarea.estado = nuevoEstado;
        actualizarTarea(tarea);

        //console.log(tarea);
    }

    /* ========================================== ACTUALIZAR TAREAS ========================================== */

    //CAMBIAR EN LA DB EL ESTADO
    async function actualizarTarea(tarea){
        const {estado, id, nombre, proyectoId } = tarea;

        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerProyecto());
        
        try {
            //generar peticion con los datos
            const url = direccion + '/API/tarea/actualizar';
            const respuesta = await fetch(url, {
                method : 'POST',
                body : datos
            });

            const resultado = await respuesta.json();
            
            if(resultado.respuesta.tipo === 'exito'){
                //alerta para que funcione con crear y actualizar
                Swal.fire(
                    resultado.respuesta.mensaje,
                    resultado.respuesta.mensaje,
                    'success'
                );

                //remover modal al actualizar
                const modal = document.querySelector('.modal');
                if(modal){
                    modal.remove();
                };
            
                //map crea un nuevo array con lo cambiado
                tareas = tareas.map(tareaMemoria => {
                    if(tareaMemoria.id === id){
                        tareaMemoria.estado = estado;
                        tareaMemoria.nombre = nombre;
                    }
                    return tareaMemoria;
                    
                });
                mostrarTarea();
            }
        } catch (error) {
            console.log(error);
        }
    }
    
    /* ========================================== ALERTA DE CONFIRMAR ELIMINACION TAREAS ========================================== */

    //ELIMINAR TAREA
    function confirmarEliminarTarea(tarea){
        Swal.fire({
            title: '¿Eliminar tarea?',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
          }).then((result) => {
            if (result.isConfirmed) {
                eliminarTarea(tarea);
            }
        })
    }

    /* ========================================== ELIMINAR TAREAS ========================================== */

    async function eliminarTarea(tarea){
        const {estado, id, nombre } = tarea;

        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerProyecto());

        try {
            //generar peticion con los datos
            const url = direccion + '/API/tarea/eliminar';
            const respuesta = await fetch(url, {
                method : 'POST',
                body : datos
            });
            const resultado = await respuesta.json();

            if(resultado.resultado){
                //mostrarAlerta(respuesta.tipo, respuesta.mensaje, document.querySelector('.alertasDiv'));
                Swal.fire('Eliminado!', resultado.mensaje, 'success');

                //filter trae todos menos uno o uno menos todos
                tareas = tareas.filter( tareaMemoria => tareaMemoria.id !== id); 
                mostrarTarea();
            }
        } catch (error) {
            console.log(error);
        }
    }

    /* ========================================== OBTENER URL DEL PROYECTO ========================================== */

    function obtenerProyecto(){
        //obtener varialbes de url en js, trae un objeto con ellas
        const proyectoParams = new URLSearchParams(window.location.search);
        const proyecto = Object.fromEntries(proyectoParams.entries());
        return proyecto.id;
    }

    /* ========================================== LIMPIAR HTML DEL JS ========================================== */

    //para el virtual dom
    function limpiarTareas(){
        const listadoTareas = document.querySelector('#listado-tareas');
        listadoTareas.innerHTML = '';

        while(listadoTareas.firstChild){
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }
})();