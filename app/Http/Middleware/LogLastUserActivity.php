<?php

namespace App\Http\Middleware;

use App\UserLogin;
use Auth;
use Cache;
use Carbon\Carbon;
use Closure;
use Illuminate\Session\Store;

class LogLastUserActivity
{
    protected $session;

    protected $timeout = 1800;

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(5);
            $cacheKey = 'user-is-online-'.Auth::user()->id;

            // cache with 5 min expiry.
            $lastLogin = Cache::has($cacheKey, true, $expiresAt);

            // if cache doesn't exists, add cache and db entry.
            if (! $lastLogin) {
                // else add cache and add in db.
                Cache::put($cacheKey, true, $expiresAt);

                UserLogin::create([
                    'user_id' => Auth::id(),
                    'login_at' => Carbon::now(),
                ]);
            }

//            // expired
//            if($lastLogin < Carbon::now()){
//                Cache::rememberForever($cacheKey, function() use ($expiresAt) {
//                    return $expiresAt;
//                });
//                UserLogin::create([
//                    'user_id' => Auth::id(),
//                    'login_at' => Carbon::now(),
//                ]);
//            }

//            dd(Cache::get('user-is-online-'.Auth::user()->id) < Carbon::now());
//            if ($user_login = UserLogin::where('user_id', Auth::id())->first()) {
//                if (Carbon::now()->diffInDays($user_login->login_at) != 0) {
//                    UserLogin::create([
//                        'user_id' => Auth::id(),
//                        'login_at' => Carbon::now(),
//                    ]);
//                }
//            } else {
//                UserLogin::create([
//                    'user_id' => Auth::id(),
//                    'login_at' => Carbon::now(),
//                ]);
//            }
        }

        if (! $this->session->has('lastActivityTimeU')) {
            $this->session->put('lastActivityTimeU', time());
        } elseif (time() - $this->session->get('lastActivityTimeU') > $this->getTimeOut()) {
            $this->session->forget('lastActivityTimeU');
            if ($user_login = UserLogin::where('user_id', Auth::id())->latest()->first()) {
                if (Carbon::now()->diffInDays($user_login->logout_at) == 0) {
                    $user_login->update(['logout_at' => Carbon::now()]);
                } else {
                    UserLogin::create([
                        'user_id' => Auth::id(),
                        'logout_at' => Carbon::now(),
                    ]);
                }
            }
            Auth::logout();

            return redirect('/login')->withErrors(['You have been inactive for 30 minutes']);
        }
        $this->session->put('lastActivityTimeU', time());

        return $next($request);
    }

    protected function getTimeOut()
    {
        if (Auth::user()) {
            $timeout = (Auth::user()->user_timeout != 0) ? Auth::user()->user_timeout : $this->timeout;
        } else {
            $timeout = $this->timeout;
        }

        return $timeout;
    }
}
