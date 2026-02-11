<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\PageVisit;
use App\Services\UserAgentParser;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisits
{
    /**
     * Bot patterns المعروفة
     */
    protected array $botPatterns = [
        'googlebot', 'bingbot', 'slurp', 'duckduckbot',
        'baiduspider', 'yandexbot', 'crawler', 'spider',
        'bot', 'facebookexternalhit', 'twitterbot',
        'whatsapp', 'telegram', 'slack', 'discord',
        'linkedin', 'pinterest', 'archive.org', 'semrush',
        'ahref', 'mj12bot', 'dotbot', 'petalbot',
    ];

    /**
     * المسارات المستثناة من التتبع
     */
    protected array $excludedPrefixes = [
        'admin',
        'api',
        'livewire',
        '_debugbar',
        'telescope',
        'horizon',
        'clear-cache',
        'deploy',
        'visit-duration',
    ];

    /**
     * امتدادات الملفات المستثناة
     */
    protected array $assetExtensions = [
        'css', 'js', 'jpg', 'jpeg', 'png', 'gif',
        'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot',
        'json', 'xml', 'pdf', 'zip', 'txt', 'map',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // فقط GET requests و صفحات HTML
        if (!$request->isMethod('get') || !$this->shouldTrack($request)) {
            return $response;
        }

        try {
            [$isBot, $botName] = $this->detectBot($request);
            $deviceInfo = UserAgentParser::parse($request->userAgent());

            $visit = PageVisit::create([
                'session_id'  => session()->getId(),
                'ip_address'  => $request->ip(),
                'url'         => $this->truncate($request->fullUrl(), 2048),
                'route_name'  => $request->route()?->getName(),
                'page_title'  => $this->getPageTitle($request),
                'is_bot'      => $isBot,
                'bot_name'    => $botName,
                'referer'     => $this->truncate($request->header('referer'), 2048),
                'user_agent'  => $this->truncate($request->userAgent(), 512),
                'device_type' => $deviceInfo['device_type'],
                'browser'     => $deviceInfo['browser'],
                'os'          => $deviceInfo['os'],
                'visited_at'  => now(),
            ]);

            // نمرر visit_id للصفحة عبر shared view data
            view()->share('_visit_id', $visit->id);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to track page visit: ' . $e->getMessage());
        }

        return $response;
    }

    protected function shouldTrack(Request $request): bool
    {
        $path = $request->path();

        // استثناء المسارات
        foreach ($this->excludedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return false;
            }
        }

        // استثناء ملفات Assets
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array(strtolower($extension), $this->assetExtensions)) {
            return false;
        }

        // استثناء AJAX requests (Livewire, fetch)
        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }

        return true;
    }

    protected function detectBot(Request $request): array
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        foreach ($this->botPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return [true, ucfirst($pattern)];
            }
        }

        return [false, null];
    }

    protected function getPageTitle(Request $request): ?string
    {
        return match ($request->route()?->getName()) {
            'home'             => 'الصفحة الرئيسية',
            'search.index'     => 'البحث',
            'books.index'      => 'تصفح الكتب',
            'book.read'        => 'قراءة كتاب',
            'authors.index'    => 'المؤلفين',
            'author.show'      => 'صفحة مؤلف',
            'articles.index'   => 'المقالات',
            'articles.show'    => 'مقالة',
            'news.index'       => 'الأخبار',
            'news.show'        => 'خبر',
            'categories.index' => 'الأقسام',
            'about'            => 'عن الموقع',
            'feedback'         => 'الملاحظات',
            default            => null,
        };
    }

    protected function truncate(?string $value, int $max): ?string
    {
        if ($value === null) return null;
        return mb_substr($value, 0, $max);
    }
}
