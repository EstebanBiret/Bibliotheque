import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { CategoriesListComponent } from './categories-list/categories-list.component';
import { CategorieDetailsComponent } from './categorie-details/categorie-details.component';
import { LivresListComponent } from './livres-list/livres-list.component';
import { LivreDetailsComponent } from './livre-details/livre-details.component';
import { AuteursListComponent } from './auteurs-list/auteurs-list.component';
import { AuteurDetailsComponent } from './auteur-details/auteur-details.component';
import { RechercheComponent } from './recherche/recherche.component';
import { HomeComponent } from './home/home.component';
import { LoginComponent } from './login/login.component';
import { AccountComponent } from './account/account.component';
import { EditProfileComponent } from './edit-profile/edit-profile.component';
import { ReservationComponent } from './reservation/reservation.component';
import { EmpruntComponent } from './emprunt/emprunt.component';

const routes: Routes = [
  { path: '',            component: HomeComponent },
  { path: 'compte', component: AccountComponent},
  { path: 'categories',  component: CategoriesListComponent },
  { path: 'categorie/:id', component: CategorieDetailsComponent },
  { path: 'connexion', component: LoginComponent},
  { path: 'compte/reservations', component: ReservationComponent},
  { path: 'compte/emprunts', component: EmpruntComponent},
  { path: 'compte/modifier', component: EditProfileComponent},
  { path: 'livres',    component: LivresListComponent },
  { path: 'livre/:id', component: LivreDetailsComponent },
  { path: 'auteurs',    component: AuteursListComponent },
  { path: 'auteur/:id', component: AuteurDetailsComponent },
  { path: 'recherche', component: RechercheComponent },

];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
