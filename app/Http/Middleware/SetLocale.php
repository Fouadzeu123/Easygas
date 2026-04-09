<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if locale is already in session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } else {
            // Detect from browser Accept-Language header
            // getPreferredLanguage(['fr', 'en']) returns 'en' if English is preferred, 
            // otherwise 'fr' by default if neither matches or if French is preferred.
            $locale = $request->getPreferredLanguage(['fr', 'en']);
            
            // Match the requirement: English if English, else French
            // getPreferredLanguage(['fr', 'en']) will return 'fr' if it can't find 'en'.
            // However, we want to be explicit: if 'en' is preferred, use 'en', else 'fr'.
            $preferred = $request->getPreferredLanguage(['en', 'fr']);
            $locale = (str_starts_with($preferred, 'en')) ? 'en' : 'fr';
            
            Session::put('locale', $locale);
        }

        App::setLocale($locale);

        return $next($request);
    }
}
