document.addEventListener('DOMContentLoaded', function () {
    const botonesBorrar = document.querySelectorAll('.btn-borrar-usuario');

    botonesBorrar.forEach(boton => {
        boton.addEventListener('click', function (e) {
            const correo = this.getAttribute('data-correo');
            if (!confirm(`¿Estás seguro de que quieres borrar al usuario con correo ${correo}?`)) {
                e.preventDefault();
            }
        });
    });
});
