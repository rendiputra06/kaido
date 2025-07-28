<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckKrsPeriodeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah ada periode KRS yang aktif
        $activePeriod = \App\Models\PeriodeKrs::where('status', 'aktif')
            ->where('tgl_mulai', '<=', now())
            ->where('tgl_selesai', '>=', now())
            ->first();

        if (!$activePeriod) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Periode pengisian KRS belum dibuka atau sudah ditutup.',
                    'error' => 'KRS_PERIOD_NOT_ACTIVE'
                ], 403);
            }

            return redirect()->route('filament.admin.pages.dashboard')
                ->with('error', 'Periode pengisian KRS belum dibuka atau sudah ditutup.');
        }

        // Tambahkan periode aktif ke request untuk digunakan di controller/page
        $request->merge(['active_krs_period' => $activePeriod]);

        return $next($request);
    }
}
