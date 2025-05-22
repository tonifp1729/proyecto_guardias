document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.form-curso');
    const fechaInicioInput = document.getElementById('fecha_inicio');
    const fechaFinInput = document.getElementById('fecha_fin');
    const errorServidorDiv = document.getElementById('error-server');

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
            const fechaInicio = fechaInicioInput.value;
            const fechaFin = fechaFinInput.value;
            const hoy = new Date().toISOString().split('T')[0];

            if (!fechaInicio || !fechaFin) {
                e.preventDefault();
                mostrarErrorCliente('Debes completar ambas fechas.');
                return;
            }

            if (fechaInicio < hoy) {
                e.preventDefault();
                mostrarErrorCliente('La fecha de inicio no puede ser anterior a hoy.');
                return;
            }

            if (fechaFin <= fechaInicio) {
                e.preventDefault();
                mostrarErrorCliente('La fecha de finalización debe ser posterior a la de inicio.');
                return;
            }

            if (fechaInicio === fechaInicioOriginal && fechaFin === fechaFinOriginal) {
                e.preventDefault();
                mostrarErrorCliente('No se realizaron cambios en las fechas, no se procederá a la modificación.');
                return;
            }

            const existente = document.getElementById('error-cliente');
            if (existente) existente.remove();
        });
    }

    if (errorServidorDiv) {
        const codigoError = errorServidorDiv.dataset.error;
        let mensajeServidor = '';

        switch (codigoError) {
            case 'fecha-solapada':
                mensajeServidor = 'Ya existe un curso con fechas que se solapan con las seleccionadas. Introduce una fecha válida por favor';
                break;
            case 'fecha-inicio-invalida':
                mensajeServidor = 'La fecha de inicio no puede ser anterior a hoy.';
                break;
            case 'rango-invalido':
                mensajeServidor = 'El año académico no puede empezar y acabar el mismo año.';
                break;
            case 'hay-solicitudes':
                mensajeServidor = 'No se puede modificar la fecha de finalización porque existen solicitudes antes de esa fecha.';
                break;
            case 'no-cambios':
                mensajeServidor = 'No se realizaron cambios en las fechas, no se procederá a la modificación.';
                break;
        }

        if (mensajeServidor) {
            mostrarErrorCliente(mensajeServidor);
            errorServidorDiv.remove();
        }
    }
    
    const enlacesBorrado = document.querySelectorAll('.btn-borrar-curso');

    enlacesBorrado.forEach(enlace => {
        enlace.addEventListener('click', (e) => {
            const anio = enlace.dataset.anio;
            const mensaje = `¿Estás seguro de que quieres borrar el curso ${anio}?`;

            if (!confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });
});
