<?php
namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException; // Pastikan ini di-import
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    // app/Exceptions/Handler.php
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($request->is('teacher') || $request->is('teacher/*')) {
            return redirect()->guest(route('teacher.form.login'));
        }
        if ($request->is('student') || $request->is('student/*')) {
            return redirect()->guest(route('student.form.login'));
        }
        if ($request->is('admin') || $request->is('admin/*')) {
            return redirect()->guest(route('admin.form.login'));
        }

        return redirect()->guest(url('/'));
    }
}