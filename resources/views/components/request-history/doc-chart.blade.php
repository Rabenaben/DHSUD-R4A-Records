@props(['hoaStats' => [], 'remStats' => [], 'hoaCertified' => 0, 'hoaNotCertified' => 0, 'remCertified' => 0, 'remNotCertified' => 0])

<div class="w-full rounded-lg bg-white p-4 shadow-sm" id="doc-chart-container" style="min-height: 400px;">
    @if (count($hoaStats) > 0 || count($remStats) > 0)
        <div class="mb-3 flex justify-end space-x-3" id="cert-badges">
            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                <svg class="mr-1.5 h-3 w-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Certified: <span id="certified-count">{{ $hoaCertified }}</span>
            </span>
            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">
                <svg class="mr-1.5 h-3 w-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                Not Certified: <span id="not-certified-count">{{ $hoaNotCertified }}</span>
            </span>
        </div>
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
            // Expose stats globally for request-history.js
            window.chartHoaStats = @json($hoaStats);
            window.chartRemStats = @json($remStats);
            window.chartHoaCertified = @json($hoaCertified);
            window.chartHoaNotCertified = @json($hoaNotCertified);
            window.chartRemCertified = @json($remCertified);
            window.chartRemNotCertified = @json($remNotCertified);
            
            // Short labels mapping - exposed globally for reuse
            window.chartLabels = {
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
            
            const colors = { HOA: { border: 'rgba(59,130,246,1)', bg: 'rgba(59,130,246,0.6)' }, REM: { border: 'rgba(34,197,94,1)', bg: 'rgba(34,197,94,0.6)' } };
            const getStats = (type) => type === 'HOA' ? window.chartHoaStats : window.chartRemStats;
            const getColor = (type) => colors[type];

            // Simplified badge update using global data from request-history.js
            window.updateChartBadges = function(type) {
                const certified = document.getElementById('certified-count');
                const notCertified = document.getElementById('not-certified-count');
                if (!certified || !notCertified) return;
                
                const data = type === 'HOA' 
                    ? { cert: window.hoaCertifiedData, notCert: window.hoaNotCertifiedData }
                    : { cert: window.remCertifiedData, notCert: window.remNotCertifiedData };
                
                certified.textContent = data.cert || (type === 'HOA' ? window.chartHoaCertified : window.chartRemCertified);
                notCertified.textContent = data.notCert || (type === 'HOA' ? window.chartHoaNotCertified : window.chartRemNotCertified);
            };

            // Simplified chart creation
            window.createChart = function(type) {
                const stats = getStats(type), clr = getColor(type);
                const keys = Object.keys(stats).map(k => window.chartLabels[k] || k);
                const vals = Object.values(stats);
                if (!keys.length) return null;
                
                const ctx = document.getElementById('docRequestsChart').getContext('2d');
                if (window.chartInstance) window.chartInstance.destroy();
                
                window.chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: { labels: keys, datasets: [{ label: 'Number of Requests', data: vals, backgroundColor: clr.bg, borderColor: clr.border, borderWidth: 1, borderRadius: 4, barThickness: 'flex', maxBarThickness: 50 }] },
                    options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: true, position: 'top', labels: { usePointStyle: true, padding: 20 } }, tooltip: { backgroundColor: 'rgba(0,0,0,0.8)', titleColor: '#fff', bodyColor: '#fff', padding: 12, cornerRadius: 8, displayColors: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 12 } }, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { ticks: { font: { size: 11 }, maxRotation: 45, minRotation: 0 }, grid: { display: false } } } }
                });
                return window.chartInstance;
            };

            // Initialize
            createChart('HOA');
            window.updateChartBadges('HOA');

            // Combined button handler
            const btnHoa = document.getElementById('btn-hoa'), btnRem = document.getElementById('btn-rem'), chartTitle = document.getElementById('chart-title');
            if (btnHoa && btnRem) {
                const switchChart = (type) => {
                    const isHoa = type === 'HOA';
                    btnHoa.className = `px-4 py-2 rounded-md transition-colors ${isHoa ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}`;
                    btnRem.className = `px-4 py-2 rounded-md transition-colors ${!isHoa ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}`;
                    if (chartTitle) chartTitle.textContent = `${type} Document Requests`;
                    createChart(type);
                    window.updateChartBadges(type);
                    if (typeof currentChartType !== 'undefined') currentChartType = type;
                };
                btnHoa.addEventListener('click', () => switchChart('HOA'));
                btnRem.addEventListener('click', () => switchChart('REM'));
            }
        })();
    </script>
@endif
