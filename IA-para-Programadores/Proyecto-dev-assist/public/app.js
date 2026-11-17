async function analizarCodigo() {
    const promptInput = document.getElementById('prompt').value;
    const codeInput = document.getElementById('code').value;
    const resultOutput = document.getElementById('resultOutput');
    const loading = document.getElementById('loading');

    if (!promptInput.trim() && !codeInput.trim()) {
        alert('Por favor, ingresa una consulta o un fragmento de código.');
        return;
    }

    // Mostrar estado de carga
    loading.style.display = 'block';
    resultOutput.textContent = 'Procesando solicitud...';

    try {
        const response = await fetch('/api/analyze', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                prompt: promptInput,
                code: codeInput
            })
        });

        const data = await response.json();

        if (data.success) {
            resultOutput.textContent = data.analysis;
        } else {
            resultOutput.textContent = 'Error: ' + data.error;
        }

    } catch (error) {
        console.error('Error de red:', error);
        resultOutput.textContent = 'Ocurrió un error de conexión con el servidor backend.';
    } finally {
        loading.style.display = 'none';
    }
}
