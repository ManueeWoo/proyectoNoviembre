window.addEventListener("load", async function () {
    const contenedor = document.getElementById("contenedorServicios");
    try {
        await cargarHtml("/Plantillas/servicios.manuel", contenedor);
        await cargarJs("/assets/js/filtroCategorias/Servicio.js");
    } catch (error) {
        console.error(error);   // para ver el error real en la consola
        contenedor.textContent = "No se pudo cargar la página. Inténtalo de nuevo.";
    }
});

async function cargarHtml(url, destino) {
    const respuesta = await fetch(url);
    if (!respuesta.ok) {
        throw new Error("Error " + respuesta.status + " al cargar " + url);
    }
    destino.innerHTML = await respuesta.text();
}

async function cargarJs(urlJs) {
    const peticionJs = await fetch(urlJs);
    if (!peticionJs.ok) {
        throw new Error("Error " + peticionJs.status + " al cargar " + urlJs);
    }
    const script = document.createElement("script");   
    script.textContent = await peticionJs.text();       
    document.body.appendChild(script);                  
}