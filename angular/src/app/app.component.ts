import { Component } from '@angular/core';
import { AuthService } from './services/auth.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'Bibliothèque';

  constructor(private authService: AuthService) {}

  isSessionId(): boolean {
    return this.authService.isSessionId();
  }
}
