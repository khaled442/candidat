<?php
session_start();

function connexion_bdd_controller()
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

function compte_compte_controller()
{
    $bdd = connexion_bdd_controller();
    $reponse = $bdd->prepare("SELECT nom,domaine_candidat,villes_possibles,pays FROM u_candidat WHERE mail = ?");
    $reponse->execute(array($_SESSION["mail"]));

    $donnees = $reponse->fetch();
      
    $compte_complet = true;
    
        if($donnees["nom"]==null || $donnees["domaine_candidat"]==null
         || $donnees["villes_possibles"]==null || $donnees["pays"]==null )
        {
            $compte_complet=false;
        }
    
    return $compte_complet;
      
}

//après la connexion

if(compte_compte_controller())
{
    if(!isset($_GET["action"]))
    {
        require(__DIR__."/controller_candidat/c_contact_journal_deco.php");
        accueil_candidat();
    }

    //controleur page journal candidat, deconnexion et page contact
    // ccjd = controleur contact journal deconnexion
    if(isset($_GET["ccjd"]))
    {
        require(__DIR__."/controller_candidat/c_contact_journal_deco.php");

        switch($_GET["action"])
        {
            case "deco":  deconnexion(); break;
            case "journal":  journal(); break;
            case "contact": contact(); break;
        }
    }

    //controleur pages relatives au compte
    //cc = controleur compte
    if(isset($_GET["cc"]))
    {
        require(__DIR__."/controller_candidat/c_compte.php");

        switch($_GET["action"])
        {
            case "voircompte":  voir_compte(); break;
            case "modifcompte":  modifier_compte(); break;
        }
    }

    //controleur pages relatives aux offres & aux candidatures
    //co = controleur offres
    if(isset($_GET["co"]))
    {
        require(__DIR__."/controller_candidat/c_offres.php");

        switch($_GET["action"])
        {
            case "voiroffres":  voir_offres(); break;
            case "detailoffre":  detail_offre(); break;
            case "candidatures":  candidatures(); break;
        }
    }

    //controleur pages relatives aux entretiens
    //ce = controleur entretiens
    if(isset($_GET["ce"]))
    {
        require(__DIR__."/controller_candidat/c_entretiens.php");

        switch($_GET["action"])
        {
            case "voirentretiens":  voir_entretiens(); break;
            case "pleconverser":  voir_details_entretien(); break; //ple pour depuis la page liste entretiens
            case "voirre":  voir_re(); break; //re rapports d'entretiens
        }
    }
}

else 
{
    if(isset($_GET["deco"]))
    {
        require(__DIR__."/controller_candidat/c_contact_journal_deco.php");
        deconnexion();

    }
    else
    {
        require(__DIR__."/controller_candidat/c_compte.php");
        modifier_compte();
    }
}

