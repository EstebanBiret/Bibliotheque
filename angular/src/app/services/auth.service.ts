import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  // private isLoggedInKey = 'isLoggedIn';
  // private sess_id = sessionStorage.getItem('PHPSESSID');
  constructor() {}

  isSessionId(): boolean {
    if(sessionStorage.getItem('PHPSESSID')) {
      return true;
    }
    return false;
  }

  // setLoggedIn(value: boolean): void {
  //   sessionStorage.setItem(this.isLoggedInKey, value.toString());
  // }
}
