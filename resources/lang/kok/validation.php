<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute मान्य करचो पडटलो.',
    'active_url'           => ':attribute हो एक वैध URL न्हय.',
    'after'                => ':attribute ही :date उपरांतची तारीख आसची.',
    'after_or_equal'       => ':attribute ही :date लागीं वा उपरांतची तारीख आसची.',
    'alpha'                => ':attribute हातूंत फकत अक्षरां आसचीं.',
    'alpha_dash'           => ':attribute हातूंत फकत अक्षरां, नंबर, डॅश आनी अंडरस्कोर आसचीं.',
    'alpha_num'            => ':attribute हातूंत फकत अक्षरां आनी नंबर आसचीं.',
    'array'                => ':attribute हो एक array आसचो.',
    'before'               => ':attribute ही :date पयलींची तारीख आसची.',
    'before_or_equal'      => ':attribute ही :date लागीं वा पयलींची तारीख आसची.',
    'between'              => [
        'numeric' => ':attribute हो :min आनी :max मदीं आसचो.',
        'file'    => ':attribute हो :min आनी :max किलोबाइट्स मदीं आसचो.',
        'string'  => ':attribute हो :min आनी :max अक्षरां मदीं आसचो.',
        'array'   => ':attribute हातूंत :min आनी :max मदीं आयटम आसचे.',
    ],
    'boolean'              => ':attribute फिल्ड खरो वा खोटो आसचो.',
    'confirmed'            => ':attribute पुष्टी जुळना.',
    'date'                 => ':attribute ही एक वैध तारीख न्हय.',
    'date_equals'          => ':attribute ही :date इतलीच तारीख आसची.',
    'date_format'          => ':attribute हो :format स्वरूपा कडेन जुळना.',
    'different'            => ':attribute आनी :other वेगवेगळे आसचे.',
    'digits'               => ':attribute हो :digits अंकी आसचो.',
    'digits_between'       => ':attribute हो :min आनी :max अंकां मदीं आसचो.',
    'dimensions'           => ':attribute ंचीं प्रतिमा मापां अवैध आसात.',
    'distinct'             => ':attribute फिल्डाक दुप्पट मोल आसा.',
    'email'                => ':attribute हो एक वैध ईमेल पत्तो आसचो.',
    'ends_with'            => ':attribute निमाणे हातूंतल्या एका सयत सोंपचो: :values.',
    'exists'               => 'निवडिल्लो :attribute अवैध आसा.',
    'file'                 => ':attribute ही एक फाइल आसची.',
    'filled'               => ':attribute फिल्डाक मोल आसचें.',
    'gt'                   => [
        'numeric' => ':attribute हो :value परस व्हड आसचो.',
        'file'    => ':attribute हो :value किलोबाइट्स परस व्हड आसचो.',
        'string'  => ':attribute हो :value अक्षरां परस व्हड आसचो.',
        'array'   => ':attribute हातूंत :value परस चड आयटम आसचे.',
    ],
    'gte'                  => [
        'numeric' => ':attribute हो :value इतलोच वा व्हड आसचो.',
        'file'    => ':attribute हो :value किलोबाइट्स इतलोच वा व्हड आसचो.',
        'string'  => ':attribute हो :value अक्षरां इतलोच वा व्हड आसचो.',
        'array'   => ':attribute हातूंत :value वा चड आयटम आसचे.',
    ],
    'image'                => ':attribute ही एक प्रतिमा आसची.',
    'in'                   => 'निवडिल्लो :attribute अवैध आसा.',
    'in_array'             => ':attribute फिल्ड :other हातूंत अस्तित्वांत ना.',
    'integer'              => ':attribute हो एक पूर्णांक आसचो.',
    'ip'                   => ':attribute हो एक वैध IP पत्तो आसचो.',
    'ipv4'                 => ':attribute हो एक वैध IPv4 पत्तो आसचो.',
    'ipv6'                 => ':attribute हो एक वैध IPv6 पत्तो आसचो.',
    'json'                 => ':attribute ही एक वैध JSON स्ट्रिंग आसची.',
    'lt'                   => [
        'numeric' => ':attribute हो :value परस ल्हान आसचो.',
        'file'    => ':attribute हो :value किलोबाइट्स परस ल्हान आसचो.',
        'string'  => ':attribute हो :value अक्षरां परस ल्हान आसचो.',
        'array'   => ':attribute हातूंत :value परस कमी आयटम आसचे.',
    ],
    'lte'                  => [
        'numeric' => ':attribute हो :value इतलोच वा ल्हान आसचो.',
        'file'    => ':attribute हो :value किलोबाइट्स इतलोच वा ल्हान आसचो.',
        'string'  => ':attribute हो :value अक्षरां इतलोच वा ल्हान आसचो.',
        'array'   => ':attribute हातूंत :value परस चड आयटम आसचे नात.',
    ],
    'max'                  => [
        'numeric' => ':attribute हो :max परस व्हड आसचो न्हय.',
        'file'    => ':attribute हो :max किलोबाइट्स परस व्हड आसचो न्हय.',
        'string'  => ':attribute हो :max अक्षरां परस व्हड आसचो न्हय.',
        'array'   => ':attribute हातूंत :max परस चड आयटम आसचे न्हय.',
    ],
    'mimes'                => ':attribute ही :values प्रकाराची फाइल आसची.',
    'mimetypes'            => ':attribute ही :values प्रकाराची फाइल आसची.',
    'min'                  => [
        'numeric' => ':attribute उणेंत उणो :min आसचो.',
        'file'    => ':attribute उणेंत उणो :min किलोबाइट्स आसचो.',
        'string'  => ':attribute उणेंत उणो :min अक्षरां आसचो.',
        'array'   => ':attribute हातूंत उणेंत उणो :min आयटम आसचे.',
    ],
    'not_in'               => 'निवडिल्लो :attribute अवैध आसा.',
    'not_regex'            => ':attribute फॉरमॅट अवैध आसा.',
    'numeric'              => ':attribute हो एक नंबर आसचो.',
    'password'             => 'पासवर्ड चुकीचो आसा.',
    'present'              => ':attribute फिल्ड हजर आसचें.',
    'regex'                => ':attribute फॉरमॅट अवैध आसा.',
    'required'             => ':attribute फिल्ड गरजेचें आसा.',
    'required_if'          => ':attribute फिल्ड गरजेचें जेन्ना :other हें :value आसता.',
    'required_unless'      => ':attribute फिल्ड गरजेचें आसता जेन्ना :other हें :values मदीं आसता.',
    'required_with'        => ':attribute फिल्ड गरजेचें जेन्ना :values हजर आसता.',
    'required_with_all'    => ':attribute फिल्ड गरजेचें जेन्ना :values हजर आसतात.',
    'required_without'     => ':attribute फिल्ड गरजेचें जेन्ना :values हजर नासता.',
    'required_without_all' => ':attribute फिल्ड गरजेचें जेन्ना :values हजर नासतात.',
    'same'                 => ':attribute आनी :other जुळचे.',
    'size'                 => [
        'numeric' => ':attribute हो :size आसचो.',
        'file'    => ':attribute हो :size किलोबाइट्स आसचो.',
        'string'  => ':attribute हो :size अक्षरां आसचो.',
        'array'   => ':attribute हातूंत :size आयटम आसचे.',
    ],
    'starts_with'          => ':attribute हातूंतल्या एका सयत सुरू जावंचो: :values.',
    'string'               => ':attribute ही एक स्ट्रिंग आसची.',
    'timezone'             => ':attribute हो एक वैध झोन आसचो.',
    'unique'               => ':attribute पयलींच घेतला.',
    'uploaded'             => ':attribute अपलोड करपांत अपयश.',
    'url'                  => ':attribute फॉरमॅट अवैध आसा.',
    'uuid'                 => ':attribute हो एक वैध UUID आसचो.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
