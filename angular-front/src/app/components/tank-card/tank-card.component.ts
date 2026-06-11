import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router'; 

@Component({
  selector: 'app-tank-card',
  standalone: true,
  imports: [
    CommonModule, 
    RouterLink // 👈 2. Déclare-le dans les imports du composant
  ], 
  templateUrl: './tank-card.component.html',
  styleUrl: './tank-card.component.css'
})
export class TankCardComponent {
  @Input() tank: any;
}