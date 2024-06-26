<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use App\Exception\UnreachableResourceContentException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = new Response();

        if ($exception instanceof UnreachableResourceContentException) {
            $message = $exception->getMessage();

            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        } 
        elseif ($exception instanceof ClientExceptionInterface) {
            $message = $exception->getMessage();

            $response->setStatusCode(Response::HTTP_PROCESSING);
        }
        else {
            $message = "An internal error occured during request processing.";

            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response->setContent(json_encode([
            "errors" => $message
        ]));

        $event->setResponse($response);
    }
}