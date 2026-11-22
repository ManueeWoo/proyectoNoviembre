function iniciarSolicitudes() {
    const lista = document.getElementById("lista");
    const aviso = document.getElementById("aviso");
    const molde = document.getElementById("molde");
    const botonesRol = document.querySelectorAll(".sOpcion");
    const pestañas = document.querySelectorAll(".sPestaña");

    // pa no hacer to el rato ifs pa comprobar segun el estado de la solicitus
    // lo meto en un obj y cuando pregunte por uno me da en q pestaña lo meto
    const ESTADOS = {
        solicitado: "pendientes",
        aceptado: "curso", ejecucion: "curso",
        completado: "cerradas", rechazado: "cerradas", cancelado: "cerradas"
    };
    const TXTESTADOS = {
        solicitado: "Pendiente de respuesta", aceptado: "Aceptada", ejecucion: "En ejecución",
        completado: "Completada", rechazado: "Rechazada", cancelado: "Cancelada"
    };

    let rol = "recibidas";
    let estado = "pendientes";

    function avisar(texto) {
        aviso.textContent = texto;
        aviso.classList.toggle("oculto", texto === "");
    }

    async function pedir(url, opciones) {
        const respuesta = await fetch(url, opciones);
        if (respuesta.status === 401) {
            location.href = "/login";
        }
        return await respuesta.json();
    }

    function crearFilaSolicitud(s, rolFila) {
        const fila = molde.cloneNode(true); //copia exacta
        fila.removeAttribute("id"); //le quito el id pq no se pueden 2 en html
        fila.dataset.id = s.id;
        fila.dataset.rol = rolFila;
        fila.dataset.estado = s.estado;
        fila.dataset.grupo = ESTADOS[s.estado];
        fila.dataset.estado = s.estado;

        fila.querySelector(".sFoto").src = "/upload/" + (s.persona.foto ? s.persona.foto : "logo.png");
        fila.querySelector(".sNombre").innerHTML= s.oferta.titulo;
        fila.querySelector(".sPersona").innerHTML = (rolFila === "recibidas" ? "Solicitada por " : "Oferta de ") + s.persona.nombre + " · " + s.fecha;
        fila.querySelector(".sDesc").innerHTML = s.oferta.descripcion;
        fila.querySelector(".sNumero").innerHTML = s.puntos;
        fila.querySelector(".sEstado").innerHTML = TXTESTADOS[s.estado];
        const botones = fila.querySelectorAll("[data-accion]");
        for (const boton of botones) {
            boton.addEventListener("click", function () {
                cambiarEstado(s.id, boton.dataset.accion);
            });
        }
        return fila;
    }

    function filtrarPorEnviadasRecibidas() {
        for (const fila of lista.children) {
            const coincide = fila.dataset.rol === rol && fila.dataset.grupo === estado;
            fila.classList.toggle("oculto", !coincide);
        }
        const visibles = lista.querySelectorAll(".sFila:not(.oculto)").length;
        avisar(visibles === 0 ? "No hay solicitudes en esta sección." : "");
    }

    async function cargar() {
        try {
            const datos = await pedir("/api/misSolicitudes");

            lista.innerHTML = ""; 

            for (const s of datos.recibidas) {
                lista.appendChild(crearFilaSolicitud(s, "recibidas"));
            }
            for (const s of datos.enviadas) {
                lista.appendChild(crearFilaSolicitud(s, "enviadas"));
            }

            filtrarPorEnviadasRecibidas();
        } catch (error) {
            avisar("No se pudieron cargar las solicitudes. Inténtalo de nuevo.");
        }
    }


    async function cambiarEstado(id, accion) {
        const formulario = new FormData();
        formulario.append("id", id);
        formulario.append("accion", accion);

        const json = await pedir("/api/solicitud/estado", { method: "POST", body: formulario });
        if (json.ok) {
            cargar();
        } else {
            let mensajes = "";
            for (let campo in json.errores) {
                mensajes += json.errores[campo] + " ";
            }
            avisar(mensajes);
        }
    }

    for (const boton of botonesRol) {
        boton.addEventListener("click", function() {
            rol = boton.dataset.rol;
            for(btn of botonesRol){
                btn.classList.toggle("activo",btn === boton)
            }
            filtrarPorEnviadasRecibidas();
        });
    }

    for (const pestaña of pestañas) {
        pestaña.addEventListener("click", function() {
            estado = pestaña.dataset.estado;
            for(p of pestañas){
                p.classList.toggle("activo",p === pestaña)
            }
            filtrarPorEnviadasRecibidas();
        });
    }
    cargar();
}