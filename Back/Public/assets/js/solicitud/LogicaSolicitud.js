window.addEventListener("load", function(){
    let btnSolicitar = document.getElementById("solicitar");
    btnSolicitar.onclick=function(ev){
        ev.preventDefault();
        cargarModal("Solicitud Oferta", "Plantillas/solicitud.manuel", "assets/js/solicitud/solicitud.js");
    }

    async function cargarModal(titulo, urlPlantilla, urlJs){
        document.body.modal = new Modal(titulo);
        await document.body.modal.cargarHtml(urlPlantilla);
        await document.body.modal.cargarURLJS(urlJs);
        
    }
})