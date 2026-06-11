import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../services/api.service';
import { SearchBarComponent } from '../../components/search-bar/search-bar.component';
import { TankCardComponent } from '../../components/tank-card/tank-card.component';
@Component({
  selector: 'app-tank-catalog',
  standalone: true,
  imports: [CommonModule, SearchBarComponent, TankCardComponent], // On importe nos petits composants
  templateUrl: './tank-catalog.component.html'
})
export class TankCatalogComponent implements OnInit {
  allTanks: any[] = [];      
  filteredTanks: any[] = []; 

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.apiService.getTanks().subscribe((data) => {
      this.allTanks = data;
      this.filteredTanks = data;
    });
  }


  filterCatalog(searchTerm: string) {
    this.filteredTanks = this.allTanks.filter(tank => 
      tank.nom.toLowerCase().includes(searchTerm.toLowerCase())
    );
  }
}