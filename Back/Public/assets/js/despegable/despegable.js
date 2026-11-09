window.addEventListener("load", function(){
    const pulsar = document.querySelector('.pulsar');
    const padre = document.querySelector('.despegable-padre');


    pulsar.addEventListener("click", function(e){
        e.preventDefault();
        if (padre.classList.contains('abierto')) {
            padre.classList.remove('abierto');
        } else {
            padre.classList.add('abierto');
        }
    })
})
