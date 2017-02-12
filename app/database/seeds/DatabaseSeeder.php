<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

        $this->call('DocumentsTableSeeder');
        $this->command->info('Documents table seeded!');

        $this->call('DocumentFieldsTableSeeder');
        $this->command->info('Document_fields table seeded!');

    }

}


class DocumentsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('documents')->delete();
        
        Document::create(['id' => 1, 
            'short_name' => 'W4',
            'full_name' => 'Federal - W4 Withholding - 2014',
            'description' => '',
            'document_tpl_path' => 'documents/w4/',
            'signature_page' => 1,
            'signature_css' => 'top:870px;left:210px;height:25px;'
            ]);
        
        Document::create(['id' => 2, 
            'short_name' => 'I9',
            'full_name' => 'Federal - I-9 Employment Eligibility - 2014',
            'description' => '',
            'document_tpl_path' => 'documents/i9/',
            'signature_page' => 7,
            'signature_css' => 'top:685px;left:120px;height:25px;'
            ]);
        
        Document::create(['id' => 3, 
            'short_name' => 'W4-State',
            'full_name' => 'State - W4 Withholding - 2014',
            'description' => '',
            'document_tpl_path' => 'documents/w4/',
            'signature_page' => 1,
            'signature_css' => 'top:870px;left:210px;height:25px;'
            ]);
        
        Document::create(['id' => 4, 
            'short_name' => 'G4',
            'full_name' => "State of Georgia Emploee's Withholding Allowance Certificate G-4",
            'description' => '',
            'document_tpl_path' => 'documents/g4/',
            'signature_page' => 1,
            'signature_css' => 'top:794px;left:180px;height:25px;height:18px;'
            ]);
        
        Document::create(['id' => 5, 
            'short_name' => 'W9',
            'full_name' => "Request for Taxpayer Identification Number and Certification",
            'description' => '',
            'document_tpl_path' => 'documents/w9/',
            'signature_page' => 1,
            'signature_css' => 'top:600px;left:151px;height:25px;height:18px;'
            ]);
    }

}



class DocumentFieldsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('document_fields')->delete();
        DB::table('document_document_fields')->delete();

        
        // w4 form
        $docimentId = 1;
        
        $df = DocumentField::create([ 'short_name' => 'First name',
            'full_name' => 'First name',
            'description' => 'First name',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.first_name'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:677px;left:10px;',
            ]);
        

        $df = DocumentField::create([ 'short_name' => 'Last name',
            'full_name' => 'Last name',
            'description' => 'Last name',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.last_name'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:677px;left:250px;',
            ]);
        
        
        $df = DocumentField::create([ 'short_name' => 'SSN',
            'full_name' => 'Social Security Number',
            'description' => 'Social Security Number',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.ssn'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:677px;left:550px',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Address',
            'full_name' => 'Address',
            'description' => 'Address',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.street1'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:705px;left:10px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'City, State, Zip',
            'full_name' => 'City, State, Zip',
            'description' => 'City, State, Zip',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.city+addresses.state+addresses.postal'
            ]);
        

        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:740px;left:10px;',
            ]);
        

        $df = DocumentField::create([ 'short_name' => 'Single',
            'full_name' => 'Marital status Single',
            'description' => 'Single',
            'type' => 'checkbox',
            'default_values' => 'X',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:695px;left:371px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Married',
            'full_name' => 'Marital status Married',
            'description' => 'Married',
            'type' => 'checkbox',
            'default_values' => 'X',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:695px;left:426px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Married, but withhold at higher Single rate.',
            'full_name' => 'Marital status Married, but withhold at higher Single rate.',
            'description' => 'Married, but withhold at higher Single rate.',
            'type' => 'checkbox',
            'default_values' => 'X',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:743px;left:679px;',
            ]);

        $df = DocumentField::create([ 'short_name' => 'Total number of allowances',
            'full_name' => 'Total number of allowances',
            'description' => 'Total number of allowances you are claiming (from line H above or from the applicable worksheet on page 2)',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:758px;left:617px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Additional amount',
            'full_name' => 'Additional amount',
            'description' => 'Additional amount, if any, you want withheld from each paycheck',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:772px;left:625px;',
            ]);
       
        $df = DocumentField::create([ 'short_name' => 'Date',
            'full_name' => 'W4 Date',
            'description' => 'Date',
            'type' => 'date',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'employees.hire_date'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:880px;left:570px;',
            ]);    
        





         // i-9 form

        $docimentId = 2;
        
        $df = DocumentField::create([ 'short_name' => 'Last name',
            'full_name' => 'Last name',
            'description' => 'Last name',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.last_name'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:225px;left:7px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'First name',
            'full_name' => 'First name',
            'description' => 'First name',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.first_name'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:225px;left:224px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Middle initial',
            'full_name' => 'Middle initial',
            'description' => 'Middle initial',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:225px;left:407px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Other Name Used',
            'full_name' => 'Other Name Used',
            'description' => 'Other Name Used (if any)',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:225px;left:468px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Address',
            'full_name' => 'Address (Street Number and Name)',
            'description' => 'Address (Street Number and Name)',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.street1'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:263px;left:7px;',
            ]);

        
        $df = DocumentField::create([ 'short_name' => 'Apt. Number',
            'full_name' => 'Apt. Number',
            'description' => 'Apt. Number',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:263px;left:263px;',
            ]);

        
        $df = DocumentField::create([ 'short_name' => 'City or Town',
            'full_name' => 'City or Town',
            'description' => 'City or Town',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.city'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:263px;left:340px;',
            ]);

        
        $df = DocumentField::create([ 'short_name' => 'State',
            'full_name' => 'State',
            'description' => 'State',
            'type' => 'select',
            'default_values' => 'AK, AL, AR, AZ, CA, CO, CT, DC, DE, FL, GA, HI, IA, ID, IL, IN, KS, KY, LA, MA, MD, ME, MI, MN, MO, MS, MT, NC, ND, NE, NH, NJ, NM, NV, NY, OH, OK, OR, PA, RI, SC, SD, TN, TX, UT, VA, VT, WA, WI, WV, WY',
            'validation' => '',
            'mirror' => 'addresses.state'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:263px;left:517px;',
            ]);

        
        $df = DocumentField::create([ 'short_name' => 'Zip Code',
            'full_name' => 'Zip Code',
            'description' => 'Zip Code',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.postal'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:263px;left:574px;',
            ]);

        
        $df = DocumentField::create([ 'short_name' => 'Date of Birth',
            'full_name' => 'Date of Birth',
            'description' => 'Date of Birth (mm/dd/yyyy)',
            'type' => 'date',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.date_of_birth'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:305px;left:7px;',
            ]);

        
        $df = DocumentField::create([ 'short_name' => 'SSN',
            'full_name' => ' U.S. Social Security Number',
            'description' => ' U.S. Social Security Number',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.ssn'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:305px;left:137px;letter-spacing:4px;',
            ]);

        
        $df = DocumentField::create([ 'short_name' => 'E-mail',
            'full_name' => 'E-mail Address',
            'description' => 'E-mail Address',
            'type' => 'email',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.email'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:305px;left:273px;',
            ]);

        
        $df = DocumentField::create([ 'short_name' => 'Phone',
            'full_name' => 'Telephone Number',
            'description' => 'Telephone Number',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.phone'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:305px;left:537px;',
            ]);

        
        $df = DocumentField::create([ 'short_name' => 'A citizen of the United States',
            'full_name' => 'A citizen of the United States',
            'description' => 'A citizen of the United States',
            'type' => 'checkbox',
            'default_values' => 'X',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:385px;left:3px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'A noncitizen national of the United States',
            'full_name' => 'A noncitizen national of the United States (See Instructions)',
            'description' => 'A noncitizen national of the United States (See Instructions)',
            'type' => 'checkbox',
            'default_values' => 'X',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:407px;left:3px;',
            ]);

        
        $df = DocumentField::create([ 'short_name' => 'A lawful permanent resident',
            'full_name' => 'A lawful permanent resident',
            'description' => 'A lawful permanent resident',
            'type' => 'checkbox',
            'default_values' => 'X',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:427px;left:3px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Alien Registration Number/USCIS Number',
            'full_name' => 'Alien Registration Number/USCIS Number',
            'description' => 'Alien Registration Number/USCIS Number',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:425px;left:388px;',
            ]);

        
        $df = DocumentField::create([ 'short_name' => 'An alien authorized to work until',
            'full_name' => 'An alien authorized to work until',
            'description' => 'An alien authorized to work until',
            'type' => 'checkbox',
            'default_values' => 'X',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:452px;left:3px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'expiration date, if applicable, mm/dd/yyyy',
            'full_name' => 'An alien authorized to work until expiration date, if applicable, mm/dd/yyyy',
            'description' => 'An alien authorized to work until expiration date, if applicable, mm/dd/yyyy',
            'type' => 'date',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:450px;left:364px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Alien Registration Number/USCIS Number',
            'full_name' => 'Alien Registration Number/USCIS Number',
            'description' => 'Alien Registration Number/USCIS Number',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:507px;left:251px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Form I-94 Admission Number',
            'full_name' => 'Form I-94 Admission Number',
            'description' => 'Form I-94 Admission Number',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:543px;left:187px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Foreign Passport Number',
            'full_name' => 'Foreign Passport Number',
            'description' => 'Foreign Passport Number',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:607px;left:188px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Country',
            'full_name' => 'Country of Issuance',
            'description' => 'Country of Issuance',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.country'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:630px;left:158px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Date',
            'full_name' => 'Date (mm/dd/yyyy):',
            'description' => 'Date of signature',
            'type' => 'date',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'employees.hire_date'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 7,
            'css' => 'top:687px;left:567px;',
            ]);

        
        //W4 State
        $docimentId = 3;
        
        $df = DocumentField::create([ 'short_name' => 'First name',
            'full_name' => 'First name',
            'description' => 'First name',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.first_name'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:677px;left:10px;',
            ]);
        

        $df = DocumentField::create([ 'short_name' => 'Last name',
            'full_name' => 'Last name',
            'description' => 'Last name',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.last_name'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:677px;left:250px;',
            ]);
        
        
        $df = DocumentField::create([ 'short_name' => 'SSN',
            'full_name' => 'Social Security Number',
            'description' => 'Social Security Number',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.ssn'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:677px;left:550px',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Address',
            'full_name' => 'Address',
            'description' => 'Address',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.street1'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:705px;left:10px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'City, State, Zip',
            'full_name' => 'City, State, Zip',
            'description' => 'City, State, Zip',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.city+addresses.state+addresses.postal'
            ]);
        

        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:740px;left:10px;',
            ]);
        

        $df = DocumentField::create([ 'short_name' => 'Single',
            'full_name' => 'Marital status Single',
            'description' => 'Single',
            'type' => 'checkbox',
            'default_values' => 'X',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:695px;left:371px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Married',
            'full_name' => 'Marital status Married',
            'description' => 'Married',
            'type' => 'checkbox',
            'default_values' => 'X',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:695px;left:426px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Married, but withhold at higher Single rate.',
            'full_name' => 'Marital status Married, but withhold at higher Single rate.',
            'description' => 'Married, but withhold at higher Single rate.',
            'type' => 'checkbox',
            'default_values' => 'X',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:743px;left:679px;',
            ]);

        $df = DocumentField::create([ 'short_name' => 'Total number of allowances',
            'full_name' => 'Total number of allowances',
            'description' => 'Total number of allowances you are claiming (from line H above or from the applicable worksheet on page 2)',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:758px;left:617px;',
            ]);
        
        $df = DocumentField::create([ 'short_name' => 'Additional amount',
            'full_name' => 'Additional amount',
            'description' => 'Additional amount, if any, you want withheld from each paycheck',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:772px;left:625px;',
            ]);
       
        $df = DocumentField::create([ 'short_name' => 'Date',
            'full_name' => 'W4 Date',
            'description' => 'Date',
            'type' => 'date',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'employees.hire_date'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:880px;left:570px;',
            ]);    
        
        
        // --------- G-4 Form --------------
        $docimentId = 4;
        
        //1
        $df = DocumentField::create([ 'short_name' => 'Full name G4',
            'full_name' => 'Full name G4',
            'description' => 'Full name G4',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.first_name+profiles.last_name'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:186px;left:57px;',
            ]);
        
        //2
        $df = DocumentField::create([ 'short_name' => 'SSN  G4',
            'full_name' => 'Social Security Number  G4',
            'description' => 'Social Security Number  G4',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.ssn'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:186px;left:375px;',
            ]);
        
        //3
        $df = DocumentField::create([ 'short_name' => 'Home Address G4',
            'full_name' => 'Home Address G4',
            'description' => 'Home Address G4',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.street1'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:217px;left:57px;',
            ]);
        
        //4
        $df = DocumentField::create([ 'short_name' => 'City, State, ZIP Code G4',
            'full_name' => 'City, State, ZIP Code',
            'description' => 'City, State, ZIP Code',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.city+addresses.state+addresses.postal'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:217px;left:375px;',
            ]);
        
        
        // Marital Status
        //5
        $df = DocumentField::create([ 'short_name' => 'Single',
            'full_name' => 'Marital status Single',
            'description' => 'Single',
            'type' => 'checkbox',
            'default_values' => '1',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:267px;left:272px;',
            ]);
        
        //6
        $df = DocumentField::create([ 'short_name' => 'Married Filing Joint, both spouses working',
            'full_name' => 'Marital status Married Filing Joint, both spouses working',
            'description' => 'Married Filing Joint, both spouses working',
            'type' => 'checkbox',
            'default_values' => '1',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:292px;left:272px;',
            ]);
        
        //7
        $df = DocumentField::create([ 'short_name' => 'Married Filing Joint, one spouses working',
            'full_name' => 'Marital status Married Filing Joint, one spouses working',
            'description' => 'Married Filing Joint, one spouses working',
            'type' => 'checkbox',
            'default_values' => '1',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:318px;left:272px;',
            ]);
        
        //8
        $df = DocumentField::create([ 'short_name' => 'Married Filing Separate',
            'full_name' => 'Marital status Married Filing Separate',
            'description' => 'Married Filing Separate',
            'type' => 'checkbox',
            'default_values' => '1',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:343px;left:272px;',
            ]);
        
        //9
        $df = DocumentField::create([ 'short_name' => 'Head of Household',
            'full_name' => 'Marital status Head of Household',
            'description' => 'Head of Household',
            'type' => 'checkbox',
            'default_values' => '1',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:368px;left:274px;',
            ]);
        // END Marital Status
        
        
        //10
        $df = DocumentField::create([ 'short_name' => 'Dependent allowance',
            'full_name' => 'Dependent allowance',
            'description' => 'Dependent allowance',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:269px;left:627px;font-size:11px;',
            ]);
        
        //11
        $df = DocumentField::create([ 'short_name' => 'Additional allowances',
            'full_name' => 'Additional allowances',
            'description' => 'Additional allowances',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:307px;left:627px;font-size:11px;',
            ]);
        
        
        //12
        $df = DocumentField::create([ 'short_name' => 'Letter Used',
            'full_name' => 'Letter Used',
            'description' => 'Letter Used',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:645px;left:285px;',
            ]);
        
        //13
        $df = DocumentField::create([ 'short_name' => 'Total Allowances',
            'full_name' => 'Total Allowances',
            'description' => 'Total Allowances',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:645px;left:589px;',
            ]);
        
                
        //14
