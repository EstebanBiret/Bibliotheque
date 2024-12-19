import { Categorie } from "./categorie";
import { Auteur } from "./auteur";

export class Livre {
  constructor(
    public id: number,
    public titre: string,
    public date_sortie: Date | null,
    public langue: string,
    public photo_couverture: string,
    public disponible: boolean,
    public categories: Categorie[],
    public auteurs: Auteur[]
  ) {}
}
