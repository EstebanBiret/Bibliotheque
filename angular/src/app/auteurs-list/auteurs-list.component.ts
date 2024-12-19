import { Component, OnInit } from '@angular/core';
import { Auteur } from '../models/auteur';
import { ApiService } from '../services/api.service';
import { SharedDataService } from '../services/shared-data.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-auteurs-list',
  templateUrl: './auteurs-list.component.html',
  styleUrl: './auteurs-list.component.css'
})

export class AuteursListComponent implements OnInit {
  auteurs: Auteur[] = [];
  selectedAuteur: Auteur | null = null;

  constructor(private apiService: ApiService, private sharedDataService: SharedDataService, private router: Router) {}

  ngOnInit(): void {
    this.sharedDataService.eraseAuteursAndCategoriesFilters();

    this.apiService.getAuteurs().subscribe((data: Auteur[]) => {
      this.auteurs = data;
    });
  }

  onAuteurDetailsClick(auteur: Auteur): void {
    this.selectedAuteur = auteur;
    this.router.navigate(['/auteur', auteur.id]);
  }

  onAuteurLivresClick(auteur: Auteur): void {
    this.sharedDataService.selectedAuteurs = [auteur];
    this.sharedDataService.selectedCategories = [];
    this.router.navigate(['/recherche']);
  }
}