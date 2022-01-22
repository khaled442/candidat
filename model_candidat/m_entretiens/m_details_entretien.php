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


function afficher_details_entretien()
{
    $bdd = connexion_bdd();

    if(isset($_GET["iduo"]) && isset($_GET["ido"]))
    {
        $_SESSION["iduo"] = $_GET["iduo"];
        $_SESSION["ido"] = $_GET["ido"];
    }

    $reponse = $bdd->prepare("SELECT organisation, date_heure_entretien,poste_offre,description_offre,statut_entretien,id_offre FROM entretiens 
    WHERE id_candidat = ? AND id_createur_offre = ? AND id_offre = ?");
    $reponse->execute(array($_SESSION["id"],$_SESSION["iduo"],$_SESSION["ido"]));

    return $reponse;

 
} 

function raison_modification_offre()
{
    $bdd = connexion_bdd();
    $reponse = $bdd->prepare("SELECT raison_modification FROM offres
    WHERE id= ?");
    $reponse->execute(array($_SESSION["ido"]));

    $retour = $reponse->fetch();

    return $retour;
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


function afficher_re_commentaires()
{
    
    $bdd = connexion_bdd();
    $ido = $_SESSION["ido"];
    $idc = $_SESSION["id"];
    $reponse = $bdd->query("SELECT commentaires FROM re_commentaires WHERE id_offre = $ido AND id_candidat = $idc");
    
    return $reponse;
}

function afficher_la_conversation()
{

 
    if(isset($_GET["iduo"]))
    {
        $_SESSION["iduo"] = $_GET["iduo"]; 
    }
    
    $bdd = connexion_bdd(); 

    $reponse = $bdd->prepare("SELECT messages_offres,date_message,nom_emetteur FROM conversations
     WHERE id_candidat = ? AND id_createur_offre = ?");
   
    $reponse->execute(array($_SESSION["id"],$_SESSION["iduo"] )); 

    
    return $reponse;

}

function envoyer_message($message_c)
{
    $bdd = connexion_bdd();

    $reponse = $bdd->prepare("INSERT INTO conversations
    (id_createur_offre,nom_emetteur,id_candidat,messages_offres,date_message,code_candidat)
                   VALUES (:id_crea_offre,:nom_em,:idc,:message_offre,:date_message,:code_candidat)");

    date_default_timezone_set($_SESSION["timezone"]);
    $date_message = date($_SESSION["dh_time_zone"]);
    
    $reponse->execute(array(":id_crea_offre" => $_SESSION["iduo"],
                        ":nom_em" => $_SESSION["nom"],
                        ":idc" => $_SESSION["id"],
                        ":message_offre" => $message_c,
                        ":date_message" => $date_message,
                        ":code_candidat" => $_SESSION["code"] ));     
}

function accepter_refuser_entretien($choix)
{
    $bdd = connexion_bdd();

    $statut_entretien = "";
    $ide = $_SESSION["ide"];
    $id_offre = $_SESSION["ido"];
    $iduo = $_SESSION["iduo"];
    $idc = $_SESSION["id"];

    //  actualisation des journaux de bord heure activité
    date_default_timezone_set($_SESSION["timezone"]);
    $date_activite = date('y-m-d H:i:s');

    //actualisation des journaux de bord Recuperation du nom de l'offre et de l'organisation
    $recup_nom_offre_orga = $bdd->prepare('SELECT poste, nom_organisation FROM offres WHERE id = '.$id_offre.' ');
    $recup_nom_offre_orga->execute(array($id_offre));

    $nom_poste_orga = $recup_nom_offre_orga->fetch();

    if($choix)
    {
        $statut_entretien = "accepté";
        $journal_candidat = "Entretien accepté pour le poste de ".$nom_poste_orga["poste"]." de l'organisation ".$nom_poste_orga["nom_organisation"];
        $journal_orga = " a accepté l'entretien pour le poste de ".$nom_poste_orga["poste"];
    }
    else
    {
        $statut_entretien = "refusé";
        $journal_candidat = "Entretien refusé pour le poste de ".$nom_poste_orga["poste"]." de l'organisation ".$nom_poste_orga["nom_organisation"];
        $journal_orga = " a refusé l'entretien pour le poste de ".$nom_poste_orga["poste"];
    }

    $reponse = $bdd->query("UPDATE entretiens SET statut_entretien = '$statut_entretien' WHERE id = $ide ");
    $reponse->execute();

    // actualisation du journal de bord du candidat sql
    $req_can = $bdd->prepare('INSERT INTO journaux_bord_c(ido,type_activite,description_activite,date_activite)
                         VALUES(:ido,:type_activite,:description_activite,:date_activite)');


    $req_can->execute(array(':ido' => $id_offre,
                        ':type_activite' => "Proposition entretien",
                        ':description_activite' => $journal_candidat,
                        ':date_activite' => $date_activite));
    $req_can->closeCursor();

    //actualisation du journal de bord de l'utilisateur organisation sql
    $req_o = $bdd->prepare('INSERT INTO journaux_bord_o(ido,iduo,type_activite,description_activite,date_activite,id_candidat)
                         VALUES(:ido,:iduo,:type_activite,:description_activite,:date_activite,:id_candidat)');


    $req_o->execute(array(':ido' => $id_offre,
                        ':iduo' => $iduo,
                        ':type_activite' => "Proposition entretien",
                        ':description_activite' => $journal_orga,
                        ':date_activite' => $date_activite,
                        ':id_candidat' => $idc));
    $req_o->closeCursor();


    

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