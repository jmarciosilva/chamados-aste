import { Chart } from 'chart.js/auto';

// ==========================
// Gráfico de Linha
// ==========================
const lineCtx = document.getElementById('lineChart');

if (lineCtx) {
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: ['01 Jan', '02 Jan', '03 Jan', '04 Jan', '05 Jan', '06 Jan', '07 Jan'],
            datasets: [
                {
                    label: 'Abertos',
                    data: [45, 52, 38, 65, 54, 32, 18],
                    tension: 0.4,
                },
                {
                    label: 'Resolvidos',
                    data: [40, 48, 36, 50, 49, 28, 15],
                    tension: 0.4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

// ==========================
// Gráfico de Pizza
// ==========================
const pieCtx = document.getElementById('pieChart');

if (pieCtx) {
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Vendas', 'Logística', 'Administrativo', 'RH', 'Marketing'],
            datasets: [{
                data: [32, 21, 18, 15, 14],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}
