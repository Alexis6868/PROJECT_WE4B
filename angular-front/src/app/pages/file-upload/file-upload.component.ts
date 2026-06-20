import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-file-upload',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './file-upload.component.html',
  styleUrl: './file-upload.component.css'
})
export class FileUploadComponent implements OnInit {
  form: FormGroup;
  selectedFile: File | null = null;
  files: any[] = [];
  loading = true;
  uploading = false;
  uploadMsg = '';
  uploadMsgType: 'ok' | 'err' = 'ok';
  fileError = '';

  readonly MAX_SIZE = 5 * 1024 * 1024;
  readonly ALLOWED_EXTS = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];

  constructor(private fb: FormBuilder, private api: ApiService) {
    this.form = this.fb.group({ file: [null, Validators.required] });
  }

  ngOnInit(): void { this.loadFiles(); }

  loadFiles(): void {
    this.loading = true;
    const userId = localStorage.getItem('userId') ?? undefined;
    this.api.getFiles(userId).subscribe({
      next: (data) => { this.files = data; this.loading = false; },
      error: () => { this.loading = false; }
    });
  }

  onFileChange(event: Event): void {
    this.fileError = '';
    this.selectedFile = null;
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    const ext = file.name.split('.').pop()?.toLowerCase() ?? '';
    if (!this.ALLOWED_EXTS.includes(ext)) {
      this.fileError = `Extension non autorisée. Formats acceptés : ${this.ALLOWED_EXTS.join(', ')}`;
      return;
    }
    if (file.size > this.MAX_SIZE) {
      this.fileError = 'Fichier trop volumineux (max 5 Mo).';
      return;
    }

    this.selectedFile = file;
    this.form.get('file')!.setValue(file.name);
  }

  submit(): void {
    if (!this.selectedFile || this.fileError) return;

    this.uploading = true;
    const userId = localStorage.getItem('userId') ?? '';

    this.api.uploadFile(this.selectedFile, userId).subscribe({
      next: () => {
        this.uploading = false;
        this.selectedFile = null;
        this.form.reset();
        this.showMsg('Fichier uploadé avec succès !', 'ok');
        this.loadFiles();
      },
      error: (err: any) => {
        this.uploading = false;
        this.showMsg(err?.error?.error ?? 'Erreur lors de l\'upload.', 'err');
      }
    });
  }

  deleteFile(id: string): void {
    if (!confirm('Supprimer ce fichier définitivement ?')) return;
    this.api.deleteFile(id).subscribe({
      next: () => { this.files = this.files.filter(f => f.id !== id); this.showMsg('Fichier supprimé.', 'ok'); },
      error: () => this.showMsg('Erreur lors de la suppression.', 'err')
    });
  }

  formatSize(bytes: number): string {
    if (bytes < 1024) return bytes + ' o';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' Ko';
    return (bytes / 1024 / 1024).toFixed(1) + ' Mo';
  }

  fileUrl(id: string): string {
    return `${environment.apiUrl}/api/fichiers/${id}/open`;
  }

  mimeIcon(mime: string): string {
    if (mime.startsWith('image/')) return '🖼️';
    if (mime === 'application/pdf') return '📄';
    if (mime.includes('word') || mime.includes('doc')) return '📝';
    return '📎';
  }

  private showMsg(msg: string, type: 'ok' | 'err'): void {
    this.uploadMsg = msg;
    this.uploadMsgType = type;
    setTimeout(() => this.uploadMsg = '', 4000);
  }
}
