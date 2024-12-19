export class Auteur {
    constructor(
      public id: number,
      public nom: string,
      public prenom: string,
      public date_naissance: Date | null,
      public date_deces: Date | null,
      public nationalite: string,
      public photo: string,
      public description: string,

    ) {}
  }