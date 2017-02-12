<?php

namespace API;

class DocumentFieldController extends \API\APIController {
    
    /**
    * This function return list of fields, which need to fill specified document
    * 
    * <b>GET /document-fields/documents/id</b>
    *
    * <h3>Request Information:</h3>
    * 
    * <b>URI parameters:</b>
    * id - id of document, for which retrives fields
     * 
     * 
     * 
     * @return string JSON  success or error message
     */
    public function getDocuments($id = 0) {
        if (!$id) {
             return \Response::json(['message' => ['error' => 'We need ID of document']], 400);
        }  else {
            try {
                $document = \Document::where('id',$id)->firstOrFail();
            }
            catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                  return \Response::json(['message' => ['error' => 'No such document']], 404);
            }
            $currentUser = \Auth::user();
            $employee = $currentUser->profile()->first()->employee()->first();
            $employeeDocuments = $document->employeeDocuments()->where('employee_id',$employee->id)->first();
            $fields = $document->documentField()->get();
            foreach ($fields as $key => $field) {
                $valueFound = true;
                try {
                    $documentFieldValue = \DocumentFieldValue::where('document_field_id', $field['id'])->where('employee_id', $employee->id)->firstOrFail(); 
                }
                catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    $valueFound = false;
                }
                if ($valueFound) {
                    $fields[$key]->value = $documentFieldValue->value;
                } else {
                    $fields[$key]->value = '';
                }
                    
                if ($field->mirror) {
                    unset($fields[$key]);
                }
            } 
            return \Response::json(['document' => $document, 'fields' => $fields, 'employeeDocuments' => $employeeDocuments], 200);
        }    
    }
    
        /**
    * This function actept filled forms and push data to database
    * 
    * <b>GET /document-fields/add-fields</b>
    *
    * <h3>Request Information:</h3>
    * 
    * <b>URI parameters:</b>
    * no
     * 
     * 
     * 
     * @return string JSON  success or error message
     */
    public function postAddFields() {
        $input = \Input::json()->all();
        $currentUser = \Auth::user();
        $employee = $currentUser->profile()->first()->employee()->first();
        foreach ($input['data'] as $form) {
            $documentId = $form['document']['id'];
            foreach ($form['fields'] as $field) {
                $documentFieldValue = \DocumentFieldValue::firstOrNew(array(
                   'document_field_id' => $field['id'], 
                   'employee_id' => $employee->id)
                );
                $documentFieldValue->document_field_id = $field['id'];
                $documentFieldValue->employee_id = $employee->id;
                $documentFieldValue->value = $field['value'];
                $documentFieldValue->save(); 
            }
            $employeeDocument =  \EmployeeDocument::where('employee_id', $employee->id)->where('document_id', $documentId)->first();
            $employeeDocument->filled = 1;
            $currentDate = new \DateTime;
            $employeeDocument->filled_at = $currentDate->format('Y-m-d H:i:s');
            if (isset($form['document']['signature']) &&  $form['document']['signature']!='') {
                $img = \Signature::sigJsonToImage($form['document']['signature'],array('imageSize' => array(298, 75)));
                ob_start();
                imagepng($img);
                $base64 = base64_encode(ob_get_clean());
                $employeeDocument->signature = $base64;
            }

            $employeeDocument->save();
                \EmployeeActivity::create(array(
                    'employee_id' => $employee->id,
                    'action_user_id' => $currentUser->id,
                    'content' => "changed his(her) ".$form['document']['short_name'],
                ));

            $docName = $employeeDocument->document()->first()->short_name;
            if ($docName == "W4") {
                \Onboarding::setValueForEmployee($employee->id, 'w4', true);
            }
            else if ($docName == "I9") {
                \Onboarding::setValueForEmployee($employee->id, 'i9', true);
            }
            else if ($docName == "G4") {
                \Onboarding::setValueForEmployee($employee->id, 'g4', true);
            }
            else if ($docName == "W9") {
                \Onboarding::setValueForEmployee($employee->id, 'w9', true);
            }
        }
        
        return \Response::json(['message' => ['success' => 'Data add successfully']], 200);
    }
    
    
    public function getDocumentPrint($id = 0) {
        if (!$id) {
            return \Response::json(['message' => ['error' => 'We need ID of document']], 400);
        }  else {
            try {
                $document = \Document::where('id',$id)->firstOrFail();
            }
            catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                  return \Response::json(['message' => ['error' => 'No such document']], 404);
            }
            $currentUser = \Auth::user();
            $profile = $currentUser->profile()->first();
            $address = $profile->address()->first();
            $employee = $profile->employee()->first();
            
            $secureInfo = \SecureInfo::where('employee_id', $employee->id)->first();
            
            $dirFiles= scandir($_SERVER['DOCUMENT_ROOT']."/".$document->document_tpl_path);
            $pages = array();
            foreach ($dirFiles as $key => $dirFile ) {
                // establis our pages, which must be picture
                if ( in_array(pathinfo($dirFile, PATHINFO_EXTENSION), array('jpg','png','gif','bmp')) ) {
                    $pages[]=$dirFile;
                }
            }
            $pdf = \App::make('dompdf');
            $html = '<html><body style="margin:0px;padding:0px;" margin="0" padding="0">';
            $html .="\n";
            for ($i=0; $i < count($pages); $i++) {
               $html .= '<img src="'.$document->document_tpl_path.$pages[$i].'" style="width:100%;">'; 
               $html .="\n";
               // Singature
               if ($document->signature_page == ($i+1)) {
                    $employeeDocument =  \EmployeeDocument::where('employee_id',$employee->id)->where('document_id',$document->id)->first();
                    $html .= '<img src="data:image/png;base64,'.$employeeDocument->signature.'"  style="position:absolute;'.$document->signature_css.'"/>';
               }
               //End Singatute
               $pageFieldsIds = \DocumentDocumentField::where('document_id',$document->id)->where('document_page',$i+1)->lists('document_field_id');
               if (count($pageFieldsIds)>0) {
                    $pageFields = \DocumentField::whereIn('id',$pageFieldsIds)->get();
                    foreach ($pageFields as $key => $pageField ) {
                        if ($pageField->mirror) {
                            $pageFields[$key]->value = '';
                            // first step split string "+" elrem
                            $pageFieldVars = explode('+',$pageField->mirror); 
                            foreach ($pageFieldVars as $pageFieldVar) {
                                $dataArray = explode('.', $pageFieldVar ); 
                                $itemTable = $dataArray[0];
                                $itemField = $dataArray[1];
                                
                                if ($itemTable=='profiles') {
                                    $profileArray = $profile->toArray();
                                    $pageFields[$key]->value .=$profileArray[$itemField]." ";
                                } 
                                elseif ($itemTable=='users') {
                                    $userArray = $currentUser->toArray();
                                    $pageFields[$key]->value .= $userArray[$itemField]." ";
                                } 
                                elseif ($itemTable=='employees') {
                                    $employeeArray =  $employee->toArray();
                                    $pageFields[$key]->value .= $employeeArray[$itemField]." ";
                                } 
                                elseif ($itemTable=='addresses') {
                                    $addressArray = $address->toArray();
                                    $pageFields[$key]->value .= $addressArray[$itemField]." ";
                                }
                                elseif ($itemTable=='SecureInfo') {
                                    if($secureInfo && isset($secureInfo->$itemField)) {
                                        $pageFields[$key]->value .= $secureInfo->$itemField." ";
                                    }
                                } 
                                else {
                                    
                                }
                            }
                            
                        } else {
                            $valueFound = true;
                             try {
                                 $documentFieldValue = \DocumentFieldValue::where('document_field_id', $pageField->id)->where('employee_id', $employee->id)->firstOrFail(); 
                             }
                             catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                                 $valueFound = false;
                                 unset($pageFields[$key]);
                             }
                             if ($valueFound) {
                                 $pageFields[$key]->value = $documentFieldValue->value;
                             }
                         }
                         $pageFieldsCSS = \DocumentDocumentField::where('document_id',$document->id)->where('document_field_id',$pageField->id)->first();
                         $html .= '<div style="position:absolute;'.$pageFieldsCSS->css.'">'.$pageFields[$key]->value.'</div>';
                         $html .="\n";
                     }
                }
            }
            $html .= '</body></html>';
            //dd($html);
            $pdf->loadHTML($html)->setPaper('a4')->setOrientation('portrait');
            return $pdf->stream();
        }
    }
}



