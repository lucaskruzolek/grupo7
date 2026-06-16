document.addEventListener('DOMContentLoaded', function() {
    const chartEl = document.getElementById('salesChart');
    if (!chartEl) return;

    const ctx = chartEl.getContext('2d');
    let currentChart = null;

    // Paleta de colores premium acorde al tema de la tienda
    const colorPalette = {
        primary: '#7d8c78',       // Verde tierra
        secondary: '#c89d7c',     // Terracota suave
        accent: '#f2d6b5',        // Crema cálido
        darkNeutral: '#4a5347',   // Slate oscuro
        lightNeutral: '#a9b8a6',  // Verde salvia claro
        primaryLight: 'rgba(125, 140, 120, 0.1)'
    };

    // Elementos del DOM
    const typeSelector = document.getElementById('chartTypeSelector');
    const periodSelector = document.getElementById('chartPeriodSelector');

    // Función para renderizar el tipo de gráfico correspondiente
    function renderChart(chartType, period) {
        // Mostrar estado de carga (opacidad)
        chartEl.style.opacity = '0.4';
        
        // Bloquear selectores temporalmente para evitar peticiones concurrentes
        if (typeSelector) typeSelector.disabled = true;
        if (periodSelector) periodSelector.disabled = true;

        fetch(`/admin/dashboard/chart-data?type=${chartType}&period=${period}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(dataSet => {
                // Si ya existe un gráfico activo, destruirlo para limpiar el canvas
                if (currentChart) {
                    currentChart.destroy();
                }

                let config = {};

                // Configurar opciones de Chart.js según el tipo seleccionado
                if (chartType === 'sales') {
                    // Completar estilos del dataset de ventas que retorna del backend
                    if (dataSet.datasets && dataSet.datasets[0]) {
                        dataSet.datasets[0].borderColor = colorPalette.primary;
                        dataSet.datasets[0].backgroundColor = colorPalette.primaryLight;
                        dataSet.datasets[0].borderWidth = 3;
                        dataSet.datasets[0].fill = true;
                        dataSet.datasets[0].tension = 0.3;
                        dataSet.datasets[0].pointBackgroundColor = colorPalette.primary;
                        dataSet.datasets[0].pointBorderColor = '#fff';
                        dataSet.datasets[0].pointHoverRadius = 6;
                    }

                    config = {
                        type: 'line',
                        data: dataSet,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                                    ticks: { font: { family: 'Poppins', size: 11 } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { family: 'Poppins', size: 11 } }
                                }
                            }
                        }
                    };
                } else if (chartType === 'categories') {
                    // Completar colores y bordes para categorías
                    if (dataSet.datasets && dataSet.datasets[0]) {
                        dataSet.datasets[0].backgroundColor = [
                            colorPalette.primary,
                            colorPalette.secondary,
                            colorPalette.accent,
                            colorPalette.darkNeutral,
                            colorPalette.lightNeutral,
                            '#dcdcdc'
                        ];
                        dataSet.datasets[0].borderWidth = 2;
                        dataSet.datasets[0].borderColor = '#fff';
                    }

                    config = {
                        type: 'doughnut',
                        data: dataSet,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 15,
                                        font: { family: 'Poppins', size: 12 }
                                    }
                                }
                            },
                            cutout: '65%'
                        }
                    };
                } else if (chartType === 'products') {
                    // Completar colores y diseño para productos
                    if (dataSet.datasets && dataSet.datasets[0]) {
                        dataSet.datasets[0].backgroundColor = colorPalette.secondary;
                        dataSet.datasets[0].borderRadius = 6;
                        dataSet.datasets[0].borderWidth = 0;
                    }

                    config = {
                        type: 'bar',
                        data: dataSet,
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                                    ticks: { font: { family: 'Poppins', size: 11 } }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { font: { family: 'Poppins', size: 11 } }
                                }
                            }
                        }
                    };
                }

                // Crear nueva instancia
                currentChart = new Chart(ctx, config);
            })
            .catch(error => {
                console.error('Error al cargar los datos del gráfico:', error);
            })
            .finally(() => {
                // Restaurar estado visual de los selectores
                chartEl.style.opacity = '1';
                if (typeSelector) typeSelector.disabled = false;
                if (periodSelector) periodSelector.disabled = false;
            });
    }

    function handleChartUpdate() {
        if (typeSelector && periodSelector) {
            renderChart(typeSelector.value, periodSelector.value);
        }
    }

    if (typeSelector) typeSelector.addEventListener('change', handleChartUpdate);
    if (periodSelector) periodSelector.addEventListener('change', handleChartUpdate);

    // Carga inicial del gráfico por defecto (Sales / Mes)
    handleChartUpdate();
});
