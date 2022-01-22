<?php

ob_start(); 

 
      include("../candidat/model_candidat/m_entretiens/m_liste_entretiens.php");  ?>

        <?php   $pack = afficher_entretiens_c();

            //remplir_bdd();

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

            if(!isset($_SESSION["aff_max_en"]))
           {
               $_SESSION["aff_max_en"] = 10;
   
           }

           if(!isset($_SESSION["en_depart"]))
            {
                $_SESSION["en_depart"] = 0;
                $_SESSION["en_n_page_actuelle"] = 1; 
            }

           if(isset($_POST["form_nb_aff_max_en"]))
            {
                $_SESSION["aff_max_en"] = $_POST["nb_max_aff_en"];

                $_SESSION["en_depart"] = 0;
                $_SESSION["en_n_page_actuelle"] = 1; 
            }

            if($pack!="rien")
                obtenir_nb_pages();

          ?>


        <p> Entretiens: Max à afficher par page   
        <form method="post" action="../candidat/candidat.php?ce&action=voirentretiens">
            <select name="nb_max_aff_en">
                <option value="10" 
                    <?php if($_SESSION["aff_max_en"]==10) echo " selected"; ?>
                >10</option>
                <option value="15" 
                    <?php if($_SESSION["aff_max_en"]==15) echo " selected"; ?>
                >15</option>
                <option value="20" 
                    <?php if($_SESSION["aff_max_en"]==20) echo " selected"; ?>
                >20</option>
            </select>
              
            <input type="submit" name="form_nb_aff_max_en" value="Changer">
            </form>
        </p>


          <?php

           
            if($pack!="rien")
            {
              $e = 0; //pour limiter le nombre d'entretiens' à affiché dans une page
              $a = 0; //le compteur qui détermine avec la variable re_depart à partir de quel entretiens
              //commencé l'affichage

              while(($entretien = $pack->fetch()) && $e<$_SESSION["aff_max_en"])
              {
                if($a==$_SESSION["en_depart"])
                {
                  echo "Organisation: ".$entretien["organisation"]."  Poste: ".$entretien["poste_offre"]."<br>Date: ".format_date_zone($entretien["date_heure_entretien"]).
                  ". Statut entretien: ".$entretien["statut_entretien"];
                  
                  ?>

                  <form method="post" action="../candidat/candidat.php?action=pleconverser&ce&<?php echo"ide=".$entretien["id"]."&iduo=".$entretien["id_createur_offre"]."&ido=".$entretien["id_offre"]; ?>">
                  <input type="submit" name="c_converser" value="Converser"/> 
                  </form> <br>

                <?php echo "<br><br>";
                $e++;
                }

                if($a!=$_SESSION["en_depart"])
                {
                     $a++;
                }
              }

              
              
             //Afficher les boutons uniquement tant qu'il y a des infos à affiché
              //dans tel ou tel direction


              echo "Page ".$_SESSION["en_n_page_actuelle"]."/".$_SESSION["nb_pages_en"];

                    if($_SESSION["en_n_page_actuelle"]!=$_SESSION["nb_pages_en"])
                    echo'
                    <form method="post" action="../candidat/candidat.php?ce&action=voirentretiens">
                    <input type="submit" name="direction_page_droite" value=">">
                    </form> ';


                    if($_SESSION["en_n_page_actuelle"]!=1)
                    echo'
                    <form method="post" action="../candidat/candidat.php?ce&action=voirentretiens">
                    <input type="submit" name="direction_page_gauche" value="<">
                    </form> ';


            }
            

        ?>
        
        <?php $content = ob_get_clean(); ?>

<?php require('vues_candidat/template_c.php'); ?>