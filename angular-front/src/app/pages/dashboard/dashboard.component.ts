import { Component, OnInit, OnDestroy, AfterViewInit, ElementRef, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.css'
})
export class DashboardComponent implements OnInit, OnDestroy {
  @ViewChild('chartTypes')    chartTypesRef!:    ElementRef<HTMLCanvasElement>;
  @ViewChild('chartMonths')   chartMonthsRef!:   ElementRef<HTMLCanvasElement>;
  @ViewChild('chartLogins')   chartLoginsRef!:   ElementRef<HTMLCanvasElement>;
  @ViewChild('chartActions')  chartActionsRef!:  ElementRef<HTMLCanvasElement>;

  loading = true;
  kpi: any = null;
  charts: any = null;
  logs: any[] = [];
  filteredLogs: any[] = [];

  filterText   = '';
  filterAction = '';
  sortField    = 'createdAt';
  sortDir: 'asc' | 'desc' = 'desc';

  private chartInstances: Chart[] = [];

  readonly actionOptions = [
    { value: '',               label: 'Toutes les actions' },
    { value: 'LOGIN',          label: 'Connexion' },
    { value: 'UPLOAD_FILE',    label: 'Upload fichier' },
    { value: 'DELETE_FILE',    label: 'Suppression fichier' },
    { value: 'UPDATE_VEHICLE', label: 'Modification blindé' },
    { value: 'DELETE_VEHICLE', label: 'Suppression blindé' },
  ];

  constructor(private api: ApiService) {}

  ngOnInit(): void { this.loadStats(); }

  ngOnDestroy(): void {
    this.chartInstances.forEach(c => c.destroy());
  }

  loadStats(): void {
    this.loading = true;
    this.chartInstances.forEach(c => c.destroy());
    this.chartInstances = [];

    this.api.getStats().subscribe({
      next: (data) => {
        this.kpi    = data.kpi;
        this.charts = data.charts;
        this.logs   = data.logs;
        this.applyFilter();
        this.loading = false;
        setTimeout(() => this.initCharts(), 50);
      },
      error: () => { this.loading = false; }
    });
  }

  // ── Filtres & tri des logs ────────────────────────────────────────────
  applyFilter(): void {
    const q = this.filterText.toLowerCase();
    this.filteredLogs = this.logs.filter(l => {
      const matchText   = !q || (l.userEmail ?? '').toLowerCase().includes(q) || l.action.toLowerCase().includes(q);
      const matchAction = !this.filterAction || l.action === this.filterAction;
      return matchText && matchAction;
    });
    this.doSort();
  }

  sortBy(field: string): void {
    this.sortDir  = this.sortField === field && this.sortDir === 'desc' ? 'asc' : 'desc';
    this.sortField = field;
    this.doSort();
  }

  private doSort(): void {
    this.filteredLogs = [...this.filteredLogs].sort((a, b) => {
      const va = a[this.sortField] ?? '';
      const vb = b[this.sortField] ?? '';
      return this.sortDir === 'asc' ? (va > vb ? 1 : -1) : (va < vb ? 1 : -1);
    });
  }

  sortIcon(f: string): string {
    if (this.sortField !== f) return '↕';
    return this.sortDir === 'asc' ? '↑' : '↓';
  }

  actionBadgeClass(action: string): string {
    const map: Record<string, string> = {
      LOGIN:          'badge--blue',
      UPLOAD_FILE:    'badge--green',
      DELETE_FILE:    'badge--red',
      UPDATE_VEHICLE: 'badge--orange',
      DELETE_VEHICLE: 'badge--red',
    };
    return map[action] ?? 'badge--gray';
  }

  formatBytes(b: number): string {
    if (b < 1024)          return b + ' o';
    if (b < 1048576)       return (b / 1024).toFixed(1) + ' Ko';
    return (b / 1048576).toFixed(2) + ' Mo';
  }

  // ── Charts Chart.js ───────────────────────────────────────────────────
  private initCharts(): void {
    if (!this.charts) return;
    this.buildTypesDoughnut();
    this.buildMonthsLine();
    this.buildLoginsBar();
    this.buildActionsBar();
  }

