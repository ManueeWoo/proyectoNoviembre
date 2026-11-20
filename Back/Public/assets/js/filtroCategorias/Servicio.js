function iniciar(){
    const listaCategorias = document.getElementById("listaCategorias");
const grid = document.getElementById("grid");
const estado = document.getElementById("estado");
const total = document.getElementById("total");
const sinResultados = document.getElementById("sinResultados");
const limpiar = document.getElementById("limpiar");
let spinner = document.getElementById("spinner");

let checks = [];
let tarjetas = [];

async function traer(url) {
    let respuesta = await fetch(url);
    if (!respuesta.ok) {
        throw new Error("Error " + respuesta.status + " en " + url);
    }
    return await respuesta.json();
}

function crear(etiqueta, clase, texto) {
    let elemento = document.createElement(etiqueta);
    if (clase) {
        elemento.className = clase;
    }
    if (texto !== undefined) {
        elemento.textContent = texto;
    }
    return elemento;
}

function pintarCategorias(categorias) {
    for (let i = 0; i < categorias.length; i++) {
        let label = crear("label", "filtros__opcion");
        let input = document.createElement("input");
        input.type = "checkbox";
        input.className = "filtro-cat";
        input.value = categorias[i].id;
        input.addEventListener("change", filtrar);

        label.appendChild(input);
        let span = document.createElement("span");
        span.innerHTML = categorias[i].titulo;
        label.appendChild(span);
        listaCategorias.appendChild(label);
        checks.push(input);
    }
}

function pintarTarjetas(servicios) {
    for (let i = 0; i < servicios.length; i++) {
        let s = servicios[i];

        let tarjeta = crear("a", "tarjeta");
        tarjeta.href = "/servicio/" + s.id;
        tarjeta.dataset.categoria = s.categoria.id;

        let img = crear("div", "tarjeta__img");
        img.appendChild(crear("span", "tarjeta__cat", s.categoria.titulo));

        let cuerpo = crear("div", "tarjeta__cuerpo");
        cuerpo.appendChild(crear("h2", "tarjeta__titulo", s.titulo));
        cuerpo.appendChild(crear("p", "tarjeta__desc", s.descripcion));

        let pie = crear("div", "tarjeta__pie");
        pie.appendChild(crear("span", "", s.numOfertas + " ofertas"));
        pie.appendChild(crear("span", "tarjeta__ver", "Ver ofertas"));
        cuerpo.appendChild(pie);

        tarjeta.appendChild(img);
        tarjeta.appendChild(cuerpo);
        grid.appendChild(tarjeta);
        tarjetas.push(tarjeta);
    }
}

function filtrar() {
    let marcadas = [];
    for (let i = 0; i < checks.length; i++) {
        if (checks[i].checked) {
            marcadas.push(checks[i].value);
        }
    }

    let visibles = 0;
    for (let i = 0; i < tarjetas.length; i++) {
        let categoria = tarjetas[i].dataset.categoria;

        if (marcadas.length === 0 || marcadas.includes(categoria)) {
            tarjetas[i].classList.remove("oculto");
            visibles++;
        } else {
            tarjetas[i].classList.add("oculto");
        }
    }

    total.textContent = visibles;

    if (tarjetas.length > 0 && visibles === 0) {
        sinResultados.classList.remove("oculto");
    } else {
        sinResultados.classList.add("oculto");
    }
}

limpiar.addEventListener("click", function (e) {
    e.preventDefault();
    for (let i = 0; i < checks.length; i++) {
        checks[i].checked = false;
    }
    filtrar();
});

async function cargarServicios() {
    spinner.classList.remove("oculto");   
    try {
        const categorias = await traer("/api/categorias");
        const servicios = await traer("/api/servicios");

        pintarCategorias(categorias);
        pintarTarjetas(servicios);

        if (tarjetas.length === 0) {
            estado.textContent = "Todavía no hay servicios publicados.";
            estado.classList.remove("oculto");
        }
        filtrar();
    } catch (error) {
        console.error(error);
        estado.textContent = "No se pudieron cargar los servicios. Inténtalo de nuevo.";
        estado.classList.remove("oculto");
    } finally {
        spinner.classList.add("oculto");  
    }
}
cargarServicios();
}


iniciar();