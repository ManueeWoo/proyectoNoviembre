function crearVelo(){
    let div = document.createElement("div");
    div.style.backgroundColor="rgba(150,150,150,0.3)";
    div.style.position="fixed";
    div.style.top="0px";
    div.style.left="0px";
    div.style.width="100%";
    div.style.height="100%";
    div.style.zIndex=999;
    document.body.appendChild(div);
    return div;
}

function crearModal(titulo, contenido){
    let div = document.createElement("div");
    div.velo=crearVelo();
    div.style.backgroundColor="#FFF";
    div.style.position="fixed";
    div.style.top="25%";
    div.style.left="25%";
    div.style.width="50%";
    div.style.height="50%";
    div.style.zIndex=1000;
    document.body.appendChild(div);
    let divTitulo=document.createElement("div");
    divTitulo.style.position="relative";
    let divTextotitulo=document.createElement("div");
    divTextotitulo.innerHTML=titulo;
    let divCerrar=document.createElement("div");
    divCerrar.innerHTML="X"
    divTextotitulo.style.position="absolute"
    divTextotitulo.style.left="10px"
    divTextotitulo.style.width="80%"
    divCerrar.onclick=function(){
        div.velo.remove();
        div.remove();
    }
    divCerrar.style.position="absolute"
    divCerrar.style.right="10px"

    divTitulo.appendChild(divTextotitulo);
    divTitulo.appendChild(divCerrar);
    let divContenido=document.createElement("div");
    divContenido.style.overflow="scroll";
    divContenido.style.position="absolute";
    divContenido.style.top="40px";
    for(let i = 0; i<contenido.length;i++){
        divContenido.appendChild(contenido[i]);
    }

    div.appendChild(divTitulo);
    div.appendChild(divContenido);

    return div;
}