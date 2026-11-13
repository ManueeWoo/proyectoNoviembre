let divErrores = document.getElementById("errores");
function mostrarErrores(errores) {
    divErrores.innerHTML = "";
    let ul = document.createElement("ul");
    for (let campo in errores) {
        let li = document.createElement("li");
        li.textContent = errores[campo];   
        ul.appendChild(li);
    }
    divErrores.appendChild(ul);
    divErrores.hidden = false;
}

function ocultarErrores() {
    divErrores.innerHTML = "";
    divErrores.hidden = true;
}
let form = document.forms["RegistrarUsuario"];
form["guardar"].onclick = function (event) {
    event.preventDefault();
    if (!form.reportValidity()) return; 
    ocultarErrores();
    registrarUsuario()
        .then(respuesta => {
            if (respuesta && respuesta.ok) {
                document.body.modal.cerrarModal();
            } else if (respuesta) {
                mostrarErrores(respuesta.errores);
            }
        })
        .catch(err => mostrarErrores({ general: "No se pudo completar el registro. Inténtalo de nuevo." }));
};

async function registrarUsuario() {
    let servicios = [];
        for (let i = 0; i < form["serviciosSelec"].options.length; i++) {
            servicios.push(form["serviciosSelec"].options[i].value);
        }
    let datos = {
        nombre: form["nombre"].value,
        ape1: form["ape1"].value,
        ape2: form["ape2"].value,
        email: form["email"].value,
        telefono: form["telefono"].value,
        passwd1: form["passwd1"].value,
        passwd2: form["passwd2"].value,
        servicios: servicios
    };
    let peticion = await fetch("/api/registrar", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos)
    });
    let texto = await peticion.text();
    return texto ? JSON.parse(texto) : null;
}

async function traerCategorias() {
    let petCategorias = await fetch("/api/categorias");
    if (petCategorias.ok) {
        let json = await petCategorias.json();
        return json;
    }
}

async function traerServicios(id) {
    let petServicios = await fetch("/api/servicios/" + id);
    if (petServicios.ok) {
        let json = await petServicios.json();
        return json;
    }
}

function seleccionar() {
    form["serviciosSelec"].appendChild(this);
    this.onclick = quitar;
}

function quitar() {
    form["servicios"].appendChild(this);
    this.onclick = seleccionar;
}

traerCategorias().then(json => {
    for (let i = 0; i < json.length; i++) {
        let option = document.createElement("option");
        option.value = json[i].id;
        option.text = json[i].titulo;
        option.onclick = function () {
            let id = this.value;
            traerServicios(id).then(servicios => {
                form["servicios"].innerHTML = "";
                for (let j = 0; j < servicios.length; j++) {
                    let option = document.createElement("option");
                    option.value = servicios[j].id;
                    option.text = servicios[j].titulo;
                    option.onclick = seleccionar;
                    form["servicios"].appendChild(option);
                }
            });
        };
        form["categoria"].appendChild(option);
    }
});
