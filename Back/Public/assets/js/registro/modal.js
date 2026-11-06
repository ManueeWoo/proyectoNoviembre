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



Modal.prototype.cargarURLJS=async function(urlJs){
    let peticionJs = await fetch(urlJs);
    if(peticionJs.ok){
        let contenido = await peticionJs.text();
        this.codigo.innerHTML=contenido;
    }else{
        throw "Error en el servidor";
    }
}


Modal.prototype.cargarLogica=function(){};

//tengo la logica del btn de la plantilla de manolo , salta el velo modal etc, tambien tng q llamar al servidor y que me de los servicios, categorias,etc
//cuando lo tnego todo validado mando el json con los campos a servidor si todo ok quito el velo y modal
//cndo inyecto los servicios en el form los tengo en options value="idDelservicio" que es lo que se manda