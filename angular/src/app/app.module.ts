import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { CategoriesListComponent } from './categories-list/categories-list.component';
import { LivresListComponent } from './livres-list/livres-list.component';
import { AuteursListComponent } from './auteurs-list/auteurs-list.component';
import { LivreDetailsComponent } from './livre-details/livre-details.component';
import { CategorieDetailsComponent } from './categorie-details/categorie-details.component';
import { AuteurDetailsComponent } from './auteur-details/auteur-details.component';
import { RechercheComponent } from './recherche/recherche.component';
import { HomeComponent } from './home/home.component';
import { LoginComponent } from './login/login.component';
import { AccountComponent } from './account/account.component';
import { EditProfileComponent } from './edit-profile/edit-profile.component';
import { ReservationComponent } from './reservation/reservation.component';
import { EmpruntComponent } from './emprunt/emprunt.component';

@NgModule({
  declarations: [
    AppComponent,
    CategoriesListComponent,
    LivresListComponent,
    AuteursListComponent,
    LivreDetailsComponent,
    CategorieDetailsComponent,
    AuteurDetailsComponent,
    RechercheComponent,
    HomeComponent,
    LoginComponent,
    AccountComponent,
    EditProfileComponent,
    ReservationComponent,
    EmpruntComponent,
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    FormsModule,
    AppRoutingModule,
    ReactiveFormsModule,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
