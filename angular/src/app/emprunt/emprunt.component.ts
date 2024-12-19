import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../services/api.service';
import { SharedDataService } from '../services/shared-data.service';
import { Emprunt } from '../models/emprunt';
import { Livre } from '../models/livre';

@Component({
  selector: 'app-emprunt',
  templateUrl: './emprunt.component.html',
  styleUrls: ['./emprunt.component.css']
})
export class EmpruntComponent implements OnInit {
  emprunts: Emprunt[] = [];
  livres: { [key: number]: Livre } = {};
  empruntsEnCours: Emprunt[] = [];
  empruntsTermines: Emprunt[] = [];

  constructor(private apiService: ApiService, private sharedDataService: SharedDataService, private route: ActivatedRoute, private router: Router) {}

  ngOnInit(): void {
    this.apiService.getEmprunts().subscribe((data: Emprunt[]) => {
      this.emprunts = data;

      this.emprunts.forEach(emprunt => {
        this.apiService.getLivreById(emprunt.livre.id).subscribe((livre: Livre) => {
          this.livres[emprunt.livre.id] = livre;
        });

        if (!emprunt.retourne) {
          this.empruntsEnCours.push(emprunt);
        } else {
          this.empruntsTermines.push(emprunt);
        }
      });
    });
  }

  retourCompte() {
    this.router.navigate(['/compte']);
  }

  getLivreTitle(emprunt: Emprunt): string {
    return this.livres[emprunt.livre.id]?.titre || 'Titre indisponible';
  }

  getLivrePhotoCouverture(emprunt: Emprunt): string {
    return this.livres[emprunt.livre.id]?.photo_couverture || '';
  }

  isEmpruntEnRetard(emprunt: Emprunt): boolean {
    const dateRetour = emprunt.date_retour ? new Date(emprunt.date_retour) : null;
    const dateActuelle = new Date();

    // Si la date de retour existe et est dépassée, l'emprunt est en retard
    return dateRetour !== null && dateRetour < dateActuelle;
  }

}
