<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validatietaalregels
    |--------------------------------------------------------------------------
    |
    | De volgende taalregels bevatten de standaardfoutmeldingen die worden
    | gebruikt door de validatorklasse. Sommige van deze regels hebben meerdere
    | versies, zoals de formaatregels. U bent vrij om elk van deze berichten
    | hier aan te passen.
    |
    */

    'accepted' => 'Het veld :attribute moet worden geaccepteerd.',
    'accepted_if' => 'Het veld :attribute moet worden geaccepteerd als :other is :value.',
    'active_url' => 'Het veld :attribute moet een geldige URL zijn.',
    'after' => 'Het veld :attribute moet een datum na :date zijn.',
    'after_or_equal' => 'Het veld :attribute moet een datum na of gelijk aan :date zijn.',
    'alpha' => 'Het veld :attribute mag alleen letters bevatten.',
    'alpha_dash' => 'Het veld :attribute mag alleen letters, getallen, streepjes en underscores bevatten.',
    'alpha_num' => 'Het veld :attribute mag alleen letters en getallen bevatten.',
    'any_of' => 'Het veld :attribute is ongeldig.',
    'array' => 'Het veld :attribute moet een array zijn.',
    'ascii' => 'Het veld :attribute mag alleen single-byte alfanumerieke tekens en symbolen bevatten.',
    'before' => 'Het veld :attribute moet een datum voor :date zijn.',
    'before_or_equal' => 'Het veld :attribute moet een datum voor of gelijk aan :date zijn.',
    'between' => [
        'array' => 'Het veld :attribute moet tussen :min en :max items hebben.',
        'file' => 'Het veld :attribute moet tussen :min en :max kilobytes zijn.',
        'numeric' => 'Het veld :attribute moet tussen :min en :max zijn.',
        'string' => 'Het veld :attribute moet tussen :min en :max tekens zijn.',
    ],
    'boolean' => 'Het veld :attribute moet waar of onwaar zijn.',
    'can' => 'Het veld :attribute bevat een niet-geautoriseerde waarde.',
    'confirmed' => 'De bevestiging van het veld :attribute komt niet overeen.',
    'contains' => 'Het veld :attribute mist een vereiste waarde.',
    'current_password' => 'Het wachtwoord is onjuist.',
    'date' => 'Het veld :attribute moet een geldige datum zijn.',
    'date_equals' => 'Het veld :attribute moet een datum gelijk aan :date zijn.',
    'date_format' => 'Het veld :attribute moet overeenkomen met het formaat :format.',
    'decimal' => 'Het veld :attribute moet :decimal decimale plaatsen hebben.',
    'declined' => 'Het veld :attribute moet worden afgewezen.',
    'declined_if' => 'Het veld :attribute moet worden afgewezen als :other is :value.',
    'different' => 'Het veld :attribute en :other moeten anders zijn.',
    'digits' => 'Het veld :attribute moet :digits cijfers zijn.',
    'digits_between' => 'Het veld :attribute moet tussen :min en :max cijfers zijn.',
    'dimensions' => 'Het veld :attribute heeft ongeldige afmetingen voor afbeeldingen.',
    'distinct' => 'Het veld :attribute heeft een dubbele waarde.',
    'doesnt_contain' => 'Het veld :attribute mag geen van het volgende bevatten: :values.',
    'doesnt_end_with' => 'Het veld :attribute mag niet eindigen met een van het volgende: :values.',
    'doesnt_start_with' => 'Het veld :attribute mag niet beginnen met een van het volgende: :values.',
    'email' => 'Het veld :attribute moet een geldig e-mailadres zijn.',
    'encoding' => 'Het veld :attribute moet in :encoding worden gecodeerd.',
    'ends_with' => 'Het veld :attribute moet eindigen met een van het volgende: :values.',
    'enum' => 'Het geselecteerde :attribute is ongeldig.',
    'exists' => 'Het geselecteerde :attribute is ongeldig.',
    'extensions' => 'Het veld :attribute moet een van de volgende extensies hebben: :values.',
    'file' => 'Het veld :attribute moet een bestand zijn.',
    'filled' => 'Het veld :attribute moet een waarde hebben.',
    'gt' => [
        'array' => 'Het veld :attribute moet meer dan :value items hebben.',
        'file' => 'Het veld :attribute moet groter zijn dan :value kilobytes.',
        'numeric' => 'Het veld :attribute moet groter zijn dan :value.',
        'string' => 'Het veld :attribute moet groter zijn dan :value tekens.',
    ],
    'gte' => [
        'array' => 'Het veld :attribute moet :value items of meer hebben.',
        'file' => 'Het veld :attribute moet groter dan of gelijk aan :value kilobytes zijn.',
        'numeric' => 'Het veld :attribute moet groter dan of gelijk aan :value zijn.',
        'string' => 'Het veld :attribute moet groter dan of gelijk aan :value tekens zijn.',
    ],
    'hex_color' => 'Het veld :attribute moet een geldige hexadecimale kleur zijn.',
    'image' => 'Het veld :attribute moet een afbeelding zijn.',
    'in' => 'Het geselecteerde :attribute is ongeldig.',
    'in_array' => 'Het veld :attribute moet bestaan in :other.',
    'in_array_keys' => 'Het veld :attribute moet ten minste een van de volgende sleutels bevatten: :values.',
    'integer' => 'Het veld :attribute moet een geheel getal zijn.',
    'ip' => 'Het veld :attribute moet een geldig IP-adres zijn.',
    'ipv4' => 'Het veld :attribute moet een geldig IPv4-adres zijn.',
    'ipv6' => 'Het veld :attribute moet een geldig IPv6-adres zijn.',
    'json' => 'Het veld :attribute moet een geldige JSON-string zijn.',
    'list' => 'Het veld :attribute moet een lijst zijn.',
    'lowercase' => 'Het veld :attribute moet kleinletters zijn.',
    'lt' => [
        'array' => 'Het veld :attribute moet minder dan :value items hebben.',
        'file' => 'Het veld :attribute moet kleiner zijn dan :value kilobytes.',
        'numeric' => 'Het veld :attribute moet kleiner zijn dan :value.',
        'string' => 'Het veld :attribute moet kleiner zijn dan :value tekens.',
    ],
    'lte' => [
        'array' => 'Het veld :attribute mag niet meer dan :value items hebben.',
        'file' => 'Het veld :attribute moet kleiner dan of gelijk aan :value kilobytes zijn.',
        'numeric' => 'Het veld :attribute moet kleiner dan of gelijk aan :value zijn.',
        'string' => 'Het veld :attribute moet kleiner dan of gelijk aan :value tekens zijn.',
    ],
    'mac_address' => 'Het veld :attribute moet een geldig MAC-adres zijn.',
    'max' => [
        'array' => 'Het veld :attribute mag niet meer dan :max items hebben.',
        'file' => 'Het veld :attribute mag niet groter zijn dan :max kilobytes.',
        'numeric' => 'Het veld :attribute mag niet groter zijn dan :max.',
        'string' => 'Het veld :attribute mag niet groter zijn dan :max tekens.',
    ],
    'max_digits' => 'Het veld :attribute mag niet meer dan :max cijfers hebben.',
    'mimes' => 'Het veld :attribute moet een bestand van het type zijn: :values.',
    'mimetypes' => 'Het veld :attribute moet een bestand van het type zijn: :values.',
    'min' => [
        'array' => 'Het veld :attribute moet ten minste :min items hebben.',
        'file' => 'Het veld :attribute moet ten minste :min kilobytes zijn.',
        'numeric' => 'Het veld :attribute moet ten minste :min zijn.',
        'string' => 'Het veld :attribute moet ten minste :min tekens zijn.',
    ],
    'min_digits' => 'Het veld :attribute moet ten minste :min cijfers hebben.',
    'missing' => 'Het veld :attribute moet ontbreken.',
    'missing_if' => 'Het veld :attribute moet ontbreken als :other is :value.',
    'missing_unless' => 'Het veld :attribute moet ontbreken tenzij :other is :value.',
    'missing_with' => 'Het veld :attribute moet ontbreken als :values aanwezig is.',
    'missing_with_all' => 'Het veld :attribute moet ontbreken als :values aanwezig zijn.',
    'multiple_of' => 'Het veld :attribute moet een veelvoud van :value zijn.',
    'not_in' => 'Het geselecteerde :attribute is ongeldig.',
    'not_regex' => 'De opmaak van het veld :attribute is ongeldig.',
    'numeric' => 'Het veld :attribute moet een getal zijn.',
    'password' => [
        'letters' => 'Het veld :attribute moet ten minste één letter bevatten.',
        'mixed' => 'Het veld :attribute moet ten minste één hoofdletter en één kleine letter bevatten.',
        'numbers' => 'Het veld :attribute moet ten minste één getal bevatten.',
        'symbols' => 'Het veld :attribute moet ten minste één symbool bevatten.',
        'uncompromised' => 'Het gegeven :attribute is verschenen in een gegevensilek. Kies alstublieft een ander :attribute.',
    ],
    'present' => 'Het veld :attribute moet aanwezig zijn.',
    'present_if' => 'Het veld :attribute moet aanwezig zijn als :other is :value.',
    'present_unless' => 'Het veld :attribute moet aanwezig zijn tenzij :other is :value.',
    'present_with' => 'Het veld :attribute moet aanwezig zijn als :values aanwezig is.',
    'present_with_all' => 'Het veld :attribute moet aanwezig zijn als :values aanwezig zijn.',
    'prohibited' => 'Het veld :attribute is verboden.',
    'prohibited_if' => 'Het veld :attribute is verboden als :other is :value.',
    'prohibited_if_accepted' => 'Het veld :attribute is verboden als :other is geaccepteerd.',
    'prohibited_if_declined' => 'Het veld :attribute is verboden als :other is afgewezen.',
    'prohibited_unless' => 'Het veld :attribute is verboden tenzij :other is :values.',
    'prohibits' => 'Het veld :attribute verbiedt dat :other aanwezig is.',
    'regex' => 'De opmaak van het veld :attribute is ongeldig.',
    'required' => 'Het veld :attribute is vereist.',
    'required_array_keys' => 'Het veld :attribute moet vermeldingen bevatten voor: :values.',
    'required_if' => 'Het veld :attribute is vereist als :other is :value.',
    'required_if_accepted' => 'Het veld :attribute is vereist als :other is geaccepteerd.',
    'required_if_declined' => 'Het veld :attribute is vereist als :other is afgewezen.',
    'required_unless' => 'Het veld :attribute is vereist tenzij :other is :values.',
    'required_with' => 'Het veld :attribute is vereist als :values aanwezig is.',
    'required_with_all' => 'Het veld :attribute is vereist als :values aanwezig zijn.',
    'required_without' => 'Het veld :attribute is vereist als :values niet aanwezig is.',
    'required_without_all' => 'Het veld :attribute is vereist als geen van :values aanwezig zijn.',
    'same' => 'Het veld :attribute moet overeenkomen met :other.',
    'size' => [
        'array' => 'Het veld :attribute moet :size items bevatten.',
        'file' => 'Het veld :attribute moet :size kilobytes zijn.',
        'numeric' => 'Het veld :attribute moet :size zijn.',
        'string' => 'Het veld :attribute moet :size tekens zijn.',
    ],
    'starts_with' => 'Het veld :attribute moet beginnen met een van het volgende: :values.',
    'string' => 'Het veld :attribute moet een string zijn.',
    'timezone' => 'Het veld :attribute moet een geldige tijdzone zijn.',
    'unique' => 'Het :attribute is al genomen.',
    'uploaded' => 'Het :attribute kon niet worden geüpload.',
    'uppercase' => 'Het veld :attribute moet in hoofdletters zijn.',
    'url' => 'Het veld :attribute moet een geldige URL zijn.',
    'ulid' => 'Het veld :attribute moet een geldige ULID zijn.',
    'uuid' => 'Het veld :attribute moet een geldige UUID zijn.',

    /*
    |--------------------------------------------------------------------------
    | Aangepaste validatietaalregels
    |--------------------------------------------------------------------------
    |
    | Hier kunt u aangepaste validatieberichten opgeven voor attributen met
    | behulp van de conventie "attribute.rule" om de regels te benoemen. Dit
    | maakt het snel om een specifieke aangepaste taalregel voor een gegeven
    | attribuut op te geven.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Aangepaste validatiekenmerken
    |--------------------------------------------------------------------------
    |
    | De volgende taalregels worden gebruikt om onze kenmerk-placeholder
    | met iets vriendelijker in te ruilen, zoals "E-mailadres" in plaats van
    | "e-mail". Dit helpt ons eenvoudig onze bericht meer expressief te maken.
    |
    */

    'attributes' => [],

];
