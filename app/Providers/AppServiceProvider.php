<?php

namespace App\Providers;

use App\Models\Conversation;
use App\Models\Message;
use App\Observers\ConversationObserver;
use App\Observers\MessageObserver;
use App\Policies\ConversationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::addNamespace('mail', resource_path('views/vendor/mail'));
        Message::observe(MessageObserver::class);
        Conversation::observe(ConversationObserver::class);
        $this->registerPolicies();
    }

    /**
     * Register policies.
     */
    private function registerPolicies(): void
    {
        Gate::policy(Conversation::class, ConversationPolicy::class);
    }
}
