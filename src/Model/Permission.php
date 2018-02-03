<?php
namespace App\Model;

class Permission {

    //ADMIN permissions
    const IS_ADMIN = "ROLE_ADMIN";

    //DYNAMIC permissions for normal users
    const IS_AUTHENTICATED = "ROLE_AUTHENTICATED";
    const IS_TEACHER = "ROLE_TEACHERS";
    const IS_STUDENT = "ROLE_STUDENTS";
    const IS_GRADUATE = "ROLE_GRADUATES";
    const HAS_PHONE = "ROLE_HAVE_PHONE";
    const IS_LOGIN = "IS_AUTHENTICATED_FULLY";


}