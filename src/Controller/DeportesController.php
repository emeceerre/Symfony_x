<?php
/**
 * Created by PhpStorm.
 * User: indi
 * Date: 15/01/2019
 * Time: 11:55
 */

namespace App\Controller;
use App\Entity\Noticia;
use Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory;
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
        return $this->render("base.html.twig");
    }

//    /**
//     * @Route("/deportes/{slug}")
//     */
//    public function mostrar($slug){
//        return new Response(sprintf('Mi artículo en mi página de deportes: ruta %s', $slug));
//    }

//    /**
//     * @Route("/deportes/{seccion}/{slug}", defaults ={"seccion":"tenis"})
//     */
//    public function noticia($slug, $seccion){
//        return new Response(sprintf('Noticia de %s, con url dináimica=%s', $seccion, $slug));
//    }

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
    public function lista($page = 1,$seccion){

        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Noticia::class);

        // buscamos las noticias de una sección
        $noticiaSec = $repository->findOneBy(['seccion' => $seccion]);

        // si el deporte que buscamos no está en  sección no existe lanzamos una excepción
        // 404 deporte no encontrado
        if(!$noticiaSec){
            throw  $this->createNotFoundException('Error 404 este deporte no está en nuestra Base de Datos');
        }

        // Almacenamos todas las noticias de una seccion en una lista
        $noticias = $repository->findBy(["seccion" => $seccion]);

        //return new Response("Hay un total de ".count($noticias)." noticias de la sección ". $seccion);
        // La función str_replace elimina los símbolos - de los títulos
        return $this->render('noticias/listar.html.twig', [
            'titulo' => ucwords(str_replace('-', ' ', $seccion)),
            'noticias' => $noticias
        ]);
    }

    /**
     * @Route("/deportes/{seccion}/{titular}", name="verNoticia",
     *     defaults={"seccion": "tenis"})
     */
    public function noticia($titular, $seccion){
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Noticia::class);
        $noticia = $repository->findOneBy(['textoTitular' => $titular]);

        // Si la noticia que buscamos no se encuentra, lanzamos eror404
        if(!$noticia){
            // Ahora que controlamos el manejo de la plantilla twig, vamos a
            // redirigir al usuario a la página de inicio
            // y mostraremos el error 404, para así no mostrar la página
            // de errores genérica de symfony

            //throw $this->createNotFoundException('Error 404, este deporte no está en la Base de Datos.');
            return $this->render('base.html.twig', ['texto' => 'Error 404 Página no encontrada']);
        }
        return $this->render('noticias/noticia.html.twig', [
            // Parseamos el titular para quitar los símbolos -
            'titulo' => ucwords(str_replace('-',' ', $titular)),
            'noticias' => $noticia
        ]);
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

    /**
     * @Route("/deportes/cargarbd", name="noticia")
     */
    public function cargarBd(){
        $em = $this->getDoctrine()->getManager();

        $noticia = new Noticia();
        $noticia->setSeccion("Tenis");
        $noticia->setEquipo("roger-federer");
        $noticia->setFecha("16022018");

        $noticia->setTextoTitular("Roger-Federer-a-una-victoria-del-número-uno-de-Nadal");
        $noticia->setTextoNoticia("El suizo Roger Federer, el tenista más laureado de la historia, está a solo 
        un paso de regresar a la cima del tenis muncial a sus 36 años. Clasificado sin admitir ni réplica para cuartos
        de final del torneo de Rotterdam, si vence este viernes a Robin Haase se convertirá en el número uno del mundo...");
        $noticia->setImagen('federer.jpg');

        $em->persist($noticia);
        $em->flush();
        return new Response("Noticia guardada con éxito con id: ". $noticia->getId());
    }

    /**
     * @Route("/deportes/actualizar", name="actualizarNoticia")
     */
    public function actualizarBd(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('id');
        $noticia = $em->getRepository(Noticia::class)->find($id);

        $noticia->setTextoTitular("Roger-Federer-a-una-victoria-del-número-uno-de-Nadal");
        $noticia->setTextoNoticia("El suizo Roger Federer, el tenista más laureado de la historia, está a solo 
        un paso de regresar a la cima del tenis muncial a sus 36 años. Clasificado sin admitir ni réplica para cuartos
        de final del torneo de Rotterdam, si vence este viernes a Robin Haase se convertirá en el número uno del mundo...");
        $noticia->setImagen('federer.jpg');

        $em->flush();
        return new Response("Noticia actualizada");
    }

    /**
     * @Route("/deportes/eliminar", name="eliminarNoticia")
     */
    public function eliminarBd(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('id');
        $noticia = $em->getRepository(Noticia::class)->find($id);
        $em->remove($noticia);
        $em->flush();
        return new Response("Noticia eliminada");
    }


}