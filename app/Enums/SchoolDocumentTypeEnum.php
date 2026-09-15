<?php

namespace App\Enums;

enum SchoolDocumentTypeEnum: string
{
    case CambridgeAffiliation = 'cambridge_affiliation';
    case MatriculationBoard = 'matriculation_board';
    case SchoolRegistrationCertificate = 'school_registration_certificate';
    case BoardAffiliationLetter = 'board_affiliation_letter';
    case PrincipalAuthorization = 'principal_authorization';
    case SchoolProfileDocument = 'school_profile_document';
}
