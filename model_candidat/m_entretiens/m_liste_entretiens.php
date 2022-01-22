<?php

function connexion_bdd()
{
            try
            {
                $bdd = new PDO('mysql:host=localhost;dbname=bdd;charset=utf8', 'root', 'root');
                $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            }

            catch (Exception $e)
            {
                die('Erreur : ' . $e->getMessage());
            }

            return $bdd;

        
}


function afficher_entretiens_c()
{
  
     $bdd = connexion_bdd();

     $minimum_un = $bdd->prepare("SELECT COUNT(*) as total FROM entretiens WHERE id_candidat = ?");
     $minimum_un->execute(array($_SESSION["id"]));

     $au_moins_un = $minimum_un->fetch();

    if( $au_moins_un["total"]>0)
    {
        $reponse = $bdd->prepare("SELECT id,id_createur_offre,organisation, date_heure_entretien,poste_offre,description_offre,statut_entretien,id_offre FROM entretiens WHERE id_candidat = ? ORDER BY date_heure_entretien ASC");
        $reponse->execute(array($_SESSION["id"]));

        $_SESSION["nb_entretiens"] = $au_moins_un["total"];
    }
    else
        $reponse = "rien"; $_SESSION["nb_entretiens"] = 0;

    return $reponse;
}

function format_date_zone($d_h)
{
    $jour = ""; $mois=""; $annee =""; $heure=""; $minute ="";
    
    
    if($_SESSION["timezone"] == "Europe/Paris")
    {
        //separation date et heure
        $date_heure = explode(" ",$d_h); 
        $a_m_j = $date_heure[0];
        $h_m = $date_heure[1];

        //separation date
        $date_zone = explode("-",$a_m_j);
        $jour = $date_zone[2];
        $mois = $date_zone[1];
        $annee = $date_zone[0];

        //separation heure
        $heure_zone = explode(":",$h_m);
        $heure = $heure_zone[0];
        $minute = $heure_zone[1];

    }

    $date_heure_zone = $jour."/".$mois."/".$annee." ".$heure.":".$minute;

    return $date_heure_zone;
}


function obtenir_nb_pages()
{
    $_SESSION["nb_pages_en"] = 0;

                if(($_SESSION["nb_entretiens"] % $_SESSION["aff_max_en"]) != 0)
                $_SESSION["nb_pages_en"] = 1;

                $_SESSION["nb_pages_en"] += (int) ($_SESSION["nb_entretiens"] / $_SESSION["aff_max_en"]);
            echo "Nombre de pages: ".$_SESSION["nb_pages_en"];
            echo " Nombre d'entretien: ".$_SESSION["nb_entretiens"];

            echo"<br><br>";
}

function vers_page_droite()
{
    $_SESSION["en_depart"]+=$_SESSION["aff_max_en"];
    $_SESSION["en_n_page_actuelle"] +=1;
}

function vers_page_gauche()
{
    $_SESSION["en_depart"]-=$_SESSION["aff_max_en"];
    $_SESSION["en_n_page_actuelle"] -=1;
}

