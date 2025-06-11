<div class="card">
    <div class="card-body">
        <canvas id="graficaBarras"> {{$valores[0]}} ddde</canvas>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', function () {
        const canvas = document.getElementById('graficaBarras');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Ventas mensuales',
                    data: @json($valores),
                    backgroundColor: '#dc3545'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

