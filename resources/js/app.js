import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

function applyChartTheme(isDark) {
    const textColor = isDark ? '#94A3B8' : '#6B7280';
    const gridColor = isDark ? 'rgba(148, 163, 184, 0.14)' : '#F1F2F5';
    const borderSurface = isDark ? '#151C2C' : '#FFFFFF';
    const mutedSlice = isDark ? '#334155' : '#CBD5E1';

    Chart.defaults.color = textColor;
    Chart.defaults.borderColor = gridColor;

    Object.values(Chart.instances).forEach((chart) => {
        if (chart.options?.plugins?.legend?.labels) {
            chart.options.plugins.legend.labels.color = textColor;
        }

        if (chart.options?.scales) {
            Object.values(chart.options.scales).forEach((scale) => {
                if (scale.ticks) {
                    scale.ticks.color = textColor;
                }
                if (scale.grid && scale.grid.display !== false) {
                    scale.grid.color = gridColor;
                }
            });
        }

        if (chart.data?.datasets) {
            chart.data.datasets.forEach((dataset) => {
                if (dataset.borderColor === '#FFFFFF' || dataset.borderColor === '#151C2C') {
                    dataset.borderColor = borderSurface;
                }
                if (Array.isArray(dataset.backgroundColor)) {
                    dataset.backgroundColor = dataset.backgroundColor.map((color) =>
                        color === '#CBD5E1' || color === '#334155' ? mutedSlice : color
                    );
                }
            });
        }

        chart.update('none');
    });
}

applyChartTheme(document.documentElement.classList.contains('dark'));

window.addEventListener('theme-changed', (event) => {
    applyChartTheme(Boolean(event.detail?.dark));
});

document.addEventListener('DOMContentLoaded', () => {
    requestAnimationFrame(() => {
        applyChartTheme(document.documentElement.classList.contains('dark'));
    });
});

Alpine.start();
