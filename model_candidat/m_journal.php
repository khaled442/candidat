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

function infos_candidat()
{

    $bdd = connexion_bdd();

    $reponse = $bdd->prepare('SELECT id,nom,domaine_candidat,code,competences,
    villes_possibles,telephone,timezone,dh_time_zone,pays FROM u_candidat WHERE mail = ? ');
    $reponse->execute(array($_SESSION['mail']));


    $donnees = $reponse->fetch();
        
    if($donnees["domaine_candidat"]!=null)
        $_SESSION["domaine"]=$donnees['domaine_candidat'];

    if($donnees["nom"]!=null)
        $_SESSION["nom"]=$donnees['nom'];

    $_SESSION["id"]=$donnees['id'];

    $_SESSION["code"]=$donnees['code'];

    if($donnees["competences"]!=null)
        $_SESSION["competences"]=$donnees['competences'];

    if($donnees["villes_possibles"]!=null)
        $_SESSION["villes_possibles"]=$donnees['villes_possibles'];

    $_SESSION["telephone"]=$donnees['telephone'];

    $_SESSION["timezone"]=$donnees['timezone'];

    $_SESSION["dh_time_zone"]=$donnees['dh_time_zone'];

    if($donnees["pays"]!=null)
        $_SESSION["pays"]=$donnees['pays'];
        
    $reponse->closeCursor();
                

        
}


function afficher_journal_bord()
{
    $bdd = connexion_bdd(); $reponse_retour = "";

    //selectionner les id d'offres de toutes les candidatures du candidat
    //programmation de manière à mettre tout les id d'offre dns une variable pour
    // les faire utiliser par la fonction sql IN, pour gérer les dates plus facilement

    $reponse = $bdd->prepare('SELECT id_offre FROM candidatures WHERE id_candidat = ? ');
    $reponse->execute(array($_SESSION['id']));


    $i=0;
    $id_offres = "";
    while($donnees = $reponse->fetch())
    {
            if($i==0)//Au premier tour pas de virgule et aussi si il y'a une seule candidature
                $id_offres .= "'".$donnees["id_offre"]."'";
            else
                $id_offres .= ",'".$donnees["id_offre"]."'";

            $i++;
    }

    //Verification si il y'a des notifications uniquement déstinées au candidat

    $verif_c_notif = $bdd->prepare('SELECT idc FROM journaux_bord_c WHERE idc = ? ');
    $verif_c_notif->execute(array($_SESSION['id']));
    
    //si il y'a des notifications uniquement destinées au candidat ou à plusieurs candidat 
    //éventuellement et si il est dans ce groupe de candidat
    if($id_offres !="" || !$verif_c_notif)
    {
        $id_candidat = $_SESSION['id'];
        $reponse2 = $bdd->query("SELECT ido, description_activite, date_activite FROM journaux_bord_c  WHERE
         (ido IN($id_offres) OR idc = $id_candidat) ORDER BY date_activite DESC");

         //code pour compter le nombre d'activité et determiné le nombre de page à affiché par limite
         // d'élément à affiché sur une page
         $reponse3 = $bdd->query("SELECT COUNT(*) as total FROM journaux_bord_c  WHERE
         (ido IN($id_offres) OR idc = $id_candidat) ORDER BY date_activite DESC");

         $_SESSION["nb_activite"] = $reponse3->fetch();

         $reponse_retour  = $reponse2->fetchAll();
    }

    else
    {
        $reponse_retour = "rien";
        $_SESSION["nb_activite"] = 0;
    }

    return $reponse_retour;

}


function vers_page_droite()
{
    $_SESSION["acti_depart"]+=$_SESSION["aff_max_activite"];
    $_SESSION["n_page_actuelle"] +=1;
}

function vers_page_gauche()
{
    $_SESSION["acti_depart"]-=$_SESSION["aff_max_activite"];
    $_SESSION["n_page_actuelle"] -=1;
}


//Pour organiser les éléments de la date en fonction d'un pays
//division de chaque élement de la date pour le replacer dans une variable
//et ordonner les élements en fonction du pays

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

//Code pour compter le nombre de page selon le nombre d'élement à afficher sur une page

            //dans le cas ou on a 3,5 la partie après la virgule signifie qu'il y'a une page en plus de la partie entière
            //qui signifie 3 pages
function obtenir_nb_pages()
{
    $_SESSION["nb_pages"] = 0;

                if(($_SESSION["nb_activite"]["total"] % $_SESSION["aff_max_activite"]) != 0)
                    $_SESSION["nb_pages"] = 1;

            $_SESSION["nb_pages"] += (int) ($_SESSION["nb_activite"]["total"] / $_SESSION["aff_max_activite"]);
            echo "Nombre de pages: ".$_SESSION["nb_pages"];
            echo " Nombre d'activite: ".$_SESSION["nb_activite"]["total"];

            echo"<br><br>";
}