//        $df = DocumentField::create([ 'short_name' => 'Date',
//            'full_name' => 'W4 Date',
//            'description' => 'Date',
//            'type' => 'date',
//            'default_values' => '',
//            'validation' => '',
//            'mirror' => 'employees.hire_date'
//            ]);
//        
//        DocumentDocumentField::create([ 
//            'document_id' => $docimentId,
//            'document_field_id' => $df->id,
//            'document_page' => 1,
//            'css' => 'top:1411px;left:275px;',
//            ]); 
        

        //15
        $df = DocumentField::create([ 'short_name' => 'Date G4',
            'full_name' => 'Date G4',
            'description' => 'Date G4',
            'type' => 'date',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'employees.hire_date'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:796px;left:505px;',
            ]);  
        //16
        $df = DocumentField::create([ 'short_name' => 'Employers FEIN G4',
            'full_name' => 'Employers FEIN G4',
            'description' => 'Employers FEIN G4',
            'type' => 'date',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'SecureInfo.fein',
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:847px;left:450px;',
            ]);    

        
        /** W9 **/
        $docimentId = 5;
        
        //1
        $df = DocumentField::create([ 'short_name' => 'Full name W9',
            'full_name' => 'Full name',
            'description' => 'Full name',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'profiles.first_name+profiles.last_name',
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:105px;left:70px;',
            ]);
        
        //2
        $df = DocumentField::create([ 'short_name' => 'Busines name W9',
            'full_name' => 'Busines name',
            'description' => 'Busines name',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:135px;left:70px;',
            ]);
        
        
        //Check appropriate box for federal tax classification
        //
        //3
        $df = DocumentField::create([ 'short_name' => 'Individual/sole proprietor or single-member LLC W9',
            'full_name' => 'Individual/sole proprietor or single-member LLC',
            'description' => 'Individual/sole proprietor or single-member LLC',
            'type' => 'checkbox',
            'default_values' => '0',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:161px;left:76px;',
            ]);
        
        //4
        $df = DocumentField::create([ 'short_name' => 'C Corporation W9',
            'full_name' => 'C Corporation',
            'description' => 'C Corporation',
            'type' => 'checkbox',
            'default_values' => '0',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:161px;left:215px;',
            ]);
        
        //5
        $df = DocumentField::create([ 'short_name' => 'S Corporation W9',
            'full_name' => 'S Corporation',
            'description' => 'S Corporation',
            'type' => 'checkbox',
            'default_values' => '0',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:161px;left:298px;',
            ]);
        
        //6
        $df = DocumentField::create([ 'short_name' => 'Partnership W9',
            'full_name' => 'S Corporation',
            'description' => 'S Corporation',
            'type' => 'checkbox',
            'default_values' => '0',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:161px;left:373px;',
            ]);
        
        //7
        $df = DocumentField::create([ 'short_name' => 'Trust/estate W9',
            'full_name' => 'Trust/estate',
            'description' => 'Trust/estate',
            'type' => 'checkbox',
            'default_values' => '0',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:161px;left:455px;',
            ]);
        
        //8
        $df = DocumentField::create([ 'short_name' => 'Limited liability company W9',
            'full_name' => 'Limited liability company',
            'description' => 'Limited liability company',
            'type' => 'checkbox',
            'default_values' => '0',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:182px;left:76px;',
            ]);
        
        //9
        $df = DocumentField::create([ 'short_name' => 'Other W9',
            'full_name' => 'Other',
            'description' => 'Other',
            'type' => 'checkbox',
            'default_values' => '0',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:216px;left:76px;',
            ]);
        
        //10
        $df = DocumentField::create([ 'short_name' => 'Other Text W9',
            'full_name' => 'Other Text',
            'description' => 'Other Text',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:216px;left:185px;',
            ]);
 
        
        //Address data
        //11
        $df = DocumentField::create([ 'short_name' => 'Address W9',
            'full_name' => 'Address',
            'description' => 'Address',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.street1'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:244px;left:70px;',
            ]);
        
        //12
        $df = DocumentField::create([ 'short_name' => 'City, State, ZIP Code W9',
            'full_name' => 'City, State, ZIP Code',
            'description' => 'City, State, ZIP Code',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'addresses.city+addresses.state+addresses.postal'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:272px;left:70px;',
            ]);
        
        //Social security number
        //13
        $df = DocumentField::create([ 'short_name' => 'Social security number 1',
            'full_name' => 'Social security number',
            'description' => 'Social security number Part1',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:352px;left:483px;letter-spacing: 8px;',
            ]);
        //14
        $df = DocumentField::create([ 'short_name' => 'Social security number 2',
            'full_name' => 'Social security number',
            'description' => 'Social security number Part2',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:352px;left:552px;letter-spacing: 8px;',
            ]);
        //15
        $df = DocumentField::create([ 'short_name' => 'Social security number 3',
            'full_name' => 'Social security number',
            'description' => 'Social security number Part3',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:352px;left:602px;letter-spacing: 8px;',
            ]);
        
        //Employer identification number
        //16
        $df = DocumentField::create(['short_name' => 'Employer identification number 1',
            'full_name' => 'Employer identification number',
            'description' => 'Social security number Part1',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:408pxpx;left:483px;letter-spacing: 8px;',
            ]);
        
        //17
        $df = DocumentField::create([ 'short_name' => 'Employer identification number 2',
            'full_name' => 'Employer identification number',
            'description' => 'Social security number Part2',
            'type' => 'text',
            'default_values' => '',
            'validation' => '',
            'mirror' => ''
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:408pxpx;left:536px;letter-spacing: 8px;',
            ]);
        
        //18
        $df = DocumentField::create([ 'short_name' => 'Date W9',
            'full_name' => 'Date W9',
            'description' => 'Date W9',
            'type' => 'date',
            'default_values' => '',
            'validation' => '',
            'mirror' => 'employees.hire_date'
            ]);
        
        DocumentDocumentField::create([ 
            'document_id' => $docimentId,
            'document_field_id' => $df->id,
            'document_page' => 1,
            'css' => 'top:604px;left:505px;',
            ]);
    }    

}




