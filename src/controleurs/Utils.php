<?php
    namespace mywishlist\controleurs;

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

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
        public static function getFilteredPost(ServerRequestInterface $request, string $key) : string
        {
            $data = $request->getParsedBody($key, null);
            return $data === null ? null : sanitize($data);
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
    }
