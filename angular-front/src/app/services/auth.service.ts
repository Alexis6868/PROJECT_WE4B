import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http'; 
import { BehaviorSubject, Observable } from 'rxjs';
import { tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8000/api'; 

  private isLoggedInSubject = new BehaviorSubject<boolean>(this.hasUserId());
  private userNameSubject = new BehaviorSubject<string | null>(localStorage.getItem('userName'));

  isLoggedIn$: Observable<boolean> = this.isLoggedInSubject.asObservable();
  userName$: Observable<string | null> = this.userNameSubject.asObservable();

  constructor(private http: HttpClient) {} // 👈 Injection du client HTTP

  private hasUserId(): boolean {
    return !!localStorage.getItem('userId');
  }


  register(userData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, userData); 
  }


  login(credentials: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/login`, credentials).pipe(
      tap(response => {
        // 🕵️‍♂️ ÉTAPE DE SÉCURITÉ : On affiche EXACTEMENT ce que Symfony renvoie
        console.log("--- DEBUG LOGIN ---");
        console.log("Réponse brute de Symfony :", response);

        // On essaie d'extraire l'ID et le Nom de toutes les manières possibles
        const id = response?.user?.id || response?.id || response?.userId;
        const name = response?.user?.nom || response?.nom || response?.user?.prenom || response?.username;

        console.log("ID détecté par Angular :", id);
        console.log("Nom détecté par Angular :", name);

        if (id) {
          localStorage.setItem('userId', id.toString());
          this.isLoggedInSubject.next(true);
        } else {
          console.error("⚠️ Impossible de stocker l'userId : aucune clé 'id' correspondante trouvée !");
        }

        if (name) {
          localStorage.setItem('userName', name);
          this.userNameSubject.next(name);
        }
        console.log("-------------------");
      })
    );
  }

  logout(): void {
    localStorage.removeItem('userId');
    localStorage.removeItem('userName');
    this.isLoggedInSubject.next(false);
    this.userNameSubject.next(null);
  }
}