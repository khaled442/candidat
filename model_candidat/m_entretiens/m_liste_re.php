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


function afficher_re()
{
    
    $bdd = connexion_bdd();

    $id = $_SESSION["id"];

    $minimum_un = $bdd->query("SELECT COUNT(*) as total FROM re_commentaires WHERE id_candidat = $id");
    $resultat = $minimum_un->fetch();

    if($resultat["total"]>0)
        $reponse = $bdd->query("SELECT n_organisation,id_entretien,date_re_com FROM re_commentaires WHERE id_candidat = $id");
    else
        $reponse ="rien";
    
    return $reponse;
}

//Pour récupérer l'id de l'organisateur ou l'organisatrice de l'entretien
function id_orga_entretien($ide)
{
    $bdd = connexion_bdd();

    $reponse = $bdd->query("SELECT id_createur_offre FROM entretiens WHERE id = $ide ");

    $id = $reponse->fetch();
    
    return $id["id_createur_offre"];
}

function id_offre($ide)
{
    $bdd = connexion_bdd();

    $reponse = $bdd->query("SELECT id_offre FROM entretiens WHERE id = $ide ");

    $id = $reponse->fetch();
    
    return $id["id_offre"];
}


function nb_re()//obtenir le nombre de rapport d'entretien 
{
    $re = afficher_re();
    $nb_re = 0;

    $i=0;
    if($re !="rien")
    {
        foreach($re as $rapport)
        {

                        $id_actuel = $rapport["id_entretien"];

                        if($i==0)
                        {
                        
                        $precedent_id = $rapport["id_entretien"]; $nb_re++;
                        }

                        if($precedent_id != $id_actuel && $i!=0)
                        {

                            $precedent_id = $rapport["id_entretien"]; $nb_re++;
                        } 
                        $i++; 
        }
    }
    return $nb_re;
                
}

function distinct_affichage_rapports_entretiens() 
//pour ne pas prendre chaque message d'un re comme un re à part entière et avoir un mauvais résultat dans l'affichage des re
{

    $re = afficher_re();
    $nb_re = 0;

    $i=0;

    $infos = array();
    if($re !="rien")
    {
            while($message_re = $re->fetch())
            {
                $id_actuel = $message_re["id_entretien"];

                if($i==0)
                {
                   
                    $precedent_id = $message_re["id_entretien"];
                    $infos[$i]["n_organisation"] = $message_re["n_organisation"];
                    $infos[$i]["id_entretien"] = $message_re["id_entretien"];
                    $infos[$i]["date_re_com"] = $message_re["date_re_com"];  
                }

                if($precedent_id != $id_actuel && $i!=0)
                {
                    
                    $precedent_id = $message_re["id_entretien"]; 
                    $infos[$i]["n_organisation"] = $message_re["n_organisation"];
                    $infos[$i]["id_entretien"] = $message_re["id_entretien"];
                    $infos[$i]["date_re_com"] = $message_re["date_re_com"];   

                }
                
                $i++;
            }

            return $infos;
    }
    else
        return "rien";
}


function obtenir_nb_pages()
{
    $_SESSION["nb_pages_re"] = 0;

                if((nb_re() % $_SESSION["aff_max_re"]) != 0)
                $_SESSION["nb_pages_re"] = 1;

                $_SESSION["nb_pages_re"] += (int) (nb_re() / $_SESSION["aff_max_re"]);
            echo "Nombre de pages: ".$_SESSION["nb_pages_re"];
            echo " Nombre de rapports d'entretien: ".nb_re();

            echo"<br><br>";
}


function vers_page_droite()
{
    $_SESSION["re_depart"]+=$_SESSION["aff_max_re"];
    $_SESSION["re_n_page_actuelle"] +=1;
}

function vers_page_gauche()
{
    $_SESSION["re_depart"]-=$_SESSION["aff_max_re"];
    $_SESSION["re_n_page_actuelle"] -=1;
}
