import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-reservation',
  standalone: true,
  templateUrl: './reservation.component.html',
  styleUrls: ['./reservation.component.css'],
  imports: [CommonModule]

})
export class ReservationComponent implements OnInit {
  tank: any = null;
  loading: boolean = true;

  constructor(
    private route: ActivatedRoute, 
    private apiService: ApiService
  ) {}

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    
    if (id) {
      this.apiService.getTankById(id).subscribe({
        next:(data) => {
        this.tank = data;
        this.loading = false;
      },
      error: (error:any) => {
        console.error('Error fetching tank details:', error);
        this.loading = false;
      }
    });
    }
  }
}