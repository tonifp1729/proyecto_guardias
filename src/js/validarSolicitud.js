const fechaInicio = document.getElementById('fecha_inicio_ausencia');
const fechaFin = document.getElementById('fecha_fin_ausencia');
const horasGroup = document.getElementById('horas-group');

const today = new Date();
today.setDate(today.getDate() + 1);
const tomorrowStr = today.toISOString().split('T')[0];
fechaInicio.value = tomorrowStr;
fechaFin.value = tomorrowStr;

function toggleHoras() {
    if (fechaInicio.value && fechaFin.value && fechaInicio.value === fechaFin.value) {
        horasGroup.style.display = 'block';
    } else {
        horasGroup.style.display = 'none';
    }
}

fechaInicio.addEventListener('change', toggleHoras);
fechaFin.addEventListener('change', toggleHoras);

toggleHoras();