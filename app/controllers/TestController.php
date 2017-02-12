<?php

class TestController extends \BaseController {

    public function getTest() {
        return Employee::getEmployeeRateForClient(164, 14);
    }

    public function getEmail() {
        $data = array(
            'link' => "http://".$_SERVER['SERVER_NAME']."/v3/index.html#/access/hired?resetpass="."asdfjklasfjkalsdf",
            );
        \Mail::send('emails.addemployee', $data, function($message) {
            $message->from('support@ngdcorp.com', 'NGD');
            $message->to("support@ngdcorp.com", "Tech Suppport")->subject('You are added to FusePath!');
        });

    }

    public function getPdf() {
        $pdf = App::make('dompdf');

        $html = '<html><body style="margin:0px;padding:0px;" margin="0" padding="0"><img src="documents/w4/p001.jpg" style="width:100%;">';
    // First name
        $html .= '<div style="position:absolute;top:682px;left:10px">Seth</div>';
    // Last name
        $html .= '<div style="position:absolute;top:682px;left:250px">Jaslow</div>';
    // SSN
        $html .= '<div style="position:absolute;top:682px;left:550px">123-45-6789</div>';
    // Address
        $html .= '<div style="position:absolute;top:714px;left:10px">301 South State Street, #200</div>';
    // Marital status
        $html .= '<div style="position:absolute;top:700px;left:431px">X</div>';
    // City, State, Zip
        $html .= '<div style="position:absolute;top:746px;left:10px">Newtown, PA 18940</div>';    
        $html .= '<img src="documents/w4/p002.jpg" style="width:100%">';

        $html .= '</body></html>';

        $pdf->loadHTML($html);


        return $pdf->stream();
    //return $html;
    }

} 