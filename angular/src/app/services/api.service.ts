import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Categorie } from '../models/categorie';
import { Livre } from '../models/livre';
import { Auteur } from '../models/auteur';
import { Adherent } from '../models/adherent';
import { Emprunt } from '../models/emprunt';
import { Reservation } from '../models/reservation';
import { ActivatedRoute, Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})

export class ApiService {

  private apiUrl = 'https://localhost:8008/api'; // URL de notre API

  constructor(
    private http: HttpClient, private route: ActivatedRoute, private router: Router
  ) {}

  // Lister les catégories
  getCategories(): Observable<Categorie[]> {
    return this.http.get<Categorie[]>(`${this.apiUrl}/categories`);
  }

  // Lister les livres complets
  getLivresPages(page: number): Observable<Livre[]> {
    return this.http.get<Livre[]>(`${this.apiUrl}/livres?page=${page}`);
  }

  getLivres(): Observable<Livre[]> {
    return this.http.get<Livre[]>(`${this.apiUrl}/livres`);
  }

  // Récupérer un adhérent
  getAdherent(): Observable<Adherent> {
    return this.http.post<Adherent>(`${this.apiUrl}/adherent/get`, {sess_id: sessionStorage.getItem("PHPSESSID")});
  }

  // Mettre à jour un adhérent
  updateAdherent(updatedProfile: any): Observable<any> {
    updatedProfile.sess_id = sessionStorage.getItem("PHPSESSID");
    return this.http.post(`${this.apiUrl}/adherent/edit`, updatedProfile);
  }

  // Récupérer un livre précis
  getLivreById(id: Number): Observable<Livre> {
    return this.http.get<Livre>(`${this.apiUrl}/livre/${id}`);
  }

  // Récupérer une catégorie précise
  getCategorieById(id: Number): Observable<Categorie> {
    return this.http.get<Categorie>(`${this.apiUrl}/categorie/${id}`);
  }

  // Récupérer un auteur précis
  getAuteurById(id: Number): Observable<Auteur> {
    return this.http.get<Auteur>(`${this.apiUrl}/auteur/${id}`);
  }

  // Lister les auteurs
  getAuteurs(): Observable<Auteur[]> {
    return this.http.get<Auteur[]>(`${this.apiUrl}/auteurs`);
  }

  getLivresByLangue(langue: String): Observable<Livre[]> {
    return this.http.get<Livre[]>(`${this.apiUrl}/livres?langue=${langue}`);
  }

  getLivresByCategorie(categorie_id: Number): Observable<Livre[]> {
    return this.http.get<Livre[]>(`${this.apiUrl}/livres?categories.id[]=${categorie_id}`);
  }

  getLivresByFilters(filters: { langue?: string, categories?: Categorie[], auteurs?: Auteur[], titre?: string, disponible?: boolean, page?: number }): Observable<Livre[]> {
    let url = `${this.apiUrl}/livres`;

    if (filters.langue && filters.langue !== 'toutes_langues') {
      url += `?langue=${filters.langue}`;
    }

    if (filters.categories && filters.categories.length > 0) {
      const categorieIds = filters.categories.map(cat => `categories.id[]=${cat.id}`).join('&');
      url += url.includes('?') ? `&${categorieIds}` : `?${categorieIds}`;
    }

    if (filters.auteurs) {
      const auteurIds = filters.auteurs.map(cat => `auteurs.id[]=${cat.id}`).join('&');
      url += url.includes('?') ? `&${auteurIds}` : `?${auteurIds}`;
    }

    if (filters.titre) {
      url += url.includes('?') ? `&titre=${filters.titre}` : `?titre=${filters.titre}`;
    }

    if (filters.disponible)  {
      const disponibleValue = filters.disponible === true ? '1' : '0';
      url += url.includes('?') ? `&disponible=${disponibleValue}` : `?disponible=${disponibleValue}`;
    }

    if (filters.page)  {
      url += url.includes('?') ? `&page=${filters.page}` : `?page=${filters.page}`;
    }

    console.log(url);
    return this.http.get<Livre[]>(url);
  }

  getEmprunts(): Observable<Emprunt[]> {
    return this.http.post<Emprunt[]>(`${this.apiUrl}/emprunts/adherent`, {sess_id: sessionStorage.getItem("PHPSESSID")});
  }

  getReservations(): Observable<Reservation[]> {
    return this.http.post<Reservation[]>(`${this.apiUrl}/reservations/adherent`, {sess_id: sessionStorage.getItem("PHPSESSID")});
  }

  deleteResa(reservation_id: Number) {
    return this.http.post(`${this.apiUrl}/reservation`, {sess_id: sessionStorage.getItem("PHPSESSID"), resa_id: reservation_id});
  }
}
