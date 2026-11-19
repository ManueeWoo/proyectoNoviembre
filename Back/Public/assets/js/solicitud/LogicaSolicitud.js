window.addEventListener("load", function () {
    const boton = document.getElementById("solicitar");
    const mensaje = document.getElementById("solicitudMsg");

    boton.addEventListener("click", async function () {
        mensaje.textContent = "";

        const datos = new FormData();
        datos.append("id_oferta", boton.dataset.oferta);

        try {
            const respuesta = await fetch("/api/solicitud", {
                method: "POST",
                body: datos
            });

            if (respuesta.status === 401) {
                //preguntar como redirigir                            
            }

            const json = await respuesta.json();

            if (json.ok) {
                mensaje.textContent = "Solicitud enviada.";
            } else {
                for (let campo in json.errores) {
                    mensaje.textContent += json.errores[campo] + " ";
                }
            }
        } catch (error) {
            console.error(error);
            mensaje.textContent = "No se pudo enviar la solicitud. Inténtalo de nuevo.";
        }
    });
});