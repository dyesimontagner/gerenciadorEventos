





document.getElementById('formEvento').addEventListener('submit', function(event) {
    event.preventDefault(); // Impede o envio tradicional do formulário

    let formData = new FormData(this);

    fetch('php/processa_evento.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        document.getElementById('resultado').innerHTML = result; // Exibe o resultado da operação
    })
    .catch(error => {
        console.error('Erro ao cadastrar evento:', error);
    });
});






