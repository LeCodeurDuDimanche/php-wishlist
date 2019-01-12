<?php
    namespace mywishlist\controleurs;

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use mywishlist\models\Liste;

    class Utils{

        /**
        * Permet de faire une redirection à partir d'un objet réponse et d'un nom de route défini, avec de potentiels arguments
        */
        public static function redirect(ResponseInterface $response, $route, $args = [])
        {
            global $app;
            return $response->withRedirect($app->getContainer()->get('router')->pathFor($route, $args));
        }


        /**
        * Check si une date est valide (compréhensible par create_date)
        */
        public static function isValidDate(string $date) : bool
        {
            try{
                new \DateTime($date);
            } catch(Exception $e)
            {
                return false;
            }
            return true;
        }

        /**
        * Permet de sanitize une string (vis-à-vis de l'affichage HTML seulement)
        */
        public static function sanitize(string $unsafe) : string
        {
            return strip_tags($unsafe);
        }

        /**
        * Permet de récupérer une variable POST et de la filtrer
        * Retourne null si $key n'est pas présentes dans la requête
        */
        public static function getFilteredPost(ServerRequestInterface $request, string $key)
        {
            $data = $request->getParsedBodyParam($key, null);
            return $data === null ? null : self::sanitize($data);
        }

        /**
        * Permet d'ajouter ou de modifier le cookie de liste pour la liste $liste
        */
        public static function setListeCookie(Liste $liste)
        {
            $data = ["token" => $liste->tokenCreateur, "createur" => $liste->createur()];

            /* On recupere la date depuis expiration, qui peut etre une chaine ('AAAA-MM-JJ')
            * ou une instance de Carbon\Carbon (qui étend DateTime) suivant que la liste ait ete sauvegardee ou non
            */
            $date = $liste->expiration instanceof \DateTime ? $liste->expiration : date_create($liste->expiration);
            //Si la date calculee est dans le passee, on prend la date d'aujourd'hui
            $now = new \DateTime();
            if ($date->getTimestamp() < $now->getTimestamp())
                $date = $now;

            setcookie("liste".$liste->id, json_encode($data), $date->getTimestamp() + 3600*24*60, "/");
        }

        /**
        * Permet de retourner les donnes du cookie de liste pour la liste d'id $id, null si malformé ou absent
        */
        public static function getListeCookie(int $id)
        {
            return isset($_COOKIE["liste$id"]) ? json_decode($_COOKIE["liste$id"]) : null;
        }

        /**
        * Retourne un tableau des id des liste valide contenues dans les cookies utilisateurs
        * $includeUserLists doit être vrai si on veut aussi les listes appartenant à des utilisateurs
        */
        public static function getValidListesCookie($includeUserLists = false) : array
        {
            $listesValides = [];
            foreach ($_COOKIE as $name => $val)
            {
                if (strpos($name, "liste") === 0)
                {
                    $id = intval(substr($name, 5));
                    $liste = Liste::find($id);
                    $data = json_decode($val);
                    if ($liste !== null && $data !== null && //Id et valeur du cookie correct
                        isset($data->token) && $liste->tokenCreateur === $data->token //Token createur correct
                        && ($includeUserLists || $liste->user_id === null)
                        && isset($data->createur) && $liste->createur() === $data->createur) //Liste n'appartenant pas a un compte et nom createur correct
                    {
                        array_push($listesValides, $liste->id);
                    }
                }
            }
            return $listesValides;
        }

        /**
        * Destiné à être utilisé comme filtre Twig
        * Le paramètre $includeTime permet de prendre en compte ou non les heures, minutes et secondes en plus de la date
        * Le paramètre $threesold définit, si $includeTime est vrai, le nombre de secondes à partir duquel on considère qu'on est "maintenant"
        */
        public static function timeDiffTwigFilter($time, bool $includeTime = true, int $threesold = 30) : string{
            $dateTime = $time instanceof \DateTime ? $time : date_create($time);
            $diff = $dateTime->diff(date_create());

            //On check le sens de l'interval
            if ($diff->invert)
                $prefix = "dans";
            else
                $prefix = "il y a";

            //On récupère le temps passé par ordre décroissant d'importance
            if ($diff->y)
                $suffix = $diff->y . " an" . ($diff->y > 1 ? "s" : "");
            else if ($diff->m)
                $suffix = $diff->m . " mois";
            else if ($diff->d || !$includeTime) //Si on inclut pas le temps, alors on s'arrete forcément là
            {
                //String customisées pour demain, aujourd'hui et hier
                if ($diff->d == 0)
                {
                    $prefix = "";
                    $suffix = "aujourd'hui";
                }
                else if($diff->d == 1)
                {
                    $prefix = "";
                    if ($diff->invert)
                        $suffix = "demain";
                    else
                        $suffix = "hier";
                }
                else
                    $suffix = $diff->d . " jour" . ($diff->d > 1 ? "s" : "");
            }
            else if ($diff->h)
                $suffix = $diff->h . " heure" . ($diff->h > 1 ? "s" : "");
            else if ($diff->i)
                $suffix = $diff->i . " minute" . ($diff->i > 1 ? "s" : "");
            else if ($diff->s > $threesold) //Si moins de $threesold secondes sont passées, on considère que c'est "maintenant"
            {
                $suffix = $diff->s . " seconde" . ($diff->h > 1 ? "s" : "");
            }
            else
                return "maintenant";

            return $prefix . " " . $suffix;

        }

        /**
        * Destiné à être utilisé comme fonction Twig
        * Le paramètre $format permet de parametrer le format voulu, par défaut est lundi 01 janvier 1970
        * Le format est celui accepté par strftime
        * La locale doit être correctement définie pour LC_TIME pour un résultat correct
        */
        public static function formatTwigFunction($time, string $format = "%A %e %B %Y") : string{
            return $time instanceof Illuminate\Support\Carbon ?
                $time->formatLocalized($format) :
                strftime($format, (new \DateTime($time))->getTimestamp());
        }

        /**
        * Affiche une page d'erreur personalisee
        */
        public static function notFound(ServerRequestInterface $request, ResponseInterface $response) {
            global $app;
            //On met a jour les donnees flash
            Flash::next();

            return $app->getContainer()->view->render($response, "erreur404.html")->withStatus(404);
        }
    }
