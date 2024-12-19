import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../services/api.service';
import { SharedDataService } from '../services/shared-data.service';
import { Categorie } from '../models/categorie';

@Component({
  selector: 'app-livre-details',
  templateUrl: './categorie-details.component.html',
  styleUrls: ['./categorie-details.component.css']
})

export class CategorieDetailsComponent implements OnInit {
  selectedCategorie: Categorie | null = null;

  constructor(private apiService: ApiService, private sharedDataService: SharedDataService, private route: ActivatedRoute, private router: Router) {}

  ngOnInit(): void {   
    const categorieId = this.route.snapshot.paramMap.get('id');
    
    this.sharedDataService.eraseAuteursAndCategoriesFilters();

    if (categorieId) {
      this.apiService.getCategorieById(Number(categorieId)).subscribe((categorie: Categorie) => {
        this.selectedCategorie = categorie;
      });
    }
  }

  retournerALaListe(): void {
    this.router.navigate(['/categories']);
  }
}
