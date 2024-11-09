function mostrarCampoBusqueda() {
    // Ocultar ambos campos
    document.getElementById('campo_id').style.display = 'none';
    document.getElementById('campo_dni').style.display = 'none';

    // Mostrar el campo correspondiente
    var seleccionado = document.querySelector('input[name="buscar_por"]:checked').value;
    if (seleccionado === 'n_id') {
        document.getElementById('campo_id').style.display = 'block';
    } else if (seleccionado === 'dni') {
        document.getElementById('campo_dni').style.display = 'block';
    }
}