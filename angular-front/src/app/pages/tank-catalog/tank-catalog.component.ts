import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../services/api.service';
import { SearchBarComponent } from '../../components/search-bar/search-bar.component';
import { TankCardComponent } from '../../components/tank-card/tank-card.component';

@Component({
  selector: 'app-tank-catalog',
  standalone: true,
  imports: [CommonModule, SearchBarComponent, TankCardComponent],
  templateUrl: './tank-catalog.component.html',
  styleUrl: './tank-catalog.component.css'
})
export class TankCatalogComponent implements OnInit {
  allTanks: any[] = [];
  filteredTanks: any[] = [];
  pagedTanks: any[] = [];
  availableCountries: string[] = [];
  errorMessage: string = '';

  currentSearchTerm: string = '';
  currentSort: string = 'nom';
  currentPays: string = '';

  readonly pageSize = 20;
  currentPage = 1;
  totalPages = 0;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.apiService.getTanks().subscribe({
      next: (data) => {
        this.allTanks = data;
        this.availableCountries = [...new Set(
          data.map((t: any) => t.pays).filter((p: any) => !!p)
        )].sort();
        this.applyFilterAndSort();
      },
      error: (err) => {
        console.error('Erreur chargement catalogue :', err);
        this.errorMessage = 'Impossible de charger le catalogue. Vérifiez que le serveur est démarré.';
      }
    });
  }

  filterCatalog(searchTerm: string) {
    this.currentSearchTerm = searchTerm;
    this.applyFilterAndSort();
  }

  filterByPays(pays: string) {
    this.currentPays = pays;
    this.applyFilterAndSort();
  }

  sortCatalog(sortType: string) {
    this.currentSort = sortType;
    this.applyFilterAndSort();
  }

  goToPage(page: number) {
    if (page < 1 || page > this.totalPages) return;
    this.currentPage = page;
    this.updatePage();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  get pageStart(): number { return (this.currentPage - 1) * this.pageSize + 1; }
  get pageEnd(): number { return Math.min(this.currentPage * this.pageSize, this.filteredTanks.length); }

  get pageNumbers(): (number | '…')[] {
    if (this.totalPages <= 7) {
      return Array.from({ length: this.totalPages }, (_, i) => i + 1);
    }
    const pages: (number | '…')[] = [1];
    if (this.currentPage > 3) pages.push('…');
    for (let i = Math.max(2, this.currentPage - 1); i <= Math.min(this.totalPages - 1, this.currentPage + 1); i++) {
      pages.push(i);
    }
    if (this.currentPage < this.totalPages - 2) pages.push('…');
    pages.push(this.totalPages);
    return pages;
  }

  private applyFilterAndSort() {
    let temp = this.allTanks.filter(tank =>
      tank.nom.toLowerCase().includes(this.currentSearchTerm.toLowerCase()) &&
      (!this.currentPays || tank.pays === this.currentPays)
    );

    if (this.currentSort === 'nom') {
      temp.sort((a, b) => a.nom.localeCompare(b.nom));
    } else if (this.currentSort === 'masseAsc') {
      temp.sort((a, b) => a.masse - b.masse);
    } else if (this.currentSort === 'masseDesc') {
      temp.sort((a, b) => b.masse - a.masse);
    }

    this.filteredTanks = temp;
    this.currentPage = 1;
    this.totalPages = Math.ceil(temp.length / this.pageSize);
    this.updatePage();
  }

  private updatePage() {
    const start = (this.currentPage - 1) * this.pageSize;
    this.pagedTanks = this.filteredTanks.slice(start, start + this.pageSize);
  }
}