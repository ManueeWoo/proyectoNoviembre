window.addEventListener("load", function(){
    let btnRegistrar = document.getElementById("registrar");
    btnRegistrar.onclick=function(ev){
        ev.preventDefault();
        traerPlantillaRegistro();
    }
    async function traerPlantillaRegistro(){
        let peticion = await fetch("Plantillas/registro.manuel");
        if(peticion.ok){
            let texto = await peticion.text();
            let div = document.createElement("div");
            div.innerHTML=texto;
            crearModal("Registro", div.children);
        }else{
            throw "Error en la descarga de la plantilla";
        }
    }
})