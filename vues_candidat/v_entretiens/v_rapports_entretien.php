<?php

ob_start(); 

 
      include("../candidat/model_candidat/m_entretiens/m_liste_re.php");  

            
           $rapports = distinct_affichage_rapports_entretiens();

            //Si on clique sur -> direction page de droite
            if(isset($_POST["direction_page_droite"]))
            {
                vers_page_droite();
            }

            //Si on clique sur <- direction page de gauche
            if(isset($_POST["direction_page_gauche"]))
            {
                vers_page_gauche();
            }

           if(!isset($_SESSION["aff_max_re"]))
           {
               $_SESSION["aff_max_re"] = 10;
   
           }

           if(!isset($_SESSION["re_depart"]))
            {
                $_SESSION["re_depart"] = 0;
                $_SESSION["re_n_page_actuelle"] = 1; 
            }

           if(isset($_POST["form_nb_aff_max_re"]))
            {
                $_SESSION["aff_max_re"] = $_POST["nb_max_aff_re"];

                $_SESSION["re_depart"] = 0;
                $_SESSION["re_n_page_actuelle"] = 1; 
            }
            
            if($rapports !="rien")    
                obtenir_nb_pages();

           ?>

        <p> Rapports d'entretiens: Max de rapports à afficher par page   
        <form method="post" action="../candidat/candidat.php?ce&action=voirre">
            <select name="nb_max_aff_re">
                <option value="10" 
                    <?php if($_SESSION["aff_max_re"]==10) echo " selected"; ?>
                >10</option>
                <option value="15" 
                    <?php if($_SESSION["aff_max_re"]==15) echo " selected"; ?>
                >15</option>
                <option value="20" 
                    <?php if($_SESSION["aff_max_re"]==20) echo " selected"; ?>
                >20</option>
            </select>
              
            <input type="submit" name="form_nb_aff_max_re" value="Changer">
            </form>
        </p>


        <?php

        if($rapports !="rien")
        {
            /*Veiller à ce que seul 1 message par rapport d'entretien s'affiche
                il faut enregistré l'id du premier entretien et le comparé à chaque tour avec
                le prochain id 
                
                dès que l'id est différent on affiche le résultat, ça signifie qu'un autre rapport d'entretien est trouvé
                on met à jour la variable précédent_id

                au premier tour il faudra affiché le premier message par defaut obligatoirement

                la variable $i va traiter le cas du premier tour
                */
                $i=0;
                $e = 0; //pour limiter le nombre de rapport à affiché dans une page
                $a = 0; //le compteur qui détermine avec la variable re_depart à partir de quel re
            //commencé l'affichage

            foreach($rapports as $rapport)
            {
                if($a==$_SESSION["re_depart"])
                {

                    $id_actuel = $rapport["id_entretien"];

                    if($i==0)
                    {
                    ?>

                            Rapport d'entretien de l'organisation <?php echo $rapport["n_organisation"]." initié le ".$rapport["date_re_com"]."<br>";?>
                            <form method="post" action="../candidat/candidat.php?action=pleconverser&ce&<?php echo "ide=".$rapport["id_entretien"]."&iduo=".id_orga_entretien($rapport["id_entretien"])."&ido=".id_offre($rapport["id_entretien"]);?>">
                            <input type="submit" value="Voir"/> 
                            </form> <br>

                    <?php
                        $precedent_id = $rapport["id_entretien"]; $e++;
                    }

                    if($precedent_id != $id_actuel && $i!=0 && $e<$_SESSION["aff_max_re"])
                    {?>

                        Rapport d'entretien de l'organisation <?php echo $rapport["n_organisation"]."<br>"; ?>
                        <form method="post" action="../candidat/candidat.php?action=pleconverser&ce&<?php echo "ide=".$rapport["id_entretien"]."&iduo=".id_orga_entretien($rapport["id_entretien"])."&ido=".id_offre($rapport["id_entretien"]);?>">
                        <input type="submit" value="Voir"/> 
                        </form> <br>      
                    <?php

                            $precedent_id = $rapport["id_entretien"]; $e++;
                    } 
                    $i++; 
                }

                if($a!=$_SESSION["re_depart"])
                {
                     $a++;
                }
            }

           ?>
           <?php
            //Afficher les boutons uniquement tant qu'il y a des infos à affiché
            //dans tel ou tel direction
            if($rapports != "rien")
            {  
                echo "Page ".$_SESSION["re_n_page_actuelle"]."/".$_SESSION["nb_pages_re"];
               
                    

                    if($_SESSION["re_n_page_actuelle"]!=$_SESSION["nb_pages_re"])
                    echo'
                    <form method="post" action="../candidat/candidat.php?ce&action=voirre">
                    <input type="submit" name="direction_page_droite" value=">">
                    </form> ';


                    if($_SESSION["re_n_page_actuelle"]!=1)
                    echo'
                    <form method="post" action="../candidat/candidat.php?ce&action=voirre">
                    <input type="submit" name="direction_page_gauche" value="<">
                    </form> ';
                
                
                
            }
        }
        ?>
      
      <?php $content = ob_get_clean(); ?>

<?php require('vues_candidat/template_c.php'); ?>