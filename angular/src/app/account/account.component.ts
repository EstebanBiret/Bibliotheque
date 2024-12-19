import { Component, OnInit } from '@angular/core';
import { Adherent } from '../models/adherent';
import { AuthService } from '../services/auth.service';
import { SharedDataService } from '../services/shared-data.service';
import { ApiService } from '../services/api.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-account',
  templateUrl: './account.component.html',
  styleUrls: ['./account.component.css']
})

export class AccountComponent implements OnInit {
  userProfile: Adherent | null = null;

  constructor(private apiService: ApiService, private sharedDataService: SharedDataService, private authService: AuthService, private router: Router) {}

  ngOnInit(): void {
    this.sharedDataService.eraseAuteursAndCategoriesFilters();

    if (this.authService.isSessionId()) {
      this.apiService.getAdherent().subscribe(
        (response: Adherent) => {
          this.userProfile = response;
        },
        (error: any) => {
          console.error(error);
        }

      );
    }
  }

  logout(): void {
    sessionStorage.removeItem('PHPSESSID');
    this.router.navigate(['/']);
  }

  isExternalImage(photo: string): boolean {
    return photo.startsWith('http');
  }

  editUserProfile(): void {
      this.router.navigate(['/compte/modifier']);
  }

  getReservations(): void {
      this.router.navigate(['/compte/reservations']);
  }

  getEmprunts(): void {
      this.router.navigate(['/compte/emprunts']);
    }
}
