<?php

namespace Devi\MultiReferral;
use Illuminate\Support\ServiceProvider;
class MultiReferralServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishConfig();
        $this->publishMigrations();
    }
    /**
     * {@inheritdoc}
     */
    public function register()
    {
    }
    /**
     * Setup the config.
     */
    protected function publishConfig()
    {
        $source = realpath(__DIR__.'/../config/multi_referral.php');
        $this->publishes([
            $source => config_path('multi_referral.php'),
        ], 'config');
        $this->mergeConfigFrom($source, 'multi_referral');
    }

    private function publishMigrations()
    {
        $timestamp = date('Y_m_d_His');

        $migrationsSourceUser = realpath(__DIR__.'/../database/migrations/_add_referral_to_users_table.php');
        $migrationsTargetUser = database_path("/migrations/{$timestamp}_add_referral_to_users_table.php");

        $migrationsSourceReferralLists = realpath(__DIR__.'/../database/migrations/_create_referral_lists_table.php');
        $migrationsTargetReferralLists = database_path("/migrations/{$timestamp}_create_referral_lists_table.php");

        $this->publishes([
            $migrationsSourceUser => $migrationsTargetUser,
            $migrationsSourceReferralLists => $migrationsTargetReferralLists,
        ], 'migrations');
    }

}