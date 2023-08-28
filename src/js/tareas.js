(function() {

    obtenerTareas();
    let tareas = [];

    //Boton para mostrar el Modal de Agregar tarea
    const nuevaTareaBtn = document.querySelector("#agregar-tarea");
    nuevaTareaBtn.addEventListener("click",mostrarFormulario);

    async function obtenerTareas(){
        try {
            const id = obtenerProyecto();
            const url = `/api/tareas?id=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            // const {tareas} = resultado;
            tareas = resultado.tareas;
            mostrarTareas();
        } catch (error) {
            console.log(error);
        }
    }

    function mostrarTareas(){
        if(tareas.length === 0){
            const contenedorTareas = document.querySelector("#listado-tareas");
            const textoNoTareas = document.createElement("LI");
            textoNoTareas.textContent = "No Hay Tareas";
            textoNoTareas.classList.add("no-tareas");
            contenedorTareas.appendChild(textoNoTareas);
            return;
        }
        const estados = {
            0: "Pendiente",
            1: "Completa"
        }

        tareas.forEach(tarea => {
            const contenedorTarea = document.createElement("LI");
            contenedorTarea.dataset.tareaId = tarea.id;
            contenedorTarea.classList.add("tarea");

            const nombreTarea = document.createElement("P");
            nombreTarea.textContent = tarea.nombre;

            const opcionesDiv = document.createElement("DIV");
            opcionesDiv.classList.add("opciones");
            //Botones
            const btnEstadoTarea = document.createElement("BUTTON");
            btnEstadoTarea.classList.add("estado-tarea");
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);
            btnEstadoTarea.textContent = estados[tarea.estado];
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;

            const btnEliminarTarea = document.createElement("BUTTON");
            btnEliminarTarea.classList.add("eliminar-tarea");
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.textContent = "Eliminar";

            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);

            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

            const listadoTarea = document.querySelector("#listado-tareas");
            listadoTarea.appendChild(contenedorTarea);
        });
    }

    function mostrarFormulario(){
        const modal = document.createElement("DIV");
        modal.classList.add("modal");
        modal.innerHTML = `
            <form class="formulario nueva-tarea">
                <legend>Añade una nueva tarea</legend>
                <div class="campo">
                    <label>Tarea</label>
                    <input type="text" name="tarea" placeholder="Añadir Tarea al Proyecto Actual" id="tarea">
                </div>
                <div class="opciones">
                    <input type="submit" class="submit-nueva-tarea" value="Añadir Tarea">
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `;
        setTimeout(()=>{
            const formulario = document.querySelector(".formulario");
            formulario.classList.add("animar");
        },0);

        modal.addEventListener("click",function(e){
            e.preventDefault();
            if(e.target.classList.contains("cerrar-modal")){
                const formulario = document.querySelector(".formulario");
                formulario.classList.add("cerrar");
                setTimeout(()=>{
                    modal.remove();
                },500);
            }
            if(e.target.classList.contains("submit-nueva-tarea")){
                submitFormularioNuevaTarea();
            }
        });

        document.querySelector(".dashboard").appendChild(modal);
    }

    function submitFormularioNuevaTarea(){
        const tarea = document.querySelector("#tarea").value.trim();
        if(tarea === ""){
            //Mostrar una alerta de error
            mostrarAlerta("El nombre de la tarea es obligatorio","error",document.querySelector(".formulario legend"));
            return;
        }
        agregarTarea(tarea);
    }
    function mostrarAlerta(mensaje,tipo,referencia){
        const alertaPrevia = document.querySelector(".alerta");
        if(alertaPrevia){
            alertaPrevia.remove();
        }
        const alerta = document.createElement("DIV");
        alerta.classList.add("alerta",tipo);
        alerta.textContent = mensaje;
        referencia.parentElement.insertBefore(alerta,referencia.nextElementSibling);
        //Eliminar la alerta
        setTimeout(()=>{
            alerta.remove();
        },5000);
    }
    async function agregarTarea(tarea){
        limpiarTareas();
        //Construir la petición
        const datos = new FormData();
        datos.append("nombre",tarea);
        datos.append("proyectoId",obtenerProyecto());
        try {
            const url = "/api/tarea";
            const respuesta = await fetch(url,{
                method: "POST",
                body: datos
            });
            const resultado = await respuesta.json();
            mostrarAlerta(resultado.mensaje,resultado.tipo,document.querySelector(".formulario legend"));

            if(resultado.tipo === "exito"){
                const modal = document.querySelector(".modal");
                setTimeout(()=>{
                    modal.remove();
                    // window.location.reload();
                },2000);
                //Agregar el objeto de tarea la global de tareas
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea,
                    estado: "0",
                    proyectoId: resultado.proyectoId
                }
                tareas = [...tareas,tareaObj];
                mostrarTareas();
            }
        } catch (error) {
            console.log(error);
        }
    }
    function obtenerProyecto(){
        const proyectoParams = new URLSearchParams(window.location.search);
        const proyecto = Object.fromEntries(proyectoParams.entries());
        return proyecto.id;
    }

    function limpiarTareas(){
        const listadoTareas = document.querySelector("#listado-tareas");
        while(listadoTareas.firstChild){
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }
})();