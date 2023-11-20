<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }

    /** Middleware responsable for enable or not access routes
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string[][]  $guards
     * @return mixed
     * @throws AuthenticationException
     * @throws RuntimeException
     * @throws RouteNotFoundException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);
        $requestedUri = $request->getPathInfo();//substr($request->getRequestUri(), 0, strpos($request->getRequestUri(), "?"));
        $redirect = !str_contains($requestedUri, '/profile');

        if ($redirect && $request->route()->getName() != 'api.mobile') {
            // Required platform_id choosed by user
            $listURI = in_array($requestedUri, $this->listOfExceptionRoutes());

            if (!$listURI) {
                if (!$request->session()->exists('platform_id')) {
                    $platformId = $request->session()->get('platform_id', false);
                    if (!$platformId) {
                        return redirect()->route('choose.platform');
                    }
                }
            }
        }

        return $next($request);
    }

    /** List of Routes is permitted without PlatformID
     * Add new routes if necessary using the example below
     * @return array (array|string)[]
     */
    private function listOfExceptionRoutes(): array
    {
        $platformUri = parse_url(route('choose.platform'), PHP_URL_PATH);
        $searchPlatforms = parse_url(route('search.all.platforms'), PHP_URL_PATH);
        $changeThumbPlatform = parse_url(route('change.platform.thumb'), PHP_URL_PATH);
        $myAccountUri = parse_url(route('choose.platform.my.account'), PHP_URL_PATH);
        $newPlatformUri = parse_url(route('new.platform'), PHP_URL_PATH);
        $acceptTermUri = parse_url(route('accept.platform.terms'), PHP_URL_PATH);
        $apiLogout = parse_url(route('api.logout'), PHP_URL_PATH);
        $userInfoUri = parse_url(route('user.info'), PHP_URL_PATH);
        $firstAccessUri = parse_url(route('first.access'), PHP_URL_PATH);
        $coproducerUri = parse_url(route('coproducer'), PHP_URL_PATH);
        $affiliatesUri = parse_url(route('affiliations'), PHP_URL_PATH);
        $affiliatesProductsUri = parse_url(route('affiliations.products'), PHP_URL_PATH);
        $affiliatesProductResumeUri = parse_url(route('affiliations.products.resume'), PHP_URL_PATH);
        $affiliatesProductTransactionsUri = parse_url(route('affiliations.products.transactions'), PHP_URL_PATH);
        $affiliatesProductWithdrawsUri = parse_url(route('affiliations.products.withdraws'), PHP_URL_PATH);
        $affliatesInviteUri = parse_url(route('affiliate.invite'), PHP_URL_PATH);
        $affliatesInviteConfirmUri = parse_url(route('affiliation.confirm'), PHP_URL_PATH);
        $documentsUri = parse_url(route('documents'), PHP_URL_PATH);

        return [
            $platformUri,
            $myAccountUri,
            $newPlatformUri,
            $acceptTermUri,
            $apiLogout,
            $userInfoUri,
            $firstAccessUri,
            $coproducerUri,
            $affiliatesUri,
            $affiliatesProductsUri,
            $affiliatesProductResumeUri,
            $searchPlatforms,
            $changeThumbPlatform,
            $affliatesInviteUri,
            $affliatesInviteConfirmUri,
            $affiliatesProductTransactionsUri,
            $documentsUri,
            $affiliatesProductWithdrawsUri,
        ];
    }
}
