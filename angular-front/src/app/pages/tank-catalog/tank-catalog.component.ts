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
  availableCountries: string[] = [];
  errorMessage: string = '';

  currentSearchTerm: string = '';
  currentSort: string = 'nom';
  currentPays: string = '';

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


  private applyFilterAndSort() {

    let temp = this.allTanks.filter(tank =>
      tank.nom.toLowerCase().includes(this.currentSearchTerm.toLowerCase()) &&
      (!this.currentPays || tank.pays === this.currentPays)
    );

    if (this.currentSort === 'nom') {
      temp.sort((a, b) => a.nom.localeCompare(b.nom));
    } else if (this.currentSort === 'masseAsc') {
      temp.sort((a, b) => a.masse - b.masse); // Du plus léger au plus lourd
    } else if (this.currentSort === 'masseDesc') {
      temp.sort((a, b) => b.masse - a.masse); // Du plus lourd au plus léger
    }


    this.filteredTanks = temp;
  }
}