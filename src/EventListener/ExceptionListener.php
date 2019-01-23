<?php
/**
 * Created by PhpStorm.
 * User: indi
 * Date: 23/01/2019
 * Time: 20:40
 */

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event){
        // Obtienes el objeto exception del evento recibido
        $exception = $event->getException();
        $message = sprintf('Mi error dice: %s en el código: %s', $exception->getMessage(), $exception->getCode());

        // Modifica tu response object para mostrar los detalles de la excepcion
        $response = new Response();
        $response->sendContent($message);

        // HttpExceptionInterface es un tipo especial de excepción
        // que mantiene los detalles de header y código de estado
        if($exception instanceof HttpExceptionInterface){
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Enviar el objeto Response modificado al evento
        $event->setResponse($response);
    }
}