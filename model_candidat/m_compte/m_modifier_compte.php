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
    villes_possibles,telephone,timezone,dh_time_zone,pays,type_contrat FROM u_candidat WHERE mail = ? ');
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

    if($donnees["type_contrat"]!=null)
            $_SESSION["type_contrat"] = explode(" ",$donnees["type_contrat"]);

    $_SESSION["telephone"]=$donnees['telephone'];

    $_SESSION["timezone"]=$donnees['timezone'];

    $_SESSION["dh_time_zone"]=$donnees['dh_time_zone'];

    if($donnees["pays"]!=null)
            $_SESSION["pays"]=$donnees['pays'];
                
    $reponse->closeCursor();

        
}

function compte_complet()
{
    $bdd = connexion_bdd();
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

function modifier_compte_candidat($mail,$nom,$mdp,$domaine,$competences,$villes_possibles,$telephone,$c_donnees_timezone,$type_contrat)
{
    $bdd = connexion_bdd();

    // le header à echelle du try s'execute malgré l'erreur alors que logiquement il doit pas se lancer
                    // je me sers d'un boolean pour le stoper
                    $header_try=true;
                    
                    //avant de changer le mail
                    //Vérification pour savoir si le mail à bien été changé dans la zone de texte
                    if($mail!=$_SESSION["mail"])
                    {

                        //avant de changer de mail on vérifie si le nouveau mail tapé par le candidat
                        //ne correspond à aucun autre dans la base éxcepté le sien
                        //Code nécessaire aussi pour empêcher l'utilisateur d'enregister un doublon

                        $reponse = $bdd->prepare('SELECT mail FROM u_candidat WHERE mail= ? UNION 
                                                SELECT mail FROM u_organisation WHERE mail= ?');
                        $reponse->execute(array($mail, $mail));

                        $donnees = $reponse->fetch();

                        $reponse_2 = $bdd->prepare('SELECT mail_organisation FROM organisation WHERE mail_organisation = ?');
                        $reponse_2->execute(array($mail));

                        $donnees_2 = $reponse_2->fetch();

                        
                       
                        //Si le mail saisie existe déja
                        if($donnees["mail"]==$mail || $donnees_2["mail_organisation"]==$mail)
                        {
                            $reponse->closeCursor(); $header_try=false;
                            header("Location: ../candidat/candidat.php?cc&action=modifcompte&doublon");
                            
                        }

                        else
                        {
                            $mettre_a_jour =  $bdd->prepare("UPDATE u_candidat SET mail = :nouveau_mail WHERE id = :id");
                            $mettre_a_jour->execute(array(
                                ':nouveau_mail' => $mail,
                                ':id'=> $_SESSION["id"]));

                                //Mise à jour de la variable de session mail
                                $_SESSION["mail"]=$mail;
                            
                            $mettre_a_jour->closeCursor();
                            

                        }
                      

                        
                    }
                    //si le nom est changé
                    if((isset($_SESSION["nom"]) && $nom!=$_SESSION["nom"]) || !isset($_SESSION["nom"]))
                    {
                            $mettre_a_jour =  $bdd->prepare("UPDATE u_candidat SET nom = :nouveau_nom WHERE id = :id");
                            $mettre_a_jour->execute(array(
                                ':nouveau_nom' => $nom,
                                ':id'=> $_SESSION["id"]));

                                //Mise à jour de la variable de session nom
                                $_SESSION["nom"]=$nom;
                            
                            $mettre_a_jour->closeCursor();
                        
                    }

                    //si quelque chose à été écrit dans la zone de texte mot de passe
                    if($mdp!=null)
                    {
                             //d'abord le récuperer dans la base, oui je crois que c'est risqué de se balader avec le mot de passe
                            //en session, je ne sais pas pk, c'est mon instinct qui me le dit

                        $req_mdp = $bdd->prepare('SELECT id,mail,nom FROM u_candidat WHERE id = ? ');
                        $req_mdp->execute(array($_SESSION['id']));

                        $donnees = $req_mdp->fetch();

                        $ancien_mdp = $donnees["mdp"];
                        //Si le mot de passe tapé dans la zone est différent alors oui on enregiste
                        if($ancien_mdp!=$mdp)
                        {
                            $mettre_a_jour =  $bdd->prepare("UPDATE u_candidat SET mdp = :nouveau_mdp WHERE id = :id");
                            $mettre_a_jour->execute(array(
                                ':nouveau_mdp' => $mdp,
                                ':id'=> $_SESSION["id"]));
                            
                            $mettre_a_jour->closeCursor();
                        }
                    }

                    if((isset($_SESSION["domaine"]) && $domaine!=$_SESSION["domaine"]) || !isset($_SESSION["domaine"]))
                    {
                            $mettre_a_jour =  $bdd->prepare("UPDATE u_candidat SET domaine_candidat = :nouveau_domaine WHERE id = :id");
                            $mettre_a_jour->execute(array(
                                ':nouveau_domaine' => $domaine,
                                ':id'=> $_SESSION["id"]));

                                $_SESSION["domaine"]=$domaine;
                            
                            $mettre_a_jour->closeCursor();
                        
                    }

                    if((isset($_SESSION["competences"]) && $competences!=$_SESSION["competences"]) || !isset($_SESSION["competences"]))
                    {
                            
                            $mettre_a_jour =  $bdd->prepare("UPDATE u_candidat SET competences = :competences WHERE id = :id");
                            $mettre_a_jour->execute(array(
                                ':competences' => $competences,
                                ':id'=> $_SESSION["id"]));

                                $_SESSION["competences"]=$competences;
                            
                            $mettre_a_jour->closeCursor();
                        
                    }

                    if((isset($_SESSION["villes_possibles"]) && $villes_possibles!=$_SESSION["villes_possibles"]) || !isset($_SESSION["villes_possibles"]))
                    {    
                            
                            $mettre_a_jour =  $bdd->prepare("UPDATE u_candidat SET villes_possibles = :villes_possibles WHERE id = :id");
                            $mettre_a_jour->execute(array(
                                ':villes_possibles' => $villes_possibles,
                                ':id'=> $_SESSION["id"]));

                            
                                $_SESSION["villes_possibles"]=$villes_possibles;
                            
                            $mettre_a_jour->closeCursor();
                    }

                    //telephone
                    if((isset($_SESSION["telephone"]) && $telephone!=$_SESSION["telephone"]) || !isset($_SESSION["competences"]))
                    {
                      
                            
                            $mettre_a_jour =  $bdd->prepare("UPDATE u_candidat SET telephone = :telephone WHERE id = :id");
                            $mettre_a_jour->execute(array(
                                ':telephone' => $telephone,
                                ':id'=> $_SESSION["id"]));

                                $_SESSION["telephone"]=$telephone;
                            
                            $mettre_a_jour->closeCursor();
                        
                    }

                    //j'extrai le pays pour comparé avec le pays de la session
                    //Nécessaire pour vérifier si la condition est

                    $tableau_donnees_timezone = explode("|",$c_donnees_timezone);
                    $pays = $tableau_donnees_timezone[0];

                    if((isset($_SESSION["pays"]) && $c_donnees_timezone!=$_SESSION["pays"]) || !isset($_SESSION["pays"]))
                    {

                            //Par exemple Europe/Paris
                            $timezone = $tableau_donnees_timezone[1]; 
                            // le format de la date et l'heure du pays
                            $dh_time_zone = $tableau_donnees_timezone[2]; 


                            $mettre_a_jour =  $bdd->prepare("UPDATE u_candidat SET pays = :pays, 
                                                                timezone = :timezone,
                                                                dh_time_zone = :dh_time_zone WHERE id = :id");
                            $mettre_a_jour->execute(array(
                                ':pays' => $pays,
                                ':timezone'=> $timezone,
                                ':dh_time_zone' => $dh_time_zone,
                                ':id'=> $_SESSION["id"]));

                                //Mise à jour de la variable de session pays
                                $_SESSION["pays"]=$pays;
                            
                            $mettre_a_jour->closeCursor();
                        
                    }

                    //type de contrat
                    if((isset($_SESSION["type_contrat"]) && $_POST["choix_type_contrat"]!=$_SESSION["type_contrat"]) || !isset($_SESSION["type_contrat"]))
                    {
                            $choix_type_contrat = implode(" ",$_POST["choix_type_contrat"]);

                            $mettre_a_jour =  $bdd->prepare("UPDATE u_candidat SET type_contrat = :type_contrat WHERE id = :id");
                            $mettre_a_jour->execute(array(
                                ':type_contrat' =>  $choix_type_contrat,
                                ':id'=> $_SESSION["id"]));

                                //Mise à jour de la variable de session type contrat
                                $_SESSION["type_contrat"] =  $_POST["choix_type_contrat"];
                            
                            $mettre_a_jour->closeCursor();
                            
                        
                    }
                    //experience
                    if($_POST["nb_exp"]!=null)
                    {
                        $nb_exp=$_POST["nb_exp"];
                        for($i=0; $i<$nb_exp; $i++)
                        {
                            $reponse_exp = $bdd->prepare("INSERT INTO experiences(id_candidat,description_exp,
                            date_debut,date_fin,organisation,nom_poste)
                            VALUES (:id_candidat,:description_exp,
                            :date_debut,:date_fin,:organisation,:nom_poste)");


                            $reponse_exp->execute(array(
                            ':id_candidat' => $_SESSION["id"],
                            ':description_exp'=> $_POST["exp_description$i"],
                            ':date_debut'=> $_POST["exp_date_debut$i"],
                            ':date_fin' => $_POST["exp_date_fin$i"],
                            ':organisation'=> $_POST["exp_nom_entreprise$i"],
                            ':nom_poste'=> $_POST["exp_nom_poste$i"]));
                        }
                    }
                    //formation
                    if($_POST["nb_formation"]!=null)
                    {
                        $nb_formation=$_POST["nb_formation"];
                        for($i=0; $i<$nb_formation; $i++)
                        {
                            $reponse_formation = $bdd->prepare("INSERT INTO formations(idc,nom_structure,
                            diplome,description_formation,date_debut,date_fin)
                            VALUES (:idc,:nom_structure,:diplome,:description_formation,
                            :date_debut,:date_fin)");


                            $reponse_formation->execute(array(
                            ':idc' => $_SESSION["id"],
                            ':nom_structure' => $_POST["formation_nom_structure$i"],
                            ':diplome' => $_POST["formation_diplome$i"],
                            ':description_formation' => $_POST["formation_description$i"],
                            ':date_debut'=> $_POST["formation_date_debut$i"],
                            ':date_fin' => $_POST["formation_date_fin$i"]));
                        }
                    }


                    if($header_try)
                        header("Location: ../candidat/candidat.php?cc&action=voircompte");


}

function experiences()
{
    $bdd = connexion_bdd();
    $reponse = $bdd->prepare('SELECT COUNT(*) as nb_exp FROM experiences WHERE id_candidat = ? ');
                    $reponse->execute(array($_SESSION['id'])); 

    $nb_exp = $reponse->fetch();

    if($nb_exp["nb_exp"]>0)
    {
        $reponse2 = $bdd->prepare('SELECT id,nom_poste,organisation,description_exp,date_debut,date_fin FROM experiences WHERE id_candidat = ? ');
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

    $nb_form = $reponse->fetch();

    if($nb_form["nb_formation"]>0)
    {
        $reponse2 = $bdd->prepare('SELECT id,nom_structure,diplome,description_formation,date_debut,date_fin FROM formations WHERE idc = ? ');
                    $reponse2->execute(array($_SESSION['id']));
        return $reponse2;
    }
    else
        return "";
}

function maj_formation()
{
    $bdd = connexion_bdd();
    //recupération de l'id d'experience mis dans un input invisible.
    $formation_id = $_POST["formation_id"]; 
    $reponse = $bdd->prepare('SELECT nom_structure,diplome,description_formation,date_debut,date_fin FROM formations WHERE id = ? ');
                    $reponse->execute(array($formation_id));
    
    $formation = $reponse->fetch();

    if($formation["nom_structure"]!=$_POST["nom_structure$formation_id"])
    {
        $mettre_a_jour =  $bdd->prepare("UPDATE formations SET nom_structure = :modif_nom_s WHERE id = :id");
                        $mettre_a_jour->execute(array(
                            ':modif_nom_s' => $_POST["nom_structure$formation_id"],
                            ':id'=> $formation_id));
                            $mettre_a_jour->closeCursor();
    }

    if($formation["diplome"]!=$_POST["diplome$formation_id"])
    {
        $mettre_a_jour =  $bdd->prepare("UPDATE formations SET diplome = :modif_dip WHERE id = :id");
                        $mettre_a_jour->execute(array(
                            ':modif_dip' => $_POST["diplome$formation_id"],
                            ':id'=> $formation_id));
                            $mettre_a_jour->closeCursor();
    }

    if($formation["description_formation"]!=$_POST["description_formation$formation_id"])
    {
        $mettre_a_jour =  $bdd->prepare("UPDATE formations SET description_formation = :modif_descrip WHERE id = :id");
                        $mettre_a_jour->execute(array(
                            ':modif_descrip' => $_POST["description_formation$formation_id"],
                            ':id'=> $formation_id));
                            $mettre_a_jour->closeCursor();
    }

    if(format_date_zone($formation["date_debut"])!=$_POST["formation_date_debut$formation_id"])
    {
        $mettre_a_jour =  $bdd->prepare("UPDATE formations SET date_debut = :modif_date_debut WHERE id = :id");
                        $mettre_a_jour->execute(array(
                            ':modif_date_debut' => $_POST["formation_date_debut$formation_id"],
                            ':id'=> $formation_id));
                            $mettre_a_jour->closeCursor();
    }

    if(format_date_zone($formation["date_fin"])!=$_POST["formation_date_fin$formation_id"])
    {
        $mettre_a_jour =  $bdd->prepare("UPDATE formations SET date_fin = :modif_date_fin WHERE id = :id");
                        $mettre_a_jour->execute(array(
                            ':modif_date_fin' => $_POST["formation_date_fin$formation_id"],
                            ':id'=> $formation_id));
                            $mettre_a_jour->closeCursor();
    }
}

function maj_exp()
{
    $bdd = connexion_bdd();
    //recupération de l'id d'experience mis dans un input invisible.
    $exp_id = $_POST["exp_id"]; 
    $reponse = $bdd->prepare('SELECT nom_poste,organisation,description_exp,date_debut,date_fin FROM experiences WHERE id = ? ');
                    $reponse->execute(array($exp_id));
    
    $experience = $reponse->fetch();

    if($experience["nom_poste"]!=$_POST["nom_poste$exp_id"])
    {
        $mettre_a_jour =  $bdd->prepare("UPDATE experiences SET nom_poste = :modif_poste WHERE id = :id");
                        $mettre_a_jour->execute(array(
                            ':modif_poste' => $_POST["nom_poste$exp_id"],
                            ':id'=> $exp_id));
                            $mettre_a_jour->closeCursor();
    }

    if($experience["organisation"]!=$_POST["nom_orga$exp_id"])
    {
        $mettre_a_jour =  $bdd->prepare("UPDATE experiences SET organisation = :modif_orga WHERE id = :id");
                        $mettre_a_jour->execute(array(
                            ':modif_orga' => $_POST["nom_orga$exp_id"],
                            ':id'=> $exp_id));
                            $mettre_a_jour->closeCursor();
    }

    if($experience["description_exp"]!=$_POST["exp_orga$exp_id"])
    {
        $mettre_a_jour =  $bdd->prepare("UPDATE experiences SET description_exp = :modif_descrip WHERE id = :id");
                        $mettre_a_jour->execute(array(
                            ':modif_descrip' => $_POST["exp_orga$exp_id"],
                            ':id'=> $exp_id));
                            $mettre_a_jour->closeCursor();
    }

    if(format_date_zone($experience["date_debut"])!=$_POST["exp_date_debut$exp_id"])
    {
        $mettre_a_jour =  $bdd->prepare("UPDATE experiences SET date_debut = :modif_date_debut WHERE id = :id");
                        $mettre_a_jour->execute(array(
                            ':modif_date_debut' => $_POST["exp_date_debut$exp_id"],
                            ':id'=> $exp_id));
                            $mettre_a_jour->closeCursor();
    }

    if(format_date_zone($experience["date_fin"])!=$_POST["exp_date_fin$exp_id"])
    {
        $mettre_a_jour =  $bdd->prepare("UPDATE experiences SET date_fin = :modif_date_fin WHERE id = :id");
                        $mettre_a_jour->execute(array(
                            ':modif_date_fin' => $_POST["exp_date_fin$exp_id"],
                            ':id'=> $exp_id));
                            $mettre_a_jour->closeCursor();
    }
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

