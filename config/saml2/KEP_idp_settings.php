<?php

// If you choose to use ENV vars to define these values, give this IdP its own env var names
// so you can define different values for each IdP, all starting with 'SAML2_'.$this_idp_env_id
$this_idp_env_id = 'KEP';

//This is variable is for simplesaml example only.
// For real IdP, you must set the url values in the 'idp' config to conform to the IdP's real urls.
$idp_host = env('SAML2_'.$this_idp_env_id.'_IDP_HOST', 'http://localhost:8000/simplesaml');

return $settings = array(

    /*****
     * One Login Settings
     */

    // If 'strict' is True, then the PHP Toolkit will reject unsigned
    // or unencrypted messages if it expects them signed or encrypted
    // Also will reject the messages if not strictly follow the SAML
    // standard: Destination, NameId, Conditions ... are validated too.
    'strict' => true, //@todo: make this depend on laravel config

    // Enable debug mode (to print errors)
    'debug' => env('APP_DEBUG', false),

    // Service Provider Data that we are deploying
    'sp' => array(

        // Specifies constraints on the name identifier to be used to
        // represent the requested subject.
        // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',

        // Usually x509cert and privateKey of the SP are provided by files placed at
        // the certs folder. But we can also provide them with the following parameters
        'x509cert' => env('SAML2_'.$this_idp_env_id.'_SP_x509',''),
        'privateKey' => env('SAML2_'.$this_idp_env_id.'_SP_PRIVATEKEY',''),

        // Identifier (URI) of the SP entity.
        // Leave blank to use the '{idpName}_metadata' route, e.g. 'test_metadata'.
        'entityId' => env('SAML2_'.$this_idp_env_id.'_SP_ENTITYID',''),

        // Specifies info about where and how the <AuthnResponse> message MUST be
        // returned to the requester, in this case our SP.
        'assertionConsumerService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-POST binding.
            // Leave blank to use the '{idpName}_acs' route, e.g. 'test_acs'
            'url' => '',
        ),
        // Specifies info about where and how the <Logout Response> message MUST be
        // returned to the requester, in this case our SP.
        // Remove this part to not include any URL Location in the metadata.
        'singleLogoutService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-Redirect binding.
            // Leave blank to use the '{idpName}_sls' route, e.g. 'test_sls'
            'url' => '',
        ),
    ),

    // Identity Provider Data that we want connect with our SP
    'idp' => array(
        // Identifier of the IdP entity  (must be a URI)
        'entityId' => env('SAML2_'.$this_idp_env_id.'_IDP_ENTITYID', $idp_host . '/saml2/idp/metadata.php'),
        // SSO endpoint info of the IdP. (Authentication Request protocol)
        'singleSignOnService' => array(
            // URL Target of the IdP where the SP will send the Authentication Request Message,
            // using HTTP-Redirect binding.
            'url' => env('SAML2_'.$this_idp_env_id.'_IDP_SSO_URL', $idp_host . '/saml2/idp/SSOService.php'),
        ),
        // SLO endpoint info of the IdP.
        'singleLogoutService' => array(
            // URL Location of the IdP where the SP will send the SLO Request,
            // using HTTP-Redirect binding.
            'url' => env('SAML2_'.$this_idp_env_id.'_IDP_SL_URL', $idp_host . '/saml2/idp/SingleLogoutService.php'),
        ),
        // Public x509 certificate of the IdP
        'x509cert' => env('SAML2_'.$this_idp_env_id.'_IDP_x509', 'MIIFJTCCA42gAwIBAgIUPMLhvNXypLwNhRnqdiuu05jNcYEwDQYJKoZIhvcNAQEL
BQAwgaExCzAJBgNVBAYTAklOMRQwEgYDVQQIDAtNYWhhcmFzaHRyYTENMAsGA1UE
BwwEUHVuZTEjMCEGA1UECgwaQ2FydmluZyBJVCBQcml2YXRlIExpbWl0ZWQxCzAJ
BgNVBAsMAklUMRcwFQYDVQQDDA5LZXRhbiBLdWxrYXJuaTEiMCAGCSqGSIb3DQEJ
ARYTa2V0YW5AY2FydmluZ2l0LmNvbTAeFw0yMTA1MTcxNTI1NDhaFw0zMTA1MTcx
NTI1NDhaMIGhMQswCQYDVQQGEwJJTjEUMBIGA1UECAwLTWFoYXJhc2h0cmExDTAL
BgNVBAcMBFB1bmUxIzAhBgNVBAoMGkNhcnZpbmcgSVQgUHJpdmF0ZSBMaW1pdGVk
MQswCQYDVQQLDAJJVDEXMBUGA1UEAwwOS2V0YW4gS3Vsa2FybmkxIjAgBgkqhkiG
9w0BCQEWE2tldGFuQGNhcnZpbmdpdC5jb20wggGiMA0GCSqGSIb3DQEBAQUAA4IB
jwAwggGKAoIBgQCsZNJ8ap9ac1ERpfDQqoHWix6trJq2ngE5TG5SUqWYV1a32C22
wUVfgaGmqPpRmUJzJUU+gD9qYDNf/a6Is1YIoO/YoL3pthtrMCvwbHlZs+P7IqM/
UOtqnBvKOxttLTSjGUO3hTxuz7zXs2SugWqOxSvXcXyhMbve5cvluggtoe0jQMs6
WedJBEafV4j/Uo0wYjtK42BsUPfK6CFtqsOnaaBsyzNYXQj+kZgPwkCW3GNM9lXD
EtCd9H4eois5Re1ERRiUZPqXZiJfVQB9JNqQc3GoCjo3gqNPAahloFeKpcMRgsjG
mGNi+v8UOM0yGemv39bO2hE6Ptla61fEE316J9H5dM1aNygSV9yI+AvFuzwOUPrX
mpi16cN1gp9F5QilMigWb/YcMnpn2Cxa91Hqsgfebqa104VleMIiWgekeCOXUD/P
we4ndCCkwFkKi+W4JXY+WH8ynZoO8+sYoCwDlFffo5s9lvWsqGhHXkF4aawFuXxl
4wnv6qrJAn4sWLsCAwEAAaNTMFEwHQYDVR0OBBYEFB/AyYmpD6LxJqtlSqhKQ5aB
mdVNMB8GA1UdIwQYMBaAFB/AyYmpD6LxJqtlSqhKQ5aBmdVNMA8GA1UdEwEB/wQF
MAMBAf8wDQYJKoZIhvcNAQELBQADggGBADFFtZ7ZZjtBJgloKurCBxRVzKOvTv7v
qIPYHHmmYQD/Sa5yf2HXndaLmFwa2mQQL53v4jBhxc1pDcobQp/CikTGascvvh28
6Em2bawXFPgiXcqtIB2Ij849lq8uLOdNHQd8HizXZUJ3WI2D7K/goC0TwNhn1NtV
54CWgC2Z5toTHu6dvlLwzbI+NzreNnd9PR4NwOrmPNpR1mafnjdnezLWDL/Zs3zB
t//6cF44G02O5r3CRq0tFvDKuTI1iKLrcvNb4FhQng3cBFITDXcM75udPQ2EPEun
ax69B/IRyL98c9KRh978/3QyROOGBHSuFVu2gc2zQQWRHck9VXWco4ekhxMcxQmg
SIKySYPk60tVlf5Nw9l5G73xA1CtkhZBOqtoVT7QwexmYoAff4kt2QCvAWohtZOj
Vbyz/8WeZ7mnDXLJ0oWmYr6khAeGHDTbEkQNztFgvkRa5D9rEbBBtafP7Suxyu39
nnHGu54Wjm7n6HFvbbcNjgeGjyjPvKqp6Q=='),
        /*
         *  Instead of use the whole x509cert you can use a fingerprint
         *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it)
         */
        // 'certFingerprint' => '',
    ),



    /***
     *
     *  OneLogin advanced settings
     *
     *
     */
    // Security settings
    'security' => array(

        /** signatures and encryptions offered */

        // Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
        // will be encrypted.
        'nameIdEncrypted' => false,

        // Indicates whether the <samlp:AuthnRequest> messages sent by this SP
        // will be signed.              [The Metadata of the SP will offer this info]
        'authnRequestsSigned' => false,

        // Indicates whether the <samlp:logoutRequest> messages sent by this SP
        // will be signed.
        'logoutRequestSigned' => false,

        // Indicates whether the <samlp:logoutResponse> messages sent by this SP
        // will be signed.
        'logoutResponseSigned' => false,

        /* Sign the Metadata
         False || True (use sp certs) || array (
                                                    keyFileName => 'metadata.key',
                                                    certFileName => 'metadata.crt'
                                                )
        */
        'signMetadata' => false,


        /** signatures and encryptions required **/

        // Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
        // <samlp:LogoutResponse> elements received by this SP to be signed.
        'wantMessagesSigned' => false,

        // Indicates a requirement for the <saml:Assertion> elements received by
        // this SP to be signed.        [The Metadata of the SP will offer this info]
        'wantAssertionsSigned' => false,

        // Indicates a requirement for the NameID received by
        // this SP to be encrypted.
        'wantNameIdEncrypted' => false,

        // Authentication context.
        // Set to false and no AuthContext will be sent in the AuthNRequest,
        // Set true or don't present thi parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
        // Set an array with the possible auth context values: array ('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'),
        'requestedAuthnContext' => true,
    ),

    // Contact information template, it is recommended to suply a technical and support contacts
    'contactPerson' => array(
        'technical' => array(
            'givenName' => 'name',
            'emailAddress' => 'no@reply.com'
        ),
        'support' => array(
            'givenName' => 'Support',
            'emailAddress' => 'no@reply.com'
        ),
    ),

    // Organization information template, the info in en_US lang is recomended, add more if required
    'organization' => array(
        'en-US' => array(
            'name' => 'Name',
            'displayname' => 'Display Name',
            'url' => 'http://url'
        ),
    ),

/* Interoperable SAML 2.0 Web Browser SSO Profile [saml2int]   http://saml2int.org/profile/current

   'authnRequestsSigned' => false,    // SP SHOULD NOT sign the <samlp:AuthnRequest>,
                                      // MUST NOT assume that the IdP validates the sign
   'wantAssertionsSigned' => true,
   'wantAssertionsEncrypted' => true, // MUST be enabled if SSL/HTTPs is disabled
   'wantNameIdEncrypted' => false,
*/

);
