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

function c_postuler()
{
    $bdd = connexion_bdd();

    //Avant de pouvoir postuler, il faut vérifier si le candidat à déja postuler sur l'offre en question
    //pour éviter un doublon
    
    $reponse = $bdd->prepare("SELECT id_offre FROM candidatures WHERE id_candidat = ?");
    $reponse->execute(array($_SESSION["id"]));

    $tableau_candidatures_candidat = array();
    
    $i=0;
    while($donnees = $reponse->fetch())
    {
        $tableau_candidatures_candidat[$i] = $donnees["id_offre"];
    }


    
    
     //Execution du code uniquement si le candidat ne postule pas à la même offre

     if(!in_array($_SESSION["offre_id"],$tableau_candidatures_candidat))
        {

            date_default_timezone_set($_SESSION["timezone"]);
            $date_candidature = date($_SESSION["dh_time_zone"]);

            $reponse2 = $bdd->prepare('INSERT INTO 
            candidatures(nom_c,mail_c,id_offre,date_candidature,id_candidat,id_createur_offre,code_candidat,statut,statut_offre)
            VALUES (:nom_can,:mail_can,:ido,:date_candidature,:id_candidat,:id_createur_offre,:code_candidat,:statut,:statut_offre)');
            $reponse2->execute(array(
                ':nom_can' => $_SESSION["nom"],
                ':mail_can'=> $_SESSION["mail"],
                ':ido' => $_SESSION["offre_id"],
                ':date_candidature' => $date_candidature,
                ':id_candidat' => $_SESSION["id"],
                ':id_createur_offre' => $_SESSION["id_createur_offre"],
                ':code_candidat' => $_SESSION["code"],
                ':statut' => "en attente",
                ':statut_offre' => "offre référencée"));
            
        }
    //Sinon un message est envoyé pour dire qu'il a déja postulé à l'offre
     else
         header("Location: /candidat/c_offres/c_voir_offre.php?n"); 

}

function afficher_candidatures()
{
   
    $bdd = connexion_bdd();



    //Verification si il y'au moins une candidature
    $verif_au_moins_une = $bdd->prepare("SELECT COUNT(*) as total FROM candidatures WHERE id_candidat = ?");
    $verif_au_moins_une->execute(array($_SESSION["id"]));

    $nb_can = $verif_au_moins_une->fetch();

    $tableau_offres_en_cours = "rien";

    if($nb_can["total"]>0)
    {
        $tableau_offres_en_cours = array();

        $reponse = $bdd->prepare("SELECT id_offre FROM candidatures WHERE id_candidat = ?");
        $reponse->execute(array($_SESSION["id"]));


        $tableau_ido = array();

        $i=0;
        while($donnees = $reponse->fetch())
        {
                $tableau_ido[$i] = $donnees["id_offre"]; $i++;
        }


        //Recuperation des offres où le candidat a postuler
        
        foreach($tableau_ido as $offre_id)
        {
            $reponse2 = $bdd->prepare("SELECT id,nom_organisation,poste,description_offre, id_createur FROM offres WHERE id = ?");
            $reponse2->execute(array($offre_id));

            $donnees2 = $reponse2->fetch();
            //récupère le tableau correspondant à une ligne à chaque tour

            $tableau_offres_en_cours[$offre_id] =  $donnees2; 
        }

        
    }

    //code pour compter le nombre de canddiatures et determiné le nombre de page à affiché par limite
         // d'élément à affiché sur une page
         $id_candidat =$_SESSION["id"];
         $reponse3 = $bdd->query("SELECT COUNT(*) as total FROM candidatures WHERE
         id_candidat = $id_candidat ORDER BY date_candidature DESC");

         $_SESSION["nb_candidatures"] = $reponse3->fetch();

    return $tableau_offres_en_cours; 

}

//Fonction qui précise le statut de la candidature
function statut_candidature($id_candidat,$id_offre)
{
    $bdd = connexion_bdd();
    $reponse = $bdd->prepare("SELECT statut FROM candidatures WHERE id_candidat = ? AND id_offre = ?");
    $reponse->execute(array($id_candidat,$id_offre));

    $statut = $reponse->fetch();

    return $statut;
}

//Fonction qui récupère la date de la candidature
function date_candidature($id_candidat,$id_offre)
{
    $bdd = connexion_bdd();
    $reponse = $bdd->prepare("SELECT date_candidature FROM candidatures WHERE id_candidat = ? AND id_offre = ?");
    $reponse->execute(array($id_candidat,$id_offre));

    $date = $reponse->fetch();

    return format_date_zone($date["date_candidature"]);
}

//Fonction qui active la capacité à converser depuis la page de candidature
//A condition que l'u_o ayant crée l'offre a déja eu une interaction avec le candidat
function interaction_candidat_uo($iduo)
{
    $bdd = connexion_bdd();


  $reponse = $bdd->prepare("SELECT id FROM conversations WHERE id_candidat = ? AND id_createur_offre = ? ");
  $reponse->execute(array($_SESSION["id"],$iduo)); 

  $au_moins_un_id = $reponse->fetch();

    if($au_moins_un_id != null)
        return true;
    else
        return false;

} 

function obtenir_nb_pages()
{
    $_SESSION["nb_pages_candidatures"] = 0;

                if(($_SESSION["nb_candidatures"]["total"] % $_SESSION["aff_max_candidatures"]) != 0)
                $_SESSION["nb_pages_candidatures"] = 1;

                $_SESSION["nb_pages_candidatures"] += (int) ($_SESSION["nb_candidatures"]["total"] / $_SESSION["aff_max_candidatures"]);
            echo "Nombre de pages: ".$_SESSION["nb_pages_candidatures"];
            echo " Nombre de candidatures: ".$_SESSION["nb_candidatures"]["total"];

            echo"<br><br>";
}

function vers_page_droite()
{
    $_SESSION["candidatures_depart"]+=$_SESSION["aff_max_candidatures"];
    $_SESSION["candidatures_n_page_actuelle"] +=1;
}

function vers_page_gauche()
{
    $_SESSION["candidatures_depart"]-=$_SESSION["aff_max_candidatures"];
    $_SESSION["candidatures_n_page_actuelle"] -=1;
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