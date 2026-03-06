function campoVacio(valor) {
    return valor === null || valor.trim() === "";
}

function validarCamposRequeridos(campos) {
    for (let i = 0; i < campos.length; i++) {
        if (campoVacio(campos[i].value)) {
            return false;
        }
    }
    return true;
}

function validarHoras(horaInicio, horaFin) {
    return horaInicio < horaFin;
}
