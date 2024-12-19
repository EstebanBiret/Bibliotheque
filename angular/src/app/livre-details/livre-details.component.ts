import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../services/api.service';
import { SharedDataService } from '../services/shared-data.service';
import { Livre } from '../models/livre';

@Component({
  selector: 'app-livre-details',
  templateUrl: './livre-details.component.html',
  styleUrls: ['./livre-details.component.css']
})
export class LivreDetailsComponent implements OnInit {
  selectedLivre: Livre | null = null;

  constructor(private apiService: ApiService, private sharedDataService: SharedDataService, private route: ActivatedRoute, private router: Router) {}

  ngOnInit(): void {
    const livreId = this.route.snapshot.paramMap.get('id');
    this.sharedDataService.eraseAuteursAndCategoriesFilters();

    if (livreId) {
      this.apiService.getLivreById(Number(livreId)).subscribe((livre: Livre) => {
        this.selectedLivre = livre;
      });
    }
  }

  retournerALaListe(): void {
    this.router.navigate(['/livres']);
  }
}
