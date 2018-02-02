<?php
namespace App\Model;

class Permission {
    //STORED permissions for normal users
    const ALLOW_LOGIN = "ROLES_LOGIN";
    const ALLOW_SCHOOL = "ROLES_SCHOOL";
    const ALLOW_SCHOOL_DOWNLOAD = "ROLES_SCHOOL_DOWNLOAD";
    const ALLOW_SCHOOL_POST = "ROLES_SCHOOL_POST";
    const ALLOW_GAMES = "ROLES_GAMES";
    const ALLOW_GAMES_SUBMIT = "ROLES_GAMES_SUBMIT";
    const ALLOW_ALUMNI = "ROLES_ALUMNI";
    const ALLOW_MEDIA = "ROLES_MEDIA";
    const ALLOW_MEDIA_COMMENT = "ROLES_MEDIACOMMENT";
    const ALLOW_MESSAGING = "ROLES_MESSAGING";
    const ALLOW_FEEDBACK = "ROLES_ALLOW_FEEDBACK";

    //ADMIN permissions
    const IS_ADMIN = "ROLE_ADMIN";
    const IS_ADMIN_ALUMNI = "ROLES_ADMIN_ALUMNI";
    const IS_ADMIN_MEDIA = "ROLES_ADMIN_MEDIA";
    const IS_ADMIN_GAME = "ROLES_ADMIN_GAME";

    //DYNAMIC permissions for normal users
    const IS_AUTHENTICATED = "ROLES_AUTHENTICATED";
    const IS_TEACHER = "ROLES_TEACHERS";
    const IS_STUDENT = "ROLES_STUDENTS";
    const IS_GRADUATE = "ROLES_GRADUATES";
    const HAS_PHONE = "ROLES_HAVE_PHONE";
    const IS_LOGIN = "IS_AUTHENTICATED_FULLY";

    const PERMISSION_ARRAY = [
        self::ALLOW_LOGIN,
        self::ALLOW_SCHOOL,
        self::ALLOW_SCHOOL_DOWNLOAD,
        self::ALLOW_SCHOOL_DOWNLOAD,
        self::ALLOW_GAMES,
        self::ALLOW_GAMES_SUBMIT,
        self::ALLOW_ALUMNI,
        self::ALLOW_MEDIA,
        self::ALLOW_MEDIA_COMMENT,
        self::ALLOW_MESSAGING,
        self::ALLOW_FEEDBACK,
        self::IS_ADMIN,
        self::IS_ADMIN_ALUMNI,
        self::IS_ADMIN_MEDIA,
        self::IS_ADMIN_GAME];

}