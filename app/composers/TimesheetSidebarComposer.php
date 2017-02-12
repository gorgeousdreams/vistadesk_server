<?php namespace Webdesk\Composers;
 
class TimesheetSidebarComposer {
 
  public function compose($view)
  {
    $view->with('timesheetModel', "hello there");
  }
 
}