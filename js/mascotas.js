const mascotasHardcoded = [
    {
        id: 1,
        nombre: "Max",
        especie: "Perro",
        raza: "Labrador",
        edad: 5,
        sexo: "Macho",
        peso: 22,
        observaciones: "Vacunas al día",
        propietario: "Alondra Matamoros"
    },
    {
        id: 2,
        nombre: "Mia",
        especie: "Gato",
        raza: "Siamés",
        edad: 3,
        sexo: "Hembra",
        peso: 4,
        observaciones: "Requiere revisión dental",
        propietario: "María López"
    },
    {
        id: 3,
        nombre: "Lola",
        especie: "Ganado",
        raza: "Holstein",
        edad: 4,
        sexo: "Hembra",
        peso: 450,
        observaciones: "Control veterinario mensual",
        propietario: "Finca San José",
        identificacion: "G-102",
        finca: "Lote 3",
        estadoSanitario: "Estable"
    }
];

function cargarMascotas() {
    const tbody = document.getElementById("tablaMascotas");

    if (!tbody) {
        return;
    }

    let filas = "";

    for (let i = 0; i < mascotasHardcoded.length; i++) {
        filas += `
            <tr>
                <td>${mascotasHardcoded[i].nombre}</td>
                <td>${mascotasHardcoded[i].especie}</td>
                <td>${mascotasHardcoded[i].raza}</td>
                <td>${mascotasHardcoded[i].edad}</td>
                <td>${mascotasHardcoded[i].propietario}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-2" onclick="editarMascota(${mascotasHardcoded[i].id})">Editar</button>
                    <a href="${pageRoute('cita-formulario')}" class="btn btn-sm btn-success">Agendar cita</a>
                </td>
            </tr>
        `;
    }

    tbody.innerHTML = filas;
}

function mostrarCamposGanado() {
    const especie = document.getElementById("especie");
    const bloqueGanado = document.getElementById("camposGanado");

    if (!especie || !bloqueGanado) {
        return;
    }

    if (especie.value === "Ganado") {
        bloqueGanado.classList.remove("oculto");
    } else {
        bloqueGanado.classList.add("oculto");
    }
}

function editarMascota(id) {
    sessionStorage.setItem("mascotaEditarId", id);
    window.location.href = pageRoute("mascota-formulario");
}

function cargarDatosMascotaFormulario() {
    const idEditar = sessionStorage.getItem("mascotaEditarId");

    if (!idEditar) {
        return;
    }

    let mascotaEncontrada = null;

    for (let i = 0; i < mascotasHardcoded.length; i++) {
        if (mascotasHardcoded[i].id == idEditar) {
            mascotaEncontrada = mascotasHardcoded[i];
        }
    }

    if (mascotaEncontrada !== null) {
        document.getElementById("nombre").value = mascotaEncontrada.nombre;
        document.getElementById("especie").value = mascotaEncontrada.especie;
        document.getElementById("raza").value = mascotaEncontrada.raza;
        document.getElementById("edad").value = mascotaEncontrada.edad;
        document.getElementById("sexo").value = mascotaEncontrada.sexo;
        document.getElementById("peso").value = mascotaEncontrada.peso;
        document.getElementById("observaciones").value = mascotaEncontrada.observaciones;
        document.getElementById("propietario").value = mascotaEncontrada.propietario;

        mostrarCamposGanado();

        if (mascotaEncontrada.especie === "Ganado") {
            document.getElementById("identificacion").value = mascotaEncontrada.identificacion || "";
            document.getElementById("finca").value = mascotaEncontrada.finca || "";
            document.getElementById("estadoSanitario").value = mascotaEncontrada.estadoSanitario || "";
        }
    }
}

function guardarMascota(event) {
    event.preventDefault();

    const campos = [
        document.getElementById("nombre"),
        document.getElementById("especie"),
        document.getElementById("raza"),
        document.getElementById("edad"),
        document.getElementById("sexo"),
        document.getElementById("peso"),
        document.getElementById("propietario")
    ];

    if (!validarCamposRequeridos(campos)) {
        alert("Por favor completa todos los campos obligatorios.");
        return;
    }

    alert("Mascota guardada correctamente.");
    sessionStorage.removeItem("mascotaEditarId");
    window.location.href = pageRoute("mascotas");
}

document.addEventListener("DOMContentLoaded", function () {
    cargarMascotas();
    cargarDatosMascotaFormulario();

    const especie = document.getElementById("especie");
    if (especie) {
        especie.addEventListener("change", mostrarCamposGanado);
        mostrarCamposGanado();
    }

    const formularioMascota = document.getElementById("formMascota");
    if (formularioMascota) {
        formularioMascota.addEventListener("submit", guardarMascota);
    }
});