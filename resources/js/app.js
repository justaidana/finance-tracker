import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

// Expose globally so inline Blade scripts can access them
window.Alpine = Alpine;
window.Chart  = Chart;

// ── Dark mode: apply saved preference BEFORE render ──
(function() {
    const saved = localStorage.getItem('theme');
    if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    }
})();

// ── Chart.js global defaults — crisp, high-DPI, modern ──
Chart.defaults.devicePixelRatio   = window.devicePixelRatio || 2;
Chart.defaults.font.family        = "'Inter', sans-serif";
Chart.defaults.font.size          = 12;
Chart.defaults.font.weight        = '500';
Chart.defaults.color              = '#6b7280';
Chart.defaults.plugins.legend.labels.boxWidth      = 10;
Chart.defaults.plugins.legend.labels.boxHeight     = 10;
Chart.defaults.plugins.legend.labels.padding       = 14;
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.tooltip.backgroundColor     = '#111827';
Chart.defaults.plugins.tooltip.titleColor          = '#f9fafb';
Chart.defaults.plugins.tooltip.bodyColor           = '#d1d5db';
Chart.defaults.plugins.tooltip.padding             = 10;
Chart.defaults.plugins.tooltip.cornerRadius        = 10;
Chart.defaults.plugins.tooltip.displayColors       = true;
Chart.defaults.animation.duration                  = 600;
Chart.defaults.animation.easing                    = 'easeInOutQuart';

Alpine.start();

// Signal that Chart and Alpine are ready for inline scripts
window.dispatchEvent(new CustomEvent('fintrack:ready'));
