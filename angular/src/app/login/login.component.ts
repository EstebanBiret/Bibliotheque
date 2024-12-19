import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  email: string = '';
  password: string = '';
  isLoading: boolean = false;
  errorMessage: string = '';

  constructor(private http: HttpClient, private router: Router, private authService: AuthService) {}

  ngOnInit(): void {
    if (this.authService.isSessionId()) {
      this.router.navigate(['/compte']);
    }
  }

  handleSubmit(): void {
    this.isLoading = true;
    this.errorMessage = '';

    const body = {
      email: this.email,
      password: this.password
    };

    this.http.post('https://127.0.0.1:8008/api/adherent', body)
      .subscribe(
        (response: any) => {
          this.isLoading = false;
          sessionStorage.setItem("PHPSESSID", response.sess_id);
          this.router.navigate(['/compte']);
        },
        (error: any) => {
          this.isLoading = false;
          this.errorMessage = error.error.erreur;
        }
      );
  }
}
