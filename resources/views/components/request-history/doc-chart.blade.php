@props(['hoaStats' => [], 'remStats' => []])

<div class="w-full rounded-lg bg-white p-4 shadow-sm" id="doc-chart-container" style="min-height: 400px;">
    @if (count($hoaStats) > 0 || count($remStats) > 0)
        <canvas id="docRequestsChart"></canvas>
    @else
        <div class="flex h-full items-center justify-center text-gray-500">
            <p>No document request data available.</p>
        </div>
    @endif
</div>

@if (count($hoaStats) > 0 || count($remStats) > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            const hoaStats = @json($hoaStats),
                remStats = @json($remStats);
            const labels = {
                'Certificate of Incorporation': 'COI',
                'Certificate of Amended By-Laws': 'Cof Amended By-Laws',
                'Certificate of Amended Articles of Incorporation': 'Cof Amended AoI',
                'Articles of Incorporation': 'AoI',
                'By-Laws': 'By-Laws',
                'Annual Report': 'Annual Report',
                'Election Report': 'Election Report',
                'Masterlist': 'Masterlist',
                'General Information Sheet': 'GIS',
                'Certificate of Registration and License to Sell (CRLS)': 'CRLS',
                'Notarized Fact Sheet / Sales Report': 'Fact Sheet',
                'Development Permit': 'Dev Permit',
                'Verified Survey Returns (VSR)': 'VSR',
                'Subdivision Development Plan (SDP)': 'SDP'
            };
            const colors = {
                HOA: {
                    border: 'rgba(59,130,246,1)',
                    bg: 'rgba(59,130,246,0.6)'
                },
                REM: {
                    border: 'rgba(34,197,94,1)',
                    bg: 'rgba(34,197,94,0.6)'
                }
            };
            const c = (type) => type === 'HOA' ? colors.HOA : colors.REM;
            const d = (type) => type === 'HOA' ? hoaStats : remStats;

            function createChart(type) {
                const docStats = d(type),
                    clr = c(type);
                const keys = Object.keys(docStats).map(k => labels[k] || k),
                    vals = Object.values(docStats);
                if (!keys.length) return null;
                const ctx = document.getElementById('docRequestsChart').getContext('2d');
                if (window.chartInstance) window.chartInstance.destroy();
                window.chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: keys,
                        datasets: [{
                            label: 'Number of Requests',
                            data: vals,
                            backgroundColor: clr.bg,
                            borderColor: clr.border,
                            borderWidth: 1,
                            borderRadius: 4,
                            barThickness: 'flex',
                            maxBarThickness: 50
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    maxRotation: 45,
                                    minRotation: 0
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
                return window.chartInstance;
            }

            createChart('HOA');

            const btnHoa = document.getElementById('btn-hoa'),
                btnRem = document.getElementById('btn-rem'),
                chartTitle = document.getElementById('chart-title');
            if (btnHoa && btnRem) {
                btnHoa.addEventListener('click', function() {
                    btnHoa.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                    btnHoa.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
                    btnRem.classList.remove('bg-green-600', 'text-white', 'hover:bg-green-700');
                    btnRem.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                    if (chartTitle) chartTitle.textContent = 'HOA Document Requests';
                    createChart('HOA');
                });
                btnRem.addEventListener('click', function() {
                    btnRem.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                    btnRem.classList.add('bg-green-600', 'text-white', 'hover:bg-green-700');
                    btnHoa.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
                    btnHoa.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                    if (chartTitle) chartTitle.textContent = 'REM Document Requests';
                    createChart('REM');
                });
            }
        })();
    </script>
@endif
