window.addEventListener("load", function () {

    const a = document.getElementById("menuServicios");
    const contenedor = document.getElementById("contenido");

    a.addEventListener("click", async function(e) {

        console.log("CLICK DETECTADO");

        e.preventDefault();

        try {
            await cargarHtml("/Plantillas/servicios.manuel", contenedor);

            await cargarJs("/assets/js/filtroCategorias/Servicio.js");

        } catch (error) {
            console.error(error);
        }

    });

    async function cargarHtml(url, destino) {
        const respuesta = await fetch(url);

        if (!respuesta.ok) {
            throw new Error("Error " + respuesta.status);
        }

        destino.innerHTML = await respuesta.text();
        css = document.createElement("link");
        css.id = "cssServicios";
        css.rel = "stylesheet";
        css.href = "/assets/css/servicios.css";

        document.head.appendChild(css);
    }

    async function cargarJs(urlJs) {
        const respuesta = await fetch(urlJs);

        if (!respuesta.ok) {
            throw new Error("Error " + respuesta.status);
        }

        const script = document.createElement("script");
        script.textContent = await respuesta.text();

        document.body.appendChild(script);
    }

});