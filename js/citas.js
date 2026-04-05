const citasHardcoded = [
    {
        animal: "Max",
        veterinario: "Dr. Carlos Vega",
        fecha: "2026-03-08",
        horaInicio: "09:00",
        horaFin: "09:30",
        tipo: "Control",
        estado: "Confirmada"
    },
    {
        animal: "Mia",
        veterinario: "Dra. Elena Ruiz",
        fecha: "2026-03-09",
        horaInicio: "10:00",
        horaFin: "10:30",
        tipo: "Vacunación",
        estado: "Pendiente"
    },
    {
        animal: "Lola",
        veterinario: "Dr. Luis Mora",
        fecha: "2026-03-10",
        horaInicio: "11:00",
        horaFin: "11:45",
        tipo: "Chequeo",
        estado: "Cancelada"
    }
];

function obtenerClaseEstado(estado) {
    if (estado === "Confirmada") {
        return "badge-exito";
    } else if (estado === "Pendiente") {
        return "badge-advertencia";
    } else {
        return "badge-error";
    }
}

function cargarCitas() {
    const tbody = document.getElementById("tablaCitas");

    if (!tbody) {
        return;
    }

    let filas = "";

    for (let i = 0; i < citasHardcoded.length; i++) {
        filas += `
            <tr>
                <td>${citasHardcoded[i].animal}</td>
                <td>${citasHardcoded[i].veterinario}</td>
                <td>${citasHardcoded[i].fecha}</td>
                <td>${citasHardcoded[i].horaInicio} - ${citasHardcoded[i].horaFin}</td>
                <td>${citasHardcoded[i].tipo}</td>
                <td><span class="${obtenerClaseEstado(citasHardcoded[i].estado)}">${citasHardcoded[i].estado}</span></td>
                <td><a href="${pageRoute('cita-formulario')}" class="btn btn-sm btn-primary">Ver / Editar</a></td>
            </tr>
        `;
    }

    tbody.innerHTML = filas;
}

function llenarSelectAnimales() {
    const selectAnimal = document.getElementById("animal");

    if (!selectAnimal) {
        return;
    }

    const animales = ["Max", "Mia", "Lola"];

    let opciones = `<option value="">Seleccione un animal</option>`;

    for (let i = 0; i < animales.length; i++) {
        opciones += `<option value="${animales[i]}">${animales[i]}</option>`;
    }

    selectAnimal.innerHTML = opciones;
}

function guardarCita(event) {
    event.preventDefault();

    const animal = document.getElementById("animal");
    const veterinario = document.getElementById("veterinario");
    const fecha = document.getElementById("fecha");
    const horaInicio = document.getElementById("horaInicio");
    const horaFin = document.getElementById("horaFin");
    const tipo = document.getElementById("tipo");
    const nota = document.getElementById("nota");

    const campos = [animal, veterinario, fecha, horaInicio, horaFin, tipo];

    if (!validarCamposRequeridos(campos)) {
        alert("Completa todos los campos obligatorios.");
        return;
    }

    if (!validarHoras(horaInicio.value, horaFin.value)) {
        alert("La hora de inicio debe ser menor que la hora de fin.");
        return;
    }

    const cita = {
        animal: animal.value,
        veterinario: veterinario.value,
        fecha: fecha.value,
        horaInicio: horaInicio.value,
        horaFin: horaFin.value,
        tipo: tipo.value,
        nota: nota.value
    };

    sessionStorage.setItem("citaActual", JSON.stringify(cita));
    window.location.href = pageRoute("cita-confirmacion");
}

function mostrarResumenCita() {
    const contenedor = document.getElementById("resumenCita");

    if (!contenedor) {
        return;
    }

    const citaGuardada = sessionStorage.getItem("citaActual");

    if (!citaGuardada) {
        contenedor.innerHTML = "<p>No hay datos de cita para mostrar.</p>";
        return;
    }

    const cita = JSON.parse(citaGuardada);

    contenedor.innerHTML = `
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Animal:</strong> ${cita.animal}</li>
            <li class="list-group-item"><strong>Veterinario:</strong> ${cita.veterinario}</li>
            <li class="list-group-item"><strong>Fecha:</strong> ${cita.fecha}</li>
            <li class="list-group-item"><strong>Hora inicio:</strong> ${cita.horaInicio}</li>
            <li class="list-group-item"><strong>Hora fin:</strong> ${cita.horaFin}</li>
            <li class="list-group-item"><strong>Tipo:</strong> ${cita.tipo}</li>
            <li class="list-group-item"><strong>Nota especial:</strong> ${cita.nota}</li>
        </ul>
    `;
}

document.addEventListener("DOMContentLoaded", function () {
    cargarCitas();
    llenarSelectAnimales();
    mostrarResumenCita();

    const formularioCita = document.getElementById("formCita");
    if (formularioCita) {
        formularioCita.addEventListener("submit", guardarCita);
    }
});