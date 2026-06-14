import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../services/api.service';
import { SearchBarComponent } from '../../components/search-bar/search-bar.component';
import { TankCardComponent } from '../../components/tank-card/tank-card.component';

@Component({
  selector: 'app-tank-catalog',
  standalone: true,
  imports: [CommonModule, SearchBarComponent, TankCardComponent],
  templateUrl: './tank-catalog.component.html'
})
export class TankCatalogComponent implements OnInit {
  allTanks: any[] = [];      
  filteredTanks: any[] = []; 
  

  currentSearchTerm: string = '';
  currentSort: string = 'nom'; 

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.apiService.getTanks().subscribe((data) => {
      this.allTanks = data;
      this.applyFilterAndSort(); 
    });
  }


  filterCatalog(searchTerm: string) {
    this.currentSearchTerm = searchTerm;
    this.applyFilterAndSort();
  }


  sortCatalog(sortType: string) {
    this.currentSort = sortType;
    this.applyFilterAndSort();
  }


  private applyFilterAndSort() {

    let temp = this.allTanks.filter(tank => 
      tank.nom.toLowerCase().includes(this.currentSearchTerm.toLowerCase())
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