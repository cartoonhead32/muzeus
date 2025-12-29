const listaContadores = document.querySelectorAll('.contador');
let contadoresActivos = [];

listaContadores.forEach(parrafoDOM => {
    const fechaFinDb = parrafoDOM.getAttribute('fecha-fin');
    
    if (fechaFinDb) {
        const fechaFinDbIso = fechaFinDb.replace(" ", 'T') + "Z";
        const fechaFinJs = Math.floor(Date.parse(fechaFinDbIso) / 1000);
        
        contadoresActivos.push({
            dom: parrafoDOM,
            fin: fechaFinJs
        });
    }
});

let intervaloContadores;

function actualizarContador() {
    const tiempoActualCliente = Math.floor(Date.now() / 1000);

    contadoresActivos = contadoresActivos.filter(parrafoArray => {
        const tiempoRestante = parrafoArray.fin - tiempoActualCliente;
        const parrafoDOM = parrafoArray.dom;

        if (tiempoRestante > 0) {
            const dias = Math.floor(tiempoRestante / 86400);
            const horas = Math.floor((tiempoRestante % 86400) / 3600);
            const minutos = Math.floor((tiempoRestante % 3600) / 60);
            const segundos = tiempoRestante % 60;

            const d = dias.toString().padStart(2, "0");
            const h = horas.toString().padStart(2, "0");
            const m = minutos.toString().padStart(2, "0");
            const s = segundos.toString().padStart(2, "0");

            parrafoDOM.innerText = `Termina: ${d}:${h}:${m}:${s}`;

            return true;

        } else {
            parrafoDOM.style.color = "red";
            parrafoDOM.innerText = "Subasta terminada";
            return false;
        }
    });

    if (contadoresActivos.length === 0) {
        clearInterval(intervaloContadores);
    }

}

if (contadoresActivos.length > 0) {
    intervaloContadores = setInterval(actualizarContador, 1000);
    actualizarContador();
}