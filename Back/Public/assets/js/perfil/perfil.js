
window.addEventListener("load", function () {

    const botonEditar = document.getElementById("editarPerfil");
    const botonCancelar = document.getElementById("cancelarEdicion");

    const vistaPerfil = document.getElementById("vistaPerfil");
    const formPerfil = document.getElementById("formPerfil");

    if (botonEditar) {
        botonEditar.addEventListener("click", function () {
            vistaPerfil.hidden = true;
            formPerfil.hidden = false;
        });
    }

    if (botonCancelar) {
        botonCancelar.addEventListener("click", function () {
            formPerfil.reset();
            formPerfil.hidden = true;
            vistaPerfil.hidden = false;
        });
    }

});
