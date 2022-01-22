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


function recherche_offre_candidat()
{
    $bdd = connexion_bdd();


    //Si l'utilisateur effectue sa premère recherche après s'être connecté
    if(!isset($_SESSION["recherche_offre_candidat"]))
        $recherche_offre_c = $_POST['recherche_offre_candidat'];

    //Si la recherche à déja été taper c'est la variable de session qui sera utilisé
    if(isset($_SESSION["recherche_offre_candidat"]))
        $recherche_offre_c = $_SESSION["recherche_offre_candidat"];

        
        $minimum_un = $bdd->query("SELECT COUNT(*) as total FROM offres WHERE (poste LIKE '%$recherche_offre_c%' ) AND 
        statut_offre = 'offre referencee'");
        
        $au_moins_un = $minimum_un->fetch();

        if($au_moins_un["total"]>0)
        {
            $listes_offres = array();

            $reponse = $bdd->query("SELECT id,poste,nom_organisation,description_offre,lieu,type_contrat,date_publication,date_modification FROM offres WHERE (poste LIKE '%$recherche_offre_c%' ) AND 
            statut_offre = 'offre referencee'");
            $reponse_2 = $reponse->fetchAll();

            $reponse_type_contrat = $bdd->query("SELECT id,poste,nom_organisation,description_offre,lieu,type_contrat,date_publication,date_modification FROM offres WHERE (poste LIKE '%$recherche_offre_c%' ) AND 
            statut_offre = 'offre referencee'");

            $_SESSION["nb_offres"]=$au_moins_un["total"];

            if(isset($_POST["recherche_type_contrat"]))
            {
                $type_contrat = implode(" ",$_POST["recherche_type_contrat"]);
                $i=0;
                
                $anti_repetition_id=0;
                while($offre = $reponse_type_contrat->fetch())
                {
                    $type_contrat_offre = explode(" ",$offre["type_contrat"]);

                    //on vérifie si chaque contrat recherché par le candidat est recherché par l'organisation
                    foreach($_POST["recherche_type_contrat"] as $candidat_contrat_recherche)
                    {   
                        //je met une condition empêchant d'ajouter plus d'un fois la même offre
                        //si une correspondance dans les contrat a déja été trouvé
                        if(in_array($candidat_contrat_recherche,$type_contrat_offre) && $anti_repetition_id!=$offre["id"])
                        { 
                            $listes_offres[$i]["nom_organisation"] = $offre["nom_organisation"];
                            $listes_offres[$i]["poste"] = $offre["poste"];
                            $listes_offres[$i]["lieu"] = $offre["lieu"];
                            $listes_offres[$i]["type_contrat"] = $offre["type_contrat"];
                            $listes_offres[$i]["date_publication"] = $offre["date_publication"];
                            $listes_offres[$i]["date_modification"] = $offre["date_modification"];
                            $listes_offres[$i]["id"] = $offre["id"];
                            
                            $i++;
                            $anti_repetition_id = $offre["id"];
                        }
                    }
                }

                //actualisation de la variable nb_offres
                //retrait éventuel du nombre d'offres ne correspondant pas aux critère
                $nb_retrait = $_SESSION["nb_offres"] - $i;
                $_SESSION["nb_offres"] -= $nb_retrait;
                
            $reponse_2 = $listes_offres;
            }
        }
        else
            $reponse_2 = "rien";

    

    return $reponse_2;


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

function obtenir_nb_pages()
{
    $_SESSION["nb_pages_of"] = 0;

                if(($_SESSION["nb_offres"] % $_SESSION["aff_max_of"]) != 0)
                $_SESSION["nb_pages_of"] = 1;

                $_SESSION["nb_pages_of"] += (int) ($_SESSION["nb_offres"] / $_SESSION["aff_max_of"]);
            echo "Nombre de pages: ".$_SESSION["nb_pages_of"];
            echo " Nombre d'offres: ".$_SESSION["nb_offres"];

            echo"<br><br>";
}

function vers_page_droite()
{
    $_SESSION["of_depart"]+=$_SESSION["aff_max_of"];
    $_SESSION["of_n_page_actuelle"] +=1;
}

function vers_page_gauche()
{
    $_SESSION["of_depart"]-=$_SESSION["aff_max_of"];
    $_SESSION["of_n_page_actuelle"] -=1;
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

