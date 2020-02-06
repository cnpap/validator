<?php

namespace Suolong\Validator;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidateFail implements MiddlewareInterface
{
    function process(ServerRequestInterface $request, RequestHandlerInterface $handle): ResponseInterface
    {
        try {
            return $handle->handle($request);
        } catch (ValidateFailException $e) {
            $conf = $request->getAttribute('conf')['validator'];
            $params = $conf[0][$e->path] ?? $e->path;
            $message = $conf[1][$e->ruleName] ?? $e->ruleName;
            if ($e->ruleParams) {
                $params .= ',' . $e->ruleParams;
            }
            $params = explode(',', $params);
            http_response_code(404);
            exit(sprintf($message, ...$params));
        } catch (Exception $e) {
            throw $e;
        }
    }
}
