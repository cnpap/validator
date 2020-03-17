<?php

namespace Suolong\Validator;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidateFail implements MiddlewareInterface
{
    const TEMPLATE  = '%s 验证出错: ';
    const TEMPLATES = [
        'must'          => '%s 是必要的',
        'int'           => '%s 必须是数字',
        'intIn'         => '%s 不符合预期',
        'intMax'        => '%s 不可超过 %d',
        'intBetween'    => '%s 必须介于 %d - %d',
        'string'        => '%s 必须是字符串',
        'stringIn'      => '%s 不符合预期',
        'stringMax'     => '%s 不能超过 %d 个字符',
        'stringLength'  => '%s 应该由 %d 个字符组成',
        'stringBetween' => '%s 字符宽度介于 %d - %d',
        'array'         => '%s 应该是一组数据',
        'arrayMax'      => '%s 的数据不能超过 %d 个',
        'arrayBetween'  => '%s 的数据数量介于 %d - %d',
        'safe'          => '%s 不能包含特殊字符、空白 等',
        'phone'         => '%s 应该是一个 11位 的手机号',
        'eq'            => '%s 应该等价于 %s',
        'notEq'         => '%s 不能等价于 %s'
    ];

    function process(ServerRequestInterface $request, RequestHandlerInterface $handle): ResponseInterface
    {
        try {
            Validator::$handles[] = new ValidateHandle;
            return $handle->handle($request);
        } catch (ValidateFailException $e) {
            $conf        = $request->getAttribute('validator') ?? [];
            $translation = $e->path;
            if (isset($conf['translation']))
            {
                $translation = $conf['translation'][$e->path] ?? $translation;
            }
            $template = static::TEMPLATE . $e->ruleName;
            $template = static::TEMPLATES[$e->ruleName] ?? $template;
            if (isset($conf['template']))
            {
                $template = $conf['template'][$e->ruleName] ?? $template;
            }
            $params = $translation;
            if ($e->ruleParams) {
                $params .= ',' . $e->ruleParams;
            }
            $params = explode(',', $params);
            http_response_code(404);
            exit(sprintf($template, ...$params));
        } catch (Exception $e) {
            throw $e;
        }
    }
}
