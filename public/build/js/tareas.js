!function(){!async function(){try{const a="/api/tareas?id="+n(),o=await fetch(a),r=await o.json();e=r.tareas,t()}catch(e){console.log(e)}}();let e=[];function t(){if(0===e.length){const e=document.querySelector("#listado-tareas"),t=document.createElement("LI");return t.textContent="No Hay Tareas",t.classList.add("no-tareas"),void e.appendChild(t)}const t={0:"Pendiente",1:"Completa"};e.forEach(e=>{const a=document.createElement("LI");a.dataset.tareaId=e.id,a.classList.add("tarea");const n=document.createElement("P");n.textContent=e.nombre;const o=document.createElement("DIV");o.classList.add("opciones");const r=document.createElement("BUTTON");r.classList.add("estado-tarea"),r.classList.add(""+t[e.estado].toLowerCase()),r.textContent=t[e.estado],r.dataset.estadoTarea=e.estado;const c=document.createElement("BUTTON");c.classList.add("eliminar-tarea"),c.dataset.idTarea=e.id,c.textContent="Eliminar",o.appendChild(r),o.appendChild(c),a.appendChild(n),a.appendChild(o);document.querySelector("#listado-tareas").appendChild(a)})}function a(e,t,a){const n=document.querySelector(".alerta");n&&n.remove();const o=document.createElement("DIV");o.classList.add("alerta",t),o.textContent=e,a.parentElement.insertBefore(o,a.nextElementSibling),setTimeout(()=>{o.remove()},5e3)}function n(){const e=new URLSearchParams(window.location.search);return Object.fromEntries(e.entries()).id}document.querySelector("#agregar-tarea").addEventListener("click",(function(){const o=document.createElement("DIV");o.classList.add("modal"),o.innerHTML='\n            <form class="formulario nueva-tarea">\n                <legend>Añade una nueva tarea</legend>\n                <div class="campo">\n                    <label>Tarea</label>\n                    <input type="text" name="tarea" placeholder="Añadir Tarea al Proyecto Actual" id="tarea">\n                </div>\n                <div class="opciones">\n                    <input type="submit" class="submit-nueva-tarea" value="Añadir Tarea">\n                    <button type="button" class="cerrar-modal">Cancelar</button>\n                </div>\n            </form>\n        ',setTimeout(()=>{document.querySelector(".formulario").classList.add("animar")},0),o.addEventListener("click",(function(r){if(r.preventDefault(),r.target.classList.contains("cerrar-modal")){document.querySelector(".formulario").classList.add("cerrar"),setTimeout(()=>{o.remove()},500)}r.target.classList.contains("submit-nueva-tarea")&&function(){const o=document.querySelector("#tarea").value.trim();if(""===o)return void a("El nombre de la tarea es obligatorio","error",document.querySelector(".formulario legend"));!async function(o){!function(){const e=document.querySelector("#listado-tareas");for(;e.firstChild;)e.removeChild(e.firstChild)}();const r=new FormData;r.append("nombre",o),r.append("proyectoId",n());try{const n="/api/tarea",c=await fetch(n,{method:"POST",body:r}),s=await c.json();if(a(s.mensaje,s.tipo,document.querySelector(".formulario legend")),"exito"===s.tipo){const a=document.querySelector(".modal");setTimeout(()=>{a.remove()},2e3);const n={id:String(s.id),nombre:o,estado:"0",proyectoId:s.proyectoId};e=[...e,n],t()}}catch(e){console.log(e)}}(o)}()})),document.querySelector(".dashboard").appendChild(o)}))}();