import { Component, Output, EventEmitter } from '@angular/core';

@Component({
  selector: 'app-search-bar',
  standalone: true,
  templateUrl: './search-bar.component.html'
})
export class SearchBarComponent {
  @Output() searchChange = new EventEmitter<string>();

  onTyping(event: any) {
    const text = event.target.value;
    this.searchChange.emit(text); 
  }
}