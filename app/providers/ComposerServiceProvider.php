<?php namespace providers;
 
use Illuminate\Support\ServiceProvider;
 
class ComposerServiceProvider extends ServiceProvider {
 
  public function register()
  {
    $this->app->view->composer('partials.left-nav.timesheets', 'Webdesk\Composers\TimesheetSidebarComposer');
    $this->app->view->composer('partials.dashboard.project', 'Webdesk\Composers\DashboardProjectComposer');
    $this->app->view->composer('partials.dashboard.revenue', 'Webdesk\Composers\DashboardRevenueComposer');
  }
 
}