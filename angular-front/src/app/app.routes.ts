import { Routes } from '@angular/router';
import { HomeComponent } from './pages/home/home.component';
import { TankCatalogComponent } from './pages/tank-catalog/tank-catalog.component';

export const routes: Routes = [
  { 
    path: '', 
    component: HomeComponent 
  },
  { 
    path: 'tanks', 
    component: TankCatalogComponent 
  },
  { 
    path: '**', 
    redirectTo: '' 
  }
];