import { Injectable } from '@angular/core';
import { Auteur } from '../models/auteur';
import { Categorie } from '../models/categorie';

@Injectable({
  providedIn: 'root'
})
export class SharedDataService {
  selectedAuteurs: Auteur[] = [];
  selectedCategories: Categorie[] = [];

  eraseAuteursAndCategoriesFilters() {
    this.selectedAuteurs = [];
    this.selectedCategories = [];
  }
}
