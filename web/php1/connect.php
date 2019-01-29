<?php
if(!isset($_GET['login']) && !isset($_GET['motdepasse']))
{
    header('Location: event.html');
}
else
{
    // On va vérifier les variables pas forcément utile peyton!!
    if(!preg_match('/^[[:alnum:]]+$/', $_GET['login']) or
!preg_match('/^[[:alnum:]]+$/', $_GET['motdepasse']))
    {
        echo 'Vous devez entrer uniquement des lettres ou des chiffres <br/>';
        echo '<a href="event3.html" temp_href="event3.html">Réessayer</a>';
        exit();
    }
    else
    {
        require('config.php'); // On réclame le fichier

        $login = $_GET['login'];
        $motdepasse = $_GET['motdepasse'];

        $sql = "SELECT * FROM table_utilisateur WHERE user='".mysql_escape_string($login)."'";

        // On vérifie si ce login existe
        $requete_1 = mysql_query($sql) or die ( mysql_error() );

        if(mysql_num_rows($requete_1)==0)
        {
            echo 'Ce login nexiste pas ! <br/>';
            echo '<a href="event3.html" temp_href="event3.html">Réessayer</a>';
            exit();
        }
        else
        {
            // On vérifie si le login et le mot de passe correspondent au compte utilisateur
            $requete_2 = mysql_query($sql." AND pass='".mysql_escape_string($motdepasse)."'")
or die ( mysql_error() );

            if(mysql_num_rows($requete_2)==0)
            {
                // On va récupérer les résultats
                $result = mysql_fetch_array($requete_1, MYSQL_ASSOC);

                // On va récupérer la date de la dernière connexion
                $lastconnection = explode(' ', $result["dates"]);
                $lastjour = explode('-', $lastconnection[0]);

                // On va récupérer le nombre de tentative et l'affecter
                $nbr_essai = $result["nbr_connect"];

                if($lastjour[2]==date("d") && $MAX_essai==$nbr_essai)
                {
                    echo 'Vous avez atteint le quota de tentative, essayez demain !<br/>';
                    exit();
                }
                else
                {
                    $nbr_essai++;
                    $update = "UPDATE table_utilisateur SET nbr_connect='".$nbr_essai."', dates=NOW()
WHERE id='".$result["id"]."'";

                    mysql_query($update) or die ( mysql_error() );

                    echo 'Le mot de passe et/ou le login sont incorrectes <br/>';
                    echo '<a href="event3.html" href="event3.html">Réessayer</a>';
                    exit();
                }
            }
            else
            {
                // On va récupérer les résultats
                $result = mysql_fetch_array($requete_2, MYSQL_ASSOC);

                $nbr_essai = 0;
                $update = "UPDATE table_utilisateur SET nbr_connect='".$nbr_essai."', dates=NOW()
WHERE id='".$result["id"]."'";

                mysql_query($update) or die ( mysql_error() );

                // On redirige vers la partie membre
                header('Location: membres/event3.html');
            }
        }
    }
}
?>