<?php

namespace App\Exceptions;

use App\Helper;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Swift_TransportException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TheSeer\Tokenizer\TokenCollectionException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return $this->response($request, 404, '页面不存在');
        } elseif ($exception instanceof HttpException) {
            if ($exception->getStatusCode() === 405) {
                return $this->response($request, $exception->getStatusCode(), 'MethodNotAllowedHttpException');
            } else {
                return $this->response($request, $exception->getStatusCode(), $exception->getMessage());
            }
        } elseif ($exception instanceof Swift_TransportException) {
            return $this->response($request, 500, $exception->getMessage());
        } elseif ($exception instanceof TokenCollectionException || $exception instanceof TokenMismatchException) {
            return $this->response($request, 419, '页面过期，请刷新后再试！');
        } elseif ($exception instanceof AuthenticationException) {
            $loginUrl = $request->is('admin/*') ? '/admin/login' : '/login';
            return $this->response($request, 401, '未登录或会话已失效@' . $loginUrl);
        }
        return parent::render($request, $exception);
    }

    private function response(Request $request, int $status, ?string $message = null)
    {
        $message = explode('@', $message);
        $url = isset($message[1]) ? $message[1] : null;
        $message = $message[0];
        if ($status < 0) {
            return Response::create(['status' => $status, 'message' => $message], 200);
        }
        if (!Helper::isPjax() && ($request->isXmlHttpRequest() || strpos($request->path(), 'api/') === 0)) {
            return Response::create(['status' => $status, 'message' => $message, 'go' => $url], 200);
        } else {
            return Response::create(view('error')->with(['status' => $status, 'error' => $message, 'url' => $url]));
        }
    }
}
