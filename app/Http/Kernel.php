<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [

        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'login_auth' => \App\Http\Middleware\LoginAuth::class,
        'volunteers_auth' => \App\Http\Middleware\volunteersAuth::class,
        'projects_auth' => \App\Http\Middleware\ProjectsAuth::class,
        'process_auth' => \App\Http\Middleware\ProcessAuth::class,
        'message_auth' => \App\Http\Middleware\MessageAuth::class,
        'calendar_events_auth' => \App\Http\Middleware\CalendarEventsAuth::class,
        'caste_data_auth' => \App\Http\Middleware\CasteDataAuth::class,
        'dept_data_auth' => \App\Http\Middleware\DeptDataAuth::class,
        'panchayats_data_auth' => \App\Http\Middleware\PanchayatsDataAuth::class,
        'places_data_auth' => \App\Http\Middleware\PlacesDataAuth::class,
        'village_data_auth' => \App\Http\Middleware\VillageDataAuth::class,
        'schemes_data_auth' => \App\Http\Middleware\SchemesDataAuth::class,
        'zp_data_auth' => \App\Http\Middleware\ZPDataAuth::class,
        'excel_import_auth' => \App\Http\Middleware\ExcelImportAuth::class,
        'polls_auth' => \App\Http\Middleware\PollsAuth::class,
        'complaints_auth' => \App\Http\Middleware\ComplaintsAuth::class,
        'suggestions_auth' => \App\Http\Middleware\SuggestionsAuth::class,
        'news_auth' => \App\Http\Middleware\NewsAuth::class,
        'achievements_auth' => \App\Http\Middleware\AchievementsAuth::class,
        'survey_auth' => \App\Http\Middleware\SurveyAuth::class,
        'user_auth' => \App\Http\Middleware\UserAuth::class,
        'survey_user_auth' => \App\Http\Middleware\SurveyUserAuth::class,
    ];

}
