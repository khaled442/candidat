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


function format_date_zone($d)
{
    $jour = ""; $mois=""; $annee =""; 
    
    
    if($_SESSION["timezone"] == "Europe/Paris")
    {
    
        //separation date
        $date_zone = explode("-",$d);
        $jour = $date_zone[2];
        $mois = $date_zone[1];
        $annee = $date_zone[0];

    }

    $date_zone = $jour."/".$mois."/".$annee;

    return $date_zone;
}

function date_creation_compte()
{
    $bdd = connexion_bdd(); 

    $reponse = $bdd->prepare('SELECT date_creation_compte FROM u_candidat WHERE mail = ? ');
                    $reponse->execute(array($_SESSION['mail']));

    $date_fetch = $reponse->fetch();

    return format_date_zone($date_fetch["date_creation_compte"]);
}

function experiences()
{
    $bdd = connexion_bdd();
    $reponse = $bdd->prepare('SELECT COUNT(*) as nb_exp FROM experiences WHERE id_candidat = ? ');
                    $reponse->execute(array($_SESSION['id'])); 

    $nb_exp = $reponse->fetch();

    if($nb_exp>0)
    {
        $reponse2 = $bdd->prepare('SELECT nom_poste,organisation,description_exp,date_debut,date_fin FROM experiences WHERE id_candidat = ? ');
                    $reponse2->execute(array($_SESSION['id']));
        return $reponse2;
    }
    else
        return "";

}

function formations()
{
    $bdd = connexion_bdd();
    $reponse = $bdd->prepare('SELECT COUNT(*) as nb_formation FROM formations WHERE idc = ? ');
                    $reponse->execute(array($_SESSION['id'])); 

    $nb_formation = $reponse->fetch();

    if($nb_formation>0)
    {
        $reponse2 = $bdd->prepare('SELECT nom_structure,diplome,description_formation,date_debut,date_fin FROM formations WHERE idc = ? ');
                    $reponse2->execute(array($_SESSION['id']));
        return $reponse2;
    }
    else
        return "";
}