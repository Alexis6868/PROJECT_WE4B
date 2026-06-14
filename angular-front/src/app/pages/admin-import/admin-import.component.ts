import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';

type EtatRecherche = 'idle' | 'loading' | 'found' | 'notfound' | 'error' | 'saving' | 'saved';

@Component({
  selector: 'app-admin-import',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './admin-import.component.html',
  styleUrl: './admin-import.component.css'
})
export class AdminImportComponent {
  searchTerm = '';
  etat: EtatRecherche = 'idle';
  tankPreview: any = null;
  errorMessage = '';
  importedNom = '';

  constructor(private apiService: ApiService) {}

  rechercher(): void {
    if (!this.searchTerm.trim()) return;

    this.etat = 'loading';
    this.tankPreview = null;
    this.errorMessage = '';

    this.apiService.searchWithAI(this.searchTerm.trim()).subscribe({
      next: (data) => {
        if (data.erreur) {
          this.etat = 'notfound';
          this.errorMessage = data.erreur;
        } else {
          this.tankPreview = data;
          this.etat = 'found';
        }
      },
      error: () => {
        this.etat = 'error';
        this.errorMessage = 'Erreur de connexion avec l\'API IA.';
      }
    });
  }

  confirmerImport(): void {
    if (!this.tankPreview) return;

    this.etat = 'saving';

    this.apiService.importTank(this.tankPreview).subscribe({
      next: (response) => {
        this.importedNom = response.nom;
        this.etat = 'saved';
        this.tankPreview = null;
        this.searchTerm = '';
      },
      error: () => {
        this.etat = 'error';
        this.errorMessage = 'Erreur lors de l\'enregistrement en base de données.';
      }
    });
  }

  reset(): void {
    this.etat = 'idle';
    this.tankPreview = null;
    this.errorMessage = '';
    this.searchTerm = '';
  }
}
