<?php

namespace API;

// REST services for tenants
class OnboardingController extends \API\APIController {

    public function getCurrent() {
        $currentUser = \Auth::user();
        $onboarding= $currentUser->profile()->first()->employee()->first()->onboarding;
        return \Response::json(['onboarding' => $onboarding], 200);
    }
	
}



