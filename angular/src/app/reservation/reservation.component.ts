import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../services/api.service';
import { SharedDataService } from '../services/shared-data.service';
import { Reservation } from '../models/reservation';
import { Livre } from '../models/livre';

@Component({
  selector: 'app-reservation',
  templateUrl: './reservation.component.html',
  styleUrl: './reservation.component.css'
})
export class ReservationComponent implements OnInit {
  reservations: Reservation[] = [];
  livres: { [key: number]: Livre } = {};
  selectedReservation: Reservation | null = null;
  sess_id = sessionStorage.getItem("PHPSESSID")??'';

  constructor(private apiService: ApiService, private sharedDataService: SharedDataService, private route: ActivatedRoute, private router: Router) {}

  getResas() {
    this.apiService.getReservations().subscribe((data: Reservation[]) => {
      this.reservations = data;

      this.reservations.forEach(reservation => {
        this.apiService.getLivreById(reservation.livre.id).subscribe((livre: Livre) => {
          this.livres[reservation.livre.id] = livre;
        });
      });

    });
  }

  ngOnInit(): void {
    if (this.sess_id) {
      this.getResas();
    }
  }

  deleteResa(reservation: Reservation): void {
    const confirmation = window.confirm("Voulez-vous vraiment supprimer la réservation ?");
    if(confirmation) {
      this.apiService.deleteResa(Number(reservation.id)).subscribe(
        (response) => {
          this.getResas();
        },
        (error) => {
          console.error('Erreur lors de la suppression', error);
        }
      );
    }
  }

  getLivreTitle(reservation: Reservation): string {
    return this.livres[reservation.livre.id]?.titre || 'Titre indisponible';
  }

  getLivrePhotoCouverture(reservation: Reservation): string {
    return this.livres[reservation.livre.id]?.photo_couverture || '';
  }

  retourCompte() {
    this.router.navigate(['/compte']);
  }

  retourLivres() {
    this.router.navigate(['/recherche']);
  }
}
