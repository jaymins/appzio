<?php

/*
    These are set by the parent class:

    public $output;     // html output
    public $donebtn;    // done button, includes the clock
    public $taskid;     // current task id
    public $token;      // current task token
    public $added;      // unix time when task was added
    public $timelimit;  // task time limit in unix time
    public $expires;    // unix time when task expires (use time() to compare)
    public $clock;      // html for the task timer
    public $configdata; // these are the custom set config variables per task type
    public $taskdata;   // this contains all data about the task
    public $usertaskid; // IMPORTANT: for any action, this is the relevant id, as is the task user is playing, $taskid is the id of the parent
    public $baseurl;    // application baseurl
    public $doneurl;    // full url for marking the task done
*/

class MobilehelpersController extends ArticleController {

    public $data;

    public function tab1() {
        $this->data = new StdClass();

        return $this->data;
    }

    public function countriesMap() {
        return array( '+93' => 'Afghanistan', '+355' => 'Albania', '+213' => 'Algeria', '+376' => 'Andorra', '+244' => 'Angola', '+54' => 'Argentina', '+374' => 'Armenia', '+61' => 'Australia', '+43' => 'Austria', '+994' => 'Azerbaigan', '+973' => 'Bahrain', '+880' => 'Bangladesh', '+375' => 'Belarus', '+32' => 'Belgium', '+591' => 'Bolivia', '+387' => 'Bosnia', '+55' => 'Brazil', '+359' => 'Bulgaria', '+237' => 'Cameroon', '+1' => 'Canada', '+235' => 'Chad', '+56' => 'Chile', '+86' => 'China', '+57' => 'Colombia', '+242' => 'Congo', '+506' => 'Costa Rica', '+385' => 'Croatia', '+53' => 'Cuba', '+357' => 'Cyprus', '+420' => 'Czech Republic', '+45' => 'Denmark', '+593' => 'Ecuador', '+20' => 'Egypt', '+503' => 'El Salvador', '+251' => 'Ethiopia', '+679' => 'Fiji', '+358' => 'Finland', '+33' => 'France', '+241' => 'Gabon', '+995' => 'Georgia', '+49' => 'Germany', '+233' => 'Ghana', '+350' => 'Gibraltar', '+30' => 'Greece', '+299' => 'Greenland', '+387' => 'Herzegovina', '+852' => 'Hong Kong', '+36' => 'Hungary', '+354' => 'Iceland', '+91' => 'India', '+62' => 'Indonesia', '+98' => 'Iran', '+964' => 'Iraq', '+353' => 'Ireland', '+36' => 'Italy', '+225' => 'Ivory Coast', '+81' => 'Japan', '+962' => 'Jordan', '+7' => 'Kazakhstan', '+254' => 'Kenya', '+85' => 'Korea', '+965' => 'Kuwait', '+856' => 'Laos', '+371' => 'Latvia', '+961' => 'Lebanon', '+231' => 'Liberia', '+218' => 'Libya', '+41' => 'Liechtenstein', '+370' => 'Lithuania', '+352' => 'Luxembourg', '+60' => 'Malaysia', '+960' => 'Maldives', '+223' => 'Mali', '+230' => 'Mauritius', '+356' => 'Malta', '+52' => 'Mexico', '+373' => 'Moldova', '+377' => 'Monaco', '+976' => 'Mongolia', '+212' => 'Morocco', '+258' => 'Mozambique', '+95' => 'Myanmar', '+264' => 'Namibia', '+31' => 'Netherlands', '+687' => 'New Caledonia', '+977' => 'Nepal', '+64' => 'New Zealand', '+234' => 'Nigeria', '+850' => 'North Korea', '+47' => 'Norway', '+968' => 'Oman', '+92' => 'Pakistan', '+507' => 'Panama', '+595' => 'Paraguay', '+51' => 'Peru', '+63' => 'Philippines', '+48' => 'Poland', '+351' => 'Portugal', '+974' => 'Qatar', '+40' => 'Romania', '+70' => 'Russia', '+250' => 'Rwanda', '+966' => 'Saudi Arabia', '+221' => 'Senegal', '+381' => 'Serbia', '+248' => 'Seychelles', '+232' => 'Sierra Leone', '+65' => 'Singapore', '+421' => 'Slovakia', '+252' => 'Somalia', '+27' => 'South Africa', '+34' => 'Spain', '+94' => 'Sri Lanka', '+249' => 'Sudan', '+46' => 'Sweden', '+41' => 'Switzerland', '+963' => 'Syria', '+886' => 'Taiwan', '+255' => 'Tanzania', '+66' => 'Thailand', '+216' => 'Tunisia', '+90' => 'Turkey', '+256' => 'Uganda', '+7' => 'Ukraine', '+971' => 'United Arab Emirates', '+44' => 'United Kingdom', '+598' => 'Uruguay', '+1' => 'USA', '+7' => 'Uzbekistan', '+39' => 'Vatican City St.', '+58' => 'Venezuela', '+84' => 'Vietnam', '+967' => 'Yemen', '+38' => 'Yugoslavia', '+243' => 'Zaire', '+260' => 'Zambia', '+263 ' => 'Zimbabwe',
        );
    }

    public function closePopup() {
        $onclick = new StdClass();
        $onclick->action = 'close-popup';
        $onclick->keep_user_data = 1;
        return $onclick;
    }

}