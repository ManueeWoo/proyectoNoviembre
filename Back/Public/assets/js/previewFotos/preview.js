const inputFoto = document.getElementById("foto");
const preview = document.getElementById("preview");
const fotoOriginal = preview.src;
let urlTemporal = null;

inputFoto.addEventListener("change", function () {
    const archivo = this.files[0];

    if (urlTemporal) {
        URL.revokeObjectURL(urlTemporal);
        urlTemporal = null;
    }

    if (!archivo) {
        preview.src = fotoOriginal;
        return;
    }

    const tiposValidos = ["image/jpeg", "image/png", "image/webp"];
    if (!tiposValidos.includes(archivo.type)) {
        alert("Formato no válido. Usa JPG, PNG o WEBP.");
        this.value = "";
        preview.src = fotoOriginal;
        return;
    }
    if (archivo.size > 2 * 1024 * 1024) {
        alert("La imagen no puede superar 2 MB.");
        this.value = "";
        preview.src = fotoOriginal;
        return;
    }

    urlTemporal = URL.createObjectURL(archivo);
    preview.src = urlTemporal;
});