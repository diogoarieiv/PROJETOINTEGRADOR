document.addEventListener('DOMContentLoaded', () => {
    const tipoInputs = document.querySelectorAll('input[name="tipo"]');
    const usuarioContainer = document.getElementById('usuario-container');
    const idContainer = document.getElementById('id-container');

    tipoInputs.forEach(input => {
        input.addEventListener('change', (event) => {
            if (event.target.value === 'admin') {
                usuarioContainer.style.display = 'block';
                idContainer.style.display = 'none';
            } else {
                usuarioContainer.style.display = 'none';
                idContainer.style.display = 'block';
            }
        });
    });

    if (document.querySelector('input[name="tipo"]:checked').value === 'admin') {
        usuarioContainer.style.display = 'block';
        idContainer.style.display = 'none';
    } else {
        usuarioContainer.style.display = 'none';
        idContainer.style.display = 'block';
    }
});
