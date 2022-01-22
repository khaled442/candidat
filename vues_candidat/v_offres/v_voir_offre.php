<?php

ob_start(); 

 
      include("../candidat/model_candidat/m_offres/m_voir_offre.php");
        
        $_SESSION["offre_id"] = $_GET["ido"];

        $reponse = offre_detail();
        $offre_en_detail = $reponse->fetch();

        $_SESSION["id_createur_offre"] = $offre_en_detail["id_createur"];

        echo "<p>organisation: ".$offre_en_detail["nom_organisation"]."</p>";
        echo "<p>Type de contrat: ".$offre_en_detail["type_contrat"]."</p>";
        echo "<p>Poste: ".$offre_en_detail["poste"]."</p>";
        echo "<p>Salaire: ".$offre_en_detail["salaire"]."</p>";
        echo "<p>Description: ".$offre_en_detail["description_offre"]."</p>";
        echo "<p>Compétences: ".$offre_en_detail["competences"]."</p>";
        echo "<p>Lieu: ".$offre_en_detail["lieu"]."</p>";
        echo "<p>Duree: ".$offre_en_detail["duree"]."</p>";
        echo "<p>Rythme: ".$offre_en_detail["rythme"]."</p>";
        echo "<p>Date de publication: ".$offre_en_detail["date_publication"]."</p>";

        if($offre_en_detail["date_modification"]!=null)
          echo "<p>Offre mise à jour le: ".$offre_en_detail["date_modification"]."</p>";

        if($offre_en_detail["raison_modification"]!=null)
        {
            echo "<p>Offre mise à jour: ".$offre_en_detail["raison_modification"]."</p>";
        }
       
        
        ?>

        
        </form>
        <?php 
              if(deja_postulé($_SESSION["offre_id"])) 
                echo "Vous avez déja postulé à cette offre";
              else
                echo '<form method="post" action="../candidat/candidat.php?co&action=candidatures">
                <input type="submit" name="form_postuler" value="Postuler" />  
                </form>'; ?>

<?php $content = ob_get_clean(); ?>

<?php require('vues_candidat/template_c.php'); ?>