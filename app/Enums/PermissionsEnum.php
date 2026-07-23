<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    // Auth
    case AuthRegister = 'auth.register';
    case AuthLogin = 'auth.login';
    case AuthPasswordForgot = 'auth.password.forgot';
    case AuthPasswordReset = 'auth.password.reset';
    case AuthLogout = 'auth.logout';
    case AuthVerificationSend = 'auth.verification.send';
    case AuthVerificationVerify = 'auth.verification.verify';

    // Events
    case EventsList = 'events.list';
    case EventsStore = 'events.store';
    case EventsShow = 'events.show';
    case EventsPatch = 'events.patch';
    case EventsDestroy = 'events.destroy';

    // Competition Types
    case CompetitionTypesList = 'competition-types.list';
    case CompetitionTypesStore = 'competition-types.store';
    case CompetitionTypesShow = 'competition-types.show';
    case CompetitionTypesPatch = 'competition-types.patch';
    case CompetitionTypesDestroy = 'competition-types.destroy';

    // Competitions
    case CompetitionsList = 'competitions.list';
    case CompetitionsStore = 'competitions.store';
    case CompetitionsShow = 'competitions.show';
    case CompetitionsPatch = 'competitions.patch';
    case CompetitionsDestroy = 'competitions.destroy';

    // Competition Categories
    case CompetitionCategoriesList = 'competitions.competition-categories.list';
    case CompetitionCategoriesStore = 'competitions.competition-categories.store';
    case CompetitionCategoriesShow = 'competitions.competition-categories.show';
    case CompetitionCategoriesPatch = 'competitions.competition-categories.patch';
    case CompetitionCategoriesDestroy = 'competitions.competition-categories.destroy';

    // Participation Types
    case ParticipationTypesList = 'participation-types.list';
    case ParticipationTypesStore = 'participation-types.store';
    case ParticipationTypesShow = 'participation-types.show';
    case ParticipationTypesPatch = 'participation-types.patch';
    case ParticipationTypesDestroy = 'participation-types.destroy';

    // Competition Category Participations
    case CompetitionCategoryParticipationsList = 'competition-categories.participations.list';
    case CompetitionCategoryParticipationsStore = 'competition-categories.participations.store';
    case CompetitionCategoryParticipationsShow = 'competition-categories.participations.show';
    case CompetitionCategoryParticipationsPatch = 'competition-categories.participations.patch';
    case CompetitionCategoryParticipationsDestroy = 'competition-categories.participations.destroy';

    // Schools
    case SchoolsList = 'schools.list';
    case SchoolsStore = 'schools.store';
    case SchoolsShow = 'schools.show';
    case SchoolsPatch = 'schools.patch';
    case SchoolsDestroy = 'schools.destroy';

    // Students
    case SchoolStudentsList = 'schools.students.list';
    case SchoolStudentsStore = 'schools.students.store';
    case SchoolStudentsShow = 'schools.students.show';
    case SchoolStudentsPatch = 'schools.students.patch';
    case SchoolStudentsDestroy = 'schools.students.destroy';

    // Teams
    case SchoolTeamsList = 'schools.teams.list';
    case SchoolTeamsStore = 'schools.teams.store';
    case SchoolTeamsShow = 'schools.teams.show';
    case SchoolTeamsPatch = 'schools.teams.patch';
    case SchoolTeamsDestroy = 'schools.teams.destroy';

    // Team Members
    case SchoolTeamMembersList = 'schools.teams.members.list';
    case SchoolTeamMembersStore = 'schools.teams.members.store';
    case SchoolTeamMembersShow = 'schools.teams.members.show';
    case SchoolTeamMembersPatch = 'schools.teams.members.patch';
    case SchoolTeamMembersDestroy = 'schools.teams.members.destroy';

    // Registrations
    case SchoolRegistrationsList = 'schools.registrations.list';
    case SchoolRegistrationsStore = 'schools.registrations.store';
    case SchoolRegistrationsShow = 'schools.registrations.show';
    case SchoolRegistrationsPatch = 'schools.registrations.patch';
    case SchoolRegistrationsDestroy = 'schools.registrations.destroy';

    // Officials
    case OfficialsList = 'officials.list';
    case OfficialsStore = 'officials.store';
    case OfficialsShow = 'officials.show';
    case OfficialsPatch = 'officials.patch';
    case OfficialsDestroy = 'officials.destroy';
}
