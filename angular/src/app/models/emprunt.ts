import { Livre } from './livre';

export class Emprunt {
  constructor(
    public id: number,
    public livre: Livre,
    public date_emprunt: Date | null,
    public date_retour: Date | null,
    public adherent_id: number,
    public retourne: number,
  ) {}
}