  private buildTypesDoughnut(): void {
    const el = this.chartTypesRef?.nativeElement;
    if (!el) return;
    const labels = this.charts.vehiclesByType.map((v: any) => v.type);
    const data   = this.charts.vehiclesByType.map((v: any) => +v.cnt);
    const c = new Chart(el, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{
          data,
          backgroundColor: ['#ff7a00','#3b82f6','#22c55e','#a855f7','#f87171','#fbbf24'],
          borderWidth: 0,
        }]
      },
      options: {
        layout: { padding: 0 },
        plugins: {
          legend: {
            position: 'right',
            labels: { color: '#aaa', font: { size: 11 }, boxWidth: 12, padding: 10 },
          }
        },
        cutout: '60%',
      }
    });
    this.chartInstances.push(c);
  }

  private buildMonthsLine(): void {
    const el = this.chartMonthsRef?.nativeElement;
    if (!el) return;
    const labels  = this.charts.reservationsByMonth.map((r: any) => r.label);
    const counts  = this.charts.reservationsByMonth.map((r: any) => +r.cnt);
    const revenue = this.charts.reservationsByMonth.map((r: any) => +r.revenue);
    const c = new Chart(el, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'Réservations',
            data: counts,
            borderColor: '#ff7a00',
            backgroundColor: 'rgba(255,122,0,.1)',
            tension: 0.4, fill: true, pointRadius: 4,
          },
          {
            label: 'Revenus (€)',
            data: revenue,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,.08)',
            tension: 0.4, fill: true, pointRadius: 4,
            yAxisID: 'y2',
          }
        ]
      },
      options: {
        scales: {
          x:  { ticks: { color: '#888' }, grid: { color: 'rgba(255,255,255,.05)' } },
          y:  { ticks: { color: '#888' }, grid: { color: 'rgba(255,255,255,.05)' }, beginAtZero: true },
          y2: { ticks: { color: '#3b82f6' }, position: 'right', beginAtZero: true, grid: { drawOnChartArea: false } },
        },
        plugins: { legend: { labels: { color: '#aaa', font: { size: 11 } } } },
      }
    });
    this.chartInstances.push(c);
  }

  private buildLoginsBar(): void {
    const el = this.chartLoginsRef?.nativeElement;
    if (!el) return;
    const labels = this.charts.loginsPerDay.map((d: any) => d.id);
    const data   = this.charts.loginsPerDay.map((d: any) => +d.count);
    const c = new Chart(el, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Connexions / jour',
          data,
          backgroundColor: 'rgba(59,130,246,.6)',
          borderColor: '#3b82f6',
          borderWidth: 1,
          borderRadius: 4,
        }]
      },
      options: {
        scales: {
          x: { ticks: { color: '#888' }, grid: { color: 'rgba(255,255,255,.05)' } },
          y: { ticks: { color: '#888', precision: 0 } as any, grid: { color: 'rgba(255,255,255,.05)' }, beginAtZero: true },
        },
        plugins: { legend: { labels: { color: '#aaa' } } },
      }
    });
    this.chartInstances.push(c);
  }

  private buildActionsBar(): void {
    const el = this.chartActionsRef?.nativeElement;
    if (!el) return;
    const labels = this.charts.actionsBreakdown.map((a: any) => a.id);
    const data   = this.charts.actionsBreakdown.map((a: any) => +a.count);
    const colors: Record<string, string> = {
      LOGIN:          'rgba(59,130,246,.7)',
      UPLOAD_FILE:    'rgba(34,197,94,.7)',
      DELETE_FILE:    'rgba(229,57,53,.7)',
      UPDATE_VEHICLE: 'rgba(255,122,0,.7)',
      DELETE_VEHICLE: 'rgba(239,68,68,.7)',
    };
    const c = new Chart(el, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Total actions',
          data,
          backgroundColor: labels.map((l: string) => colors[l] ?? 'rgba(150,150,150,.5)'),
          borderRadius: 4,
          borderWidth: 0,
        }]
      },
      options: {
        indexAxis: 'y',
        scales: {
          x: { ticks: { color: '#888', precision: 0 } as any, grid: { color: 'rgba(255,255,255,.05)' }, beginAtZero: true },
          y: { ticks: { color: '#ccc' }, grid: { display: false } },
        },
        plugins: { legend: { display: false } },
      }
    });
    this.chartInstances.push(c);
  }
}
