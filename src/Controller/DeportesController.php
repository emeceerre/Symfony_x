<?php
/**
 * Created by PhpStorm.
 * User: indi
 * Date: 15/01/2019
 * Time: 11:55
 */

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class DeportesController
{
    /**
     * @Route("/")
     */
    public function inicio(){
        return new Response('Mi primera página en Symfony con annotation!');
    }

    /**
     * @Route("/deportes")
     */
    public function deportes_inicio(){
        return new Response('Mi página de Deportes!');
    }

    /**
     * @Route("/deportes/{slug}")
     */
    public function mostrar($slug){
        return new Response(sprintf('Mi artículo en mi página de deportes: ruta %s', $slug));
    }


}