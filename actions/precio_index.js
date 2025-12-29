const TARJETA_SELECTOR = '.marcos[data-subasta-id]';
const PRECIO_SELECTOR = '.precio-subasta';

let subastasParaPrecio = [];
let intervaloPrecio;

document.querySelectorAll(TARJETA_SELECTOR).forEach(tarjetaDOM => {
    const subastaId = tarjetaDOM.getAttribute('data-subasta-id');
    const precioDOM = tarjetaDOM.querySelector(PRECIO_SELECTOR);
    
    if (precioDOM && subastaId) {
        subastasParaPrecio.push({
            id: subastaId,
            precioDOM: precioDOM
        });
    }
});

async function actualizarPreciosEnIndex() {
    for (const subasta of subastasParaPrecio) {
        try {
            const respuesta = await fetch('actions/get_precio_actual.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `subasta_id=${encodeURIComponent(subasta.id)}`
            });
            const datos = await respuesta.json();

            if (datos.exito && datos.data) {
                subasta.precioDOM.textContent = `$${datos.data.precio_actual}`;
            }

        } catch (error) {
            console.error(`Error al actualizar precio de subasta ${subasta.id}:`, error);
        }
    }
}

if (subastasParaPrecio.length > 0) {
    intervaloPrecio = setInterval(actualizarPreciosEnIndex, 4000);
    actualizarPreciosEnIndex();
}