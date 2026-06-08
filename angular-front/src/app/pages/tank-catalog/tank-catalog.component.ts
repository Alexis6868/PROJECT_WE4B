import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { NgFor, NgIf } from '@angular/common'; 
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-tank-catalog',
  standalone: true,
  imports: [FormsModule, NgFor, NgIf], 
  templateUrl: './tank-catalog.component.html',
  styleUrl: './tank-catalog.component.css'
})
export class TankCatalogComponent implements OnInit {
  allTanks: any[] = [];
  filteredTanks: any[] = [];

  searchQuery: string = '';
  sortBy: string = 'none';

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.apiService.getTanks().subscribe({
      next: (data: any[]) => {
        this.allTanks = data;
        this.filteredTanks = [...this.allTanks];
        this.applyFilterAndSort();
      },
      error: (err) => console.error('Erreur catalogue :', err)
    });
  }

  applyFilterAndSort(): void {
    let result = this.allTanks.filter(tank => 
      tank.name.toLowerCase().includes(this.searchQuery.toLowerCase())
    );

    if (this.sortBy === 'priceAsc') {
      result.sort((a, b) => a.price - b.price);
    } else if (this.sortBy === 'priceDesc') {
      result.sort((a, b) => b.price - a.price);
    }

    this.filteredTanks = result;
  }
}