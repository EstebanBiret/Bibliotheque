import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../services/api.service';
import { SharedDataService } from '../services/shared-data.service';
import { Auteur } from '../models/auteur';

@Component({
  selector: 'app-livre-details',
  templateUrl: './auteur-details.component.html',
  styleUrls: ['./auteur-details.component.css']
})

export class AuteurDetailsComponent implements OnInit {
  selectedAuteur: Auteur | null = null;

  constructor(private apiService: ApiService, private sharedDataService: SharedDataService, private route: ActivatedRoute, private router: Router) {}

  ngOnInit(): void {
    this.sharedDataService.eraseAuteursAndCategoriesFilters();


    const auteurId = this.route.snapshot.paramMap.get('id');

    if (auteurId) {
      this.apiService.getAuteurById(Number(auteurId)).subscribe((auteur: Auteur) => {
        this.selectedAuteur = auteur;
      });
    }
  }

  retournerALaListe(): void {
    this.router.navigate(['/auteurs']);
  }
}
