<?php
    namespace mywishlist\controleurs;

    class Utils{

        public static function redirect($response, $route, $args = [])
        {
            global $app;
            return $response->withRedirect($app->getContainer()->get('router')->pathFor($route, $args));
        }

        public static function timeDiffTwigFilter($time){
            $dateTime = $time instanceof \DateTime ? $time : date_create($time);
            $diff = $dateTime->diff(date_create());

            $threesold = 30;

            if ($diff->y)
                $suffix = $diff->y . " an" . ($diff->y > 1 ? "s" : "");
            else if ($diff->m)
                $suffix = $diff->m . " mois";
            else if ($diff->d)
                $suffix = $diff->d . " jour" . ($diff->d > 1 ? "s" : "");
            else if ($diff->h)
                $suffix = $diff->h . " heure" . ($diff->h > 1 ? "s" : "");
            else if ($diff->i)
                $suffix = $diff->i . " minute" . ($diff->i > 1 ? "s" : "");
            else if ($diff->s > $threesold)
            {
                $suffix = $diff->s . " seconde" . ($diff->h > 1 ? "s" : "");

                if ($diff->invert)
                    $prefix = "il y a";
                else
                    $prefix = "dans";
            }
            else
                return "maintenant";



            return $prefix . " " . $suffix;

        }

        public static function formatTwigFunction($time, $format = "%A %e %B %Y"){
            return $time instanceof Illuminate\Support\Carbon ?
                $time->formatLocalized($format) :
                strftime($format, (new \DateTime($time))->getTimestamp());
        }
    }
