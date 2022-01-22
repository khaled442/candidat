<?php

ob_start(); 

 
      include("../candidat/model_candidat/m_entretiens/m_details_entretien.php");  ?> 

         <?php 

          if(isset($_POST["form_accepter"]))
          {
            accepter_refuser_entretien(1); // paramètre 1 pour accepter 
          }
          if(isset($_POST["form_refuser"]))
          {
            accepter_refuser_entretien(0);
          }


           $details_entretien = afficher_details_entretien();

           $details_offre = $details_entretien->fetch();

           echo "Organisation: ".$details_offre["organisation"]."<br>";
           echo "Candidature pour le poste de ".$details_offre["poste_offre"]."<br>";
           echo "Date candidature ".date_candidature($_SESSION["id"],$details_offre["id_offre"])."<br><br>";
           echo "description du poste : ".$details_offre["description_offre"]."<br><br>";
           echo "Date et heure de l'entretien: ".$details_offre["date_heure_entretien"]."<br><br>";
           echo "statut de l'entretien: ".$details_offre["statut_entretien"]."<br><br>";

           $raison_modif = raison_modification_offre();
           if($raison_modif["raison_modification"]!=null)
           {
             echo "Offre liée à la candidature mise à jour: ".$raison_modif["raison_modification"];
           }
           



           ?>

           <form method="post" action="../candidat/candidat.php?action=pleconverser&ce">
        
            <input type="submit" name="form_accepter" value="Accepter"/>
            <input type="submit" name="form_refuser" value="Refuser"/>
            </form>

            

          <?php
        
           if(isset($_POST["form_message_c"]))
           {
                  envoyer_message($_POST["message_c"]);
           }
  
           $conversation = afficher_la_conversation();
       
              
           while($message = $conversation->fetch())
           {
  
               echo $message["nom_emetteur"]." - date: ".format_date_zone($message["date_message"])." : ".$message["messages_offres"]."<br>";
           } 
           
           ?>

           Envoyer un message:
           <form method="post" action="../candidat/candidat.php?action=pleconverser&ce">
           <textarea name="message_c" cols="30" rows="5"></textarea>
      
          <input type="submit" name="form_message_c" value="Envoyer"/> 
          </form>

          <?php
           $commentaires= afficher_re_commentaires();
           echo "Commentaires des membres de l'entreprise:<br>";
          $i=1;
           while($commentaire = $commentaires->fetch())
           {

               echo "Membre ".$i.": ".$commentaire["commentaires"]."<br>"; $i++;
           } 
         
         ?>

<?php $content = ob_get_clean(); ?>

<?php require('vues_candidat/template_c.php'); ?>