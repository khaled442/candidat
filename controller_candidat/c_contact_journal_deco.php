<?php

function accueil_candidat()
{
   
    require(__DIR__."/../vues_candidat/v_journal.php");
}

function deconnexion()
{
    session_start();
    session_destroy();
    header("Location: ../index.php");
}

function journal()
{
    require(__DIR__."/../vues_candidat/v_journal.php");
}

function contact()
{
    require(__DIR__."/../vues_candidat/v_contact.php");
}