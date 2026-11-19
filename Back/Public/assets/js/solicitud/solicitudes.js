function iniciarSolicitudes() {
    const lista = document.getElementById("lista");
    const aviso = document.getElementById("aviso");
    const botonesRol = document.querySelectorAll(".sol__op");
    const botonesTab = document.querySelectorAll(".sol__tab");

    const GRUPOS = {
        pendientes: ["solicitado"],
        curso: ["aceptado", "ejecucion"],
        cerradas: ["completado", "rechazado", "cancelado"]
    };
    const ETIQUETAS = {
        solicitado: "Pendiente de respuesta",
        aceptado: "Aceptada",
        ejecucion: "En ejecución",
        completado: "Completada",
        rechazado: "Rechazada",
        cancelado: "Cancelada"
    };

    let datos = { recibidas: [], enviadas: [] };
    let rol = "recibidas";
    let grupo = "pendientes";


    function crear(etiqueta, clase, texto) {
        const el = document.createElement(etiqueta);
        if (clase) {
            el.className = clase;
        }
        if (texto !== undefined) {
            el.textContent = texto;
        }
        return el;
    }

    function mostrarAviso(texto) {
        aviso.textContent = texto;
        aviso.hidden = false;
    }

    function formatearFecha(fecha) {
        const partes = fecha.split("-");
        return partes[2] + "/" + partes[1] + "/" + partes[0];
    }

    function marcarActivo(botones, elegido) {
        for (let i = 0; i < botones.length; i++) {
            botones[i].classList.remove("activo");
        }
        elegido.classList.add("activo");
    }

    // Pide al servidor. Un 401 (sin sesión) lleva al login
    async function pedir(url, opciones) {
        const respuesta = await fetch(url, opciones);
        if (respuesta.status === 401) {
            //preguntar como redirigir
            throw new Error("Sin sesión");
        }
        const texto = await respuesta.text();
        return JSON.parse(texto);
        
    }

    // ===== Pintar =====

    function crearFila(s) {
        const fila = crear("article", "sol__fila");

        const foto = crear("img", "sol__foto");
        foto.src = "/upload/" + (s.persona.foto ? s.persona.foto : "logo.png");
        foto.alt = "Foto de " + s.persona.nombre;

        const info = crear("div", "sol__info");
        info.appendChild(crear("h2", "sol__nombre", s.oferta.titulo));
        const quien = rol === "recibidas" ? "Solicitada por " : "Oferta de ";
        info.appendChild(crear("p", "sol__texto", quien + s.persona.nombre + " · " + formatearFecha(s.fecha)));
        info.appendChild(crear("p", "sol__texto sol__desc", s.oferta.descripcion));
        info.appendChild(crear("span", "sol__etq sol__etq--" + s.estado, ETIQUETAS[s.estado]));

        const puntos = crear("div", "sol__puntos");
        puntos.appendChild(crear("strong", "", s.puntos));
        puntos.appendChild(crear("span", "", "puntos"));

        const acciones = crear("div", "sol__acciones");
        if (s.estado === "solicitado" && rol === "recibidas") {
            acciones.appendChild(crearBoton("Aceptar", "sol__btn sol__btn--ok", s.id, "aceptar"));
            acciones.appendChild(crearBoton("Rechazar", "sol__btn sol__btn--no", s.id, "rechazar"));
        } else if (s.estado === "solicitado") {
            acciones.appendChild(crearBoton("Cancelar", "sol__btn sol__btn--no", s.id, "cancelar"));
        } else {
            const ver = crear("a", "sol__btn", "Ver oferta");
            ver.href = "/oferta/" + s.oferta.id;
            acciones.appendChild(ver);
        }

        fila.appendChild(foto);
        fila.appendChild(info);
        fila.appendChild(puntos);
        fila.appendChild(acciones);
        return fila;
    }

    function crearBoton(texto, clase, id, accion) {
        const boton = crear("button", clase, texto);
        boton.type = "button";
        boton.addEventListener("click", function () {
            cambiarEstado(id, accion, boton);
        });
        return boton;
    }

    function pintar() {
        const todas = datos[rol];

        // Contadores de las pestañas (según Recibidas o Enviadas)
        for (let i = 0; i < botonesTab.length; i++) {
            const estados = GRUPOS[botonesTab[i].dataset.grupo];
            let n = 0;
            for (let j = 0; j < todas.length; j++) {
                if (estados.includes(todas[j].estado)) {
                    n++;
                }
            }
            botonesTab[i].querySelector(".sol__n").textContent = n;
        }

        // Lista de la pestaña activa
        lista.textContent = "";
        let visibles = 0;
        for (let i = 0; i < todas.length; i++) {
            if (GRUPOS[grupo].includes(todas[i].estado)) {
                lista.appendChild(crearFila(todas[i]));
                visibles++;
            }
        }

        if (visibles === 0) {
            mostrarAviso("No hay solicitudes en esta sección.");
        } else {
            aviso.hidden = true;
        }
    }

    // ===== Datos =====

    async function cargar() {
        try {
            datos = await pedir("/api/misSolicitudes");
            pintar();
        } catch (error) {
            console.error(error);
            mostrarAviso("No se pudieron cargar las solicitudes. Inténtalo de nuevo.");
        }
    }

    async function cambiarEstado(id, accion, boton) {
        boton.disabled = true;
        const formulario = new FormData();
        formulario.append("id", id);
        formulario.append("accion", accion);

        try {
            const json = await pedir("/api/solicitud/estado", { method: "POST", body: formulario });
            if (json.ok) {
                await cargar();   // se vuelve a pedir la lista y se repinta
            } else {
                for (let campo in json.errores) {
                    mostrarAviso(json.errores[campo] + " ");
                }
                boton.disabled = false;
            }
        } catch (error) {
            console.error(error);
            mostrarAviso("No se pudo completar la acción. Inténtalo de nuevo.");
            boton.disabled = false;
        }
    }

    // ===== Eventos =====

    for (let i = 0; i < botonesRol.length; i++) {
        botonesRol[i].addEventListener("click", function () {
            rol = this.dataset.rol;
            marcarActivo(botonesRol, this);
            pintar();
        });
    }

    for (let i = 0; i < botonesTab.length; i++) {
        botonesTab[i].addEventListener("click", function () {
            grupo = this.dataset.grupo;
            marcarActivo(botonesTab, this);
            pintar();
        });
    }

    cargar();
}