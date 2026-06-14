import { Routes } from '@angular/router';
import { HomeComponent } from './pages/home/home.component';
import { TankCatalogComponent } from './pages/tank-catalog/tank-catalog.component';
import { ReservationComponent } from './pages/reservation/reservation.component';
import { RegisterComponent } from './pages/register/register.component';
import { LoginComponent } from './pages/login/login.component';
import { authGuard } from './guards/auth.guard';

export const routes: Routes = [
  { path: '', component: HomeComponent},
  { path: 'tanks', component: TankCatalogComponent},
  { path: 'reservation/:id',component: ReservationComponent, canActivate: [authGuard] },
  { path: 'register', component: RegisterComponent },
  { path: 'login', component: LoginComponent },  
  { 
    path: '**', 
    redirectTo: '' 
  }
];