import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, shareReplay } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = environment.apiUrl;

  private tanksCache$: Observable<any[]> | null = null;

  constructor(private http: HttpClient) {}

  getStatus(): Observable<any> {
    return this.http.get(`${this.apiUrl}/status`);
  }

  getTanks(): Observable<any[]> {
    if (!this.tanksCache$) {
      this.tanksCache$ = this.http.get<any[]>(`${this.apiUrl}/api/vehicules`).pipe(
        shareReplay(1)
      );
    }
    return this.tanksCache$;
  }
  
  getTankById(id: string): Observable<any> {
  return this.http.get<any>(`${this.apiUrl}/api/vehicules/${id}`);
}
  createReservation(reservationData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/api/reservations/new`, reservationData);
  }

  searchWithAI(nom: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/api/admin/recherche-ia`, { nom });
  }

  importTank(tankData: any): Observable<any> {
    this.tanksCache$ = null;
    return this.http.post(`${this.apiUrl}/api/admin/vehicules/import`, tankData);
  }
}
