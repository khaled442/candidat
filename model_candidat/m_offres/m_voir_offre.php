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


function deja_postulé($ido)
{
   
    $bdd = connexion_bdd();
    $reponse = $bdd->prepare("SELECT id_candidat,id_offre FROM candidatures WHERE id_candidat  = ? AND id_offre = ? ");
    $reponse->execute(array($_SESSION["id"], $ido));

    $donnees = $reponse->fetch();
    
    if($donnees["id_candidat"] != null && $donnees["id_offre"] != null)
        return true;
    
    else
        return false;


}

function offre_detail()
{
    $bdd = connexion_bdd();

    $ido = $_GET["ido"];
    $_SESSION["ido"]=$ido;

    $reponse = $bdd->prepare("SELECT id,poste, nom_organisation,description_offre,id_createur,competences,type_contrat
    ,salaire,lieu,duree,rythme,raison_modification,date_publication,date_modification FROM offres WHERE id = ?");
    $reponse->execute(array($ido));

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