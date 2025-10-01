<?php

function demarrerJeu($categorie = null) {
    $dictionnairePath = __DIR__ . "/data/dictionnaire.json";
    
    if (!file_exists($dictionnairePath)) {
        echo "Erreur Fatale : Fichier dictionnaire introuvable a l'adresse: $dictionnairePath\n";
        echo "Assurez-vous que le dossier 'data' contient 'dictionnaire.json' et qu'il est au meme niveau que 'app.php'.\n";
        exit;
    }
    
    $jsonData = file_get_contents($dictionnairePath);
    $dictionnaire = json_decode($jsonData, true);

    if (!$dictionnaire) {
        echo "Erreur : dictionnaire invalide (format JSON incorrect).\n";
        exit;
    }

    if ($categorie && isset($dictionnaire[$categorie])) {
        $mots = $dictionnaire[$categorie];
        echo "Categorie selectionnee : $categorie\n";
    } else {
        $mots = [];
        foreach ($dictionnaire as $cat => $liste) {
            $mots = array_merge($mots, $liste);
        }
        echo "Categorie selectionnee : Aleatoire\n";
    }

    if (empty($mots)) {
        echo "Erreur : Aucune categorie trouvee ou la categorie selectionnee est vide.\n";
        exit;
    }

    $motMystere = $mots[array_rand($mots)];
    $motMystere = strtolower($motMystere);

    $longueur = strlen($motMystere);
    $motAffiche = str_repeat("-", $longueur);

    $vies = 6;
    $lettresProposees = [];

    echo "Bienvenue dans le jeu du pendu !\n";

    while ($vies > 0 && $motAffiche !== $motMystere) {
        echo "\n---------------------------------------\n";
        echo "Vies restantes : $vies\n";
        echo "Lettres proposees : [ " . implode(" - ", $lettresProposees) . " ]\n";
        echo "Mot : $motAffiche\n";
        echo "---------------------------------------\n";

        $input = strtolower(readline("Proposez une lettre : "));

        if (strlen($input) !== 1 || $input < "a" || $input > "z") {
            echo "Avertissement : Entree invalide ! Veuillez saisir une seule lettre minuscule (a-z).\n";
            continue;
        }

        if (in_array($input, $lettresProposees)) {
            echo "Avertissement : Vous avez deja propose la lettre '$input'.\n";
            continue;
        }

        $lettresProposees[] = $input;

        if (str_contains($motMystere, $input)) {
            echo "Succes : La lettre \"$input\" se trouve dans le mot mystere !\n";

            $nouveauMot = "";
            for ($i = 0; $i < $longueur; $i++) {
                if ($motMystere[$i] === $input) {
                    $nouveauMot .= $input;
                } else {
                    $nouveauMot .= $motAffiche[$i];
                }
            }
            $motAffiche = $nouveauMot;
        } else {
            $vies--;
            echo "Echec : La lettre \"$input\" ne se trouve pas dans le mot mystere ! Vous perdez une vie.\n";
        }
    }

    echo "\n=========================================\n";
    if ($motAffiche === $motMystere) {
        echo "GAGNE ! Vous avez trouve le mot : $motMystere\n";
    } else {
        echo "PERDU ! Le mot a decouvrir etait : $motMystere\n";
    }
    echo "=========================================\n";
}

if (php_sapi_name() === "cli") {
    $args = $GLOBALS['argv'] ?? [];
    $categorie = $args[1] ?? null; 
    demarrerJeu($categorie);
}
?>