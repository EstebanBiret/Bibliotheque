<?php

namespace App\DataFixtures;

use DateInterval;
use Faker\Factory;
use App\Entity\Livre;
use App\Entity\Auteur;
use App\Entity\Emprunt;
use App\Entity\Adherent;
use App\Entity\Categorie;
use App\Entity\Reservation;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->addProvider(new class($faker) extends \Faker\Provider\Base {
            protected static array $languages = [
                'Anglais', 'Allemand', 'Arabe', 'Chinois', 'Coréen', 'Espagnol', 'Français', 'Grec', 'Hébreu',
                'Italien', 'Japonais', 'Néerlandais', 'Polonais', 'Portugais', 'Russe', 'Suédois', 'Turc'
            ];

            public function language()
            {
                return static::randomElement(static::$languages);
            }
        });

        $auteurs = [];
        for ($i = 0; $i < 25; $i++) {
            $auteur = new Auteur();
            $auteur->setNom($faker->lastName);
            $auteur->setPrenom($faker->firstName);
            $auteur->setDateNaissance($faker->dateTimeBetween('-70 years', '-40 years'));
            $auteur->setDateDeces($faker->optional(0.2)->dateTimeBetween('-40 years'));
            $auteur->setNationalite($faker->country);
            $auteur->setPhoto($faker->imageUrl());
            $auteur->setDescription($faker->text);

            $auteurs[] = $auteur;

            $manager->persist($auteur);
            $manager->flush();
        }    

        $categories = [];
        $categoryNames = ['Romans français', 'Romans étrangers', 'Essais politiques', 'Essais économiques',
                        'Cuisine', 'Bandes dessinées', 'Art musique et cinéma', 'Histoire', 'Jeunesse',
                        'Sciences sociales', 'Relation et spiritualité', 'Scolaire', 'Littérature', 'Humour',
                        'Informatique et internet', 'Sport et tourisme'];
        foreach ($categoryNames as $categoryName) {
            $categorie = new Categorie();
            $categorie->setNom($categoryName);
            $categorie->setDescription($faker->text);

            $categories[] = $categorie;
            $manager->persist($categorie);
            $manager->flush();
        }

        $livres = [];
        for ($i = 0; $i < 250; $i++) {
            $livre = new Livre();
            $livre->setTitre($faker->sentence(4));
            $livre->setDateSortie($faker->dateTimeThisDecade);
            $livre->setLangue($faker->language());
            $livre->setPhotoCouverture($faker->imageUrl(300, 300, 'books'));

            $nombreCategories = $faker->numberBetween(1, 3);
            for ($j = 0; $j < $nombreCategories; $j++)
                $livre->addCategory($faker->randomElement($categories));

            $nombreAuteurs = $faker->numberBetween(1, 2);
            for ($k = 0; $k < $nombreAuteurs; $k++)
                $livre->addAuteur($faker->randomElement($auteurs));

            $livres[] = $livre;
            $manager->persist($livre);
            $manager->flush();
        }

        $adherents = [];
        for ($i = 0; $i < 25; $i++) {
            $adherent = new Adherent();
            $adherent->setDateAdhesion($faker->dateTimeThisYear);
            $adherent->setNom($faker->lastName);
            $adherent->setPrenom($faker->firstName);
            $adherent->setDateNaissance($faker->dateTimeBetween('-40 years', '-18 years'));
            $adherent->setEmail($faker->email);
            $adherent->setRoles(['ROLE_ADHERENT']);
            $adherent->setAdresse($faker->address);
            $adherent->setTelephone($faker->phoneNumber);
            $adherent->setPhoto($faker->imageUrl());
            $adherent->setPassword(password_hash($adherent->getNom(), PASSWORD_DEFAULT));

            $adherents[] = $adherent;
            $manager->persist($adherent);
            $manager->flush();
        }

        // emprunts et réservations
        foreach ($adherents as $adherent) {
            // emprunts
            $numEmprunts = $faker->numberBetween(0, 5);
            for ($i = 0; $i < $numEmprunts; $i++) {
                $emprunt = new Emprunt();
                $emprunt->setAdherent($adherent);
                // prend un livre aléatoire pas déjà emprunté
                do {
                    $livre = $faker->randomElement($livres);
                } while ($livre->getDisponible() == false);
                $emprunt->setLivre($livre);
                $livre->setDisponible(false);
                // défini les dates
                $dateEmprunt = $faker->dateTimeThisYear;
                $emprunt->setDateEmprunt($dateEmprunt);
                $dateEmprunt = date_add($dateEmprunt, DateInterval::createFromDateString('1 day'));
                $emprunt->setDateRetour($faker->dateTimeBetween($dateEmprunt, '+28 days'));
                // si la date de retour est dépacé, défini l'emprunt comme retourné
                // sauf dans ~20% des cas (retard)
                if (strtotime('now') > $emprunt->getDateRetour()->getTimestamp()) {
                    if ($faker->numberBetween(1, 5) < 5) {
                        $emprunt->setRetourne(true);
                        $livre->setDisponible(true);
                    }
                }
                $manager->persist($emprunt);
                $manager->persist($livre);
                $manager->flush();
            }
            // réservations
            $numReservations = $faker->numberBetween(0, 3);
            for ($i = 0; $i < $numReservations; $i++) {
                $reservation = new Reservation();
                // TODO créer des réservations valides (<7 jours)
                $reservation->setDateResa($faker->dateTimeThisYear);
                // prend un livre aléatoire pas déjà emprunté
                do {
                    $livre = $faker->randomElement($livres);
                } while ($livre->getDisponible() == false);
                $reservation->setLivre($livre);
                $livre->setDisponible(false);
                $reservation->setAdherent($adherent);

                $manager->persist($reservation);
                $manager->persist($livre);
                $manager->flush();
            }
        }
    }
}
