<?php

namespace App\Enums;

enum Permissions: string
{
    case VIEW_PATIENTS = 'view patients';
    case CREATE_PATIENTS = 'create patients';
    case EDIT_PATIENTS = 'edit patients';
    case DELETE_PATIENTS = 'delete patients';
    case MANAGE_PATIENTS = 'manage patients';

    case VIEW_VISITS = 'view visits';
    case CREATE_VISITS = 'create visits';
    case EDIT_VISITS = 'edit visits';

        // nurse, doctor
    case VIEW_ADMISSIONS = 'view admissions';
    case CREATE_ADMISSIONS = 'create admissions';
    case EDIT_ADMISSIONS = 'edit admissions';
    case ASSIGN_WARD = 'assign ward';

    case VIEW_BILLS = 'view bills';
    case CREATE_BILLS = 'create bills';
    case EDIT_BILLS = 'edit bills';

    case MANAGE_USERS = 'manage users';
    case MANAGE_ROLES = 'manage roles';

    case CREATE_POSTS = 'posts.create';
    case VIEW_POSTS = 'posts.views';
    case EDIT_POSTS = 'posts.edit';
    case DELETE_POSTS = 'posts.delete';

    case CREATE_VITALS = 'vitals.create';
    case CREATE_DRUG_ADMINISTRATION = 'create drug administration';
    case EDIT_NOTES = 'notes.update';
    case DELETE_NOTE = 'notes.delete';

    // pharmacy
    case VIEW_PRESCRIPTIONS = 'prescriptions.view';
    case EDIT_PRESCRIPTIONS = 'prescriptions.edit';
    case DELETE_PRESCRIPTIONS = 'prescriptions.delete';
    case MANAGE_PRESCRIPTIONS = 'prescriptions.manage';

    // Lab
    case ORDER_TEST = 'tests.order';
    case MANAGE_TESTS = 'tests.manage';
    case DELETE_TEST = 'tests.delete';

    // Radiology
    case MANAGE_SCANS = 'scans.manage';
    case DELETE_SCANS = 'scans.delete';

    // Finance & Accounting
    case VIEW_ACCOUNTS = 'finance.view-accounts';
    case VIEW_PAYMENTS = 'finance.view-payments';
    case GET_PAYMENT_REPORTS = 'finance.export-payment-reports';
}
