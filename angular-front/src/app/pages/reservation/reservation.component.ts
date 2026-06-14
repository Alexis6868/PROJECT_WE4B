import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-reservation',
  standalone: true,
  templateUrl: './reservation.component.html',
  styleUrls: ['./reservation.component.css'],
  imports: [CommonModule, ReactiveFormsModule] 
})
export class ReservationComponent implements OnInit {
  tank: any = null;
  loading: boolean = true;
  reservationForm!: FormGroup;
  tankId!: string | null;

  constructor(
    private route: ActivatedRoute, 
    private apiService: ApiService,
    private fb: FormBuilder,
    private router: Router
  ) {}

  ngOnInit(): void {
this.tankId = this.route.snapshot.paramMap.get('id');
    
    // 🛡️ VERROU : Si l'utilisateur n'est pas connecté, on bloque tout de suite
    const userId = localStorage.getItem('userId');
    if (!userId) {
      alert('Accès refusé. Vous devez être connecté pour réserver un véhicule opérationnel.');
      this.router.navigate(['/login']);
      return; // On stoppe l'exécution ici
    }

    // Initialisation du formulaire
    this.reservationForm = this.fb.group({
      dateDebut: ['', [Validators.required]],
      dateFin: ['', [Validators.required]]
    });

    if (this.tankId) {
      this.apiService.getTankById(this.tankId).subscribe({
        next: (data) => {
          this.tank = data;
          this.loading = false;
        },
        error: (error: any) => {
          console.error('Error fetching tank details:', error);
          this.loading = false;
        }
      });
    }
  }

 onSubmit(): void {
    if (this.reservationForm.valid && this.tankId) {
      
      const userId = localStorage.getItem('userId'); 

      if (!userId) {
        alert(" Erreur critique : Aucun ID utilisateur trouvé dans le LocalStorage ! Vous devez vous déconnecter et vous reconnecter.");
        console.error("localStorage.getItem('userId') a renvoyé :", userId);
        return; 
      }

      const payload = {
        tankId: this.tankId,
        userId: userId, 
        ...this.reservationForm.value
      };

      console.log("Colis complet envoyé à Symfony :", payload);

      this.apiService.createReservation(payload).subscribe({
        next: (response) => {
          alert('Demande de réservation enregistrée avec succès !');
          this.router.navigate(['/tanks']);
        },
        error: (err) => {
          console.error('Erreur reçue du serveur :', err);
          alert('Impossible de valider la location.');
        }
      });
    }
  }
}