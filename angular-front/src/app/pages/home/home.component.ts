import { Component, OnInit } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { HeroSectionComponent } from '../../components/hero-section/hero-section.component';
import { RevealDirective } from '../../directives/reveal.directive';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, HeroSectionComponent, RevealDirective],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent implements OnInit {
  tankCount = 0;
  countryCount = 0;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.apiService.getTanks().subscribe({
      next: (data: any[]) => {
        this.tankCount = data.length;
        this.countryCount = new Set(data.map((t: any) => t.pays).filter(Boolean)).size;
      }
    });
  }
}
