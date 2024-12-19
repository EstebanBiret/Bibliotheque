export class Adherent {
    constructor(
      public id: number,
      public nom: string,
      public prenom: string,
      public date_naissance: Date | null,
      public date_adhesion: Date | null,
      public email: string,
      public password: string,
      public adresse: string,
      public telephone: string,
      public photo: string,
      public roles: string,
    ) {}
  }
