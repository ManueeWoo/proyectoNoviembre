window.addEventListener("load", function(){
    let btnRegistrar = document.getElementById("registrar");
    btnRegistrar.onclick=function(ev){
        ev.preventDefault();
        cargarModal("Registro Usuario", "Plantillas/registro.manuel", "assets/js/registro/registro.js");
    }

    async function cargarModal(titulo, urlPlantilla, urlJs){
        document.body.modal = new Modal(titulo);
        await document.body.modal.cargarHtml(urlPlantilla);
        await document.body.modal.cargarURLJS(urlJs);
        
    }
})