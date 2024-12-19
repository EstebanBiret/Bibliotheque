import { Livre } from './livre';

export class Reservation {
    constructor(
      public id: number,
      public livre: Livre,
      public adherent_id: number,
      public livre_id: number,
      public date_resa: Date | null,
    ) {}
  }
