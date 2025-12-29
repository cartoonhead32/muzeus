const fechaFinDb = document.getElementById("contador");
const fechaFinDbValue = fechaFinDb.getAttribute('fecha-fin');
const fechaFinDbIso = fechaFinDbValue.replace(" ", 'T') + "Z";
const fechaFinJs = Math.floor(Date.parse(fechaFinDbIso) / 1000);
const inputPrecio = document.getElementById('precio');
const inputOfertas = document.getElementById('total-ofertas');

async function actualizarPrecio() {
    const tiempoActualCliente = Math.floor(Date.now() / 1000);
    const tiempoRestante = fechaFinJs - tiempoActualCliente;
    const inputSubasta = document.getElementById('input-subasta');

    if (tiempoRestante > 0 || tiempoRestante <= 0) {
        const respuesta = await fetch('actions/get_precio_actual.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `subasta_id=${encodeURIComponent(inputSubasta.value)}`
        });
        const datos = await respuesta.json();
        if (datos.exito && datos.data) {
            inputPrecio.textContent = "$" + datos.data.precio_actual; 
            inputOfertas.textContent = datos.data.total_ofertas; 
            if (tiempoRestante <= 0) {
                clearInterval(intervaloPrecio);
                console.log('La subasta termino');
            }
        }
    }
}

intervaloPrecio = setInterval(actualizarPrecio,4000);
actualizarPrecio();