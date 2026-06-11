import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-reservation',
  templateUrl: './reservation.component.html'
})
export class ReservationComponent implements OnInit {
  tankId!: string | null;
  tank: any;

  constructor(private route: ActivatedRoute, private apiService: ApiService) {}

  ngOnInit(): void {
    this.tankId = this.route.snapshot.paramMap.get('id');
    
    if (this.tankId) {
      this.apiService.getTankById(this.tankId).subscribe(data => {
        this.tank = data;
      });
    }
  }
}