--
-- PostgreSQL database dump
--

\restrict uZP6DmQPQcwST2HIsbOn2EJNgslwNpKyCOA5gmuAOdqkBJlJRTUhbxcxiwe0KVX

-- Dumped from database version 16.11 (Ubuntu 16.11-0ubuntu0.24.04.1)
-- Dumped by pg_dump version 16.11 (Ubuntu 16.11-0ubuntu0.24.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: item_cost_source; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.item_cost_source AS ENUM (
    'GRN',
    'MANUAL',
    'AUTO_ADJUST',
    'TRANSFER'
);


--
-- Name: stock_tx_type; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.stock_tx_type AS ENUM (
    'RECEIPT',
    'ISSUE',
    'TRANSFER',
    'ADJUSTMENT',
    'RETURN',
    'DISPOSAL'
);


--
-- Name: apply_stock_transaction(bigint, bigint, bigint, numeric); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.apply_stock_transaction(p_item_id bigint, p_location_id bigint, p_lot_id bigint, p_qty_delta numeric) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
         DECLARE v_new_qty NUMERIC;
         BEGIN
            -- Insert if not exists
            INSERT INTO inventory_balances (item_id, location_id, lot_id, qty_on_hand, last_updated, created_at, updated_at)
            VALUES (p_item_id, p_location_id, p_lot_id, 0, NOW(), NOW(), NOW())
            ON CONFLICT (item_id, location_id, lot_id) DO NOTHING;

            -- Update qty_on_hand atomically
            UPDATE inventory_balances
            SET qty_on_hand = qty_on_hand + p_qty_delta,
                last_updated = NOW(),
                updated_at = NOW()
            WHERE item_id = p_item_id
                AND location_id = p_location_id
                AND (lot_id IS NOT DISTINCT FROM p_lot_id)
            RETURNING qty_on_hand INTO v_new_qty;

            RETURN v_new_qty;
        END;
        $$;


--
-- Name: rebuild_inventory_balances(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.rebuild_inventory_balances() RETURNS void
    LANGUAGE plpgsql
    AS $$
        BEGIN
            TRUNCATE inventory_balances RESTART IDENTITY;

            INSERT INTO inventory_balances (
                item_id, location_id, lot_id,
                qty_on_hand, last_updated,
                created_at, updated_at
            )
            SELECT
                item_id,
                location_id,
                lot_id,
                SUM(qty_delta) AS qty_on_hand,
                MAX(ts) AS last_updated,
                NOW(), NOW()
            FROM (
                -- INBOUND: to_location_id is REAL (not virtual)
                SELECT
                    item_id,
                    to_location_id AS location_id,
                    lot_id,
                    quantity AS qty_delta,
                    created_at AS ts
                FROM stock_transactions
                WHERE to_location_id NOT IN (0, 1000)

                UNION ALL

                -- OUTBOUND: from_location_id is REAL (not virtual)
                SELECT
                    item_id,
                    from_location_id AS location_id,
                    lot_id,
                    -quantity AS qty_delta,
                    created_at AS ts
                FROM stock_transactions
                WHERE from_location_id NOT IN (0, 1000)
            ) AS tx
            GROUP BY item_id, location_id, lot_id;
        END;
        $$;


--
-- Name: trg_after_stock_transaction(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.trg_after_stock_transaction() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
        BEGIN
            -- Inbound: from virtual location → real location
            IF NEW.to_location_id NOT IN (0, 1000) THEN
                PERFORM apply_stock_transaction(
                    NEW.item_id,
                    NEW.to_location_id,
                    NEW.lot_id,
                    NEW.quantity
                );
            END IF;

            -- Outbound: from real location → virtual location
            IF NEW.from_location_id NOT IN (0, 1000) THEN
                PERFORM apply_stock_transaction(
                    NEW.item_id,
                    NEW.from_location_id,
                    NEW.lot_id,
                    -NEW.quantity
                );
            END IF;

            RETURN NEW;
        END;
        $$;


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: admission_logs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.admission_logs (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    vitals json,
    treatments_delivered json,
    output_measures json,
    admission_id bigint NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: admission_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.admission_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: admission_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.admission_logs_id_seq OWNED BY public.admission_logs.id;


--
-- Name: admission_plans; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.admission_plans (
    id bigint NOT NULL,
    admission_id bigint,
    user_id bigint NOT NULL,
    indication character varying(255) NOT NULL,
    note character varying(512),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status smallint DEFAULT '1'::smallint NOT NULL
);


--
-- Name: admission_plans_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.admission_plans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: admission_plans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.admission_plans_id_seq OWNED BY public.admission_plans.id;


--
-- Name: admission_treatment_administrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.admission_treatment_administrations (
    id bigint NOT NULL,
    treatment_id bigint NOT NULL,
    minister_id bigint NOT NULL,
    admission_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: admission_treatment_administrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.admission_treatment_administrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: admission_treatment_administrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.admission_treatment_administrations_id_seq OWNED BY public.admission_treatment_administrations.id;


--
-- Name: admissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.admissions (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    visit_id bigint NOT NULL,
    ward_id bigint,
    discharged_on timestamp(0) without time zone,
    discharge_summary text,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    admittable_type character varying(255) NOT NULL,
    admittable_id bigint NOT NULL,
    status smallint DEFAULT '1'::smallint NOT NULL
);


--
-- Name: admissions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.admissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: admissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.admissions_id_seq OWNED BY public.admissions.id;


--
-- Name: anc_visits; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.anc_visits (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    vitals_by bigint,
    doctor_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    antenatal_profile_id bigint,
    presentation character varying(255),
    lie character varying(255),
    fundal_height character varying(255),
    fetal_heart_rate character varying(255),
    presentation_relationship character varying(255),
    edema character varying(255),
    protein character varying(255),
    glucose character varying(255),
    vdrl character varying(255),
    pcv character varying(255),
    drugs character varying(255),
    note character varying(255),
    return_visit date,
    tt boolean,
    ipt boolean
);


--
-- Name: anc_visits_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.anc_visits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: anc_visits_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.anc_visits_id_seq OWNED BY public.anc_visits.id;


--
-- Name: antenatal_profiles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.antenatal_profiles (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    lmp date,
    edd date,
    card_type character varying(255) DEFAULT '1'::character varying NOT NULL,
    spouse_name character varying(255),
    spouse_phone character varying(255),
    spouse_occupation character varying(255),
    spouse_educational_status character varying(255),
    gravida character varying(255),
    parity character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status smallint DEFAULT '1'::smallint NOT NULL,
    presentation character varying(255),
    lie character varying(255),
    fundal_height character varying(255),
    fetal_heart_rate character varying(255),
    note character varying(255),
    presentation_relationship character varying(255),
    drugs character varying(255),
    next_visit date,
    vitals json,
    awaiting_lab boolean DEFAULT true NOT NULL,
    awaiting_vitals boolean DEFAULT true NOT NULL,
    awaiting_doctor boolean DEFAULT true NOT NULL,
    risk_assessment character varying(255),
    closed_on timestamp(0) without time zone,
    closed_by bigint,
    close_reason text,
    closed_date timestamp(0) without time zone,
    examination json
);


--
-- Name: antenatal_profiles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.antenatal_profiles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: antenatal_profiles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.antenatal_profiles_id_seq OWNED BY public.antenatal_profiles.id;


--
-- Name: bill_details; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.bill_details (
    id bigint NOT NULL,
    bill_id bigint NOT NULL,
    user_id bigint NOT NULL,
    description character varying(255) NOT NULL,
    quantity integer DEFAULT 1 NOT NULL,
    unit_price numeric(10,2) NOT NULL,
    total_price numeric(10,2) NOT NULL,
    tag character varying(255),
    meta json,
    chargeable_type character varying(255),
    chargeable_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    quoted_at timestamp(0) without time zone,
    quoted_by bigint,
    status smallint
);


--
-- Name: bill_details_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.bill_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: bill_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.bill_details_id_seq OWNED BY public.bill_details.id;


--
-- Name: bill_payments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.bill_payments (
    id bigint NOT NULL,
    bill_id bigint NOT NULL,
    user_id bigint NOT NULL,
    payment_date timestamp(0) without time zone NOT NULL,
    amount numeric(10,2) NOT NULL,
    payment_method character varying(255) NOT NULL,
    reference character varying(255),
    notes text,
    status smallint DEFAULT '3'::smallint NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: bill_payments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.bill_payments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: bill_payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.bill_payments_id_seq OWNED BY public.bill_payments.id;


--
-- Name: bills; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.bills (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    bill_number character varying(255) NOT NULL,
    bill_date timestamp(0) without time zone NOT NULL,
    paid_amount numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    status smallint DEFAULT '7'::smallint NOT NULL,
    created_by bigint NOT NULL,
    billable_type character varying(255) NOT NULL,
    billable_id bigint NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: bills_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.bills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: bills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.bills_id_seq OWNED BY public.bills.id;


--
-- Name: consultation_notes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.consultation_notes (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    consultant_id bigint NOT NULL,
    visit_id bigint NOT NULL,
    note text NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    visit_type character varying(255) DEFAULT 'App\Models\Visit'::character varying,
    code character varying(255)
);


--
-- Name: consultation_notes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.consultation_notes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: consultation_notes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.consultation_notes_id_seq OWNED BY public.consultation_notes.id;


--
-- Name: datalogs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.datalogs (
    id bigint NOT NULL,
    action character varying(255) NOT NULL,
    user_id bigint NOT NULL,
    data json NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: datalogs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.datalogs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: datalogs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.datalogs_id_seq OWNED BY public.datalogs.id;


--
-- Name: departments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.departments (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    code character varying(8)
);


--
-- Name: departments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.departments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: departments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.departments_id_seq OWNED BY public.departments.id;


--
-- Name: dispense_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.dispense_lines (
    id bigint NOT NULL,
    source_type character varying(255) NOT NULL,
    source_id bigint NOT NULL,
    qty_dispensed numeric(8,2) NOT NULL,
    user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: dispense_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.dispense_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: dispense_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.dispense_lines_id_seq OWNED BY public.dispense_lines.id;


--
-- Name: documentation_complaints; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.documentation_complaints (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    documentation_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    documentable_type character varying(255) NOT NULL,
    documentable_id bigint NOT NULL,
    duration character varying(255)
);


--
-- Name: documentation_complaints_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.documentation_complaints_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: documentation_complaints_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.documentation_complaints_id_seq OWNED BY public.documentation_complaints.id;


--
-- Name: documentation_prescriptions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.documentation_prescriptions (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    dosage character varying(255),
    duration character varying(255),
    comment character varying(255),
    requested_by bigint,
    dispensed_by bigint,
    status smallint DEFAULT '3'::smallint NOT NULL,
    prescriptionable_type character varying(255) NOT NULL,
    prescriptionable_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    frequency character varying(255),
    available boolean DEFAULT false NOT NULL,
    amount numeric(8,2),
    route character varying(255),
    event_type character varying(255) NOT NULL,
    event_id bigint NOT NULL,
    deleted_at timestamp(0) without time zone
);


--
-- Name: documentation_prescriptions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.documentation_prescriptions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: documentation_prescriptions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.documentation_prescriptions_id_seq OWNED BY public.documentation_prescriptions.id;


--
-- Name: documentation_tests; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.documentation_tests (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    results json,
    tested_by bigint,
    status smallint DEFAULT '3'::smallint NOT NULL,
    testable_type character varying(255) NOT NULL,
    testable_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    describable_type character varying(255),
    describable_id bigint
);


--
-- Name: documentation_tests_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.documentation_tests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: documentation_tests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.documentation_tests_id_seq OWNED BY public.documentation_tests.id;


--
-- Name: documentations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.documentations (
    id bigint NOT NULL,
    visit_id bigint NOT NULL,
    patient_id bigint NOT NULL,
    user_id bigint NOT NULL,
    symptoms character varying(255),
    prognosis character varying(255),
    comment character varying(255),
    status smallint,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    complaints_history character varying(255),
    complaints_durations character varying(255)
);


--
-- Name: documentations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.documentations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: documentations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.documentations_id_seq OWNED BY public.documentations.id;


--
-- Name: documented_diagnoses; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.documented_diagnoses (
    id bigint NOT NULL,
    diagnosable_type character varying(255) NOT NULL,
    diagnosable_id bigint NOT NULL,
    diagnoses character varying(255) NOT NULL,
    user_id bigint NOT NULL,
    patient_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: documented_diagnoses_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.documented_diagnoses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: documented_diagnoses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.documented_diagnoses_id_seq OWNED BY public.documented_diagnoses.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: general_visits; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.general_visits (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    vitals_by bigint,
    doctor_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: general_visits_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.general_visits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: general_visits_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.general_visits_id_seq OWNED BY public.general_visits.id;


--
-- Name: insurance_authorizations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.insurance_authorizations (
    id bigint NOT NULL,
    authorizable_type character varying(255) NOT NULL,
    authorizable_id bigint NOT NULL,
    authorization_code character varying(255) NOT NULL,
    patient_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: insurance_authorizations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.insurance_authorizations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: insurance_authorizations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.insurance_authorizations_id_seq OWNED BY public.insurance_authorizations.id;


--
-- Name: insurance_profiles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.insurance_profiles (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    hmo_name character varying(255) NOT NULL,
    hmo_company character varying(255),
    hmo_id_no character varying(255),
    status smallint DEFAULT '3'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: insurance_profiles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.insurance_profiles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: insurance_profiles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.insurance_profiles_id_seq OWNED BY public.insurance_profiles.id;


--
-- Name: inventory_balances; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.inventory_balances (
    id bigint NOT NULL,
    item_id bigint NOT NULL,
    location_id bigint NOT NULL,
    lot_id bigint,
    qty_on_hand numeric(14,4) DEFAULT '0'::numeric NOT NULL,
    last_updated timestamp(0) with time zone DEFAULT now() NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: inventory_balances_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.inventory_balances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: inventory_balances_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.inventory_balances_id_seq OWNED BY public.inventory_balances.id;


--
-- Name: lab_test_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lab_test_categories (
    id bigint NOT NULL,
    test_id bigint NOT NULL,
    category_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: lab_test_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lab_test_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lab_test_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lab_test_categories_id_seq OWNED BY public.lab_test_categories.id;


--
-- Name: lab_tests; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lab_tests (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255),
    price numeric(8,2),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: lab_tests_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lab_tests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lab_tests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lab_tests_id_seq OWNED BY public.lab_tests.id;


--
-- Name: locations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.locations (
    id bigint NOT NULL,
    code character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    type character varying(255),
    parent_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: locations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.locations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: locations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.locations_id_seq OWNED BY public.locations.id;


--
-- Name: media; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.media (
    id bigint NOT NULL,
    medially_type character varying(255) NOT NULL,
    medially_id bigint NOT NULL,
    file_url text NOT NULL,
    file_name character varying(255) NOT NULL,
    file_type character varying(255),
    size bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: media_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.media_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: media_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.media_id_seq OWNED BY public.media.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: notifications; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.notifications (
    id uuid NOT NULL,
    type character varying(255) NOT NULL,
    notifiable_type character varying(255) NOT NULL,
    notifiable_id bigint NOT NULL,
    data text NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: operation_notes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.operation_notes (
    id bigint NOT NULL,
    unit character varying(255),
    consultant character varying(255) NOT NULL,
    operation_date date NOT NULL,
    surgeons character varying(255) NOT NULL,
    assistants character varying(255) NOT NULL,
    scrub_nurse character varying(255) NOT NULL,
    circulating_nurse character varying(255),
    anaesthesists character varying(255),
    anaesthesia_type character varying(255),
    indication text NOT NULL,
    incision text,
    findings text NOT NULL,
    procedure text NOT NULL,
    patient_id bigint NOT NULL,
    user_id bigint NOT NULL,
    admission_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: operation_notes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.operation_notes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: operation_notes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.operation_notes_id_seq OWNED BY public.operation_notes.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: patient_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.patient_categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: patient_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.patient_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: patient_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.patient_categories_id_seq OWNED BY public.patient_categories.id;


--
-- Name: patient_examinations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.patient_examinations (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    general character varying(255),
    specifics json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    visit_type character varying(255),
    visit_id bigint
);


--
-- Name: patient_examinations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.patient_examinations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: patient_examinations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.patient_examinations_id_seq OWNED BY public.patient_examinations.id;


--
-- Name: patient_histories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.patient_histories (
    id bigint NOT NULL,
    visit_type character varying(255) NOT NULL,
    visit_id bigint NOT NULL,
    patient_id bigint NOT NULL,
    presentation character varying(255) NOT NULL,
    duration character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: patient_histories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.patient_histories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: patient_histories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.patient_histories_id_seq OWNED BY public.patient_histories.id;


--
-- Name: patient_imagings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.patient_imagings (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    documentation_id bigint,
    name character varying(255) NOT NULL,
    type character varying(255),
    path character varying(255),
    comment character varying(255),
    status character varying(255) DEFAULT '3'::character varying NOT NULL,
    requested_by bigint NOT NULL,
    uploaded_by bigint,
    uploaded_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    documentable_type character varying(255) NOT NULL,
    documentable_id bigint NOT NULL,
    describable_type character varying(255),
    describable_id bigint,
    results json,
    deleted_at timestamp(0) without time zone
);


--
-- Name: patient_imagings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.patient_imagings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: patient_imagings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.patient_imagings_id_seq OWNED BY public.patient_imagings.id;


--
-- Name: patients; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.patients (
    id bigint NOT NULL,
    card_number character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    phone character varying(255),
    dob date,
    gender smallint,
    marital_status smallint,
    address character varying(255),
    occupation character varying(255),
    religion smallint,
    email character varying(255),
    tribe character varying(255),
    place_of_origin character varying(255),
    nok_name character varying(255),
    nok_phone character varying(255),
    nok_address character varying(255),
    spouse_name character varying(255),
    spouse_phone character varying(255),
    spouse_occupation character varying(255),
    spouse_educational_status character varying(255),
    category_id bigint,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: patients_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.patients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: patients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.patients_id_seq OWNED BY public.patients.id;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: posts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.posts (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    description character varying(64),
    post text NOT NULL,
    status smallint DEFAULT '1'::smallint NOT NULL,
    user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    image character varying(255)
);


--
-- Name: posts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.posts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: posts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.posts_id_seq OWNED BY public.posts.id;


--
-- Name: prescription_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.prescription_lines (
    id bigint NOT NULL,
    prescription_id bigint,
    item_id bigint,
    dosage character varying(255) NOT NULL,
    frequency character varying(255),
    duration smallint DEFAULT '1'::smallint NOT NULL,
    status smallint DEFAULT '3'::smallint NOT NULL,
    dispensed_by bigint,
    prescribed_by bigint NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    profile character varying(255),
    description character varying(255),
    qty_dispensed numeric(8,1)
);


--
-- Name: prescription_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.prescription_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: prescription_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.prescription_lines_id_seq OWNED BY public.prescription_lines.id;


--
-- Name: prescriptions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.prescriptions (
    id bigint NOT NULL,
    event_type character varying(255) NOT NULL,
    event_id bigint NOT NULL,
    patient_id bigint NOT NULL,
    status smallint DEFAULT '1'::smallint NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: prescriptions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.prescriptions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: prescriptions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.prescriptions_id_seq OWNED BY public.prescriptions.id;


--
-- Name: product_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.product_categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    department_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: product_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.product_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: product_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.product_categories_id_seq OWNED BY public.product_categories.id;


--
-- Name: products; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.products (
    id bigint NOT NULL,
    name character varying(60) NOT NULL,
    description character varying(256),
    amount numeric(8,2) NOT NULL,
    is_visible smallint DEFAULT '1'::smallint NOT NULL,
    product_category_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- Name: purchase_order_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.purchase_order_lines (
    id bigint NOT NULL,
    po_id bigint NOT NULL,
    item_id bigint NOT NULL,
    qty_ordered numeric(14,4) NOT NULL,
    unit character varying(255),
    unit_cost numeric(14,4),
    qty_received numeric(14,4) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: purchase_order_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.purchase_order_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: purchase_order_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.purchase_order_lines_id_seq OWNED BY public.purchase_order_lines.id;


--
-- Name: purchase_orders; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.purchase_orders (
    id bigint NOT NULL,
    supplier_id bigint NOT NULL,
    po_number character varying(255) NOT NULL,
    status integer DEFAULT 3 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: purchase_orders_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.purchase_orders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: purchase_orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.purchase_orders_id_seq OWNED BY public.purchase_orders.id;


--
-- Name: requisition_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.requisition_lines (
    id bigint NOT NULL,
    requisition_id bigint NOT NULL,
    item_id bigint NOT NULL,
    qty numeric(14,4) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: requisition_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.requisition_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: requisition_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.requisition_lines_id_seq OWNED BY public.requisition_lines.id;


--
-- Name: requisitions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.requisitions (
    id bigint NOT NULL,
    requested_by bigint NOT NULL,
    from_location_id bigint NOT NULL,
    to_location_id bigint NOT NULL,
    status smallint DEFAULT '3'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: requisitions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.requisitions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: requisitions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.requisitions_id_seq OWNED BY public.requisitions.id;


--
-- Name: stock_count_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.stock_count_lines (
    id bigint NOT NULL,
    stock_count_id bigint NOT NULL,
    item_id bigint NOT NULL,
    counted_qty integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    lot_id bigint,
    system_qty numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    applied boolean DEFAULT false NOT NULL,
    stock_transaction_id bigint
);


--
-- Name: stock_count_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.stock_count_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: stock_count_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.stock_count_lines_id_seq OWNED BY public.stock_count_lines.id;


--
-- Name: stock_counts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.stock_counts (
    id bigint NOT NULL,
    performed_by bigint NOT NULL,
    location_id bigint NOT NULL,
    count_date timestamp(0) with time zone DEFAULT '2025-12-11 22:08:08.429583+01'::timestamp with time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status integer DEFAULT 1 NOT NULL,
    applied_at timestamp(0) with time zone,
    approved_by bigint
);


--
-- Name: stock_counts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.stock_counts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: stock_counts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.stock_counts_id_seq OWNED BY public.stock_counts.id;


--
-- Name: stock_item_costs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.stock_item_costs (
    id bigint NOT NULL,
    item_id bigint NOT NULL,
    cost numeric(12,4) NOT NULL,
    source public.item_cost_source NOT NULL,
    lot_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: stock_item_costs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.stock_item_costs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: stock_item_costs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.stock_item_costs_id_seq OWNED BY public.stock_item_costs.id;


--
-- Name: stock_item_prices; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.stock_item_prices (
    id bigint NOT NULL,
    item_id bigint NOT NULL,
    price_type character varying(255) NOT NULL,
    price numeric(12,4) NOT NULL,
    currency character varying(255) DEFAULT 'NGN'::character varying NOT NULL,
    effective_at timestamp(0) with time zone,
    created_by bigint NOT NULL,
    active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: stock_item_prices_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.stock_item_prices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: stock_item_prices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.stock_item_prices_id_seq OWNED BY public.stock_item_prices.id;


--
-- Name: stock_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.stock_items (
    id bigint NOT NULL,
    sku character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255),
    category character varying(255) NOT NULL,
    is_pharmaceutical boolean DEFAULT true NOT NULL,
    requires_lot boolean DEFAULT false NOT NULL,
    base_unit character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    weight numeric(8,2),
    si_unit character varying(255),
    deleted_at timestamp(0) without time zone
);


--
-- Name: stock_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.stock_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: stock_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.stock_items_id_seq OWNED BY public.stock_items.id;


--
-- Name: stock_lots; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.stock_lots (
    id bigint NOT NULL,
    item_id bigint NOT NULL,
    lot_number character varying(255) NOT NULL,
    manufacture_date date,
    expiry_date date,
    quantity_received numeric(14,4) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: stock_lots_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.stock_lots_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: stock_lots_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.stock_lots_id_seq OWNED BY public.stock_lots.id;


--
-- Name: stock_transactions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.stock_transactions (
    id bigint NOT NULL,
    tx_type public.stock_tx_type NOT NULL,
    item_id bigint NOT NULL,
    lot_id bigint,
    quantity numeric(14,4) NOT NULL,
    unit character varying(255) NOT NULL,
    unit_cost numeric(12,4) NOT NULL,
    from_location_id bigint NOT NULL,
    to_location_id bigint NOT NULL,
    related_document character varying(255),
    reason character varying(255) NOT NULL,
    performed_by bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    selling_price numeric(12,4)
);


--
-- Name: stock_transactions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.stock_transactions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: stock_transactions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.stock_transactions_id_seq OWNED BY public.stock_transactions.id;


--
-- Name: suppliers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.suppliers (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    contact jsonb NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: suppliers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.suppliers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: suppliers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.suppliers_id_seq OWNED BY public.suppliers.id;


--
-- Name: surgeries; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.surgeries (
    id bigint NOT NULL,
    admission_plan_id bigint NOT NULL,
    procedure character varying(255) NOT NULL,
    patient_id bigint,
    status smallint DEFAULT '3'::smallint NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: surgeries_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.surgeries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: surgeries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.surgeries_id_seq OWNED BY public.surgeries.id;


--
-- Name: surgery_notes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.surgery_notes (
    id bigint NOT NULL,
    note character varying(512) NOT NULL,
    user_id bigint,
    surgery_id bigint NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: surgery_notes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.surgery_notes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: surgery_notes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.surgery_notes_id_seq OWNED BY public.surgery_notes.id;


--
-- Name: test_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.test_categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: test_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.test_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: test_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.test_categories_id_seq OWNED BY public.test_categories.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    firstname character varying(255) NOT NULL,
    lastname character varying(255) NOT NULL,
    phone character varying(255) NOT NULL,
    password character varying(255),
    department_id integer NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: visits; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.visits (
    id bigint NOT NULL,
    patient_id bigint NOT NULL,
    visit_type character varying(255) NOT NULL,
    visit_id bigint NOT NULL,
    status smallint DEFAULT '1'::smallint NOT NULL,
    parent_id bigint,
    awaiting_vitals boolean DEFAULT true NOT NULL,
    awaiting_lab_results boolean DEFAULT false NOT NULL,
    awaiting_doctor boolean DEFAULT true NOT NULL,
    awaiting_tests boolean DEFAULT false NOT NULL,
    awaiting_pharmacy boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    awaiting_radiology boolean DEFAULT false NOT NULL,
    consultant_id bigint
);


--
-- Name: visits_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.visits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: visits_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.visits_id_seq OWNED BY public.visits.id;


--
-- Name: vitals; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vitals (
    id bigint NOT NULL,
    recordable_type character varying(255) NOT NULL,
    recordable_id bigint NOT NULL,
    blood_pressure character varying(8),
    weight double precision,
    temperature double precision,
    respiration integer,
    pulse integer,
    recording_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    recorded_date timestamp(0) without time zone,
    spo2 double precision,
    fetal_heart_rate character varying(255),
    extra json
);


--
-- Name: vitals_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vitals_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: vitals_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vitals_id_seq OWNED BY public.vitals.id;


--
-- Name: wards; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wards (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    beds integer DEFAULT 1 NOT NULL,
    type character varying(255) DEFAULT 'private'::character varying NOT NULL,
    filled_beds integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: wards_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wards_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wards_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wards_id_seq OWNED BY public.wards.id;


--
-- Name: admission_logs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_logs ALTER COLUMN id SET DEFAULT nextval('public.admission_logs_id_seq'::regclass);


--
-- Name: admission_plans id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_plans ALTER COLUMN id SET DEFAULT nextval('public.admission_plans_id_seq'::regclass);


--
-- Name: admission_treatment_administrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_treatment_administrations ALTER COLUMN id SET DEFAULT nextval('public.admission_treatment_administrations_id_seq'::regclass);


--
-- Name: admissions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admissions ALTER COLUMN id SET DEFAULT nextval('public.admissions_id_seq'::regclass);


--
-- Name: anc_visits id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.anc_visits ALTER COLUMN id SET DEFAULT nextval('public.anc_visits_id_seq'::regclass);


--
-- Name: antenatal_profiles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.antenatal_profiles ALTER COLUMN id SET DEFAULT nextval('public.antenatal_profiles_id_seq'::regclass);


--
-- Name: bill_details id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bill_details ALTER COLUMN id SET DEFAULT nextval('public.bill_details_id_seq'::regclass);


--
-- Name: bill_payments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bill_payments ALTER COLUMN id SET DEFAULT nextval('public.bill_payments_id_seq'::regclass);


--
-- Name: bills id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bills ALTER COLUMN id SET DEFAULT nextval('public.bills_id_seq'::regclass);


--
-- Name: consultation_notes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.consultation_notes ALTER COLUMN id SET DEFAULT nextval('public.consultation_notes_id_seq'::regclass);


--
-- Name: datalogs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.datalogs ALTER COLUMN id SET DEFAULT nextval('public.datalogs_id_seq'::regclass);


--
-- Name: departments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.departments ALTER COLUMN id SET DEFAULT nextval('public.departments_id_seq'::regclass);


--
-- Name: dispense_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dispense_lines ALTER COLUMN id SET DEFAULT nextval('public.dispense_lines_id_seq'::regclass);


--
-- Name: documentation_complaints id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_complaints ALTER COLUMN id SET DEFAULT nextval('public.documentation_complaints_id_seq'::regclass);


--
-- Name: documentation_prescriptions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_prescriptions ALTER COLUMN id SET DEFAULT nextval('public.documentation_prescriptions_id_seq'::regclass);


--
-- Name: documentation_tests id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_tests ALTER COLUMN id SET DEFAULT nextval('public.documentation_tests_id_seq'::regclass);


--
-- Name: documentations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentations ALTER COLUMN id SET DEFAULT nextval('public.documentations_id_seq'::regclass);


--
-- Name: documented_diagnoses id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documented_diagnoses ALTER COLUMN id SET DEFAULT nextval('public.documented_diagnoses_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: general_visits id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.general_visits ALTER COLUMN id SET DEFAULT nextval('public.general_visits_id_seq'::regclass);


--
-- Name: insurance_authorizations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.insurance_authorizations ALTER COLUMN id SET DEFAULT nextval('public.insurance_authorizations_id_seq'::regclass);


--
-- Name: insurance_profiles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.insurance_profiles ALTER COLUMN id SET DEFAULT nextval('public.insurance_profiles_id_seq'::regclass);


--
-- Name: inventory_balances id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_balances ALTER COLUMN id SET DEFAULT nextval('public.inventory_balances_id_seq'::regclass);


--
-- Name: lab_test_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_test_categories ALTER COLUMN id SET DEFAULT nextval('public.lab_test_categories_id_seq'::regclass);


--
-- Name: lab_tests id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_tests ALTER COLUMN id SET DEFAULT nextval('public.lab_tests_id_seq'::regclass);


--
-- Name: locations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.locations ALTER COLUMN id SET DEFAULT nextval('public.locations_id_seq'::regclass);


--
-- Name: media id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.media ALTER COLUMN id SET DEFAULT nextval('public.media_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: operation_notes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.operation_notes ALTER COLUMN id SET DEFAULT nextval('public.operation_notes_id_seq'::regclass);


--
-- Name: patient_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_categories ALTER COLUMN id SET DEFAULT nextval('public.patient_categories_id_seq'::regclass);


--
-- Name: patient_examinations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_examinations ALTER COLUMN id SET DEFAULT nextval('public.patient_examinations_id_seq'::regclass);


--
-- Name: patient_histories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_histories ALTER COLUMN id SET DEFAULT nextval('public.patient_histories_id_seq'::regclass);


--
-- Name: patient_imagings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_imagings ALTER COLUMN id SET DEFAULT nextval('public.patient_imagings_id_seq'::regclass);


--
-- Name: patients id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patients ALTER COLUMN id SET DEFAULT nextval('public.patients_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: posts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.posts ALTER COLUMN id SET DEFAULT nextval('public.posts_id_seq'::regclass);


--
-- Name: prescription_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prescription_lines ALTER COLUMN id SET DEFAULT nextval('public.prescription_lines_id_seq'::regclass);


--
-- Name: prescriptions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prescriptions ALTER COLUMN id SET DEFAULT nextval('public.prescriptions_id_seq'::regclass);


--
-- Name: product_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_categories ALTER COLUMN id SET DEFAULT nextval('public.product_categories_id_seq'::regclass);


--
-- Name: products id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- Name: purchase_order_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchase_order_lines ALTER COLUMN id SET DEFAULT nextval('public.purchase_order_lines_id_seq'::regclass);


--
-- Name: purchase_orders id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchase_orders ALTER COLUMN id SET DEFAULT nextval('public.purchase_orders_id_seq'::regclass);


--
-- Name: requisition_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.requisition_lines ALTER COLUMN id SET DEFAULT nextval('public.requisition_lines_id_seq'::regclass);


--
-- Name: requisitions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.requisitions ALTER COLUMN id SET DEFAULT nextval('public.requisitions_id_seq'::regclass);


--
-- Name: stock_count_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_count_lines ALTER COLUMN id SET DEFAULT nextval('public.stock_count_lines_id_seq'::regclass);


--
-- Name: stock_counts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_counts ALTER COLUMN id SET DEFAULT nextval('public.stock_counts_id_seq'::regclass);


--
-- Name: stock_item_costs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_item_costs ALTER COLUMN id SET DEFAULT nextval('public.stock_item_costs_id_seq'::regclass);


--
-- Name: stock_item_prices id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_item_prices ALTER COLUMN id SET DEFAULT nextval('public.stock_item_prices_id_seq'::regclass);


--
-- Name: stock_items id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_items ALTER COLUMN id SET DEFAULT nextval('public.stock_items_id_seq'::regclass);


--
-- Name: stock_lots id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_lots ALTER COLUMN id SET DEFAULT nextval('public.stock_lots_id_seq'::regclass);


--
-- Name: stock_transactions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_transactions ALTER COLUMN id SET DEFAULT nextval('public.stock_transactions_id_seq'::regclass);


--
-- Name: suppliers id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.suppliers ALTER COLUMN id SET DEFAULT nextval('public.suppliers_id_seq'::regclass);


--
-- Name: surgeries id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.surgeries ALTER COLUMN id SET DEFAULT nextval('public.surgeries_id_seq'::regclass);


--
-- Name: surgery_notes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.surgery_notes ALTER COLUMN id SET DEFAULT nextval('public.surgery_notes_id_seq'::regclass);


--
-- Name: test_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.test_categories ALTER COLUMN id SET DEFAULT nextval('public.test_categories_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: visits id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.visits ALTER COLUMN id SET DEFAULT nextval('public.visits_id_seq'::regclass);


--
-- Name: vitals id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vitals ALTER COLUMN id SET DEFAULT nextval('public.vitals_id_seq'::regclass);


--
-- Name: wards id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wards ALTER COLUMN id SET DEFAULT nextval('public.wards_id_seq'::regclass);


--
-- Name: admission_logs admission_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_logs
    ADD CONSTRAINT admission_logs_pkey PRIMARY KEY (id);


--
-- Name: admission_plans admission_plans_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_plans
    ADD CONSTRAINT admission_plans_pkey PRIMARY KEY (id);


--
-- Name: admission_treatment_administrations admission_treatment_administrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_treatment_administrations
    ADD CONSTRAINT admission_treatment_administrations_pkey PRIMARY KEY (id);


--
-- Name: admissions admissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admissions
    ADD CONSTRAINT admissions_pkey PRIMARY KEY (id);


--
-- Name: anc_visits anc_visits_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.anc_visits
    ADD CONSTRAINT anc_visits_pkey PRIMARY KEY (id);


--
-- Name: antenatal_profiles antenatal_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.antenatal_profiles
    ADD CONSTRAINT antenatal_profiles_pkey PRIMARY KEY (id);


--
-- Name: bill_details bill_details_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bill_details
    ADD CONSTRAINT bill_details_pkey PRIMARY KEY (id);


--
-- Name: bill_payments bill_payments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bill_payments
    ADD CONSTRAINT bill_payments_pkey PRIMARY KEY (id);


--
-- Name: bills bills_bill_number_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bills
    ADD CONSTRAINT bills_bill_number_unique UNIQUE (bill_number);


--
-- Name: bills bills_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bills
    ADD CONSTRAINT bills_pkey PRIMARY KEY (id);


--
-- Name: consultation_notes consultation_notes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.consultation_notes
    ADD CONSTRAINT consultation_notes_pkey PRIMARY KEY (id);


--
-- Name: datalogs datalogs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.datalogs
    ADD CONSTRAINT datalogs_pkey PRIMARY KEY (id);


--
-- Name: departments departments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_pkey PRIMARY KEY (id);


--
-- Name: dispense_lines dispense_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dispense_lines
    ADD CONSTRAINT dispense_lines_pkey PRIMARY KEY (id);


--
-- Name: documentation_complaints documentation_complaints_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_complaints
    ADD CONSTRAINT documentation_complaints_pkey PRIMARY KEY (id);


--
-- Name: documentation_prescriptions documentation_prescriptions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_prescriptions
    ADD CONSTRAINT documentation_prescriptions_pkey PRIMARY KEY (id);


--
-- Name: documentation_tests documentation_tests_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_tests
    ADD CONSTRAINT documentation_tests_pkey PRIMARY KEY (id);


--
-- Name: documentations documentations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentations
    ADD CONSTRAINT documentations_pkey PRIMARY KEY (id);


--
-- Name: documented_diagnoses documented_diagnoses_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documented_diagnoses
    ADD CONSTRAINT documented_diagnoses_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: general_visits general_visits_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.general_visits
    ADD CONSTRAINT general_visits_pkey PRIMARY KEY (id);


--
-- Name: insurance_authorizations insurance_authorizations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.insurance_authorizations
    ADD CONSTRAINT insurance_authorizations_pkey PRIMARY KEY (id);


--
-- Name: insurance_profiles insurance_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.insurance_profiles
    ADD CONSTRAINT insurance_profiles_pkey PRIMARY KEY (id);


--
-- Name: inventory_balances inventory_balances_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_balances
    ADD CONSTRAINT inventory_balances_pkey PRIMARY KEY (id);


--
-- Name: lab_test_categories lab_test_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_test_categories
    ADD CONSTRAINT lab_test_categories_pkey PRIMARY KEY (id);


--
-- Name: lab_tests lab_tests_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_tests
    ADD CONSTRAINT lab_tests_pkey PRIMARY KEY (id);


--
-- Name: locations locations_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT locations_code_unique UNIQUE (code);


--
-- Name: locations locations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT locations_pkey PRIMARY KEY (id);


--
-- Name: media media_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.media
    ADD CONSTRAINT media_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: operation_notes operation_notes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.operation_notes
    ADD CONSTRAINT operation_notes_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: patient_categories patient_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_categories
    ADD CONSTRAINT patient_categories_pkey PRIMARY KEY (id);


--
-- Name: patient_examinations patient_examinations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_examinations
    ADD CONSTRAINT patient_examinations_pkey PRIMARY KEY (id);


--
-- Name: patient_histories patient_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_histories
    ADD CONSTRAINT patient_histories_pkey PRIMARY KEY (id);


--
-- Name: patient_imagings patient_imagings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_imagings
    ADD CONSTRAINT patient_imagings_pkey PRIMARY KEY (id);


--
-- Name: patients patients_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patients
    ADD CONSTRAINT patients_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: posts posts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.posts
    ADD CONSTRAINT posts_pkey PRIMARY KEY (id);


--
-- Name: prescription_lines prescription_lines_item_id_dosage_frequency_duration_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prescription_lines
    ADD CONSTRAINT prescription_lines_item_id_dosage_frequency_duration_unique UNIQUE (item_id, dosage, frequency, duration);


--
-- Name: prescription_lines prescription_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prescription_lines
    ADD CONSTRAINT prescription_lines_pkey PRIMARY KEY (id);


--
-- Name: prescriptions prescriptions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prescriptions
    ADD CONSTRAINT prescriptions_pkey PRIMARY KEY (id);


--
-- Name: product_categories product_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_categories
    ADD CONSTRAINT product_categories_pkey PRIMARY KEY (id);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: purchase_order_lines purchase_order_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchase_order_lines
    ADD CONSTRAINT purchase_order_lines_pkey PRIMARY KEY (id);


--
-- Name: purchase_orders purchase_orders_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchase_orders
    ADD CONSTRAINT purchase_orders_pkey PRIMARY KEY (id);


--
-- Name: purchase_orders purchase_orders_po_number_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchase_orders
    ADD CONSTRAINT purchase_orders_po_number_unique UNIQUE (po_number);


--
-- Name: requisition_lines requisition_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.requisition_lines
    ADD CONSTRAINT requisition_lines_pkey PRIMARY KEY (id);


--
-- Name: requisitions requisitions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.requisitions
    ADD CONSTRAINT requisitions_pkey PRIMARY KEY (id);


--
-- Name: stock_count_lines stock_count_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_count_lines
    ADD CONSTRAINT stock_count_lines_pkey PRIMARY KEY (id);


--
-- Name: stock_counts stock_counts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_counts
    ADD CONSTRAINT stock_counts_pkey PRIMARY KEY (id);


--
-- Name: stock_item_costs stock_item_costs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_item_costs
    ADD CONSTRAINT stock_item_costs_pkey PRIMARY KEY (id);


--
-- Name: stock_item_prices stock_item_prices_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_item_prices
    ADD CONSTRAINT stock_item_prices_pkey PRIMARY KEY (id);


--
-- Name: stock_items stock_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_items
    ADD CONSTRAINT stock_items_pkey PRIMARY KEY (id);


--
-- Name: stock_items stock_items_sku_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_items
    ADD CONSTRAINT stock_items_sku_unique UNIQUE (sku);


--
-- Name: stock_lots stock_lots_item_id_lot_number_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_lots
    ADD CONSTRAINT stock_lots_item_id_lot_number_unique UNIQUE (item_id, lot_number);


--
-- Name: stock_lots stock_lots_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_lots
    ADD CONSTRAINT stock_lots_pkey PRIMARY KEY (id);


--
-- Name: stock_transactions stock_transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_transactions
    ADD CONSTRAINT stock_transactions_pkey PRIMARY KEY (id);


--
-- Name: suppliers suppliers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_pkey PRIMARY KEY (id);


--
-- Name: surgeries surgeries_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.surgeries
    ADD CONSTRAINT surgeries_pkey PRIMARY KEY (id);


--
-- Name: surgery_notes surgery_notes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.surgery_notes
    ADD CONSTRAINT surgery_notes_pkey PRIMARY KEY (id);


--
-- Name: test_categories test_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.test_categories
    ADD CONSTRAINT test_categories_pkey PRIMARY KEY (id);


--
-- Name: users users_phone_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_phone_unique UNIQUE (phone);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: visits visits_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.visits
    ADD CONSTRAINT visits_pkey PRIMARY KEY (id);


--
-- Name: vitals vitals_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vitals
    ADD CONSTRAINT vitals_pkey PRIMARY KEY (id);


--
-- Name: wards wards_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wards
    ADD CONSTRAINT wards_pkey PRIMARY KEY (id);


--
-- Name: admissions_admittable_type_admittable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX admissions_admittable_type_admittable_id_index ON public.admissions USING btree (admittable_type, admittable_id);


--
-- Name: bill_details_chargeable_type_chargeable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX bill_details_chargeable_type_chargeable_id_index ON public.bill_details USING btree (chargeable_type, chargeable_id);


--
-- Name: bills_billable_type_billable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX bills_billable_type_billable_id_index ON public.bills USING btree (billable_type, billable_id);


--
-- Name: dispense_lines_source_type_source_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dispense_lines_source_type_source_id_index ON public.dispense_lines USING btree (source_type, source_id);


--
-- Name: documentation_complaints_documentable_type_documentable_id_inde; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX documentation_complaints_documentable_type_documentable_id_inde ON public.documentation_complaints USING btree (documentable_type, documentable_id);


--
-- Name: documentation_prescriptions_event_type_event_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX documentation_prescriptions_event_type_event_id_index ON public.documentation_prescriptions USING btree (event_type, event_id);


--
-- Name: documentation_tests_describable_type_describable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX documentation_tests_describable_type_describable_id_index ON public.documentation_tests USING btree (describable_type, describable_id);


--
-- Name: documentation_tests_testable_type_testable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX documentation_tests_testable_type_testable_id_index ON public.documentation_tests USING btree (testable_type, testable_id);


--
-- Name: documented_diagnoses_diagnosable_type_diagnosable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX documented_diagnoses_diagnosable_type_diagnosable_id_index ON public.documented_diagnoses USING btree (diagnosable_type, diagnosable_id);


--
-- Name: insurance_authorizations_authorizable_type_authorizable_id_inde; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX insurance_authorizations_authorizable_type_authorizable_id_inde ON public.insurance_authorizations USING btree (authorizable_type, authorizable_id);


--
-- Name: inventory_balances_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX inventory_balances_unique ON public.inventory_balances USING btree (item_id, location_id, lot_id) NULLS NOT DISTINCT;


--
-- Name: media_medially_type_medially_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX media_medially_type_medially_id_index ON public.media USING btree (medially_type, medially_id);


--
-- Name: notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX notifications_notifiable_type_notifiable_id_index ON public.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: patient_examinations_visit_type_visit_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX patient_examinations_visit_type_visit_id_index ON public.patient_examinations USING btree (visit_type, visit_id);


--
-- Name: patient_histories_visit_type_visit_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX patient_histories_visit_type_visit_id_index ON public.patient_histories USING btree (visit_type, visit_id);


--
-- Name: patient_imagings_describable_type_describable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX patient_imagings_describable_type_describable_id_index ON public.patient_imagings USING btree (describable_type, describable_id);


--
-- Name: patient_imagings_documentable_type_documentable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX patient_imagings_documentable_type_documentable_id_index ON public.patient_imagings USING btree (documentable_type, documentable_id);


--
-- Name: patient_imagings_name_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX patient_imagings_name_index ON public.patient_imagings USING btree (name);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: prescription_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX prescription_type_index ON public.documentation_prescriptions USING btree (prescriptionable_type, prescriptionable_id);


--
-- Name: prescriptions_event_type_event_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX prescriptions_event_type_event_id_index ON public.prescriptions USING btree (event_type, event_id);


--
-- Name: visits_visit_type_visit_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX visits_visit_type_visit_id_index ON public.visits USING btree (visit_type, visit_id);


--
-- Name: vitals_recordable_type_recordable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vitals_recordable_type_recordable_id_index ON public.vitals USING btree (recordable_type, recordable_id);


--
-- Name: stock_transactions after_stock_transaction; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER after_stock_transaction AFTER INSERT ON public.stock_transactions FOR EACH ROW EXECUTE FUNCTION public.trg_after_stock_transaction();


--
-- Name: admission_logs admission_logs_admission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_logs
    ADD CONSTRAINT admission_logs_admission_id_foreign FOREIGN KEY (admission_id) REFERENCES public.admissions(id);


--
-- Name: admission_logs admission_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_logs
    ADD CONSTRAINT admission_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: admission_plans admission_plans_admission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_plans
    ADD CONSTRAINT admission_plans_admission_id_foreign FOREIGN KEY (admission_id) REFERENCES public.admissions(id) ON DELETE SET NULL;


--
-- Name: admission_plans admission_plans_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_plans
    ADD CONSTRAINT admission_plans_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: admission_treatment_administrations admission_treatment_administrations_admission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_treatment_administrations
    ADD CONSTRAINT admission_treatment_administrations_admission_id_foreign FOREIGN KEY (admission_id) REFERENCES public.admissions(id) ON DELETE RESTRICT;


--
-- Name: admission_treatment_administrations admission_treatment_administrations_minister_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admission_treatment_administrations
    ADD CONSTRAINT admission_treatment_administrations_minister_id_foreign FOREIGN KEY (minister_id) REFERENCES public.users(id);


--
-- Name: admissions admissions_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admissions
    ADD CONSTRAINT admissions_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id) ON DELETE RESTRICT;


--
-- Name: admissions admissions_visit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admissions
    ADD CONSTRAINT admissions_visit_id_foreign FOREIGN KEY (visit_id) REFERENCES public.visits(id) ON DELETE RESTRICT;


--
-- Name: admissions admissions_ward_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admissions
    ADD CONSTRAINT admissions_ward_id_foreign FOREIGN KEY (ward_id) REFERENCES public.wards(id) ON DELETE RESTRICT;


--
-- Name: anc_visits anc_visits_antenatal_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.anc_visits
    ADD CONSTRAINT anc_visits_antenatal_profile_id_foreign FOREIGN KEY (antenatal_profile_id) REFERENCES public.antenatal_profiles(id) ON DELETE SET NULL;


--
-- Name: anc_visits anc_visits_doctor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.anc_visits
    ADD CONSTRAINT anc_visits_doctor_id_foreign FOREIGN KEY (doctor_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: anc_visits anc_visits_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.anc_visits
    ADD CONSTRAINT anc_visits_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id) ON DELETE RESTRICT;


--
-- Name: anc_visits anc_visits_vitals_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.anc_visits
    ADD CONSTRAINT anc_visits_vitals_by_foreign FOREIGN KEY (vitals_by) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: antenatal_profiles antenatal_profiles_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.antenatal_profiles
    ADD CONSTRAINT antenatal_profiles_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id) ON DELETE RESTRICT;


--
-- Name: bill_details bill_details_bill_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bill_details
    ADD CONSTRAINT bill_details_bill_id_foreign FOREIGN KEY (bill_id) REFERENCES public.bills(id) ON DELETE CASCADE;


--
-- Name: bill_details bill_details_quoted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bill_details
    ADD CONSTRAINT bill_details_quoted_by_foreign FOREIGN KEY (quoted_by) REFERENCES public.users(id);


--
-- Name: bill_payments bill_payments_bill_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bill_payments
    ADD CONSTRAINT bill_payments_bill_id_foreign FOREIGN KEY (bill_id) REFERENCES public.bills(id) ON DELETE RESTRICT;


--
-- Name: bill_payments bill_payments_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bill_payments
    ADD CONSTRAINT bill_payments_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: bills bills_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bills
    ADD CONSTRAINT bills_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: bills bills_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bills
    ADD CONSTRAINT bills_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id) ON DELETE RESTRICT;


--
-- Name: consultation_notes consultation_notes_consultant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.consultation_notes
    ADD CONSTRAINT consultation_notes_consultant_id_foreign FOREIGN KEY (consultant_id) REFERENCES public.users(id);


--
-- Name: consultation_notes consultation_notes_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.consultation_notes
    ADD CONSTRAINT consultation_notes_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id);


--
-- Name: datalogs datalogs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.datalogs
    ADD CONSTRAINT datalogs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: dispense_lines dispense_lines_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dispense_lines
    ADD CONSTRAINT dispense_lines_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: documentation_complaints documentation_complaints_documentation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_complaints
    ADD CONSTRAINT documentation_complaints_documentation_id_foreign FOREIGN KEY (documentation_id) REFERENCES public.documentations(id) ON DELETE SET NULL;


--
-- Name: documentation_prescriptions documentation_prescriptions_dispensed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_prescriptions
    ADD CONSTRAINT documentation_prescriptions_dispensed_by_foreign FOREIGN KEY (dispensed_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: documentation_prescriptions documentation_prescriptions_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_prescriptions
    ADD CONSTRAINT documentation_prescriptions_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id);


--
-- Name: documentation_prescriptions documentation_prescriptions_requested_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_prescriptions
    ADD CONSTRAINT documentation_prescriptions_requested_by_foreign FOREIGN KEY (requested_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: documentation_tests documentation_tests_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_tests
    ADD CONSTRAINT documentation_tests_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id);


--
-- Name: documentation_tests documentation_tests_tested_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentation_tests
    ADD CONSTRAINT documentation_tests_tested_by_foreign FOREIGN KEY (tested_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: documentations documentations_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentations
    ADD CONSTRAINT documentations_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id);


--
-- Name: documentations documentations_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentations
    ADD CONSTRAINT documentations_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: documentations documentations_visit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documentations
    ADD CONSTRAINT documentations_visit_id_foreign FOREIGN KEY (visit_id) REFERENCES public.visits(id);


--
-- Name: documented_diagnoses documented_diagnoses_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documented_diagnoses
    ADD CONSTRAINT documented_diagnoses_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id);


--
-- Name: documented_diagnoses documented_diagnoses_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.documented_diagnoses
    ADD CONSTRAINT documented_diagnoses_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: general_visits general_visits_doctor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.general_visits
    ADD CONSTRAINT general_visits_doctor_id_foreign FOREIGN KEY (doctor_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: general_visits general_visits_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.general_visits
    ADD CONSTRAINT general_visits_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id) ON DELETE RESTRICT;


--
-- Name: general_visits general_visits_vitals_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.general_visits
    ADD CONSTRAINT general_visits_vitals_by_foreign FOREIGN KEY (vitals_by) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: insurance_authorizations insurance_authorizations_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.insurance_authorizations
    ADD CONSTRAINT insurance_authorizations_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id);


--
-- Name: insurance_profiles insurance_profiles_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.insurance_profiles
    ADD CONSTRAINT insurance_profiles_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id) ON DELETE CASCADE;


--
-- Name: inventory_balances inventory_balances_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_balances
    ADD CONSTRAINT inventory_balances_item_id_foreign FOREIGN KEY (item_id) REFERENCES public.stock_items(id);


--
-- Name: inventory_balances inventory_balances_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_balances
    ADD CONSTRAINT inventory_balances_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.locations(id);


--
-- Name: inventory_balances inventory_balances_lot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_balances
    ADD CONSTRAINT inventory_balances_lot_id_foreign FOREIGN KEY (lot_id) REFERENCES public.stock_lots(id);


--
-- Name: lab_test_categories lab_test_categories_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_test_categories
    ADD CONSTRAINT lab_test_categories_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.test_categories(id);


--
-- Name: lab_test_categories lab_test_categories_test_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_test_categories
    ADD CONSTRAINT lab_test_categories_test_id_foreign FOREIGN KEY (test_id) REFERENCES public.lab_tests(id);


--
-- Name: locations locations_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT locations_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: operation_notes operation_notes_admission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.operation_notes
    ADD CONSTRAINT operation_notes_admission_id_foreign FOREIGN KEY (admission_id) REFERENCES public.admissions(id);


--
-- Name: operation_notes operation_notes_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.operation_notes
    ADD CONSTRAINT operation_notes_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id);


--
-- Name: operation_notes operation_notes_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.operation_notes
    ADD CONSTRAINT operation_notes_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: patient_examinations patient_examinations_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_examinations
    ADD CONSTRAINT patient_examinations_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id);


--
-- Name: patient_histories patient_histories_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_histories
    ADD CONSTRAINT patient_histories_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id);


--
-- Name: patient_imagings patient_imagings_documentation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_imagings
    ADD CONSTRAINT patient_imagings_documentation_id_foreign FOREIGN KEY (documentation_id) REFERENCES public.documentations(id);


--
-- Name: patient_imagings patient_imagings_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_imagings
    ADD CONSTRAINT patient_imagings_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id);


--
-- Name: patient_imagings patient_imagings_requested_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_imagings
    ADD CONSTRAINT patient_imagings_requested_by_foreign FOREIGN KEY (requested_by) REFERENCES public.users(id);


--
-- Name: patient_imagings patient_imagings_uploaded_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patient_imagings
    ADD CONSTRAINT patient_imagings_uploaded_by_foreign FOREIGN KEY (uploaded_by) REFERENCES public.users(id);


--
-- Name: patients patients_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.patients
    ADD CONSTRAINT patients_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.patient_categories(id);


--
-- Name: posts posts_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.posts
    ADD CONSTRAINT posts_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: prescription_lines prescription_lines_dispensed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prescription_lines
    ADD CONSTRAINT prescription_lines_dispensed_by_foreign FOREIGN KEY (dispensed_by) REFERENCES public.users(id);


--
-- Name: prescription_lines prescription_lines_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prescription_lines
    ADD CONSTRAINT prescription_lines_item_id_foreign FOREIGN KEY (item_id) REFERENCES public.stock_items(id);


--
-- Name: prescription_lines prescription_lines_prescribed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prescription_lines
    ADD CONSTRAINT prescription_lines_prescribed_by_foreign FOREIGN KEY (prescribed_by) REFERENCES public.users(id);


--
-- Name: prescription_lines prescription_lines_prescription_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prescription_lines
    ADD CONSTRAINT prescription_lines_prescription_id_foreign FOREIGN KEY (prescription_id) REFERENCES public.prescriptions(id);


--
-- Name: prescriptions prescriptions_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prescriptions
    ADD CONSTRAINT prescriptions_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id);


--
-- Name: product_categories product_categories_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_categories
    ADD CONSTRAINT product_categories_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id);


--
-- Name: products products_product_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_product_category_id_foreign FOREIGN KEY (product_category_id) REFERENCES public.product_categories(id);


--
-- Name: purchase_order_lines purchase_order_lines_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchase_order_lines
    ADD CONSTRAINT purchase_order_lines_item_id_foreign FOREIGN KEY (item_id) REFERENCES public.stock_items(id);


--
-- Name: purchase_order_lines purchase_order_lines_po_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchase_order_lines
    ADD CONSTRAINT purchase_order_lines_po_id_foreign FOREIGN KEY (po_id) REFERENCES public.purchase_orders(id) ON DELETE CASCADE;


--
-- Name: purchase_orders purchase_orders_supplier_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchase_orders
    ADD CONSTRAINT purchase_orders_supplier_id_foreign FOREIGN KEY (supplier_id) REFERENCES public.suppliers(id);


--
-- Name: requisition_lines requisition_lines_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.requisition_lines
    ADD CONSTRAINT requisition_lines_item_id_foreign FOREIGN KEY (item_id) REFERENCES public.stock_items(id);


--
-- Name: requisition_lines requisition_lines_requisition_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.requisition_lines
    ADD CONSTRAINT requisition_lines_requisition_id_foreign FOREIGN KEY (requisition_id) REFERENCES public.requisitions(id) ON DELETE CASCADE;


--
-- Name: requisitions requisitions_from_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.requisitions
    ADD CONSTRAINT requisitions_from_location_id_foreign FOREIGN KEY (from_location_id) REFERENCES public.locations(id);


--
-- Name: requisitions requisitions_requested_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.requisitions
    ADD CONSTRAINT requisitions_requested_by_foreign FOREIGN KEY (requested_by) REFERENCES public.users(id);


--
-- Name: requisitions requisitions_to_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.requisitions
    ADD CONSTRAINT requisitions_to_location_id_foreign FOREIGN KEY (to_location_id) REFERENCES public.locations(id);


--
-- Name: stock_count_lines stock_count_lines_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_count_lines
    ADD CONSTRAINT stock_count_lines_item_id_foreign FOREIGN KEY (item_id) REFERENCES public.stock_items(id);


--
-- Name: stock_count_lines stock_count_lines_lot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_count_lines
    ADD CONSTRAINT stock_count_lines_lot_id_foreign FOREIGN KEY (lot_id) REFERENCES public.stock_lots(id);


--
-- Name: stock_count_lines stock_count_lines_stock_count_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_count_lines
    ADD CONSTRAINT stock_count_lines_stock_count_id_foreign FOREIGN KEY (stock_count_id) REFERENCES public.stock_counts(id) ON DELETE CASCADE;


--
-- Name: stock_count_lines stock_count_lines_stock_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_count_lines
    ADD CONSTRAINT stock_count_lines_stock_transaction_id_foreign FOREIGN KEY (stock_transaction_id) REFERENCES public.stock_transactions(id);


--
-- Name: stock_counts stock_counts_approved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_counts
    ADD CONSTRAINT stock_counts_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES public.users(id);


--
-- Name: stock_counts stock_counts_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_counts
    ADD CONSTRAINT stock_counts_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.locations(id);


--
-- Name: stock_counts stock_counts_performed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_counts
    ADD CONSTRAINT stock_counts_performed_by_foreign FOREIGN KEY (performed_by) REFERENCES public.users(id);


--
-- Name: stock_item_costs stock_item_costs_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_item_costs
    ADD CONSTRAINT stock_item_costs_item_id_foreign FOREIGN KEY (item_id) REFERENCES public.stock_items(id);


--
-- Name: stock_item_costs stock_item_costs_lot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_item_costs
    ADD CONSTRAINT stock_item_costs_lot_id_foreign FOREIGN KEY (lot_id) REFERENCES public.stock_lots(id);


--
-- Name: stock_item_prices stock_item_prices_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_item_prices
    ADD CONSTRAINT stock_item_prices_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id);


--
-- Name: stock_item_prices stock_item_prices_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_item_prices
    ADD CONSTRAINT stock_item_prices_item_id_foreign FOREIGN KEY (item_id) REFERENCES public.stock_items(id);


--
-- Name: stock_lots stock_lots_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_lots
    ADD CONSTRAINT stock_lots_item_id_foreign FOREIGN KEY (item_id) REFERENCES public.stock_items(id);


--
-- Name: stock_transactions stock_transactions_from_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_transactions
    ADD CONSTRAINT stock_transactions_from_location_id_foreign FOREIGN KEY (from_location_id) REFERENCES public.locations(id);


--
-- Name: stock_transactions stock_transactions_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_transactions
    ADD CONSTRAINT stock_transactions_item_id_foreign FOREIGN KEY (item_id) REFERENCES public.stock_items(id);


--
-- Name: stock_transactions stock_transactions_lot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_transactions
    ADD CONSTRAINT stock_transactions_lot_id_foreign FOREIGN KEY (lot_id) REFERENCES public.stock_lots(id);


--
-- Name: stock_transactions stock_transactions_performed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_transactions
    ADD CONSTRAINT stock_transactions_performed_by_foreign FOREIGN KEY (performed_by) REFERENCES public.users(id);


--
-- Name: stock_transactions stock_transactions_to_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_transactions
    ADD CONSTRAINT stock_transactions_to_location_id_foreign FOREIGN KEY (to_location_id) REFERENCES public.locations(id);


--
-- Name: surgeries surgeries_admission_plan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.surgeries
    ADD CONSTRAINT surgeries_admission_plan_id_foreign FOREIGN KEY (admission_plan_id) REFERENCES public.admission_plans(id);


--
-- Name: surgeries surgeries_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.surgeries
    ADD CONSTRAINT surgeries_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id) ON DELETE SET NULL;


--
-- Name: surgery_notes surgery_notes_surgery_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.surgery_notes
    ADD CONSTRAINT surgery_notes_surgery_id_foreign FOREIGN KEY (surgery_id) REFERENCES public.surgeries(id) ON DELETE CASCADE;


--
-- Name: surgery_notes surgery_notes_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.surgery_notes
    ADD CONSTRAINT surgery_notes_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: visits visits_patient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.visits
    ADD CONSTRAINT visits_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES public.patients(id) ON DELETE RESTRICT;


--
-- Name: vitals vitals_recording_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vitals
    ADD CONSTRAINT vitals_recording_user_id_foreign FOREIGN KEY (recording_user_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- PostgreSQL database dump complete
--

\unrestrict uZP6DmQPQcwST2HIsbOn2EJNgslwNpKyCOA5gmuAOdqkBJlJRTUhbxcxiwe0KVX

--
-- PostgreSQL database dump
--

\restrict UxEwWczhgPjtyqSJ65mWInzsHnKF67axFic8Hf8T2x0MCu0FYOZtz6BpOyWvU5s

-- Dumped from database version 16.11 (Ubuntu 16.11-0ubuntu0.24.04.1)
-- Dumped by pg_dump version 16.11 (Ubuntu 16.11-0ubuntu0.24.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_000000_create_users_table	1
2	2014_10_12_100000_create_password_reset_tokens_table	2
3	2019_08_19_000000_create_failed_jobs_table	3
4	2019_12_14_000001_create_personal_access_tokens_table	4
5	2020_06_14_000001_create_media_table	5
6	2023_08_18_221650_create_departments_table	6
7	2023_08_18_221955_create_lab_tests_table	7
8	2023_08_18_223719_create_test_categories_table	8
9	2023_08_18_223803_create_lab_test_categories_table	9
10	2023_08_29_103556_create_patient_categories_table	10
11	2023_08_29_103557_create_patients_table	11
12	2023_08_31_221552_create_antenatal_profiles_table	12
13	2023_08_31_233302_create_visits_table	13
14	2023_08_31_233307_create_general_visits_table	14
15	2023_08_31_233315_create_anc_visits_table	15
16	2023_09_01_073614_add_status_to_antenatal_profiles_table	16
17	2023_09_01_210321_create_notifications_table	17
18	2023_09_01_222647_add_vitals_to_visits_table	18
19	2023_09_01_222736_drop_vitals_from_general_visits_table	19
20	2023_09_01_222812_drop_vitals_from_anc_visits_table	20
21	2023_09_02_132329_create_documentations_table	21
22	2023_09_02_205440_add_antenatal_profile_id_to_anc_visits_table	22
23	2023_09_02_211857_modify_anc_visits_table	23
24	2023_09_03_130034_modify_antenatal_profiles_table	24
25	2023_09_04_053023_add_return_visit_to_anc_visits	25
26	2023_09_04_153906_create_documentation_tests_table	26
27	2023_09_04_161156_create_documentation_prescriptions_table	27
28	2023_09_06_021127_remodify_antenatal_profiles_table	28
29	2023_09_13_124614_add_tests_to_antenatal_profiles_table	29
30	2023_10_01_170826_create_documentation_complaints_table	30
31	2023_10_01_171651_add_history_to_documentations_table	31
32	2023_10_01_173047_create_patient_examinations_table	32
33	2023_10_01_194153_create_patient_imagings_table	33
34	2023_10_01_194830_add_awaiting_radiology_to_visits_table	34
35	2023_10_26_122922_add_frequency_to_documentation_prescriptions_table	35
36	2023_10_26_143547_add_billing_quotes_to_documentation_prescriptions_table	36
37	2023_10_26_194739_create_wards_table	37
38	2023_10_26_195231_create_admissions_table	38
39	2023_10_26_225813_create_insurance_profiles_table	39
40	2023_10_27_080314_create_datalogs_table	40
41	2023_10_27_090707_create_insurance_authorizations_table	41
42	2023_10_27_094047_allow_null_lmp_and_edd_on_antenatal_profiles_table	42
43	2023_10_27_124832_add_documentable_to_documentation_complaints	43
44	2023_10_27_125945_add_documentable_to_patient_imagings	44
45	2023_10_27_131944_add_route_to_documentation_prescriptions	45
46	2023_10_27_235109_create_documented_diagnoses_table	46
47	2023_10_28_014608_add_admittable_to_admissions_table	47
48	2023_10_31_072048_make_discharge_summary_nullable_on_admissions_table	48
49	2023_10_31_192841_add_status_to_admissions	49
50	2023_10_31_210823_create_admission_logs_table	50
51	2023_11_01_195541_add_complaints_duration_to_documentations_table	51
52	2023_11_03_075549_add_duration_to_documentation_complaints_table	52
53	2023_11_15_205607_create_vitals_table	53
54	2023_11_15_205610_add_code_to_departments_table	54
55	2023_11_15_222650_create_admission_treatment_administrations_table	55
56	2024_09_17_064319_create_consultation_notes_table	56
57	2024_09_22_190106_create_product_categories_table	57
58	2024_09_22_190934_create_products_table	58
59	2024_09_22_224108_add_event_morphs_to_documentation_prescriptions_table	59
60	2024_09_23_215518_add_describable_morph_to_documentation_tests	60
61	2024_09_24_035701_add_describable_morph_to_patient_imagings	61
62	2024_09_24_042849_add_visit_type_to_consultation_notes	62
63	2024_09_24_044707_drop_visit_index_on_consultation_notes	63
64	2024_09_25_055246_create_patient_histories_table	64
65	2024_09_25_105128_add_visit_morph_to_patient_examinations	65
66	2024_09_28_151820_drop_tests_from_antenatal_profiles	66
67	2024_10_01_122233_add_risk_assessment_to_antenatal_profiles	67
68	2024_10_23_054633_create_admission_plans_table	68
69	2024_10_23_054634_create_surgeries_table	69
70	2024_10_23_093739_create_surgery_notes_table	70
71	2025_10_12_164350_create_posts_table	71
72	2025_10_15_091647_add_image_column_to_posts	72
73	2025_10_23_000420_create_posts	73
74	2025_10_24_000421_add_slug_to_posts	74
75	2025_10_29_154756_add_closing_to_antenatal_profiles	74
76	2025_10_29_183241_add_consultant_to_visits_table	74
77	2025_10_30_071544_add_recorded_date_to_vitals_table	74
78	2025_10_30_080407_create_bills_table	74
79	2025_10_30_080653_create_bill_details_table	74
80	2025_10_30_080758_create_bill_payments_table	74
81	2025_10_31_175245_add_status_to_admission_plans_table	74
82	2025_11_01_072553_remove_vitals_column_from_visits_table	74
83	2025_11_05_005958_add_status_to_bill_details_table	74
84	2025_11_08_184950_add_deleted_at_to_admission_treatment_administrations	74
85	2025_11_08_185514_add_deleted_at_to_documentation_prescriptions	74
86	2025_11_08_190828_modify_foreign_constraint_on_admission_treatment_administrations	74
87	2025_11_08_195118_add_spo2_to_vitals	74
88	2025_11_11_094658_add_status_to_bill__details	74
89	2025_11_11_204623_add_fhr_to_vitals	74
90	2025_11_11_222947_add_examination_to_antenatal_profiles	74
91	2025_11_12_165925_add_immunization_fields_to_anc_visits	74
92	2025_11_12_220937_drop_complaints_from_anc_visits	74
93	2025_11_19_162920_create_operation_notes_table	74
94	2025_11_20_082844_add_code_to_consultation_notes	74
95	2025_11_24_192853_add_results_to_patient_imagings	74
227	2025_12_03_131135_create_stock_items_table	75
228	2025_12_03_131435_create_suppliers_table	75
229	2025_12_03_131536_create_locations_table	75
230	2025_12_03_131839_create_stock_lots_table	75
231	2025_12_03_132527_create_stock_transactions_table	75
232	2025_12_03_144504_create_purchase_orders_table	75
233	2025_12_03_144630_create_purchase_order_lines_table	75
234	2025_12_03_152943_create_requisitions_table	75
235	2025_12_03_153201_create_requisition_lines_table	75
236	2025_12_03_164850_create_stock_counts_table	75
237	2025_12_03_165233_create_stock_count_lines_table	75
238	2025_12_03_165452_create_inventory_balances_table	75
239	2025_12_03_200046_create_stock_item_costs_table	75
240	2025_12_03_200055_create_stock_item_prices_table	75
241	2025_12_03_200914_extends_stock_transactions	75
242	2025_12_05_223223_setup_stored_procedures_for_inventory	75
243	2025_12_08_091139_fix_unique_index_on_inventory_balances	75
244	2025_12_08_102432_add_item_info_to_stock_items	75
245	2025_12_08_141000_add_more_to_vitals	75
246	2025_12_09_122224_create_prescriptions_table	75
247	2025_12_09_122227_create_prescription_lines_table	75
248	2025_12_10_070004_add_profile_to_prescription_lines	75
249	2025_12_10_084204_add_description_to_prescription_lines	75
250	2025_12_10_133207_add_qty_dispensed_to_prescription_lines	75
251	2025_12_11_182311_create_dispense_lines_table	75
253	2025_12_11_223053_correct_rebuild_inventory_function	76
254	2025_12_12_141232_soft_delete_stock_items	77
255	2025_12_15_121128_ad_status_to_stock_counts	78
259	2025_12_15_121128_add_status_to_stock_counts	79
260	2025_12_15_152628_modify_columns_on_stock_count_lines	79
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 260, true);


--
-- PostgreSQL database dump complete
--

\unrestrict UxEwWczhgPjtyqSJ65mWInzsHnKF67axFic8Hf8T2x0MCu0FYOZtz6BpOyWvU5s

