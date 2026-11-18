window.addEventListener("load", function () {
    const checks = document.querySelectorAll('.filtro-cat');
    const tarjetas = document.querySelectorAll('.tarjeta');
    const sinResultados = document.getElementById('sinResultados');
    const limpiar = document.getElementById('limpiar');

    function filtrar() {
        const marcadas = [];
        for (let i = 0; i < checks.length; i++) {
            if (checks[i].checked) {
                marcadas.push(checks[i].value);
            }
        }
        let visibles = 0;
        for (let i = 0; i < tarjetas.length; i++) {
            const tarjeta = tarjetas[i];
            const categoria = tarjeta.dataset.categoria; //a la hora de pasar informacion esta al ser un id no hay problema que se vea en el html, pero objetos gordos no
            //ya que se verian en html

            if (marcadas.length === 0 || marcadas.includes(categoria)) {
                tarjeta.classList.remove('oculto');
                visibles++;
            } else {
                tarjeta.classList.add('oculto');
            }
        }

        if (visibles === 0) {
            sinResultados.classList.remove('oculto');
        } else {
            sinResultados.classList.add('oculto');
        }
    }

    for (let i = 0; i < checks.length; i++) {
        checks[i].addEventListener("change", filtrar);
    }

    limpiar.addEventListener("click", function (e) {
        e.preventDefault();
        for (let i = 0; i < checks.length; i++) {
            checks[i].checked = false;
        }
        filtrar();
    });
});