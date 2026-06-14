import { Component, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-search-bar',
  standalone: true,
  templateUrl: './search-bar.component.html',
  styleUrl: './search-bar.component.css',
  imports: [CommonModule, FormsModule]
})
export class SearchBarComponent {
  @Output() searchChange = new EventEmitter<string>();

  searchTerm: string = '';

  emitSearch() {
    this.searchChange.emit(this.searchTerm);
  }

}