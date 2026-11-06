window.addEventListener("load", function(){
    let btnRegistrar = document.getElementById("registrar");
    btnRegistrar.onclick=function(ev){
        ev.preventDefault();
        cargarModal("Registro Usuario", "Plantillas/registro.manuel", "js/registro/registro.js");
    }

    async function cargarModal(titulo, urlPlantilla, urlJs){
        let modal = new Modal(titulo);
        await modal.cargarHtml(urlPlantilla);
        await modal.cargarURLJS(urlJs);
        modal.cerrar.onclick=function(){
            modal.cerrarModal();
        };
    }
})