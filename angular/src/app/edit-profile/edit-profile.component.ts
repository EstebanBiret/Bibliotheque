import { Component, OnInit } from '@angular/core';
import { Adherent } from '../models/adherent';
import { Router } from '@angular/router';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-edit-profile',
  templateUrl: './edit-profile.component.html',
  styleUrls: ['./edit-profile.component.css']
})
export class EditProfileComponent implements OnInit {
  userProfileForm!: FormGroup;
  loading: boolean = true;
  errorMessage: string = '';

  constructor(
    private router: Router,
    private formBuilder: FormBuilder,
    private apiService: ApiService
  ) {
    this.initForm();
  }

  ngOnInit(): void {

    this.apiService.getAdherent().subscribe(
      (response: Adherent) => {
        this.userProfileForm.patchValue(response);
        this.loading = false;
      },
      (error: any) => {
        console.error(error);
        this.loading = false;
      }
    );
  }

  initForm(): void {
    this.userProfileForm = this.formBuilder.group({
      id: ['', Validators.required],
      nom: ['', Validators.required],
      prenom: ['', Validators.required],
      email: ['', Validators.required],
      adresse: ['', Validators.required],
      telephone: ['', [Validators.required, Validators.pattern(/^(\+33|0)[1-9](\d{2}){4}$/)]],
      photo: ['', Validators.required],
      old_password: ['', Validators.required],
      new_password: ['', Validators.required],
      sess_id: ['']
    });
  }

  submitForm(): void {
    this.errorMessage = '';
    this.apiService.updateAdherent(this.userProfileForm.value).subscribe(
      () => {
        this.router.navigate(['/compte']);
      },
      (error: any) => {
        console.error(error);
        this.errorMessage = error.error.erreur;
      }
    );
  }

  goBack(): void {
    this.router.navigate(['/compte']);
  }

  error(): void {
    window.alert(this.errorMessage);
    this.errorMessage = '';
  }
}
