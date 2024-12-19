import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Livre } from '../models/livre';
import { ApiService } from '../services/api.service';
import { AuthService } from '../services/auth.service';
import { SharedDataService } from '../services/shared-data.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-livres-list',
  templateUrl: './livres-list.component.html',
  styleUrls: ['./livres-list.component.css']
})

export class LivresListComponent implements OnInit {
  livres: Livre[] = [];
  selectedLivre: Livre | null = null;
  currentPage: number = 1;
  sess_id = sessionStorage.getItem("PHPSESSID")??'';

  constructor(private http: HttpClient, private apiService: ApiService, private authService: AuthService, private sharedDataService: SharedDataService, private router: Router) {}

  ngOnInit(): void {
    this.sharedDataService.eraseAuteursAndCategoriesFilters();
    this.fetchLivres();
  }

  fetchLivres(): void {
    this.apiService.getLivresPages(this.currentPage).subscribe((data: Livre[]) => {
      this.livres = data;
    });
  }

  onLivreClick(livre: Livre): void {
    this.selectedLivre = livre;
    this.router.navigate(['/livre', livre.id]);
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

  pagePrecedente(): void {
    if (this.currentPage > 1) {
      this.currentPage -= 1;
      this.fetchLivres();
    }
  }

  pageSuivante(): void {
    if (this.livres.length == 30) {
      this.currentPage += 1;
      this.fetchLivres();
    }
  }

  get isSessionId(): boolean {
    return this.authService.isSessionId();
  }

  isActive(page: number): boolean {
    return this.currentPage === page;
  }
}
