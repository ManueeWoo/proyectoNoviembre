function Modal(titulo){
    this.velo=document.createElement("div");
    this.modal=document.createElement("div");
    this.titulo=document.createElement("div");
    this.tituloTexto=document.createElement("div");
    this.cerrar=document.createElement("div");
    this.contenido=document.createElement("div");
    this.codigo=document.createElement("script");;

    document.body.appendChild(this.velo);    
    this.velo.id="velo";
    document.body.appendChild(this.modal);
    this.modal.id="modal";
    this.tituloTexto.innerHTML=titulo;
    this.cerrar.innerHTML="X";
    let modal=this;
    this.cerrar.onclick=function(){
        modal.cerrarModal();
    }
    this.titulo.appendChild(this.tituloTexto);
    this.titulo.appendChild(this.cerrar);
    this.modal.appendChild(this.titulo);
    this.modal.appendChild(this.contenido);
}

Modal.prototype.cerrarModal=function(){
    this.velo.remove();
    this.modal.remove();
}

Modal.prototype.cargarHtml=async function(urlPlantilla){
     let peticionPlantilla = await fetch(urlPlantilla);
    if(peticionPlantilla.ok){
        let textoHtml = await peticionPlantilla.text();
        this.contenido.innerHTML = textoHtml; 
    } else {
        throw "Error en el servidor al cargar la plantilla";
    }
}

Modal.prototype.cargarURLJS = async function(urlJs){
    let peticionJs = await fetch(urlJs);
    if(peticionJs.ok){
        this.codigo.innerHTML = await peticionJs.text();
        document.body.appendChild(this.codigo);   
    }else{
        throw "Error en el servidor";
    }
};


Modal.prototype.cargarLogica=function(){};
