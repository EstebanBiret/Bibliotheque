import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { ApiService } from '../services/api.service';
import { AuthService } from '../services/auth.service';
import { SharedDataService } from '../services/shared-data.service';
import { Categorie } from '../models/categorie';
import { Livre } from '../models/livre';
import { Auteur } from '../models/auteur';
import { Router, ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-recherche',
  templateUrl: './recherche.component.html',
  styleUrls: ['./recherche.component.css'],
})

export class RechercheComponent implements OnInit {

  categories: Categorie[] = [];
  langues: string[] =[
    'Anglais', 'Allemand', 'Arabe', 'Chinois', 'Coréen', 'Espagnol', 'Français', 'Grec', 'Hébreu',
    'Italien', 'Japonais', 'Néerlandais', 'Polonais', 'Portugais', 'Russe', 'Suédois', 'Turc'
  ];

  livres: Livre[] = [];
  auteurs: Auteur[] = [];
  page: number = 1;
  selectedLivre: Livre | null = null;
  selectedLangue: string = 'toutes_langues';
  onlyDisponiblesBooks: boolean = false;
  selectedCategories: Categorie[] = [];
  selectedAuteurs: Auteur[] = [];
  selectedTitre: string = '';
  sess_id = sessionStorage.getItem("PHPSESSID")??'';

  constructor(private http: HttpClient, private apiService: ApiService, private sharedDataService: SharedDataService, private router: Router, private route: ActivatedRoute, private authService: AuthService) {}

  ngOnInit(): void {

    this.selectedAuteurs = this.sharedDataService.selectedAuteurs;
    this.selectedCategories = this.sharedDataService.selectedCategories;

    this.apiService.getCategories().subscribe((data: Categorie[]) => {
      this.categories = data;
    });
    this.apiService.getLivres().subscribe((data: Livre[]) => {
      this.livres = data;
    });
    this.apiService.getAuteurs().subscribe((data: Auteur[]) => {
      this.auteurs = data;
    });
    this.route.params.subscribe(params => {
      this.updateSearchResults();
    });
  }

  onCategorieClick(categorie: Categorie): void {
    const index = this.selectedCategories.findIndex(cat => cat.id === categorie.id);
    if (index === -1) {
      if (this.selectedCategories.length < 3) {
        this.selectedCategories.push(categorie);
      }
    } else {
      this.selectedCategories.splice(index, 1);
    }
    this.page = 1;
    this.updateSearchResults();
  }

  onAuteurClick(auteur: Auteur): void {
    const index = this.selectedAuteurs.findIndex(aut => aut.id === auteur.id);
    if (index === -1) {
      if (this.selectedAuteurs.length < 3) {
        this.selectedAuteurs.push(auteur);
      }
    } else {
      this.selectedAuteurs.splice(index, 1);
    }
    this.page = 1;
    this.updateSearchResults();
  }

  effacerFiltres(): void {
    this.selectedLangue = 'toutes_langues';
    this.selectedCategories = [];
    this.selectedAuteurs = [];
    this.selectedTitre = '';
    this.onlyDisponiblesBooks = false;
    this.page = 1;
    this.updateSearchResults();
  }

  pagePrecedente(): void {
    if (this.page > 1) {
      this.page -= 1;
      this.updateSearchResults();
    }
  }

  pageSuivante(): void {
    if (this.livres.length == 30) {
      this.page += 1;
      this.updateSearchResults();
    }
  }

  onLangueChange(): void {
    if (this.selectedLangue) {
      this.page = 1;
      this.updateSearchResults();
    }
  }

  onCheckboxChange(): void {
    this.page = 1;
    this.updateSearchResults();
  }

  onLivreClick(livre: Livre): void {
    this.selectedLivre = livre;
    this.router.navigate(['/livre', livre.id]);
  }

  onTitleSearch(): void {
    if (this.selectedTitre) {
      this.page = 1;
      this.updateSearchResults();
    }
  }

  updateSearchResults(): void {
    this.apiService.getLivresByFilters({
      langue: this.selectedLangue,
      categories: this.selectedCategories,
      auteurs: this.selectedAuteurs,
      titre: this.selectedTitre,
      disponible: this.onlyDisponiblesBooks,
      page: this.page
    }).subscribe((data: Livre[]) => {
      this.livres = data;
      console.log(this.livres.length);
    });
  }

  isSelectedAuteur(auteur: Auteur): boolean {
    return this.selectedAuteurs.some(selectedAuteur => selectedAuteur.id === auteur.id);
  }

  isSelectedCategorie(categorie: Categorie): boolean {
    return this.selectedCategories.some(selectedCategorie => selectedCategorie.id === categorie.id);
  }

  get isSessionId(): boolean {
    return this.authService.isSessionId();
  }

  reserver(livre: Livre): void {
    this.http.post('https://127.0.0.1:8008/api/reservations', {sess_id: this.sess_id, livre_id: livre.id}).subscribe(
      (response: any) => {
        this.router.navigate(['compte/reservations']);
      },
      (error: any) => {
        if ((error.error.erreur) && error.error.erreur === "Cet adhérent a déjà 3 réservations, il ne peut pas en avoir plus.") {
          window.alert("Vous avez déjà 3 réservations !");
        }
      }
    );
  }
}
