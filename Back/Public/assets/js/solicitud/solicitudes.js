function iniciarSolicitudes() {
    const lista = document.getElementById("lista");
    const aviso = document.getElementById("aviso");
    const molde = document.getElementById("molde");
    const botonesRol = document.querySelectorAll(".sol__op");
    const tabs = document.querySelectorAll(".sol__tab");

    // Cada estado de la BD pertenece a una pestaña
    const GRUPOS = {
        solicitado: "pendientes",
        aceptado: "curso", ejecucion: "curso",
        completado: "cerradas", rechazado: "cerradas", cancelado: "cerradas"
    };
    const ETIQUETAS = {
        solicitado: "Pendiente de respuesta", aceptado: "Aceptada", ejecucion: "En ejecución",
        completado: "Completada", rechazado: "Rechazada", cancelado: "Cancelada"
    };

    let rol = "recibidas";
    let grupo = "pendientes";

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

    // Copia el molde y le rellena los datos de una solicitud
    function crearFila(s, rolFila) {
        const fila = molde.cloneNode(true);
        fila.removeAttribute("id");
        fila.dataset.id = s.id;
        fila.dataset.rol = rolFila;
        fila.dataset.estado = s.estado;
        fila.dataset.grupo = GRUPOS[s.estado];

        fila.querySelector(".sol__foto").src = "/upload/" + (s.persona.foto ? s.persona.foto : "logo.png");
        fila.querySelector(".sol__nombre").textContent = s.oferta.titulo;
        fila.querySelector(".sol__quien").textContent =
            (rolFila === "recibidas" ? "Solicitada por " : "Oferta de ") + s.persona.nombre + " · " + s.fecha;
        fila.querySelector(".sol__desc").textContent = s.oferta.descripcion;
        fila.querySelector(".sol__num").textContent = s.puntos;
        fila.querySelector(".sol__etq").textContent = ETIQUETAS[s.estado];
        fila.querySelector(".sol__ver").href = "/oferta/" + s.oferta.id;
        return fila;
    }

    // Muestra solo las filas del rol y la pestaña elegidos
    function filtrar() {
        for (const fila of lista.children) {
            const coincide = fila.dataset.rol === rol && fila.dataset.grupo === grupo;
            fila.classList.toggle("oculto", !coincide);
        }

        for (const tab of tabs) {
            const filas = lista.querySelectorAll(`[data-rol="${rol}"][data-grupo="${tab.dataset.grupo}"]`);
            tab.querySelector(".sol__n").textContent = filas.length;
        }

        const visibles = lista.querySelectorAll(".sol__fila:not(.oculto)").length;
        avisar(visibles === 0 ? "No hay solicitudes en esta sección." : "");
    }

    async function cargar() {
        try {
            const datos = await pedir("/api/misSolicitudes");
            lista.replaceChildren(
                ...datos.recibidas.map(s => crearFila(s, "recibidas")),
                ...datos.enviadas.map(s => crearFila(s, "enviadas"))
            );
            filtrar();
        } catch (error) {
            console.error(error);
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
            avisar(Object.values(json.errores).join(" "));
        }
    }

    // ===== Eventos =====

    for (const boton of botonesRol) {
        boton.addEventListener("click", () => {
            rol = boton.dataset.rol;
            botonesRol.forEach(b => b.classList.toggle("activo", b === boton));
            filtrar();
        });
    }

    for (const tab of tabs) {
        tab.addEventListener("click", () => {
            grupo = tab.dataset.grupo;
            tabs.forEach(t => t.classList.toggle("activo", t === tab));
            filtrar();
        });
    }

    // Un solo listener en la lista vale para los botones de todas las filas
    lista.addEventListener("click", e => {
        const boton = e.target.closest("[data-accion]");
        if (boton !== null) {
            cambiarEstado(boton.closest(".sol__fila").dataset.id, boton.dataset.accion);
        }
    });

    cargar();
}