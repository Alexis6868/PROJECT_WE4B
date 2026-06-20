import { Routes } from '@angular/router';
import { HomeComponent } from './pages/home/home.component';
import { TankCatalogComponent } from './pages/tank-catalog/tank-catalog.component';
import { ReservationComponent } from './pages/reservation/reservation.component';
import { RegisterComponent } from './pages/register/register.component';
import { LoginComponent } from './pages/login/login.component';
import { AdminImportComponent } from './pages/admin-import/admin-import.component';
import { FileUploadComponent } from './pages/file-upload/file-upload.component';
import { DashboardComponent } from './pages/dashboard/dashboard.component';
import { authGuard } from './guards/auth.guard';
import { adminGuard } from './guards/admin.guard';

export const routes: Routes = [
  { path: '', component: HomeComponent},
  { path: 'tanks', component: TankCatalogComponent},
  { path: 'reservation/:id',component: ReservationComponent, canActivate: [authGuard] },
  { path: 'register', component: RegisterComponent },
  { path: 'login', component: LoginComponent },
  { path: 'admin/import-ia', component: AdminImportComponent, canActivate: [adminGuard] },
  { path: 'admin/fichiers', component: FileUploadComponent, canActivate: [authGuard] },
  { path: 'admin/dashboard', component: DashboardComponent, canActivate: [adminGuard] },
  {
    path: '**',
    redirectTo: ''
  }
];