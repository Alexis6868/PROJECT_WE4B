import { Component, OnInit, AfterViewInit, HostListener } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { NavbarComponent } from './components/navbar/navbar.component';
import { FooterComponent } from './components/footer/footer.component';
import { ApiService } from './services/api.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, NavbarComponent, FooterComponent],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent implements OnInit, AfterViewInit {
  private orb!: HTMLElement;
  private halfSize = 0;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.apiService.getStatus().subscribe({
      next: (data: any) => console.log('API Status:', data),
      error: (error: any) => console.error('Error fetching API status:', error)
    });
  }

  ngAfterViewInit(): void {
    this.orb = document.querySelector<HTMLElement>('.app-bg__orb--1')!;
    this.calcHalf();
  }

  @HostListener('window:resize')
  calcHalf(): void {
    this.halfSize = window.innerWidth * 0.35;
  }

  @HostListener('document:mousemove', ['$event'])
  onMouseMove(e: MouseEvent): void {
    this.orb.style.transform = `translate(${e.clientX - this.halfSize}px, ${e.clientY - this.halfSize}px)`;
  }
}