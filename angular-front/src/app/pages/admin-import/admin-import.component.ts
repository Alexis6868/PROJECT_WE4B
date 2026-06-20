import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';

type EtatRecherche = 'idle' | 'loading' | 'found' | 'notfound' | 'error' | 'saving' | 'saved';

const TYPES = ['Char lourd', 'Char moyen', 'Char léger', 'Transport', 'Spécial'];

@Component({
  selector: 'app-admin-import',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './admin-import.component.html',
  styleUrl: './admin-import.component.css'
})
export class AdminImportComponent implements OnInit {
  // ── IA import ──────────────────────────────────────────────
  searchTerm = '';
  etat: EtatRecherche = 'idle';
  tankPreview: any = null;
  errorMessage = '';
  importedNom = '';

  // ── CRUD ───────────────────────────────────────────────────
  readonly types = TYPES;
  crudVehicles: any[] = [];
  crudLoading = true;
  crudFilter = '';
  editingVehicle: any | null = null;
  deleteConfirmId: number | null = null;
  crudMsg = '';
  crudMsgType: 'ok' | 'err' = 'ok';

  constructor(private apiService: ApiService) {}

  ngOnInit(): void { this.loadCrud(); }

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
    this.loadCrud();
  }

  // ── CRUD methods ───────────────────────────────────────────
  get filteredVehicles(): any[] {
    const q = this.crudFilter.toLowerCase();
    return q ? this.crudVehicles.filter(v => v.nom.toLowerCase().includes(q) || (v.pays || '').toLowerCase().includes(q)) : this.crudVehicles;
  }

  loadCrud(): void {
    this.crudLoading = true;
    this.apiService.getTanks().subscribe({
      next: (data) => { this.crudVehicles = data; this.crudLoading = false; },
      error: () => { this.crudLoading = false; }
    });
  }

  startEdit(v: any): void {
    this.editingVehicle = { ...v };
    this.deleteConfirmId = null;
    this.crudMsg = '';
  }

  cancelEdit(): void { this.editingVehicle = null; }

  saveEdit(): void {
    if (!this.editingVehicle) return;
    this.apiService.updateTank(this.editingVehicle.id, this.editingVehicle).subscribe({
      next: () => {
        const idx = this.crudVehicles.findIndex(v => v.id === this.editingVehicle.id);
        if (idx !== -1) this.crudVehicles[idx] = { ...this.editingVehicle };
        this.editingVehicle = null;
        this.showMsg('Modifications enregistrées.', 'ok');
      },
      error: () => this.showMsg('Erreur lors de la sauvegarde.', 'err'),
    });
  }

  askDelete(id: number): void {
    this.deleteConfirmId = id;
    this.editingVehicle = null;
  }

  cancelDelete(): void { this.deleteConfirmId = null; }

  confirmDelete(id: number): void {
    this.apiService.deleteTank(id).subscribe({
      next: () => {
        this.crudVehicles = this.crudVehicles.filter(v => v.id !== id);
        this.deleteConfirmId = null;
        this.showMsg('Blindé supprimé.', 'ok');
      },
      error: () => this.showMsg('Erreur lors de la suppression.', 'err'),
    });
  }

  private showMsg(msg: string, type: 'ok' | 'err'): void {
    this.crudMsg = msg;
    this.crudMsgType = type;
    setTimeout(() => this.crudMsg = '', 3000);
  }
}
