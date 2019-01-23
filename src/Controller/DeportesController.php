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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class DeportesController extends Controller
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

    /**
     * @Route("/deportes/{seccion}/{slug}", defaults ={"seccion":"tenis"})
     */
    public function noticia($slug, $seccion){
        return new Response(sprintf('Noticia de %s, con url dináimica=%s', $seccion, $slug));
    }

    /**
     * @Route("/deportes/{_locale}/{fecha}/{seccion}/{equipo}/{pagina}",
     *     defaults={"pagina":"1"},
     *     requirements={"_locale": "es|en", "fecha": "[\d+]{8}", "pagina": "\d+"}
     * )
     */
    public function rutaAvanzadaListado($_locale, $fecha, $seccion, $equipo, $pagina)
    {
        return new Response(sprintf('Listado de noticias en idioma=%s, fecha=%s, deporte=%s, equipo=%s, página=%s',
            $_locale, $fecha, $seccion, $equipo, $pagina));
    }

    /**
     * @Route("/deportes/{_locale}/{fecha}/{seccion}/{equipo}/{slug}.{_format}",
     *     defaults = {"slug": "1", "_format":"html"},
     *     requirements={ "_locale": "es|en",
     *                    "_format": "html|json|xml",
     *                    "fecha": "[\d+]{8}"
     *     }
     * )
     */
    public function rutaAvanzada($_locale, $fecha, $seccion, $equipo, $slug){
        // Simulamos una base de datos de equipos o personas
        $sports = ["valencia", "barcelona", "federer", "rafa-nadal"];

        // Si el equipo o persona que buscamos no se encuentra, redirigimos al usuario a la página de inicio
        if(!in_array($equipo,$sports)){
            return $this->redirectToRoute('lista_paginas', array('seccion'=>$seccion,'page'=>"1"));
        }
        return new Response(sprintf('Mi noticia en idioma=%s, fecha=%s, deporte=%s, 
        equipo=%s, noticia=%s', $_locale, $fecha, $seccion, $equipo, $slug));
    }

    /**
     * @Route("/deportes/{seccion}/{page}", name="lista_paginas",
     *     requirements={"page"="\d+"},
     *     defaults={"seccion":"tenis"})
     */
    public function lista($seccion, $page){
        // Simulamos una base de datos de deportes
        $sports = ["futbol", "tenis", "rugby"];

        // Si el deporte que buscamos no se encuentra, lanzamos la excepcion 404 deporte no encontrado
        if(!in_array($seccion, $sports)){
            throw $this->createNotFoundException('Error 404 este deporte no está en nuestra Base de Datos');
        }
        return new Response(sprintf('Deportes seccion: %s, listado de noticias página: %s', $seccion, $page));
    }

    /**
     * @Route("/deportes/usuario", name="usuario")
     */
    public function sesionUsuario(Request $request){
        $usuario_get = $request->query->get('nombre');
        $session = $request->getSession();
        $session->set('nombre', $usuario_get);
        return $this->redirectToRoute('usuario_session', array('nombre'=>$usuario_get));
    }

    /**
     * @Route("/deportes/usuario/{nombre}", name="usuario_session")
     */
    public function paginaUsuario(){
        $session = new Session();
        $usuario = $session->get('nombre');
        return new Response(sprintf('Sesion iniciada con el atributo nombre: %s', $usuario));
    }
}