import { Component, OnInit } from '@angular/core';
import { Categorie } from '../models/categorie';
import { ApiService } from '../services/api.service';
import { SharedDataService } from '../services/shared-data.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-categories-list',
  templateUrl: './categories-list.component.html',
  styleUrl: './categories-list.component.css'
})

export class CategoriesListComponent implements OnInit {
  categories: Categorie[] = [];
  selectedCategorie: Categorie | null = null;

  constructor(private apiService: ApiService, private sharedDataService: SharedDataService, private router: Router) {}

  ngOnInit(): void {
    this.sharedDataService.eraseAuteursAndCategoriesFilters();

    this.apiService.getCategories().subscribe((data: Categorie[]) => {
      this.categories = data;
    });
  }

  onCategorieDetailsClick(categorie: Categorie): void {
    this.selectedCategorie = categorie;
    this.router.navigate(['/categorie', categorie.id]);
  }

  onCategorieLivresClick(categorie: Categorie): void {
    this.sharedDataService.selectedCategories = [categorie];
    this.sharedDataService.selectedAuteurs = [];
    this.router.navigate(['/recherche']);
  }
}
