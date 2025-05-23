document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.form-usuario');
    const correoInput = document.getElementById('correo');
    const rolSelect = document.getElementById('rol');
    const errorServidorDiv = document.getElementById('error-server');

    const correoOriginal = correoInput.dataset.correoOriginal.trim();
    const rolOriginal = rolSelect.dataset.rolOriginal;

    const mostrarErrorCliente = (mensaje) => {
        const anterior = document.getElementById('error-cliente');
        if (anterior) anterior.remove();

        const errorDiv = document.createElement('div');
        errorDiv.id = 'error-cliente';
        errorDiv.className = 'mensaje-error';

        const parrafo = document.createElement('p');
        parrafo.textContent = mensaje;

        errorDiv.appendChild(parrafo);

        const container = document.querySelector('.container');
        if (errorServidorDiv) {
            container.insertBefore(errorDiv, errorServidorDiv);
        } else {
            container.appendChild(errorDiv);
        }
    };

    if (form) {
        form.addEventListener('submit', (e) => {
            const correoActual = correoInput.value.trim();
            const rolActual = rolSelect.value;

            // 1. Validar cambios
            if (correoActual === correoOriginal && rolActual === rolOriginal) {
                e.preventDefault();
                mostrarErrorCliente('No se realizaron cambios, por favor modifica algún dato antes de guardar.');
                return;
            }

            // 2. Validar formato del correo
            const regexCorreo = /^[a-zA-Z0-9._%+-]+@fundacionloyola\.es$/;
            if (!regexCorreo.test(correoActual)) {
                e.preventDefault();
                mostrarErrorCliente('El correo debe tener el formato usuario@fundacionloyola.es.');
                return;
            }

            // 3. Limpiar errores anteriores si todo es válido
            const existente = document.getElementById('error-cliente');
            if (existente) existente.remove();
        });
    }

    // Mostrar errores del servidor
    if (errorServidorDiv) {
        const codigoError = errorServidorDiv.dataset.error;
        let mensajeServidor = '';

        switch (codigoError) {
            case 'correo-duplicado':
                mensajeServidor = 'El correo electrónico ya está en uso por otro usuario.';
                break;
            case 'correo-no-valido':
                mensajeServidor = 'El correo debe tener el formato usuario@fundacionloyola.es.';
                break;
            case 'campos-obligatorios':
                mensajeServidor = 'Todos los campos son obligatorios.';
                break;
        }

        if (mensajeServidor) {
            mostrarErrorCliente(mensajeServidor);
            errorServidorDiv.remove();
        }
    }

    const botonesBorrar = document.querySelectorAll('.btn-borrar-usuario');
    botonesBorrar.forEach(boton => {
        boton.addEventListener('click', (e) => {
            const correo = boton.dataset.correo;
            const mensaje = `¿Estás seguro de que quieres borrar al usuario con correo ${correo}?`;
            if (!confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });
});