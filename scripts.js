//prueba cache
const formularioRegistro = document.getElementById('formulario-registro');
const formularioOferta = document.getElementById('formulario-oferta');
const esPerfil = document.getElementById('perfil');
let envioCodigo; 
let verificacionCodigo;

function espera(ms) {
    return new Promise((resolve) => {
        setTimeout(resolve, ms);
    });
}

function menuHamburguesa() {
    const btn = document.getElementById("hamburguesa-btn");
    const sidebar = document.getElementById("sidebar");

    if (!btn || !sidebar) 
        return;
    
    btn.addEventListener("click", () => {
        sidebar
            .classList
            .toggle("active");
    });

    document.addEventListener("click", (e) => {
        if (sidebar.classList.contains("active") && !sidebar.contains(e.target) && e.target !== btn) {
            sidebar
                .classList
                .remove("active");
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1100) {
            sidebar
                .classList
                .remove("active");
        }
    });
}

menuHamburguesa();

if (formularioRegistro) {
    const campoMensaje = document.getElementById('mensaje');
    const campoCorreo = document.getElementById('correo');
    const campoNombre = document.getElementById('nombre');
    const campoContrasena = document.getElementById('contrasena');
    const campoVerificacion = document.getElementById('contenedor-codigo');
    const campoCodigo = document.getElementById('codigo');

    formularioRegistro.addEventListener('submit', async (e) => {
        e.preventDefault();
        campoMensaje.textContent = "";
        campoMensaje.style = 'color: white;';
        campoMensaje.textContent = "Creando cuenta...";
        try {
            const respuesta = await fetch('actions/registro_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `nombre=${encodeURIComponent(campoNombre.value)}&correo=${encodeURIComponent(campoCorreo.value)}&contrasena=${encodeURIComponent(campoContrasena.value)}`
            });
            const datos = await respuesta.json();
            if (datos.exito) {
                campoMensaje.textContent = datos.mensaje;
                campoMensaje.style = 'color: green;';
                await espera(1000);
                window.location.href = 'index.php';
            } else {
                campoMensaje.style = 'color: red;';
            }
            campoMensaje.textContent = datos.mensaje;
        } catch(error) {
            console.error(`Error: ${error}`);
            campoMensaje.textContent = datos.mensaje;
        }
    });

    envioCodigo = async () => {
        campoMensaje.textContent = "";
        campoMensaje.style = 'color: white;';
        campoMensaje.textContent = "Procesando...";
        try {
            const respuesta = await fetch('actions/enviar_codigo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `correo=${encodeURIComponent(campoCorreo.value)}&nombre=${encodeURIComponent(campoNombre.value)}`
            });
            const datos = await respuesta.json();
            if (datos.exito) {
                campoVerificacion
                        .classList
                        .add("active");
                campoCodigo.setAttribute('required', 'required');
                campoMensaje.style = 'color: green;';
            } else {
                campoMensaje.style = 'color: red;';
            }
            campoMensaje.textContent = datos.mensaje;
        } catch(error) {
            console.error(`Error: ${error}`);
        }
    }

    verificacionCodigo = async () => {
        campoMensaje.textContent = "";
        campoMensaje.style = 'color: white;';
        campoMensaje.textContent = "Verificando...";
        try {
            const respuesta = await fetch('actions/verificar_codigo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `codigo=${encodeURIComponent(campoCodigo.value)}&correo=${encodeURIComponent(campoCorreo.value)}`
            });
            const datos = await respuesta.json();
            if (datos.exito) {
                campoMensaje.style = 'color: green;';
            } else {
                campoMensaje.style = 'color: red;';
            }
            campoMensaje.textContent = datos.mensaje;
        } catch(error) {
            console.error(`Error: ${error}`);
        }
    }
}

if (formularioOferta) {
    const campoMensaje = document.getElementById('mensaje');
    const inputOferta = document.getElementById('input-oferta');
    const inputSubasta = document.getElementById('input-subasta');
    formularioOferta.addEventListener('submit', async (e) => {
        e.preventDefault();
        campoMensaje.textContent = "";
        campoMensaje.style = 'color: white;';
        campoMensaje.textContent = "Ofertando...";
        try {
            const respuesta = await fetch('actions/realizar_oferta.php', {
                method: "POST",
                headers: {
                    "Content-Type": 'application/x-www-form-urlencoded',
                },
                body: `oferta=${inputOferta.value}&subasta_id=${inputSubasta.value}`
            });
            const datos = await respuesta.json();
            campoMensaje.textContent = "";
            if (datos.exito) {
                campoMensaje.style = 'color: green;';
                window.location.reload();
            } else {
                campoMensaje.style = 'color: red;';
            }
            campoMensaje.textContent = datos.mensaje;
        } catch (e) {
            console.error(e);
            campoMensaje.textContent = "";
            campoMensaje.style = 'color: red;';
            campoMensaje.textContent = e;
        }
    });
}

if (esPerfil) {
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.carousel-btn');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetId = btn.dataset.target;
                const container = document.getElementById(targetId);
                if (!container) return;

                const card = container.querySelector('.tarjeta-producto');
                const gap = 12;
                const scrollAmount = card ? (card.offsetWidth + gap) : container.clientWidth;
                const direction = btn.classList.contains('next') ? 1 : -1;

                container.scrollBy({
                    left: direction * scrollAmount,
                    behavior: 'smooth'
                });
            });
        });
    });
}