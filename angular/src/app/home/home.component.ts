import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { SharedDataService } from '../services/shared-data.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})

export class HomeComponent {
  constructor(private router: Router, private authService: AuthService, private sharedDataService: SharedDataService) {}

  ngOnInit(): void {
    this.sharedDataService.eraseAuteursAndCategoriesFilters();
  }

  redirectToLivres() {
    this.router.navigate(['/livres']);
  }

  redirectToAdmin() {
    window.location.href = 'https://127.0.0.1:8008/login';
  }

  redirectToLogin() {
    this.router.navigate(['/connexion']);
  }

  get isSessionId(): boolean {
    return this.authService.isSessionId();
  }
}
