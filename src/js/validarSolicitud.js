document.addEventListener('DOMContentLoaded', () => {
    const fechaInicio = document.getElementById('fecha_inicio_ausencia');
    const fechaFin = document.getElementById('fecha_fin_ausencia');
    const grupoHoras = document.getElementById('grupoHoras');

    //Establecemos las fechas por defecto
    const hoy = new Date();
    hoy.setDate(hoy.getDate() + 1);
    const maniana = hoy.toISOString().split('T')[0];
    fechaInicio.value = maniana;
    fechaFin.value = maniana;

    //Mostramos horas si las fechas de inicio y fin de la ausencia son el mismo día
    function mostrarHoras() {
        if (fechaInicio.value && fechaFin.value && fechaInicio.value === fechaFin.value) {
            grupoHoras.style.display = 'block';
        } else {
            grupoHoras.style.display = 'none';
        }
    }

    function mostrarErrorCliente(mensaje) {
        const anterior = document.getElementById('error-cliente');
        if (anterior) anterior.remove();

        const errorDiv = document.createElement('div');
        errorDiv.id = 'error-cliente';
        errorDiv.className = 'mensaje-error';

        const parrafo = document.createElement('p');
        parrafo.textContent = mensaje;

        errorDiv.appendChild(parrafo);

        const container = document.querySelector('.container');
        const errorServidorDiv = document.getElementById('error-server');
        if (errorServidorDiv) {
            container.insertBefore(errorDiv, errorServidorDiv);
        } else {
            container.appendChild(errorDiv);
        }
    }

    function esFinDeSemana(fechaStr) {
        const fecha = new Date(fechaStr);
        const diaSemana = fecha.getDay();
        return diaSemana === 0 || diaSemana === 6;
    }

    function validarDiasLaborables(e) {
        const fechaInicioVal = fechaInicio.value;
        const fechaFinVal = fechaFin.value;

        if (esFinDeSemana(fechaInicioVal)) {
            e.preventDefault();
            mostrarErrorCliente('La fecha de inicio no puede ser en fin de semana.');
            return false;
        }

        if (esFinDeSemana(fechaFinVal)) {
            e.preventDefault();
            mostrarErrorCliente('La fecha de finalización no puede ser en fin de semana.');
            return false;
        }

        const anterior = document.getElementById('error-cliente');
        if (anterior) anterior.remove();

        return true;
    }

    function validarHorasSeleccionadas(e) {
        if (fechaInicio.value === fechaFin.value) {
            const checkboxes = document.querySelectorAll('input[name="horas[]"]:checked');
            if (checkboxes.length === 0) {
                e.preventDefault();
                mostrarErrorCliente('Debes seleccionar al menos una hora si la ausencia es de un solo día.');
                return false;
            }
        }
        return true;
    }

    fechaInicio.addEventListener('change', () => {
        if (fechaFin.value < fechaInicio.value) {
            fechaFin.value = fechaInicio.value;
        }
        mostrarHoras();
    });

    fechaFin.addEventListener('change', () => {
        if (fechaFin.value < fechaInicio.value) {
            fechaFin.value = fechaInicio.value;
        }
        mostrarHoras();
    });

    //Mostrar horas después de que se establecen las fechas por defecto
    mostrarHoras();

    const form = document.querySelector('.form-solicitud');
    if (form) {
        form.addEventListener('submit', (e) => {
            const esValidoDias = validarDiasLaborables(e);
            const esValidoHoras = validarHorasSeleccionadas(e);
            if (!esValidoDias || !esValidoHoras) return;
        });
    }

        //Mostrar errores del servidor
        const errorServidorDiv = document.getElementById('error-server');
        if (errorServidorDiv) {
            const codigoError = errorServidorDiv.dataset.error;
            let mensajeServidor = '';

            switch (codigoError) {
                case 'faltan-campos':
                    mensajeServidor = 'Faltan campos obligatorios en el formulario.';
                    break;
                case 'no-hay-curso':
                    mensajeServidor = 'Actualmente no hay ningún curso activo para realizar solicitudes.';
                    break;
                case 'fechas-fuera-curso':
                    mensajeServidor = 'Las fechas seleccionadas están fuera del periodo del curso activo.';
                    break;
                case 'fechas-anteriores-manana':
                    mensajeServidor = 'Las fechas deben ser a partir de mañana.';
                    break;
                case 'solapamiento-fechas':
                    mensajeServidor = 'Ya tienes una solicitud que se solapa con las fechas indicadas.';
                    break;
                case 'fallo-insercion':
                    mensajeServidor = 'Ha ocurrido un error al guardar la solicitud. Intenta de nuevo más tarde.';
                    break;
                default:
                    mensajeServidor = 'Se ha producido un error desconocido.';
            }

            if (mensajeServidor) {
                mostrarErrorCliente(mensajeServidor);
                errorServidorDiv.remove(); // Limpia el marcador
            }
        }
});